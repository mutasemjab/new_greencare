@extends('admin.layouts.app')
@section('title', __('messages.edit_role'))

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.role.index') }}">{{ __('messages.role') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ __('messages.Edit') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.edit_role') }}</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.role.update', $data->id) }}" method="post">
        @csrf
        {{ method_field('PATCH') }}
        <div class="row">

            {{-- Role Name --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="font-weight-bold">{{ __('messages.name_field') }}</label>
                            </div>
                            <div class="col-md-5">
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name" value="{{ $data->name }}"
                                    placeholder="{{ __('messages.name_field') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permissions --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ __('messages.permission') }}</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">تحديد الكل</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">إلغاء الكل</button>
                        </div>
                    </div>
                    @error('perms')
                        <div class="alert alert-danger mx-3 mt-2">{{ $message }}</div>
                    @enderror
                    <div class="card-body">
                        <div class="row">
                            @foreach($grouped as $sectionLabel => $permissions)
                            @php
                                $slug = Str::slug($sectionLabel);
                                $allChecked = $permissions->every(fn($p) => in_array($p->id, $role_permissions));
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border shadow-sm h-100">
                                    <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                                        <span class="font-weight-bold text-dark">{{ $sectionLabel }}</span>
                                        <input type="checkbox" class="section-check"
                                            data-section="{{ $slug }}"
                                            {{ $allChecked ? 'checked' : '' }}
                                            onchange="toggleSection('{{ $slug }}', this.checked)">
                                    </div>
                                    <div class="card-body py-2">
                                        @foreach($permissions as $perm)
                                        <div class="custom-control custom-checkbox mb-1">
                                            <input type="checkbox"
                                                class="custom-control-input perm-check-{{ $slug }}"
                                                id="perm_{{ $perm->id }}"
                                                name="perms[]"
                                                value="{{ $perm->id }}"
                                                {{ in_array($perm->id, $role_permissions) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="perm_{{ $perm->id }}">
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

            {{-- Buttons --}}
            <div class="col-12 mb-4">
                <button type="submit" class="btn btn-success waves-effect waves-light">
                    {{ __('messages.update') }}
                </button>
                <a href="{{ route('admin.role.index') }}" class="btn btn-danger waves-effect waves-light ml-2">
                    {{ __('messages.Cancel') }}
                </a>
            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function toggleSection(section, checked) {
        document.querySelectorAll('.perm-check-' + section).forEach(cb => cb.checked = checked);
    }
    function selectAll() {
        document.querySelectorAll('input[name="perms[]"]').forEach(cb => cb.checked = true);
        document.querySelectorAll('.section-check').forEach(cb => cb.checked = true);
    }
    function deselectAll() {
        document.querySelectorAll('input[name="perms[]"]').forEach(cb => cb.checked = false);
        document.querySelectorAll('.section-check').forEach(cb => cb.checked = false);
    }
</script>
@endpush
