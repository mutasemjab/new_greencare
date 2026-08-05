@extends('admin.layouts.app')

@section('title', 'بطاقات الاستحمام')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">بطاقات الاستحمام</h4>
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
                        placeholder="بحث بالكود..."
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
                            <th>الكود</th>
                            <th>الاستخدامات القصوى</th>
                            <th>المستخدمة</th>
                            <th>المتبقية</th>
                            <th>نقطة البيع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cards as $card)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <code class="fs-6 user-select-all">{{ $card->code }}</code>
                            </td>
                            <td class="text-center">{{ $card->max_uses }}</td>
                            <td class="text-center">{{ $card->used_count }}</td>
                            <td class="text-center">
                                <span class="{{ ($card->max_uses - $card->used_count) <= 0 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                    {{ $card->max_uses - $card->used_count }}
                                </span>
                            </td>
                            <td>{{ $card->soldAtPoint?->name ?? '—' }}</td>
                            <td>
                                @if($card->is_active)
                                    <span class="badge bg-success-subtle text-success">فعّالة</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">معطّلة</span>
                                @endif
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
                                لا توجد بطاقات بعد
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
