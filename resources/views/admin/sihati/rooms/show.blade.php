@extends('admin.layouts.app')
@section('title', 'غرفة: ' . $room->name)

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <a href="{{ route('admin.sihati.rooms.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">{{ $room->name }}</h4>
            <span class="small text-muted">
                مريض: {{ $room->patient?->name }}
                &nbsp;·&nbsp;
                أنشأها: {{ $room->createdBy?->name ?? '—' }}
            </span>
        </div>
        @if($room->is_active)
            <span class="badge bg-success-subtle text-success">نشطة</span>
        @else
            <span class="badge bg-danger-subtle text-danger">معطلة</span>
        @endif
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.sihati.rooms.edit', $room) }}" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> تعديل
            </a>
            <form action="{{ route('admin.sihati.rooms.toggle', $room) }}" method="POST">
                @csrf
                @method('PATCH')
                <button class="btn btn-sm {{ $room->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                    <i class="bi bi-{{ $room->is_active ? 'pause-circle' : 'play-circle' }} me-1"></i>
                    {{ $room->is_active ? 'تعطيل الغرفة' : 'تفعيل الغرفة' }}
                </button>
            </form>
            <form action="{{ route('admin.sihati.rooms.destroy', $room) }}" method="POST"
                onsubmit="return confirm('هل أنت متأكد من حذف هذه الغرفة؟ لا يمكن التراجع عن هذا الإجراء.')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> حذف
                </button>
            </form>
        </div>
    </div>

    @include('admin.includes.alerts.success')

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="roomTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-info">
                <i class="bi bi-info-circle me-1"></i>المعلومات
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-members">
                <i class="bi bi-people me-1"></i>الأعضاء
                <span class="badge bg-secondary ms-1">{{ $members->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-templates">
                <i class="bi bi-journal-medical me-1"></i>القوالب
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-reports">
                <i class="bi bi-clipboard2-pulse me-1"></i>التقارير
                <span class="badge bg-secondary ms-1">{{ $reports->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-orders">
                <i class="bi bi-chat-left-text me-1"></i>أوامر الدكتور
                <span class="badge bg-secondary ms-1">{{ $doctorOrders->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-medications">
                <i class="bi bi-capsule me-1"></i>الأدوية
                <span class="badge bg-secondary ms-1">{{ $medications->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ── Tab: Info ─────────────────────────────────────────────────── --}}
        <div class="tab-pane fade show active" id="tab-info">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-house me-2"></i>بيانات الغرفة
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">المريض</span>
                                    <span class="fw-semibold">{{ $room->patient?->name ?? '—' }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">كود المريض</span>
                                    <span class="fw-semibold text-primary">{{ $room->patient_code ?? '—' }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الرعاية الصحية أنشأها</span>
                                    <span class="fw-semibold">{{ $room->createdBy?->name ?? '—' }}</span>
                                </li>
                                @if($room->address)
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">العنوان</span>
                                    <span class="fw-semibold">{{ $room->address }}</span>
                                </li>
                                @endif
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">نسبة الخصم</span>
                                    <span class="fw-semibold">
                                        @if($room->discount_value > 0)
                                            <span class="badge bg-warning-subtle text-warning fs-6">{{ $room->discount_value }}%</span>
                                        @else
                                            لا يوجد خصم
                                        @endif
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">قالب التسجيل</span>
                                    <span class="fw-semibold">{{ $room->registrationTemplate?->name ?? '—' }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2">
                                    <span class="text-muted">تاريخ الإنشاء</span>
                                    <span>{{ $room->created_at->format('Y/m/d') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Registration Report --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i>تقرير التسجيل
                        </div>
                        <div class="card-body">
                            @if($registrationReport)
                                <div class="small text-muted mb-3">
                                    قدّمه: {{ $registrationReport->submittedBy?->name }}
                                    — {{ $registrationReport->submitted_at?->format('Y/m/d H:i') }}
                                </div>
                                <ul class="list-unstyled mb-0">
                                    @foreach($registrationReport->answers as $answer)
                                    <li class="py-2 border-bottom">
                                        <div class="text-muted small">{{ $answer->field_question }}</div>
                                        <div class="fw-semibold">{{ $answer->display_answer }}</div>
                                    </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-clipboard fs-3 d-block mb-2"></i>
                                    لم يُقدَّم تقرير التسجيل بعد
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Patient intake data --}}
            <div class="row g-4 mt-1">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-person-vcard me-2"></i>بيانات المريض (نموذج الاستقبال)
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">العمر</span>
                                    <span class="fw-semibold">{{ $room->age ?? '—' }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الجنس</span>
                                    <span class="fw-semibold">{{ $room->gender === 'male' ? 'ذكر' : ($room->gender === 'female' ? 'أنثى' : '—') }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الوزن</span>
                                    <span class="fw-semibold">{{ $room->weight ? $room->weight . ' كغ' : '—' }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">فصيلة الدم</span>
                                    <span class="fw-semibold">{{ $room->blood_group ?? '—' }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الحالة الاجتماعية</span>
                                    <span class="fw-semibold">
                                        @php
                                            $maritalMap = ['single'=>'أعزب','married'=>'متزوج','divorced'=>'مطلق','widowed'=>'أرمل'];
                                        @endphp
                                        {{ $maritalMap[$room->marital_status] ?? '—' }}
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">الحالة الوظيفية</span>
                                    <span class="fw-semibold">
                                        @php
                                            $funcMap = ['independent'=>'مستقل','partially_dependent'=>'معتمد جزئياً','fully_dependent'=>'معتمد كلياً'];
                                        @endphp
                                        {{ $funcMap[$room->functional_status] ?? '—' }}
                                    </span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">العرق</span>
                                    <span class="fw-semibold">{{ $room->race === 'white' ? 'أبيض' : ($room->race === 'black' ? 'أسود' : '—') }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">المستوى التعليمي</span>
                                    <span class="fw-semibold">{{ $room->education_level ?? '—' }}</span>
                                </li>
                                <li class="d-flex justify-content-between py-2">
                                    <span class="text-muted">الحساسية</span>
                                    <span class="fw-semibold">
                                        @if($room->has_allergies)
                                            <span class="badge bg-warning-subtle text-warning">{{ $room->allergy_details ?: 'يوجد' }}</span>
                                        @else
                                            لا يوجد
                                        @endif
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-clipboard2-pulse me-2"></i>التشخيصات والأمراض المزمنة والمرفقات
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small mb-1">التشخيصات</div>
                                @forelse($room->diagnoses as $diagnosis)
                                    <span class="badge bg-info-subtle text-info me-1 mb-1">{{ $diagnosis->name }}</span>
                                @empty
                                    <span class="text-muted small">لا يوجد</span>
                                @endforelse
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small mb-1">الأمراض المزمنة</div>
                                @forelse($room->chronicDiseases as $chronicDisease)
                                    <span class="badge bg-secondary-subtle text-secondary me-1 mb-1">{{ $chronicDisease->name }}</span>
                                @empty
                                    <span class="text-muted small">لا يوجد</span>
                                @endforelse
                            </div>
                            <div>
                                <div class="text-muted small mb-1">المرفقات</div>
                                @forelse($room->attachments as $attachment)
                                    <a href="{{ $attachment->url }}" target="_blank" class="btn btn-sm btn-outline-secondary me-1 mb-1">
                                        <i class="bi bi-file-earmark me-1"></i> {{ $attachment->original_name ?? 'مرفق ' . $loop->iteration }}
                                    </a>
                                @empty
                                    <span class="text-muted small">لا يوجد</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tab: Members ──────────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-members">
            <div class="row g-4">
                {{-- Members list --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-people me-2"></i>أعضاء الغرفة
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>الهاتف</th>
                                        <th>الدور</th>
                                        <th class="text-end">إزالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($members as $member)
                                    @php
                                        $roleColors = ['doctor' => 'primary', 'nurse' => 'info', 'patient_family' => 'secondary'];
                                        $rc = $roleColors[$member->role] ?? 'light';
                                    @endphp
                                    <tr>
                                        <td class="text-muted small">{{ $loop->iteration }}</td>
                                        <td class="fw-semibold">{{ $member->user?->name ?? '—' }}</td>
                                        <td class="small text-muted">{{ $member->user?->phone ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $rc }}-subtle text-{{ $rc }}">
                                                {{ $member->role_label }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.sihati.rooms.members.remove', [$room, $member]) }}"
                                                method="POST"
                                                onsubmit="return confirm('إزالة {{ $member->user?->name }} من الغرفة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-person-dash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا يوجد أعضاء بعد</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Add Member Form --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-person-plus me-2"></i>إضافة عضو
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.sihati.rooms.members.add', $room) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                        placeholder="07xxxxxxxx">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">الدور <span class="text-danger">*</span></label>
                                    <select name="role" class="form-select form-select-sm @error('role') is-invalid @enderror">
                                        <option value="doctor"         {{ old('role') === 'doctor'         ? 'selected' : '' }}>طبيب</option>
                                        <option value="nurse"          {{ old('role') === 'nurse'          ? 'selected' : '' }}>ممرض</option>
                                        <option value="patient_family" {{ old('role') === 'patient_family' ? 'selected' : '' }}>عيلة المريض</option>
                                    </select>
                                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-person-plus me-1"></i> إضافة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tab: Templates ────────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-templates">
            <div class="row g-4">

                {{-- Active Assignments --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-check2-circle me-2 text-success"></i>القوالب النشطة الحالية
                        </div>
                        <div class="card-body p-0">
                            @forelse($activeAssignments as $assignment)
                            <div class="p-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $assignment->template?->type_color }}-subtle text-{{ $assignment->template?->type_color }}">
                                        {{ $assignment->template?->type_label }}
                                    </span>
                                    <span class="fw-semibold">{{ $assignment->template?->name }}</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    عُيِّن في {{ $assignment->assigned_at?->format('Y/m/d H:i') }}
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                لا يوجد قالب نشط مُعيَّن
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Assign new template --}}
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-arrow-repeat me-2"></i>تعيين قالب
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.sihati.rooms.assign-template', $room) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">اختر قالباً</label>
                                    <select name="report_template_id"
                                        class="form-select form-select-sm @error('report_template_id') is-invalid @enderror">
                                        <option value="">— اختر —</option>
                                        @foreach($availableTemplates->where('template_type', 'nurse') as $t)
                                            <option value="{{ $t->id }}">تمريض: {{ $t->name }}</option>
                                        @endforeach
                                        @foreach($availableTemplates->where('template_type', 'doctor') as $t)
                                            <option value="{{ $t->id }}">دكتور: {{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('report_template_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="alert alert-warning py-2 small">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    تعيين قالب جديد سيؤرشف القالب الحالي من نفس النوع ويبدأ بالجديد.
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-journal-plus me-1"></i> تعيين القالب
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Assignment History --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent fw-bold">
                            <i class="bi bi-clock-history me-2"></i>سجل القوالب السابقة
                        </div>
                        <div class="card-body p-0">
                            @forelse($assignmentHistory as $history)
                            <div class="p-3 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-secondary border">
                                        {{ $history->template?->type_label }}
                                    </span>
                                    <span class="text-muted">{{ $history->template?->name }}</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    من {{ $history->assigned_at?->format('Y/m/d') }}
                                    إلى {{ $history->unassigned_at?->format('Y/m/d') }}
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4">لا يوجد سجل سابق</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Tab: Reports ──────────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-reports">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-clipboard2-pulse me-2"></i>تقارير الغرفة
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>النوع</th>
                                <th>قدّمه</th>
                                <th>تاريخ التقديم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-{{ $report->report_type_color }}-subtle text-{{ $report->report_type_color }}">
                                        {{ $report->report_type_label }}
                                    </span>
                                </td>
                                <td>{{ $report->submittedBy?->name ?? '—' }}</td>
                                <td class="small">{{ $report->submitted_at?->format('Y/m/d H:i') ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    <i class="bi bi-clipboard2 fs-3 d-block mb-2"></i>
                                    لا توجد تقارير بعد
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Tab: Doctor Orders ────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-orders">
            <div class="d-flex flex-column gap-3">
                @forelse($doctorOrders as $order)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                <span class="fw-bold text-primary">د. {{ $order->doctor?->name ?? '—' }}</span>
                                <span class="text-muted small ms-2">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            @if($order->is_executed)
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-check2 me-1"></i>مُنفَّذ
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">بانتظار التنفيذ</span>
                            @endif
                        </div>
                        <p class="mb-2">{{ $order->order_text }}</p>

                        {{-- Replies --}}
                        @if($order->replies->count())
                        <div class="bg-light rounded p-3 mt-2">
                            @foreach($order->replies as $reply)
                            <div class="d-flex gap-2 mb-2">
                                <i class="bi bi-reply text-muted mt-1"></i>
                                <div>
                                    <span class="fw-semibold small">{{ $reply->nurse?->name ?? '—' }}</span>
                                    <span class="text-muted small ms-1">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                                    <div>{{ $reply->reply_text }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        <i class="bi bi-chat-left-text fs-3 d-block mb-2"></i>
                        لا توجد أوامر طبية بعد
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── Tab: Medications ─────────────────────────────────────────── --}}
        <div class="tab-pane fade" id="tab-medications">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">
                    <i class="bi bi-capsule me-2"></i>أدوية الغرفة
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الدواء</th>
                                <th>الجرعة</th>
                                <th>التكرار</th>
                                <th>من</th>
                                <th>حتى</th>
                                <th>أضافه</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medications as $med)
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $med->medication_name }}</td>
                                <td>{{ $med->dosage ?? '—' }}</td>
                                <td>{{ $med->frequency ?? '—' }}</td>
                                <td class="small">{{ $med->start_date?->format('Y/m/d') ?? '—' }}</td>
                                <td class="small">{{ $med->end_date?->format('Y/m/d') ?? '—' }}</td>
                                <td class="small text-muted">{{ $med->addedBy?->name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-capsule fs-3 d-block mb-2"></i>
                                    لا توجد أدوية مُسجَّلة
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
