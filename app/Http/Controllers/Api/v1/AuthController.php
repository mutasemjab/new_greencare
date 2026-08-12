<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to phone or email — whichever the app sent.
     */
    public function sendOtp(Request $request)
    {
        ['field' => $field, 'value' => $identifier] = $this->resolveIdentifier($request);

        $userExists = User::where($field, $identifier)->exists();

        $otpResult = $this->issueOtp($identifier, $field);

        if (!$otpResult['success']) {
            return $this->error($otpResult['message'], $otpResult['error'] ?? null);
        }

        return $this->success([
            'otp_sent'    => true,
            'otp'         => $otpResult['otp'] ?? null,
            'is_new_user' => !$userExists,
        ], 'تم إرسال رمز التحقق');
    }

    /**
     * Resend OTP to phone or email — whichever the app sent.
     */
    public function resendOtp(Request $request)
    {
        ['field' => $field, 'value' => $identifier] = $this->resolveIdentifier($request);

        $otpResult = $this->issueOtp($identifier, $field);

        if (!$otpResult['success']) {
            return $this->error($otpResult['message'], $otpResult['error'] ?? null);
        }

        return $this->success([
            'otp_sent' => true,
            'otp'      => $otpResult['otp'] ?? null,
        ], 'تم إعادة إرسال رمز التحقق');
    }

    /**
     * Validate that exactly one of phone/email was sent, and return which one.
     */
    private function resolveIdentifier(Request $request, bool $requireOtp = false): array
    {
        $rules = [
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
        ];

        if ($requireOtp) {
            $rules['otp'] = 'required|string';
        }

        $request->validate($rules);

        $hasPhone = $request->filled('phone');
        $hasEmail = $request->filled('email');

        if ($hasPhone === $hasEmail) {
            throw ValidationException::withMessages([
                'phone' => ['يجب إرسال رقم الهاتف أو البريد الإلكتروني فقط'],
            ]);
        }

        return $hasPhone
            ? ['field' => 'phone', 'value' => $request->phone]
            : ['field' => 'email', 'value' => $request->email];
    }

    /**
     * Generate and cache the OTP synchronously (fast), then send it via
     * SMS/email after the HTTP response has already gone out — the delivery
     * channel can be slow/unreachable and must never block the mobile app's request.
     */
    private function issueOtp(string $identifier, string $field): array
    {
        try {
            $otp = $this->otpService->generateOTP();
            $this->otpService->storeOTP($identifier, $otp);
        } catch (\Throwable $e) {
            Log::error('Failed to generate/store OTP', ['identifier' => $identifier, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Failed to send OTP',
                'error'   => $e->getMessage(),
            ];
        }

        dispatch(function () use ($identifier, $otp, $field) {
            $service = app(OtpService::class);

            if ($field === 'email') {
                $service->sendOTPEmail($identifier, $otp);
            } else {
                $service->sendOTPSMS($identifier, $otp);
            }
        })->afterResponse();

        return [
            'success' => true,
            'otp'     => config('app.debug') ? $otp : null,
        ];
    }

    /**
     * Verify OTP and return token.
     */
    public function verifyOtp(Request $request)
    {
        ['field' => $field, 'value' => $identifier] = $this->resolveIdentifier($request, requireOtp: true);

        $otpResult = $this->otpService->verifyOTPWithTestCase($identifier, $request->otp);

        if (!$otpResult['success']) {
            return $this->error($otpResult['message'], $otpResult['error_code'] ?? null, 422);
        }

        $user = User::firstOrCreate(
            [$field => $identifier],
            [
                'name'      => $identifier,
                'role'      => 'patient',
                'is_active' => true,
            ]
        );

        if (!$user->is_active) {
            return $this->error('الحساب غير مفعّل', null, 403);
        }

        $token = $user->createToken('mobile')->accessToken;

        return $this->success([
            'token' => $token,
            'user'  => new UserResource($user),
        ], 'تم تسجيل الدخول بنجاح');
    }

    /**
     * Revoke the current token (logout).
     */
    public function logout(Request $request)
    {
        $request->user('user-api')->token()->revoke();

        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Return the authenticated user.
     */
    public function me(Request $request)
    {
        return $this->success(new UserResource($request->user('user-api')));
    }

    /**
     * Update profile fields.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|nullable|email|max:255',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender'        => 'sometimes|nullable|in:male,female',
        ]);

        $user = $request->user('user-api');
        $user->update($request->only(['name', 'email', 'date_of_birth', 'gender']));

        return $this->success(new UserResource($user->fresh()), 'تم تحديث البيانات');
    }

    /**
     * Update FCM push token.
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user('user-api')->update(['fcm_token' => $request->fcm_token]);

        return $this->success(null, 'تم تحديث رمز الإشعارات');
    }
}
