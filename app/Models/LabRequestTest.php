<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabRequestTest extends Model
{
    use HasFactory;

    protected $fillable = ['lab_request_id', 'lab_test_id', 'unit_price'];

    protected $casts = ['unit_price' => 'decimal:2'];

    public function labRequest()
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function test()
    {
        return $this->belongsTo(LabTest::class, 'lab_test_id');
    }
}
