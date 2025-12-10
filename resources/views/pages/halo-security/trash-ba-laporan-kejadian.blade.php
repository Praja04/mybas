@extends('pages.halo-security.layout.base')

@section('title', 'BA Laporan Kejadian')

@section('content')

    <div class="container-fluid">
        {{-- FILTER --}}
        <form action="{{ route('listlaporankejadian.trash') }}" method="get">
            {{ csrf_field() }}

            <div class="col-12">
                <div class="card">

                    <div class="card-header text-center">
                        <h4 class="card-title mb-0">Filter Recycle Bin Laporan Kejadian</h4>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            {{-- Tanggal --}}
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="input-group">
                                    <label class="input-group-text">Tanggal</label>
                                    <input type="date" name="created_at" value="{{ request('created_at') }}"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- Jenis Kejadian --}}
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="input-group">
                                    <label class="input-group-text">Jenis Kejadian</label>
                                    <select class="form-select" name="jenis_kejadian">
                                        <option value="">Semua Jenis Kejadian</option>
                                        @foreach (['kecelakaan lalu lintas', 'penemuan barang', 'kecelakaan kerja', 'pencurian', 'perkelahian', 'tindak kekerasan', 'kebakaran', 'demonstrasi', 'tindakan asusila', 'pengerusakan', 'tindakan indispliner'] as $jenis)
                                            <option value="{{ $jenis }}"
                                                {{ request('jenis_kejadian') == $jenis ? 'selected' : '' }}>
                                                {{ ucwords($jenis) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Status Terlapor --}}
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="input-group">
                                    <label class="input-group-text">Status Terlapor</label>
                                    <select class="form-select" name="status_terlapor">
                                        <option value="">Semua Status Terlapor</option>
                                        @foreach (['sudah kawin', 'belum kawin', 'janda/duda'] as $status)
                                            <option value="{{ $status }}"
                                                {{ request('status_terlapor') == $status ? 'selected' : '' }}>
                                                {{ ucwords($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Tombol Filter --}}
                            <div class="col-12 col-md-3 col-lg-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    Filter
                                </button>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </form>

        {{-- TABLE --}}
        <div class="card">
            <div class="card-header justify-content-around d-flex">
                @foreach ($balaporankejadian as $item)
                    @if ($loop->iteration == 1)
                        <a href="{{ route('listlaporankejadian.kembalikan_semua') }}"
                            onclick="return confirm('Apakah anda yakin ingin mengembalikan semua data berita acara laporan kejadian ini yang sudah dihapus ?');"
                            class="btn btn-md btn-outline-success"><i class="ri-recycle-fill"
                                style="margin-top: 8px; margin-right: 4px;"></i> Kembalikan Semua</a>
                    @endif
                @endforeach
                <h4 class="card-title flex-grow-1 text-center mt-2">Recycling Berita Acara Laporan Kejadian</h4>
                @foreach ($balaporankejadian as $item)
                    @if ($loop->iteration == 1)
                        <a style="display: none" href="{{ route('listlaporankejadian.hapus_permanen_semua') }}"
                            onclick="return confirm('Apakah anda yakin ingin menghapus semua data berita acara laporan kejadian ini yang sudah dihapus secara permanen ?');"
                            class="btn btn-md btn-outline-danger"><i class="ri-delete-bin-2-fill"
                                style="margin-top: 8px; margin-right: 4px;"></i> Hapus Permanen Semua</a>
                    @endif
                @endforeach
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="trashbakejadian" class="table table-md table-bordered border-secondary table-nowrap"
                        style="width:100%">
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
                                        <a href="{{ route('listlaporankejadian.kembalikan', ['lk_id' => $item->lk_id]) }}"
                                            onclick="return confirm('Apakah anda yakin ingin mengembalikan data berita acara laporan kejadian ini yang sudah dihapus ?');"
                                            class="btn btn-md btn-outline-success" style="margin-right: 8px;"><i
                                                class="ri-leaf-fill"></i></a>
                                        <a style="display: none"
                                            href="{{ route('listlaporankejadian.hapus_permanen', ['lk_id' => $item->lk_id]) }}"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data berita acara laporan kejadian ini yang sudah dihapus secara permanen ?');"
                                            class="btn btn-md btn-outline-danger"><i class="ri-delete-back-2-fill"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12">
                                        <div class="text-center">
                                            <span class="text-center text-muted">Data tidak ditemukan</span>
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
        $(document).ready(function() {
            $('#trashbakejadian').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [-1]
                }]
            });
        });
    </script>
@endpush
