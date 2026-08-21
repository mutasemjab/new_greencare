@extends('admin.layouts.app')
@section('title', 'تفاصيل نموذج الزيارة')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.sihati.visit-forms.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">نموذج زيارة #{{ $visitForm->id }}</h4>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-clipboard2-pulse me-2"></i>الإجابات
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>السؤال</th>
                                    <th>الإجابة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visitForm->answers as $answer)
                                <tr>
                                    <td class="fw-semibold">{{ $answer->field_question }}</td>
                                    <td>{{ $answer->display_answer }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">لا توجد إجابات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($visitForm->attachments->isNotEmpty())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-paperclip me-2"></i>المرفقات
                </div>
                <div class="card-body d-flex flex-wrap gap-2">
                    @foreach($visitForm->attachments as $attachment)
                        <a href="{{ $attachment->url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark me-1"></i> مرفق {{ $loop->iteration }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-info-circle me-2"></i>معلومات
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">المريض</span>
                            <span class="fw-semibold">{{ $visitForm->patient?->name ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">الممرض المسؤول</span>
                            <span class="fw-semibold">{{ $visitForm->submittedBy?->name ?? '—' }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted">التاريخ</span>
                            <span class="fw-semibold">{{ $visitForm->created_at->format('Y/m/d H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
