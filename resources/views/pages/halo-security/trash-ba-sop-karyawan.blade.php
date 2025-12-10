@extends('pages.halo-security.layout.base')

@section('title', 'BA S.O.P Karyawan')

@section('content')

    <div class="container-fluid">
        {{-- FILTER DATA --}}
        <form action="{{ route('listkaryawan.trash') }}" method="get">
            {{ csrf_field() }}

            <div class="col-12">
                <div class="card">

                    <div class="card-header text-center">
                        <h4 class="card-title mb-0">Filter Recycle Bin Karyawan</h4>
                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            {{-- Tanggal --}}
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="input-group">
                                    <label class="input-group-text">Tanggal</label>
                                    <input type="date" name="created_at" value="{{ request('created_at') }}"
                                        class="form-control">
                                </div>
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="input-group">
                                    <label class="input-group-text">Jenis Kelamin</label>
                                    <select class="form-select" name="jenis_kelamin">
                                        <option value="">Semua</option>
                                        <option value="laki-laki"
                                            {{ request('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>Laki - Laki
                                        </option>
                                        <option value="perempuan"
                                            {{ request('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>Perempuan
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- Shift --}}
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="input-group">
                                    <label class="input-group-text">Shift</label>
                                    <select class="form-select" name="shift">
                                        <option value="">Semua Shift</option>
                                        <option value="1" {{ request('shift') == '1' ? 'selected' : '' }}>Shift 1
                                        </option>
                                        <option value="2" {{ request('shift') == '2' ? 'selected' : '' }}>Shift 2
                                        </option>
                                        <option value="3" {{ request('shift') == '3' ? 'selected' : '' }}>Shift 3
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- Tombol Filter --}}
                            <div class="col-12 col-md-6 col-lg-2">
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
                @foreach ($basopkaryawan as $item)
                    @if ($loop->iteration == 1)
                        <a href="{{ route('listkaryawan.kembalikan_semua') }}"
                            onclick="return confirm('Apakah anda yakin ingin mengembalikan semua data ba s.o.p karyawan ini yang sudah dihapus ?');"
                            class="btn btn-md btn-outline-success"><i class="ri-recycle-fill"
                                style="margin-top: 8px; margin-right: 4px;"></i> Kembalikan Semua</a>
                    @endif
                @endforeach
                <h4 class="card-title flex-grow-1 text-center mt-2">Recycling Berita Acara S.O.P Karyawan</h4>
                @foreach ($basopkaryawan as $item)
                    @if ($loop->iteration == 1)
                        <a style="display: none" href="{{ route('listkaryawan.hapus_permanen_semua') }}"
                            onclick="return confirm('Apakah anda yakin ingin menghapus semua data ba s.o.p karyawan ini yang sudah dihapus secara permanen ?');"
                            class="btn btn-md btn-outline-danger"><i class="ri-delete-bin-2-fill"
                                style="margin-top: 8px; margin-right: 4px;"></i> Hapus Permanen Semua</a>
                    @endif
                @endforeach
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="trashbasopkaryawan" class="table table-md table-bordered border-secondary table-nowrap"
                        style="width:100%">
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
                                        <a href="{{ route('listkaryawan.kembalikan', ['id' => $item->id]) }}"
                                            onclick="return confirm('Apakah anda yakin ingin mengembalikan data ba s.o.p karyawan ini yang sudah dihapus ?');"
                                            class="btn btn-md btn-outline-success" style="margin-bottom: 10px;"><i
                                                class="ri-leaf-fill"></i></a>
                                        <a style="display: none"
                                            href="{{ route('listkaryawan.hapus_permanen', ['id' => $item->id]) }}"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ba s.o.p karyawan ini yang sudah dihapus secara permanen ?');"
                                            class="btn btn-md btn-outline-danger"><i
                                                class="ri-delete-back-2-fill"></i></a>
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
                <div class="col-12 mt-4">
                    <a href="{{ route('ba-sop-list-karyawan') }}" class="btn btn-outline-dark btn-md">Kembali</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#trashbasopkaryawan').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [-1]
                }]

            });
        });
    </script>
@endpush
