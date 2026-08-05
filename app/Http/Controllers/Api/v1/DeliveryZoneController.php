<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\DeliveryZone;

class DeliveryZoneController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $zones = DeliveryZone::where('is_active', true)
            ->get(['id', 'name', 'fee']);

        return $this->success($zones);
    }
}
