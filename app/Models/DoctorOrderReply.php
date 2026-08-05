<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorOrderReply extends Model
{
    protected $fillable = ['doctor_order_id', 'nurse_id', 'reply_text'];

    public function order()
    {
        return $this->belongsTo(DoctorOrder::class, 'doctor_order_id');
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }
}
