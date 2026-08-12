<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Mail\JobApplicationReceived;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CareerController extends Controller
{
    use ApiResponse;

    public function apply(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20',
            'email'    => 'sometimes|nullable|email|max:255',
            'position' => 'required|string|max:255',
            'notes'    => 'sometimes|nullable|string',
            'cv'       => 'sometimes|nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('cv')) {
            $data['cv'] = $request->file('cv')->store('careers', 'public');
        }

        $data['user_id'] = $request->user('user-api')->id;

        $application = JobApplication::create($data);

        dispatch(function () use ($application) {
            $hrEmail = env('HR_NOTIFICATION_EMAIL', config('mail.from.address'));
            Mail::to($hrEmail)->send(new JobApplicationReceived($application));
        })->afterResponse();

        return $this->success(['id' => $application->id], 'تم إرسال طلبك بنجاح', 201);
    }
}
