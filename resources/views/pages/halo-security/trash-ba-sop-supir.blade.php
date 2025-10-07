@extends('pages.halo-security.layout.base')

@section('title', 'BA S.O.P Supir')

@section('content')

<div class="container-fluid">
    <form action="{{route('listsupir.trash')}}" method="get">
        {{csrf_field()}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Tanggal</label>
                                <input type="date" name="created_at" value="{{isset($_GET['created_at']) ? $_GET['created_at'] : ''}}" class="form-control" id="exampleInputdate">
                            </div>
                        </div>
                        <div class="col-4">
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
            @foreach ($basopsupir as $item)
            @if($loop->iteration == 1)
            <a href="{{ route('listsupir.kembalikan_semua') }}" onclick="return confirm('Apakah anda yakin ingin mengembalikan semua data ba s.o.p supir ini yang sudah dihapus ?');" class="btn btn-md btn-outline-success"><i class="ri-recycle-fill" style="margin-top: 8px; margin-right: 4px;"></i> Kembalikan Semua</a>
            @endif
            @endforeach
            <h4 class="card-title flex-grow-1 text-center mt-2">Recycling Berita Acara S.O.P Supir</h4>
            @foreach ($basopsupir as $item)
            @if($loop->iteration == 1)
            <a style="display: none" href="{{ route('listsupir.hapus_permanen_semua') }}" onclick="return confirm('Apakah anda yakin ingin menghapus semua data ba s.o.p supir ini yang sudah dihapus secara permanen ?');" class="btn btn-md btn-outline-danger"><i class="ri-delete-bin-2-fill" style="margin-top: 8px; margin-right: 4px;"></i> Hapus Permanen Semua</a>
            @endif
            @endforeach
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="trashbasopsupir" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">Nama</th>
                            <th scope="col" class="text-center">Ekspedisi</th>
                            <th scope="col" class="text-center">No.KTP</th>
                            <th scope="col" class="text-center">No.Polisi</th>
                            <th scope="col" class="text-center">No.Handphone</th>
                            <th scope="col" class="text-center">No.Kartu</th>
                            <th scope="col" class="text-center">Alamat</th>
                            <th scope="col" class="text-center">Shift</th>
                            <th scope="col" class="text-center">Nama Pembuat</th>
                            <th scope="col" class="text-center">Jabatan Pembuat</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($basopsupir as $item)
                        <tr>
                            <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                            <td scope="row" class="text-center">{{ $item->nama }}</td>
                            <td scope="row" class="text-center">{{ $item->ekspedisi }}</td>
                            <td scope="row" class="text-center">{{ $item->no_ktp }}</td>
                            <td scope="row" class="text-center">{{ $item->no_polisi }}</td>
                            <td scope="row" class="text-center">{{ $item->no_handphone }}</td>
                            <td scope="row" class="text-center">{{ $item->no_kartu }}</td>
                            <td scope="row" class="text-center">{{ $item->alamat }}</td>
                            <td scope="row" class="text-center">{{ $item->shift }}</td>
                            <td scope="row" class="text-center">{{ $item->nama_pembuat }}</td>
                            <td scope="row" class="text-center">{{ $item->jabatan_pembuat }}</td>
                            <td scope="row" class="text-center">
                                <a href="{{ route('listsupir.kembalikan', ['id'=>$item->id]) }}" onclick="return confirm('Apakah anda yakin ingin mengembalikan data ba s.o.p supir ini yang sudah dihapus ?');" class="btn btn-md btn-outline-success" style="margin-bottom: 10px;"><i class="ri-leaf-fill"></i></a>
                                <a style="display: none" href="{{ route('listsupir.hapus_permanen', ['id'=>$item->id]) }}" onclick="return confirm('Apakah anda yakin ingin menghapus data ba s.o.p supir ini yang sudah dihapus secara permanen ?');" class="btn btn-md btn-outline-danger"><i class="ri-delete-back-2-fill"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12">
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
                <a href="{{ route('ba-sop-list-supir') }}" class="btn btn-outline-dark btn-md">Kembali</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#trashbasopsupir').DataTable();
    });
</script>
@endpush