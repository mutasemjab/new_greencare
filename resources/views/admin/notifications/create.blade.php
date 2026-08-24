@extends('admin.layouts.app')
@section('title', 'إرسال إشعار')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">إرسال إشعار</h4>
    </div>

    @include('admin.includes.alerts.error')

    <div class="card border-0 shadow-sm mx-auto" style="max-width:700px;">
        <div class="card-body p-4">
            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">العنوان <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">النص <span class="text-danger">*</span></label>
                    <textarea name="body" rows="4" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                    @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold d-block">المستلم <span class="text-danger">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="target" id="targetAll" value="all"
                            {{ old('target', 'all') === 'all' ? 'checked' : '' }}>
                        <label class="form-check-label" for="targetAll">الجميع</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="target" id="targetSpecific" value="specific"
                            {{ old('target') === 'specific' ? 'checked' : '' }}>
                        <label class="form-check-label" for="targetSpecific">شخص معين</label>
                    </div>
                    @error('target') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4" id="userPickerGroup">
                    <label class="form-label fw-semibold">اختر المستخدم</label>
                    <select name="user_id" class="form-select select2 @error('user_id') is-invalid @enderror">
                        <option value="">اختر مستخدم</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} @if($user->phone) ({{ $user->phone }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="alert alert-info small" id="allWarning">
                    <i class="bi bi-info-circle me-1"></i>
                    هيك رح يوصل الإشعار لكل المستخدمين النشطين ({{ $users->count() }} مستخدم).
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send me-1"></i> إرسال
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>

            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    (function () {
        var allRadio = document.getElementById('targetAll');
        var specificRadio = document.getElementById('targetSpecific');
        var userPickerGroup = document.getElementById('userPickerGroup');
        var allWarning = document.getElementById('allWarning');

        function toggle() {
            var isSpecific = specificRadio.checked;
            userPickerGroup.style.display = isSpecific ? '' : 'none';
            allWarning.style.display = isSpecific ? 'none' : '';
        }

        allRadio.addEventListener('change', toggle);
        specificRadio.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
@endsection
