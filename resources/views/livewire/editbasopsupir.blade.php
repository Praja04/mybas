@extends('pages.halo-security.layout.base')

@section('title', 'BA S.O.P Supir')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Ubah Berita Acara SOP Supir</h4>
        </div>

        <div class="card-body">
                <form action="{{route('basopsupir.editsupir',[$basopsupir->id])}}" method="post" autocomplete="off">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Supir</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{$basopsupir->nama}}" placeholder="Masukan Nama">
                                <p class="text-danger ml-2">
                                    @error('nama')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="ekspedisi" class="form-label">Ekspedisi</label>
                                <input type="text" class="form-control" id="ekspedisi" name="ekspedisi" value="{{$basopsupir->ekspedisi}}" placeholder="Ekspedisi Tempat">
                                <p class="text-danger ml-2">
                                    @error('ekspedisi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_ktp" class="form-label">Nomor KTP (Kartu Tanda Penduduk)</label>
                                <input type="text" class="form-control" id="no_ktp" name="no_ktp" value="{{$basopsupir->no_ktp}}" placeholder="Masukan Nomor KTP">
                                <p class="text-danger ml-2">
                                    @error('no_ktp')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_polisi" class="form-label">Nomor Polisi</label>
                                <input type="text" class="form-control" id="no_polisi" name="no_polisi" value="{{$basopsupir->no_polisi}}" placeholder="Masukan Nomor Polisi">
                                <p class="text-danger ml-2">
                                    @error('no_polisi')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_handphone" class="form-label">Nomor Handphone</label>
                                <input type="text" class="form-control" id="no_handphone" name="no_handphone" value="{{$basopsupir->no_handphone}}" placeholder="Masukan Nomor Handphone">
                                <p class="text-danger ml-2">
                                    @error('no_handphone')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="no_kartu" class="form-label">Nomor Kartu</label>
                                <input type="text" class="form-control" id="no_kartu" name="no_kartu" value="{{$basopsupir->no_kartu}}" placeholder="Masukan Nomor Handphone">
                                <p class="text-danger ml-2">
                                    @error('no_kartu')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" placeholder="Masukan Alamat" rows="3">{{ $basopsupir->alamat }}</textarea>
                                <p class="text-danger ml-2">
                                    @error('alamat')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="shift" class="form-label">Shift</label>
                                <select class="form-select mb-3" name="shift">
                                    <option value="">Pilih Shift</option>
                                    <option @if($basopsupir['shift']=='1') selected @endif value="1">1</option>
									<option @if($basopsupir['shift']=='2') selected @endif value="2">2</option>
                                    <option @if($basopsupir['shift']=='3') selected @endif value="3">3</option>
                                </select>
                                <p class="text-danger ml-2">
                                    @error('shift')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama_pembuat" class="form-label">Nama Pembuat</label>
                                <input type="text" id="nama_pembuat" name="nama_pembuat" value="{{$basopsupir->nama_pembuat}}" class="form-control" placeholder="Masukan Nama Pembuat Dokumen">
                                <p class="text-danger ml-2">
                                    @error('nama_pembuat')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="jabatan_pembuat" class="form-label">Jabatan Pembuat</label>
                                <input type="text" id="jabatan_pembuat" name="jabatan_pembuat" value="{{$basopsupir->jabatan_pembuat}}" class="form-control" placeholder="Masukan Jabatan Pembuat Dokumen">
                                <p class="text-danger ml-2">
                                    @error('jabatan_pembuat')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between">
                                <div class="d-flex">
                                    <a href="{{ route('ba-sop-list-supir') }}" class="btn btn-dark btn-md">Kembali</a>
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