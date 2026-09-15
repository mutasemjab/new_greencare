@extends('admin.layouts.app')
@section('title', 'مفاتيح API')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-key me-2"></i>مفاتيح API — تكاملات خارجية
        </h4>
        <a href="{{ route('admin.api-clients.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> مفتاح جديد
        </a>
    </div>

    @include('admin.includes.alerts.success')

    @if(session('generated_key'))
    <div class="alert alert-warning">
        <div class="fw-bold mb-2"><i class="bi bi-exclamation-triangle me-1"></i> هذا المفتاح لن يظهر مرة أخرى — انسخه الآن وسلّمه للمنصة الخارجية:</div>
        <div class="input-group">
            <input type="text" class="form-control font-monospace" value="{{ session('generated_key') }}" id="generatedKeyInput" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="copyGeneratedKey()">
                <i class="bi bi-clipboard me-1"></i> نسخ
            </button>
        </div>
        <div class="small text-muted mt-2">
            يُرسل بترويسة الطلب: <code>X-API-Key: {{ session('generated_key') }}</code>
        </div>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>الحالة</th>
                        <th>آخر استخدام</th>
                        <th>تاريخ الإنشاء</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apiClients as $client)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $client->name }}</td>
                        <td>
                            @if($client->is_active)
                                <span class="badge bg-success-subtle text-success">مفعّل</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">معطّل</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $client->last_used_at?->format('Y/m/d H:i') ?? 'لم يُستخدم بعد' }}</td>
                        <td class="small text-muted">{{ $client->created_at->format('Y/m/d') }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.api-clients.toggle', $client) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm {{ $client->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                    <i class="bi bi-{{ $client->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.api-clients.destroy', $client) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('حذف هذا المفتاح نهائيًا؟ أي منصة تستخدمه ستفقد الوصول فورًا.')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-key fs-3 d-block mb-2"></i>
                            لا توجد مفاتيح API بعد
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function copyGeneratedKey() {
    const input = document.getElementById('generatedKeyInput');
    input.select();
    navigator.clipboard.writeText(input.value);
}
</script>
@endsection
