<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XrayRequestTest extends Model
{
    use HasFactory;

    protected $fillable = ['xray_request_id', 'xray_test_id', 'unit_price'];

    protected $casts = ['unit_price' => 'decimal:2'];

    public function xrayRequest()
    {
        return $this->belongsTo(XrayRequest::class);
    }

    public function test()
    {
        return $this->belongsTo(XrayTest::class, 'xray_test_id');
    }
}
