@extends('admin.layouts.app')
@section('title', __('messages.role'))

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{ env('APP_NAME') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('messages.role') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('messages.role') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            {{ $data->links() }}
                        </div>
                        <div class="col-sm-6 text-sm-right">
                            <a href="{{ route('admin.role.create') }}"
                                class="btn btn-primary waves-effect waves-light text-white">
                                <i class="mdi mdi-plus"></i> {{ __('messages.new_role') }}
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:200px;">{{ __('messages.name_field') }}</th>
                                    <th>{{ __('messages.permissions') }}</th>
                                    <th style="width:110px;">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $role)
                                <tr>
                                    <td><span class="font-weight-bold">{{ $role->name }}</span></td>
                                    <td>
                                        @php
                                            $groups = $role->permissions->groupBy(function($p) {
                                                $parts = explode('-', $p->name);
                                                return count($parts) >= 3 ? $parts[0].'-'.$parts[1] : $parts[0];
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
                                    <td>
                                        <a class="btn btn-sm btn-outline-info"
                                            href="{{ route('admin.role.edit', $role->id) }}">
                                            <i class="mdi mdi-pencil-box"></i> {{ __('messages.Edit') }}
                                        </a>
                                        <a class="btn btn-sm btn-outline-danger" href="javascript:void(0)"
                                            @if(env('Environment') == 'sendbox')
                                                onclick="myFunction()"
                                            @else
                                                onclick="Delete('{{ $role->id }}','{{ route('admin.role.delete') }}')"
                                            @endif>
                                            <i class="mdi mdi-trash-can"></i> {{ __('messages.Delete') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
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
