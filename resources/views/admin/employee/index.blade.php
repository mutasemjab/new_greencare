@extends('admin.layouts.app')
@section('title', 'الموظفون')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">الموظفون</li>
                    </ol>
                </div>
                <h4 class="page-title">إدارة الموظفين</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- Toolbar --}}
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.employee.index') }}" class="d-flex">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control ml-2"
                                    placeholder="بحث بالاسم أو البريد أو اسم المستخدم...">
                                <button type="submit" class="btn btn-outline-secondary mr-2">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('admin.employee.index') }}" class="btn btn-outline-danger">
                                        <i class="mdi mdi-close"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-6 text-md-right mt-2 mt-md-0">
                            @can('employee-add')
                                <a href="{{ route('admin.employee.create') }}"
                                    class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-plus"></i> إضافة موظف
                                </a>
                            @endcan
                        </div>
                    </div>

                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="mdi mdi-check-circle ml-1"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="mdi mdi-alert-circle ml-1"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>اسم المستخدم</th>
                                    <th>الأدوار</th>
                                    <th style="width:140px;" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $employee)
                                <tr>
                                    <td>{{ $data->firstItem() + $loop->index }}</td>
                                    <td><span class="font-weight-bold">{{ $employee->name }}</span></td>
                                    <td>{{ $employee->email ?? '—' }}</td>
                                    <td><code>{{ $employee->username }}</code></td>
                                    <td>
                                        @forelse($employee->roles as $role)
                                            <span class="badge badge-soft-primary mr-1">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted small font-italic">لا يوجد دور</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        @can('employee-edit')
                                            <a href="{{ route('admin.employee.edit', $employee->id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="mdi mdi-pencil-box"></i> تعديل
                                            </a>
                                        @endcan
                                        @can('employee-delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                onclick="Delete('{{ $employee->id }}', '{{ route('admin.employee.delete') }}')">
                                                <i class="mdi mdi-trash-can"></i> حذف
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="mdi mdi-account-off mdi-36px d-block mb-2"></i>
                                        لا يوجد موظفون
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $data->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('assets/js/category.js') }}"></script>
@endpush
