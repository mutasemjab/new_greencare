<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AddressResource;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $addresses = $request->user('user-api')
            ->addresses()
            ->with('deliveryZone')
            ->get();

        return $this->success(AddressResource::collection($addresses));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label'            => 'required|string|max:255',
            'address'          => 'nullable|string',
            'city'             => 'nullable|string|max:255',
            'delivery_zone_id' => 'required|exists:delivery_zones,id',
            'latitude'         => 'required|numeric',
            'longitude'        => 'required|numeric',
            'is_default'       => 'sometimes|boolean',
        ]);

        $user = $request->user('user-api');

        if ($request->boolean('is_default')) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            'label'            => $request->label,
            'address'          => $request->address ?? null,
            'city'             => $request->city ?? null,
            'delivery_zone_id' => $request->delivery_zone_id,
            'latitude'         => $request->latitude,
            'longitude'        => $request->longitude,
            'is_default'       => $request->boolean('is_default', false),
        ]);

        $address->load('deliveryZone');

        return $this->success(new AddressResource($address), 'تم إضافة العنوان', 201);
    }

    public function update(Request $request, $id)
    {
        $user    = $request->user('user-api');
        $address = $user->addresses()->findOrFail($id);

        $request->validate([
            'label'            => 'sometimes|string|max:255',
            'address'          => 'sometimes|string',
            'city'             => 'sometimes|string|max:255',
            'delivery_zone_id' => 'sometimes|exists:delivery_zones,id',
            'latitude'         => 'sometimes|numeric',
            'longitude'        => 'sometimes|numeric',
            'is_default'       => 'sometimes|boolean',
        ]);

        if ($request->boolean('is_default')) {
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($request->only([
            'label', 'address', 'city', 'delivery_zone_id',
            'latitude', 'longitude', 'is_default',
        ]));

        $address->load('deliveryZone');

        return $this->success(new AddressResource($address), 'تم تحديث العنوان');
    }

    public function destroy(Request $request, $id)
    {
        $address = $request->user('user-api')->addresses()->findOrFail($id);
        $address->delete();

        return $this->success(null, 'تم حذف العنوان');
    }

    public function setDefault(Request $request, $id)
    {
        $user = $request->user('user-api');
        $user->addresses()->update(['is_default' => false]);
        $address = $user->addresses()->findOrFail($id);
        $address->update(['is_default' => true]);

        return $this->success(new AddressResource($address->load('deliveryZone')), 'تم تعيين العنوان الافتراضي');
    }
}
