@extends('pages.halo-security.layout.base')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/velzon/libs/quill/quill.bubble.css') }}">
    <link href="{{ asset('assets/velzon/libs/quill/quill.snow.css') }}" rel="stylesheet" />
@endpush

@section('title', 'Template BA Introgasi')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Ubah Template Tanya Jawab Introgasi</h4>
        </div>

        <div class="card-body">
            <form action="{{route('update.template',[$template->id])}}" method="POST" autocomplete="off">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="pertanyaan_introgasi" class="form-label">Pertanyaan Introgasi</label>
                            <textarea class="form-control" name="pertanyaan_introgasi" id="pertanyaan_introgasi-textarea" rows="3">{!! old('pertanyaan_introgasi', $template->pertanyaan_introgasi) !!}</textarea>
                            {{-- <div id="pertanyaan_introgasi">{!! old('pertanyaan_introgasi', $template->pertanyaan_introgasi) !!}</div> --}}
                            <p class="text-danger ml-2">
                                @error('pertanyaan_introgasi')
                                    {{ $message }}
                                @enderror
                            </p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="jawaban_introgasi" class="form-label">Jawaban Introgasi</label>
                            <textarea class="form-control" name="jawaban_introgasi" id="jawaban_introgasi-textarea" rows="3">{!! old('jawaban_introgasi', $template->jawaban_introgasi) !!}</textarea>
                            {{-- <div id="jawaban_introgasi">{!! old('jawaban_introgasi', $template->jawaban_introgasi) !!}</div> --}}
                            <p class="text-danger ml-2">
                                @error('jawaban_introgasi')
                                    {{ $message }}
                                @enderror
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <a href="{{ route('template') }}" class="btn btn-dark btn-md">Kembali</a>
                            </div>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-md btn-primary">
                                    Ubah
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/libs/quill/quill.min.js') }}"></script>
<script src="{{ asset('assets/velzon/js/pages/form-editor.init.js') }}"></script>
<script>

    // Untuk textarea uraian kejadian
    var quill = new Quill('#pertanyaan_introgasi', {
        modules: {
            toolbar: false
        },
        placeholder: 'Masukan Pertanyaan Introgasi',
        theme: 'snow'
    });

    quill.on('text-change', function(delta, source) {
        $('#pertanyaan_introgasi-textarea').val($('#pertanyaan_introgasi .ql-editor').html())
    });

    // Untuk textarea tindakan pengamanan
    var quill = new Quill('#jawaban_introgasi', {
        modules: {
            toolbar: false
        },
        placeholder: 'Masukan Jawaban Introgasi',
        theme: 'snow'
    });

    quill.on('text-change', function(delta, source) {
        $('#jawaban_introgasi-textarea').val($('#jawaban_introgasi .ql-editor').html())
    });
</script>
@endpush