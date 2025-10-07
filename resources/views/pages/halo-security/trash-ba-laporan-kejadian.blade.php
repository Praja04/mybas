@extends('pages.halo-security.layout.base')

@section('title', 'BA Laporan Kejadian')

@section('content')

<div class="container-fluid">
    <form action="{{route('listlaporankejadian.trash')}}" method="get">
        {{csrf_field()}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Tanggal</label>
                                <input type="date" name="created_at" value="{{isset($_GET['created_at']) ? $_GET['created_at'] : ''}}" class="form-control" id="exampleInputdate">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Jenis Kejadian</label>
                                <select class="form-select" id="inputGroupSelect01" name="jenis_kejadian">
                                    <option value="">Semua Jenis Kejadian</option>
                                    <option {{ Request('jenis_kejadian') == 'kecelakaan lalu lintas' ? 'selected' : '' }} value="kecelakaan lalu lintas">Kecelakaan Lalu Lintas</option>
                                    <option {{ Request('jenis_kejadian') == 'penemuan barang' ? 'selected' : '' }} value="penemuan barang">Penemuan Barang</option>
                                    <option {{ Request('jenis_kejadian') == 'kecelakaan kerja' ? 'selected' : '' }} value="kecelakaan kerja">Kecelakaan Kerja</option>
                                    <option {{ Request('jenis_kejadian') == 'pencurian' ? 'selected' : '' }} value="pencurian">Pencurian</option>
                                    <option {{ Request('jenis_kejadian') == 'perkelahian' ? 'selected' : '' }} value="perkelahian">Perkelahian</option>
                                    <option {{ Request('jenis_kejadian') == 'tindak kekerasan' ? 'selected' : '' }} value="tindak kekerasan">Tindak Kekerasan</option>
                                    <option {{ Request('jenis_kejadian') == 'kebakaran' ? 'selected' : '' }} value="kebakaran">Kebakaran</option>
                                    <option {{ Request('jenis_kejadian') == 'demonstrasi' ? 'selected' : '' }} value="demonstrasi">Demonstrasi</option>
                                    <option {{ Request('jenis_kejadian') == 'tindakan asusila' ? 'selected' : '' }} value="tindakan asusila">Tindakan Asusila</option>
                                    <option {{ Request('jenis_kejadian') == 'pengerusakan' ? 'selected' : '' }} value="pengerusakan">Pengerusakan</option>
                                    <option {{ Request('jenis_kejadian') == 'tindakan indispliner' ? 'selected' : '' }} value="tindakan indispliner">Tindakan Indispliner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Status Terlapor</label>
                                <select class="form-select" id="inputGroupSelect01" name="status_terlapor">
                                    <option value="">Semua Status Terlapor</option>
                                    <option {{ Request('status_terlapor') == 'sudah kawin' ? 'selected' : '' }} value="sudah kawin">Sudah Kawin</option>
                                    <option {{ Request('status_terlapor') == 'belum kawin' ? 'selected' : '' }} value="belum kawin">Belum Kawin</option>
                                    <option {{ Request('status_terlapor') == 'janda/duda' ? 'selected' : '' }} value="janda/duda">Janda/Duda</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-2 mt-3">
                            <button type="submit" class="btn btn-md btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
        </div>
    </form>
    <div class="card">
        <div class="card-header justify-content-around d-flex">
            @foreach ($balaporankejadian as $item)
            @if($loop->iteration == 1)
            <a href="{{ route('listlaporankejadian.kembalikan_semua') }}" onclick="return confirm('Apakah anda yakin ingin mengembalikan semua data berita acara laporan kejadian ini yang sudah dihapus ?');" class="btn btn-md btn-outline-success"><i class="ri-recycle-fill" style="margin-top: 8px; margin-right: 4px;"></i> Kembalikan Semua</a>
            @endif
            @endforeach
            <h4 class="card-title flex-grow-1 text-center mt-2">Recycling Berita Acara Laporan Kejadian</h4>
            @foreach ($balaporankejadian as $item)
            @if($loop->iteration == 1)
            <a style="display: none" href="{{ route('listlaporankejadian.hapus_permanen_semua') }}" onclick="return confirm('Apakah anda yakin ingin menghapus semua data berita acara laporan kejadian ini yang sudah dihapus secara permanen ?');" class="btn btn-md btn-outline-danger"><i class="ri-delete-bin-2-fill" style="margin-top: 8px; margin-right: 4px;"></i> Hapus Permanen Semua</a>
            @endif
            @endforeach
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="trashbakejadian" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">Jenis Kejadian</th>
                            <th scope="col" class="text-center">Nama Korban</th>
                            <th scope="col" class="text-center">Nik Korban</th>
                            <th scope="col" class="text-center">Perusahaan Korban</th>
                            <th scope="col" class="text-center">Bagian Korban</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($balaporankejadian as $item)
                        <tr>
                            <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                            <td scope="row" class="text-center">{{ $item->jenis_kejadian }}</td>
                            <td scope="row" class="text-center">{{ $item->nama_korban }}</td>
                            <td scope="row" class="text-center">{{ $item->nik_korban }}</td>
                            <td scope="row" class="text-center">{{ $item->perusahaan_korban }}</td>
                            <td scope="row" class="text-center">{{ $item->bagian_korban }}</td>
                            <td scope="row" class="text-center">
                                <a href="{{ route('listlaporankejadian.kembalikan', ['lk_id'=>$item->lk_id]) }}" onclick="return confirm('Apakah anda yakin ingin mengembalikan data berita acara laporan kejadian ini yang sudah dihapus ?');" class="btn btn-md btn-outline-success" style="margin-right: 8px;"><i class="ri-leaf-fill"></i></a>
                                <a style="display: none" href="{{ route('listlaporankejadian.hapus_permanen', ['lk_id'=>$item->lk_id]) }}" onclick="return confirm('Apakah anda yakin ingin menghapus data berita acara laporan kejadian ini yang sudah dihapus secara permanen ?');" class="btn btn-md btn-outline-danger"><i class="ri-delete-back-2-fill"></i></a>
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
            <div class="col-12 mt-3">
                <a href="{{ route('ba-list-laporankejadian') }}" class="btn btn-outline-dark btn-md">Kembali</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#trashbakejadian').DataTable();
    });
</script>
@endpush