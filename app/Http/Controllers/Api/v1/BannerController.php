<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BannerResource;
use App\Http\Traits\ApiResponse;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $query = Banner::active();

        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        $banners = $query->orderBy('sort_order')->get();

        return $this->success(BannerResource::collection($banners));
    }
}
