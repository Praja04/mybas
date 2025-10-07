@extends('pages.halo-security.layout.base')

@section('title', 'Security User GA')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Ubah Security User GA</h4>
        </div>

        <div class="card-body">
            <form action="{{route('update.security',[$security->user_id])}}" method="POST" autocomplete="off">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" name="nik" id="nik" value="{{old('nik', $security->nik)}}" class="form-control @error('nik') is-invalid @enderror" placeholder="Masukan nik">
                            <p class="text-danger mt-3">
                                @error('nik')
                                    {{ $message }}
                                @enderror
                            </p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" name="nama" id="nama" value="{{old('nama', $security->nama)}}" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukan nama">
                            <p class="text-danger mt-3">
                                @error('nama')
                                    {{ $message }}
                                @enderror
                            </p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" placeholder="Masukan Keterangan" rows="3">{{ old('keterangan', $security->keterangan) }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex">
                                <a href="{{ route('security') }}" class="btn btn-dark btn-md">Kembali</a>
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