@extends('admin.layouts.app')

@section('title', 'بطاقات مجموعة: ' . $group->name)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.bathing.cards') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">بطاقات مجموعة: {{ $group->name }}</h4>
    </div>

    @include('admin.includes.alerts.success')

    {{-- Group summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">عدد البطاقات</div>
                    <div class="fs-4 fw-bold">{{ $cards->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">سعر البطاقة الواحدة</div>
                    <div class="fs-4 fw-bold">{{ number_format($group->unit_price, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">نقطة البيع</div>
                    <div class="fs-5 fw-semibold">{{ $group->pointOfSale?->name ?? '—' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">تاريخ التوليد</div>
                    <div class="fs-6 fw-semibold">{{ $group->created_at->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>كود البطاقة</th>
                            <th>الاستخدامات القصوى</th>
                            <th>مرات الاستخدام</th>
                            <th>المتبقية</th>
                            <th>الحالة</th>
                            <th>من استخدمها</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cards as $card)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="text-center fw-semibold">{{ $card->code }}</td>
                            <td class="text-center">{{ $card->max_uses }}</td>
                            <td class="text-center">{{ $card->used_count }}</td>
                            <td class="text-center">
                                <span class="{{ ($card->max_uses - $card->used_count) <= 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                    {{ $card->max_uses - $card->used_count }}
                                </span>
                            </td>
                            <td>
                                @if($card->is_active)
                                    <span class="badge bg-success-subtle text-success">فعّالة</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">معطّلة</span>
                                @endif
                            </td>
                            <td>
                                @forelse($card->requests as $usage)
                                    <div class="small mb-1">
                                        <span class="fw-semibold">{{ $usage->user?->name ?? 'مستخدم محذوف' }}</span>
                                        <span class="text-muted">— {{ $usage->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                @empty
                                    <span class="text-muted small">لم تُستخدم بعد</span>
                                @endforelse
                            </td>
                            <td>
                                <form action="{{ route('admin.bathing.cards.destroy', $card) }}"
                                    method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من حذف البطاقة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد بطاقات في هذه المجموعة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($cards->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-center">
            {{ $cards->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
