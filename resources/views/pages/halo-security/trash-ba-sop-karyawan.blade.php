@extends('pages.halo-security.layout.base')

@section('title', 'BA S.O.P Karyawan')

@section('content')

<div class="container-fluid">
    <form action="{{route('listkaryawan.trash')}}" method="get">
        {{csrf_field()}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-3">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Tanggal</label>
                                <input type="date" name="created_at" value="{{isset($_GET['created_at']) ? $_GET['created_at'] : ''}}" class="form-control" id="exampleInputdate">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Jenis Kelamin</label>
                                <select class="form-select" id="inputGroupSelect01" name="jenis_kelamin">
                                    <option value="">Semua</option>
                                    <option {{ Request('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }} value="laki-laki">Laki - Laki</option>
                                    <option {{ Request('jenis_kelamin') == 'perempuan' ? 'selected' : '' }} value="perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Shift</label>
                                <select class="form-select" id="inputGroupSelect01" name="shift">
                                    <option value="">Semua Shift</option>
                                    <option {{ Request('shift') == '1' ? 'selected' : '' }} value="1">Shift 1</option>
                                    <option {{ Request('shift') == '2' ? 'selected' : '' }} value="2">Shift 2</option>
                                    <option {{ Request('shift') == '3' ? 'selected' : '' }} value="3">Shift 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-md btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
        </div>
    </form>
    <div class="card">
        <div class="card-header justify-content-around d-flex">
            @foreach ($basopkaryawan as $item)
            @if($loop->iteration == 1)
            <a href="{{ route('listkaryawan.kembalikan_semua') }}" onclick="return confirm('Apakah anda yakin ingin mengembalikan semua data ba s.o.p karyawan ini yang sudah dihapus ?');" class="btn btn-md btn-outline-success"><i class="ri-recycle-fill" style="margin-top: 8px; margin-right: 4px;"></i> Kembalikan Semua</a>
            @endif
            @endforeach
            <h4 class="card-title flex-grow-1 text-center mt-2">Recycling Berita Acara S.O.P Karyawan</h4>
            @foreach ($basopkaryawan as $item)
            @if($loop->iteration == 1)
            <a style="display: none" href="{{ route('listkaryawan.hapus_permanen_semua') }}" onclick="return confirm('Apakah anda yakin ingin menghapus semua data ba s.o.p karyawan ini yang sudah dihapus secara permanen ?');" class="btn btn-md btn-outline-danger"><i class="ri-delete-bin-2-fill" style="margin-top: 8px; margin-right: 4px;"></i> Hapus Permanen Semua</a>
            @endif
            @endforeach
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="trashbasopkaryawan" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">Nama</th>
                            <th scope="col" class="text-center">Nik</th>
                            <th scope="col" class="text-center">Jabatan</th>
                            <th scope="col" class="text-center">Jenis Kelamin</th>
                            <th scope="col" class="text-center">Shift</th>
                            <th scope="col" class="text-center">Nama Pembuat</th>
                            <th scope="col" class="text-center">Jabatan Pembuat</th>
                            <th scope="col" class="text-center">Nama Area</th>
                            <th scope="col" class="text-center">Barang</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($basopkaryawan as $item)
                        <tr>
                            <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                            <td scope="row" class="text-center">{{ $item->nama }}</td>
                            <td scope="row" class="text-center">{{ $item->nik }}</td>
                            <td scope="row" class="text-center">{{ $item->jabatan }}</td>
                            <td scope="row" class="text-center">
                                @if ($item->jenis_kelamin == 'laki-laki')
                                <span style="color: rgb(94, 115, 236); font-weight: bold;">Laki - Laki</span>
                                @else($item->jenis_kelamin == 'Perempuan')
                                <span style="color: palevioletred; font-weight: bold;">Perempuan</span>
                                @endif
                            </td>
                            <td scope="row" class="text-center">{{ $item->shift }}</td>
                            <td scope="row" class="text-center">{{ $item->nama_pembuat }}</td>
                            <td scope="row" class="text-center">{{ $item->jabatan_pembuat }}</td>
                            <td scope="row" class="text-center">{{ $item->nama_area }}</td>
                            <td scope="row" class="text-center">{{ $item->barang }}</td>
                            <td scope="row" class="text-center">
                                <a href="{{ route('listkaryawan.kembalikan', ['id'=>$item->id]) }}" onclick="return confirm('Apakah anda yakin ingin mengembalikan data ba s.o.p karyawan ini yang sudah dihapus ?');" class="btn btn-md btn-outline-success" style="margin-bottom: 10px;"><i class="ri-leaf-fill"></i></a>
                                <a style="display: none" href="{{ route('listkaryawan.hapus_permanen', ['id'=>$item->id]) }}" onclick="return confirm('Apakah anda yakin ingin menghapus data ba s.o.p karyawan ini yang sudah dihapus secara permanen ?');" class="btn btn-md btn-outline-danger"><i class="ri-delete-back-2-fill"></i></a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="d-flex justify-content-center">
                                        <img src="/found.png" style="width: 200px; margin: 50px;">
                                    </div>
                                    <div class="text-center">
                                        <h1>Data tidak ditemukan</h1>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
            </table>
            </div>
            <div class="col-12 mt-4">
                <a href="{{ route('ba-sop-list-karyawan') }}" class="btn btn-outline-dark btn-md">Kembali</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#trashbasopkaryawan').DataTable();
    });
</script>
@endpush