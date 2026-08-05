@extends('admin.layouts.app')
@section('title', $document->type_label)

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex align-items-center gap-3 mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-file-earmark-text me-2 text-primary"></i>
            {{ $document->type_label }}
        </h4>
        @if($document->updatedBy)
            <span class="text-muted small ms-auto">
                آخر تعديل بواسطة: {{ $document->updatedBy->name }}
                — {{ $document->updated_at->diffForHumans() }}
            </span>
        @endif
    </div>

    @include('admin.includes.alerts.success')

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    @if($document->type === 'authorization')
                        @php $updateRoute = route('admin.sihati.documents.authorization.update') @endphp
                    @else
                        @php $updateRoute = route('admin.sihati.documents.pledge.update') @endphp
                    @endif

                    <form action="{{ $updateRoute }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">عنوان الوثيقة <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $document->title) }}"
                                class="form-control @error('title') is-invalid @enderror">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">محتوى الوثيقة <span class="text-danger">*</span></label>
                            <textarea name="content" rows="20"
                                class="form-control font-monospace @error('content') is-invalid @enderror"
                                style="font-size:0.9rem; line-height:1.7"
                                placeholder="اكتب نص الوثيقة هنا...">{{ old('content', $document->content) }}</textarea>
                            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">يمكنك استخدام {patient_name} و{room_name} و{date} كمتغيرات تُستبدل تلقائياً.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="bi bi-floppy me-1"></i> حفظ الوثيقة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
