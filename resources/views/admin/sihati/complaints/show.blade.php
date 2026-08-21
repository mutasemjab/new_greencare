@extends('admin.layouts.app')
@section('title', 'تفاصيل الشكوى')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.sihati.complaints.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">شكوى #{{ $complaint->id }}</h4>
        @if($complaint->status === 'reviewed')
            <span class="badge bg-success-subtle text-success fs-6">تمت المراجعة</span>
        @else
            <span class="badge bg-warning-subtle text-warning fs-6">قيد المراجعة</span>
        @endif
    </div>

    @include('admin.includes.alerts.success')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-chat-left-text me-2"></i>نص الشكوى
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $complaint->complaint_text }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-info-circle me-2"></i>معلومات
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">الغرفة</span>
                            <span class="fw-semibold">
                                @if($complaint->room)
                                    <a href="{{ route('admin.sihati.rooms.show', $complaint->room) }}">{{ $complaint->room->name }}</a>
                                @else
                                    —
                                @endif
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">المريض</span>
                            <span class="fw-semibold">{{ $complaint->patient?->name ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">مقدّم الشكوى</span>
                            <span class="fw-semibold">{{ $complaint->submittedBy?->name ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">التاريخ</span>
                            <span class="fw-semibold">{{ $complaint->created_at->format('Y/m/d H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            @if($complaint->status !== 'reviewed')
            <form action="{{ route('admin.sihati.complaints.reviewed', $complaint) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-check-lg me-1"></i> وضع علامة "تمت المراجعة"
                </button>
            </form>
            @endif
        </div>
    </div>

</div>
@endsection
