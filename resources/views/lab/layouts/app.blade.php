@php $dir = app()->getLocale() === 'ar' ? 'rtl' : 'ltr'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة المختبر') — Green Care</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if($dir === 'rtl')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; background: #f1f5f9; }
        .lab-navbar {
            background: linear-gradient(90deg, #0f172a 0%, #14532d 100%);
        }
        .lab-navbar .navbar-brand { color: #fff; font-weight: 800; letter-spacing: -.02em; }
        .lab-navbar .navbar-brand span { color: #86efac; }
    </style>
    @stack('styles')
</head>
<body>

    <nav class="navbar navbar-dark lab-navbar px-3">
        <a class="navbar-brand" href="{{ route('lab.dashboard') }}">
            <i class="bi bi-clipboard2-pulse-fill me-1"></i> لوحة <span>المختبر</span>
        </a>
        <form action="{{ route('lab.logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light">
                <i class="bi bi-box-arrow-right me-1"></i> تسجيل الخروج
            </button>
        </form>
    </nav>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
