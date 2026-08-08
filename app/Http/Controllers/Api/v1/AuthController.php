<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to phone number.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $request->phone;

        $userExists = User::where('phone', $phone)->exists();

        // Find or create user
        User::firstOrCreate(
            ['phone' => $phone],
            [
                'role'      => 'patient',
                'is_active' => true,
            ]
        );

        $otpResult = $this->otpService->sendOTP($phone);

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
     * Resend OTP to phone number.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $request->phone;

        $otpResult = $this->otpService->sendOTP($phone);

        if (!$otpResult['success']) {
            return $this->error($otpResult['message'], $otpResult['error'] ?? null);
        }

        return $this->success([
            'otp_sent' => true,
            'otp'      => $otpResult['otp'] ?? null,
        ], 'تم إعادة إرسال رمز التحقق');
    }

    /**
     * Verify OTP and return token.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp'   => 'required|string',
        ]);

        $otpResult = $this->otpService->verifyOTPWithTestCase($request->phone, $request->otp);

        if (!$otpResult['success']) {
            return $this->error($otpResult['message'], $otpResult['error_code'] ?? null, 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return $this->error('المستخدم غير موجود', null, 404);
        }

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
