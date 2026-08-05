<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareRequestService extends Model
{
    use HasFactory;

    protected $fillable = ['care_request_id', 'care_service_id', 'unit_price'];

    protected $casts = ['unit_price' => 'decimal:2'];

    public function careRequest()
    {
        return $this->belongsTo(CareRequest::class);
    }

    public function service()
    {
        return $this->belongsTo(CareService::class, 'care_service_id');
    }
}
