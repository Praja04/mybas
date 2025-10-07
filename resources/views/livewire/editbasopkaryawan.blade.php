@extends('pages.halo-security.layout.base')

@section('title', 'BA S.O.P Karyawan')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Ubah Berita Acara S.O.P Karyawan</h4>
        </div>

        <div class="card-body">
            <form action="{{route('basopkaryawan.editkaryawan',[$basopkaryawan->id])}}" method="post" autocomplete="off">
                @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Karyawan</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="{{$basopkaryawan->nama}}" placeholder="Masukan Nama Karyawan">
                                <p class="text-danger ml-2">
                                    @error('nama')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nik" class="form-label">Nik (Nomor Induk Karyawan)</label>
                                <input type="text"class="form-control" id="nik" name="nik" value="{{$basopkaryawan->nik}}" placeholder="Masukan Nomor NIK (Nomor Induk Karyawan)">
                                <p class="text-danger ml-2">
                                    @error('nik')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan / Bagian</label>
                                <input type="text" id="jabatan" name="jabatan" value="{{$basopkaryawan->jabatan}}" class="form-control" placeholder="Masukan Jabatan / Bagian Karyawan">
                                <p class="text-danger ml-2">
                                    @error('jabatan')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select mb-3" name="jenis_kelamin">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option @if($basopkaryawan['jenis_kelamin']=='laki-laki') selected @endif value="laki-laki">Laki - Laki</option>
									<option @if($basopkaryawan['jenis_kelamin']=='perempuan') selected @endif value="perempuan">Perempuan</option>
                                </select>
                                <p class="text-danger ml-2">
                                    @error('jenis_kelamin')
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
                                    <option @if($basopkaryawan['shift']=='1') selected @endif value="1">1</option>
									<option @if($basopkaryawan['shift']=='2') selected @endif value="2">2</option>
                                    <option @if($basopkaryawan['shift']=='3') selected @endif value="3">3</option>
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
                                <input type="text" id="nama_pembuat" name="nama_pembuat" value="{{$basopkaryawan->nama_pembuat}}" class="form-control" placeholder="Masukan Nama Pembuat Dokumen">
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
                                <input type="text" id="jabatan_pembuat" name="jabatan_pembuat" value="{{$basopkaryawan->jabatan_pembuat}}" class="form-control" placeholder="Masukan Jabatan Pembuat Dokumen">
                                <p class="text-danger ml-2">
                                    @error('jabatan_pembuat')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="nama_area" class="form-label">Nama Area</label>
                                <input type="text" id="nama_area" name="nama_area" value="{{$basopkaryawan->nama_area}}" class="form-control" placeholder="Masukan Nama Area">
                                <p class="text-danger ml-2">
                                    @error('nama_area')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="barang" class="form-label">Barang</label>
                                <input type="text" id="barang" name="barang" value="{{$basopkaryawan->barang}}" class="form-control" placeholder="Masukan Nama Barang">
                                <p class="text-danger ml-2">
                                    @error('barang')
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex">
                                            <a href="{{ route('ba-sop-list-karyawan') }}" class="btn btn-dark btn-md">Kembali</a>
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