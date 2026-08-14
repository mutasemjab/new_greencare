@extends('admin.layouts.app')

@section('title', 'بطاقات الاستحمام')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">مجموعات بطاقات الاستحمام</h4>
        <a href="{{ route('admin.bathing.cards.generate') }}" class="btn btn-success btn-lg">
            <i class="bi bi-plus-square me-1"></i> توليد بطاقات
        </a>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bathing.cards') }}" class="row g-3">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control"
                        placeholder="بحث باسم المجموعة..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                    <a href="{{ route('admin.bathing.cards') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>اسم المجموعة</th>
                            <th>عدد البطاقات</th>
                            <th>سعر البطاقة الواحدة</th>
                            <th>نقطة البيع</th>
                            <th>تاريخ التوليد</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $group->name }}</td>
                            <td class="text-center">{{ $group->cards_count }}</td>
                            <td class="text-center">{{ number_format($group->unit_price, 2) }}</td>
                            <td>{{ $group->pointOfSale?->name ?? '—' }}</td>
                            <td class="text-muted small">{{ $group->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.bathing.cards.show', $group) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> عرض البطاقات
                                    </a>
                                    <form action="{{ route('admin.bathing.cards.group.destroy', $group) }}"
                                        method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من حذف المجموعة وجميع بطاقاتها؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد مجموعات بطاقات بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($groups->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $groups->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
