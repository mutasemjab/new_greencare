@extends('admin.layouts.app')
@section('title', 'تعديل المقال')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right"></i>
        </a>
        <h4 class="mb-0 fw-bold">تعديل المقال: {{ Str::limit($article->title, 40) }}</h4>
    </div>

    <div class="card shadow-sm border-0" style="max-width:750px">
        <div class="card-body p-4">
            @include('admin.includes.alerts.error')

            <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label class="form-label fw-semibold">العنوان <span class="text-danger">*</span></label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $article->title) }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">المحتوى <span class="text-danger">*</span></label>
                    <textarea name="description" rows="8"
                        class="form-control @error('description') is-invalid @enderror"
                        required>{{ old('description', $article->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Current Image --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">الصورة الحالية</label>
                    @if($article->image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($article->image) }}"
                                class="rounded border"
                                style="max-width:200px;max-height:150px;object-fit:cover;">
                        </div>
                    @else
                        <p class="text-muted small mb-2">لا توجد صورة</p>
                    @endif
                    <label class="form-label fw-semibold">استبدال الصورة</label>
                    <input type="file" name="image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/*">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">تاريخ ووقت النشر</label>
                    <input type="datetime-local" name="published_at"
                        class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at', $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('Y-m-d\TH:i') : '') }}">
                    @error('published_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            id="is_active" {{ old('is_active', $article->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">منشور</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary px-4">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
