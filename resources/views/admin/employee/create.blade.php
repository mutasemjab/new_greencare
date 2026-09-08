@extends('admin.layouts.app')
@section('title', 'إضافة موظف')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.employee.index') }}">الموظفون</a></li>
                        <li class="breadcrumb-item active">إضافة</li>
                    </ol>
                </div>
                <h4 class="page-title">إضافة موظف جديد</h4>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.employee.store') }}" method="post">
        @csrf
        <div class="row">

            {{-- بيانات الموظف --}}
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">البيانات الأساسية</h5>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">الاسم الكامل <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="أدخل الاسم الكامل">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="example@domain.com"
                                dir="ltr">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="username">اسم المستخدم <span class="text-danger">*</span></label>
                            <input type="text" id="username" name="username"
                                value="{{ old('username') }}"
                                class="form-control @error('username') is-invalid @enderror"
                                placeholder="اسم المستخدم للدخول"
                                dir="ltr">
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">كلمة المرور <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="6 أحرف على الأقل">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- الأدوار --}}
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">الأدوار والصلاحيات <span class="text-danger">*</span></h5>
                    </div>
                    <div class="card-body">
                        @error('roles')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        @forelse($roles as $role)
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox"
                                    class="custom-control-input"
                                    id="role_{{ $role->id }}"
                                    name="roles[]"
                                    value="{{ $role->id }}"
                                    {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted">لا توجد أدوار. <a href="{{ route('admin.role.create') }}">أضف دوراً أولاً</a></p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- أزرار --}}
            <div class="col-12 mb-4">
                <button type="submit" class="btn btn-success waves-effect waves-light">
                    <i class="mdi mdi-check ml-1"></i> حفظ
                </button>
                <a href="{{ route('admin.employee.index') }}" class="btn btn-secondary waves-effect ml-2">
                    إلغاء
                </a>
            </div>

        </div>
    </form>

</div>
@endsection
