@extends('admin.layouts.app')

@section('title', __('messages.page_dashboard'))

@section('content')

{{-- Page Header --}}
<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">{{ __('messages.page_dashboard') }}</h1>
        <p class="page-sub">{{ __('messages.welcome_back') }}</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">{{ __('messages.page_dashboard') }}</li>
        </ol>
    </nav>
</div>

{{-- Flash Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif


{{-- Main content row --}}
<div class="row g-3 mb-3">

    {{-- Unread Messages --}}
    <div class="col-12 col-xl-8">
        <div class="panel-card h-100">
            <div class="panel-card-header">
                <h2 class="panel-card-title">{{ __('messages.new_messages') }}</h2>
                <a href="{{ route('admin.contact_messages.index') }}" class="btn-outline-sm">{{ __('messages.view_all') }}</a>
            </div>
         
        </div>
    </div>

   

</div>

@endsection
