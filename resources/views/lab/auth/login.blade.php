@php $dir = app()->getLocale() === 'ar' ? 'rtl' : 'ltr'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — لوحة المختبر</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(145deg, #0f172a 0%, #14532d 50%, #33A552 100%);
            padding: 20px;
        }
        .card-login {
            width: 100%;
            max-width: 380px;
            background: #fff;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 20px 50px rgba(0,0,0,.25);
        }
        .brand { text-align: center; margin-bottom: 24px; }
        .brand-icon {
            width: 56px; height: 56px;
            background: rgba(51,165,82,.12);
            color: #33A552;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 14px;
        }
        .brand h1 { font-size: 1.3rem; font-weight: 800; color: #0f172a; }
        .brand p { font-size: .85rem; color: #64748b; margin-top: 4px; }

        .alert-err {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
            padding: 12px 16px; margin-bottom: 18px;
            font-size: .845rem; color: #dc2626; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
        }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 11px 14px; border: 1.5px solid #e5e7eb; border-radius: 10px;
            font-size: .875rem; font-family: inherit; outline: none; transition: all .2s ease;
        }
        .form-input:focus { border-color: #33A552; box-shadow: 0 0 0 3px rgba(51,165,82,.12); }
        .form-input.is-invalid { border-color: #ef4444; }
        .invalid-feedback { font-size: .78rem; color: #ef4444; margin-top: 5px; display: block; }
        .btn-login {
            width: 100%; padding: 12px; background: #33A552; color: #fff; border: none;
            border-radius: 10px; font-size: .9rem; font-weight: 600; font-family: inherit;
            cursor: pointer; margin-top: 8px; transition: all .2s ease;
        }
        .btn-login:hover { background: #278a44; }
    </style>
</head>
<body>
    <div class="card-login">
        <div class="brand">
            <div class="brand-icon"><i class="bi bi-clipboard2-pulse-fill"></i></div>
            <h1>لوحة المختبر</h1>
            <p>سجّل الدخول لإدارة طلبات التحاليل</p>
        </div>

        @if($errors->any() || session('error'))
        <div class="alert-err">
            <i class="bi bi-exclamation-circle-fill"></i>
            {{ $errors->first() ?: session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('lab.login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="phone">رقم الهاتف</label>
                <input id="phone" name="phone" type="text"
                    class="form-input {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                    value="{{ old('phone') }}" required autofocus>
                @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="password">كلمة المرور</label>
                <input id="password" name="password" type="password"
                    class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" required>
                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i> تسجيل الدخول
            </button>
        </form>
    </div>
</body>
</html>
