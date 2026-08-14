<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BathingCard;
use App\Models\BathingCardGroup;
use App\Models\BathingRequest;
use App\Models\PointOfSale;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BathingController extends Controller
{
    // ── Points of Sale ────────────────────────────────────────────────────

    public function pointsOfSale(Request $request)
    {
        $query = PointOfSale::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('address', 'like', "%{$request->search}%");
            });
        }

        $points = $query->paginate(20)->withQueryString();

        return view('admin.bathing.pos.index', compact('points'));
    }

    public function createPoint()
    {
        return view('admin.bathing.pos.create');
    }

    public function storePoint(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string|max:500',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        PointOfSale::create($data);

        return redirect()->route('admin.bathing.pos')
            ->with('success', 'تم إضافة نقطة البيع بنجاح');
    }

    public function editPoint(PointOfSale $point)
    {
        return view('admin.bathing.pos.edit', compact('point'));
    }

    public function updatePoint(Request $request, PointOfSale $point)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string|max:500',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);

        $point->update($data);

        return redirect()->route('admin.bathing.pos')
            ->with('success', 'تم تعديل نقطة البيع بنجاح');
    }

    public function destroyPoint(PointOfSale $point)
    {
        $point->delete();

        return redirect()->route('admin.bathing.pos')
            ->with('success', 'تم حذف نقطة البيع');
    }

    // ── Card Groups ──────────────────────────────────────────────────────

    public function cards(Request $request)
    {
        $query = BathingCardGroup::withCount('cards')->with('pointOfSale')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $groups = $query->paginate(20)->withQueryString();

        return view('admin.bathing.cards.index', compact('groups'));
    }

    public function generateCards(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'unit_price'        => 'required|numeric|min:0',
            'quantity'          => 'required|integer|min:1|max:500',
            'max_uses'          => 'required|integer|min:1|max:20',
            'sold_at_point_id'  => 'nullable|exists:points_of_sale,id',
        ]);

        $group = BathingCardGroup::create([
            'name'             => $data['name'],
            'unit_price'       => $data['unit_price'],
            'sold_at_point_id' => $data['sold_at_point_id'] ?? null,
        ]);

        for ($i = 0; $i < $data['quantity']; $i++) {
            $code = (string) random_int(100000, 999999);

            // ensure uniqueness
            while (BathingCard::where('code', $code)->exists()) {
                $code = (string) random_int(100000, 999999);
            }

            BathingCard::create([
                'bathing_card_group_id' => $group->id,
                'code'                  => $code,
                'max_uses'              => $data['max_uses'],
                'used_count'            => 0,
                'sold_at_point_id'      => $data['sold_at_point_id'] ?? null,
            ]);
        }

        return redirect()->route('admin.bathing.cards')
            ->with('success', "تم توليد {$data['quantity']} بطاقة ضمن مجموعة \"{$group->name}\" بنجاح");
    }

    public function generateCardsForm()
    {
        $points = PointOfSale::active()->get();

        return view('admin.bathing.cards.generate', compact('points'));
    }

    public function showGroup(BathingCardGroup $group)
    {
        $group->load('pointOfSale');

        $cards = $group->cards()
            ->with(['requests' => fn ($q) => $q->with('user')->latest()])
            ->latest()
            ->paginate(20);

        return view('admin.bathing.cards.show', compact('group', 'cards'));
    }

    public function destroyCard(BathingCard $card)
    {
        $groupId = $card->bathing_card_group_id;
        $card->delete();

        return $groupId
            ? redirect()->route('admin.bathing.cards.show', $groupId)->with('success', 'تم حذف البطاقة')
            : redirect()->route('admin.bathing.cards')->with('success', 'تم حذف البطاقة');
    }

    public function destroyGroup(BathingCardGroup $group)
    {
        $group->delete();

        return redirect()->route('admin.bathing.cards')
            ->with('success', 'تم حذف المجموعة');
    }

    // ── Requests ──────────────────────────────────────────────────────────

    public function requests(Request $request)
    {
        $query = BathingRequest::with(['user', 'bathingCard', 'pointOfSale'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('search')) {
            $query->where('patient_code', 'like', "%{$request->search}%")
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.bathing.requests.index', compact('requests'));
    }

    public function showRequest(BathingRequest $request)
    {
        $request->load(['user', 'bathingCard.pointOfSale', 'pointOfSale', 'address']);

        return view('admin.bathing.requests.show', compact('request'));
    }

    public function updateRequestStatus(Request $httpRequest, BathingRequest $request)
    {
        $httpRequest->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        $request->update(['status' => $httpRequest->status]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }
}
