<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PatientTransferResource;
use App\Http\Traits\ApiResponse;
use App\Models\PatientTransfer;
use App\Models\DisplayNoteTransfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    use ApiResponse;

    public function display_note_in_transfer()
    {
        $note = DisplayNoteTransfer::first();
        
        return $this->success($note);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'from_location'    => 'required|string',
            'from_lat'         => 'required|numeric',
            'from_lng'         => 'required|numeric',
            'to_location'      => 'required|string',
            'to_lat'           => 'required|numeric',
            'to_lng'           => 'required|numeric',
            'case_description' => 'sometimes|nullable|string',
            'date'             => 'required|date',
            'time'             => 'required|string',
            'notes'            => 'sometimes|nullable|string',
        ]);

        $transfer = PatientTransfer::create([
            'user_id'          => $request->user('user-api')->id,
            'from_location'    => $request->from_location,
            'from_latitude'    => $request->from_lat,
            'from_longitude'   => $request->from_lng,
            'to_location'      => $request->to_location,
            'to_latitude'      => $request->to_lat,
            'to_longitude'     => $request->to_lng,
            'booking_date'     => $request->date,
            'booking_time'     => $request->time,
            'case_description' => $request->case_description,
            'notes'            => $request->notes,
            'status'           => 'pending',
        ]);

        $transfer->load('fromZone', 'toZone');

        return $this->success(new PatientTransferResource($transfer), 'تم إرسال طلب النقل', 201);
    }

    public function index(Request $request)
    {
        $transfers = PatientTransfer::where('user_id', $request->user('user-api')->id)
            ->latest()
            ->paginate(15);

        return $this->success(PatientTransferResource::collection($transfers)->response()->getData(true));
    }

    public function show(Request $request, int $id)
    {
        $transfer = PatientTransfer::where('user_id', $request->user('user-api')->id)
            ->findOrFail($id);

        return $this->success(new PatientTransferResource($transfer));
    }
}
