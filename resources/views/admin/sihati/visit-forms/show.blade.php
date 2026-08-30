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

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-eyedropper me-2"></i>طلبات التحاليل بهذا الكود
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الفحوصات</th>
                                <th>التاريخ</th>
                                <th>القيمة</th>
                                <th>الحالة</th>
                                <th>النتيجة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visitForm->labRequests as $lab)
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td>{{ $lab->tests->pluck('test.name')->filter()->implode('، ') ?: '—' }}</td>
                                <td class="small">{{ $lab->booking_date?->format('Y/m/d') ?? '—' }}</td>
                                <td class="small">{{ number_format($lab->total, 2) }} د.أ</td>
                                <td>
                                    <span class="badge bg-{{ $lab->status_color }}-subtle text-{{ $lab->status_color }}">
                                        {{ $lab->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($lab->result_file_url)
                                        <a href="{{ $lab->result_file_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-file-earmark-pdf"></i> عرض
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">لا توجد طلبات تحاليل بهذا الكود</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-radioactive me-2"></i>طلبات الأشعة بهذا الكود
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الأشعة</th>
                                <th>التاريخ</th>
                                <th>القيمة</th>
                                <th>الحالة</th>
                                <th>النتيجة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visitForm->xrayRequests as $xray)
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td>{{ $xray->tests->pluck('test.name')->filter()->implode('، ') ?: '—' }}</td>
                                <td class="small">{{ $xray->booking_date?->format('Y/m/d') ?? '—' }}</td>
                                <td class="small">{{ number_format($xray->total, 2) }} د.أ</td>
                                <td>
                                    <span class="badge bg-{{ $xray->status_color }}-subtle text-{{ $xray->status_color }}">
                                        {{ $xray->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($xray->result_file_url)
                                        <a href="{{ $xray->result_file_url }}" target="_blank" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-file-earmark-pdf"></i> عرض
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">لا توجد طلبات أشعة بهذا الكود</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @include('admin.includes.alerts.success')

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
                            <span class="text-muted">كود الزيارة</span>
                            <span class="fw-semibold text-primary">{{ $visitForm->code ?? '—' }}</span>
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

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-percent me-2"></i>نسبة الخصم عند استخدام الكود
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.sihati.visit-forms.discount', $visitForm) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">نسبة الخصم (%)</label>
                            <input type="number" name="discount_value" min="0" max="100" step="0.01"
                                value="{{ old('discount_value', $visitForm->discount_value) }}"
                                class="form-control @error('discount_value') is-invalid @enderror">
                            @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-1"></i> حفظ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
