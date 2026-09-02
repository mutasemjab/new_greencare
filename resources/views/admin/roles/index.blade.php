@extends('admin.layouts.app')
@section('title', 'الأدوار')

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">الأدوار</li>
                    </ol>
                </div>
                <h4 class="page-title">إدارة الأدوار</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row align-items-center mb-3">
                        <div class="col-sm-6">
                            {{ $data->links() }}
                        </div>
                        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
                            <a href="{{ route('admin.role.create') }}"
                                class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-plus"></i> إضافة دور
                            </a>
                        </div>
                    </div>

                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="mdi mdi-check-circle ml-1"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:200px;">اسم الدور</th>
                                    <th>الصلاحيات</th>
                                    <th style="width:120px;" class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $role)
                                <tr>
                                    <td><span class="font-weight-bold">{{ $role->name }}</span></td>
                                    <td>
                                        @php
                                            $groups = $role->permissions->groupBy(function($p) {
                                                $parts = explode('-', $p->name);
                                                return count($parts) >= 3
                                                    ? $parts[0] . '-' . $parts[1]
                                                    : $parts[0];
                                            });
                                        @endphp
                                        @foreach($groups as $section => $perms)
                                            <span class="badge badge-soft-primary mr-1 mb-1">
                                                {{ $section }}
                                                <span class="badge badge-primary badge-pill ml-1">{{ $perms->count() }}</span>
                                            </span>
                                        @endforeach
                                        @if($role->permissions->isEmpty())
                                            <span class="text-muted font-italic small">لا توجد صلاحيات</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.role.edit', $role->id) }}"
                                            class="btn btn-sm btn-outline-info">
                                            <i class="mdi mdi-pencil-box"></i> تعديل
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            @if(env('Environment') == 'sendbox')
                                                onclick="myFunction()"
                                            @else
                                                onclick="Delete('{{ $role->id }}','{{ route('admin.role.delete') }}')"
                                            @endif>
                                            <i class="mdi mdi-trash-can"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">
                                        <i class="mdi mdi-shield-off mdi-36px d-block mb-2"></i>
                                        لا توجد أدوار بعد
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

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.3/dist/sweetalert2.min.js"></script>
    <script src="{{ asset('assets/js/category.js') }}"></script>
@endpush
