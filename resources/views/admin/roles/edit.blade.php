@extends('admin.layouts.app')
@section('title', 'تعديل دور')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.role.index') }}">الأدوار</a></li>
                        <li class="breadcrumb-item active">تعديل</li>
                    </ol>
                </div>
                <h4 class="page-title">تعديل دور: {{ $data->name }}</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.role.update', $data->id) }}" method="post">
        @csrf
        @method('PATCH')
        <div class="row">

            {{-- اسم الدور --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <label class="font-weight-bold mb-0">اسم الدور <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="name"
                                    value="{{ old('name', $data->name) }}"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="اسم الدور">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الصلاحيات --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">الصلاحيات</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                <i class="mdi mdi-check-all"></i> تحديد الكل
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary mr-1" onclick="deselectAll()">
                                <i class="mdi mdi-close"></i> إلغاء الكل
                            </button>
                        </div>
                    </div>
                    @error('perms')
                        <div class="alert alert-danger mx-3 mt-2 mb-0">{{ $message }}</div>
                    @enderror
                    <div class="card-body">
                        <div class="row">
                            @foreach($grouped as $sectionLabel => $permissions)
                            @php
                                $slug       = Str::slug($sectionLabel);
                                $sectionIds = $permissions->pluck('id')->all();
                                $checkedIds = old('perms', $role_permissions);
                                $allChecked = count(array_intersect($sectionIds, $checkedIds)) === count($sectionIds);
                                $someChecked = count(array_intersect($sectionIds, $checkedIds)) > 0;
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border shadow-sm h-100">
                                    <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                                        <span class="font-weight-bold text-dark small">{{ $sectionLabel }}</span>
                                        <div class="custom-control custom-checkbox mb-0">
                                            <input type="checkbox" class="custom-control-input section-master"
                                                id="master_{{ $slug }}"
                                                {{ $allChecked ? 'checked' : '' }}
                                                onchange="toggleSection('{{ $slug }}', this.checked)">
                                            <label class="custom-control-label small text-muted" for="master_{{ $slug }}">
                                                الكل
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        @foreach($permissions as $perm)
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox"
                                                class="custom-control-input perm-{{ $slug }}"
                                                id="perm_{{ $perm->id }}"
                                                name="perms[]"
                                                value="{{ $perm->id }}"
                                                {{ in_array($perm->id, $checkedIds) ? 'checked' : '' }}
                                                onchange="updateMaster('{{ $slug }}')">
                                            <label class="custom-control-label small" for="perm_{{ $perm->id }}">
                                                {{ $perm->name }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- أزرار --}}
            <div class="col-12 mb-4">
                <button type="submit" class="btn btn-success waves-effect waves-light">
                    <i class="mdi mdi-check ml-1"></i> حفظ التعديلات
                </button>
                <a href="{{ route('admin.role.index') }}" class="btn btn-secondary waves-effect ml-2">
                    إلغاء
                </a>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    // Set indeterminate state on load
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.section-master').forEach(function (master) {
            const slug    = master.id.replace('master_', '');
            const all     = document.querySelectorAll('.perm-' + slug);
            const checked = document.querySelectorAll('.perm-' + slug + ':checked');
            if (checked.length > 0 && checked.length < all.length) {
                master.indeterminate = true;
            }
        });
    });

    function toggleSection(slug, checked) {
        document.querySelectorAll('.perm-' + slug).forEach(cb => cb.checked = checked);
    }
    function updateMaster(slug) {
        const all     = document.querySelectorAll('.perm-' + slug);
        const checked = document.querySelectorAll('.perm-' + slug + ':checked');
        const master  = document.getElementById('master_' + slug);
        master.checked       = all.length === checked.length;
        master.indeterminate = checked.length > 0 && checked.length < all.length;
    }
    function selectAll() {
        document.querySelectorAll('input[name="perms[]"]').forEach(cb => cb.checked = true);
        document.querySelectorAll('.section-master').forEach(cb => { cb.checked = true; cb.indeterminate = false; });
    }
    function deselectAll() {
        document.querySelectorAll('input[name="perms[]"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('.section-master').forEach(cb => { cb.checked = false; cb.indeterminate = false; });
    }
</script>
@endpush
