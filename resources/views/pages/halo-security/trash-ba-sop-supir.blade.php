@extends('pages.halo-security.layout.base')

@section('title', 'BA S.O.P Supir')

@section('content')

    <div class="container-fluid">
        {{-- FILTER DATA --}}
        <form action="{{ route('listsupir.trash') }}" method="get">
            {{ csrf_field() }}

            <div class="col-12">
                <div class="card">

                    <div class="card-header text-center">
                        <h4 class="card-title mb-0">Filter Recycle Bin S.O.P Supir</h4>
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

                            {{-- Shift --}}
                            <div class="col-12 col-md-6 col-lg-4">
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
                            <div class="col-12 col-md-4 col-lg-2">
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
            <div class="card-header">
                <div class="row align-items-center g-3">

                    {{-- Tombol Kembalikan Semua --}}
                    <div class="col-12 col-md-2 text-center text-md-start">
                        @if ($basopsupir->count() > 0)
                            <a href="{{ route('listsupir.kembalikan_semua') }}"
                                onclick="return confirm('Apakah anda yakin ingin mengembalikan semua data BA S.O.P Supir yang sudah dihapus?');"
                                class="btn btn-outline-success w-100 w-md-auto">
                                <i class="ri-recycle-fill me-1"></i> Kembalikan Semua
                            </a>
                        @endif
                    </div>

                    {{-- Judul --}}
                    <div class="col-12 col-md-8 text-center">
                        <h4 class="card-title mb-0">Recycling Berita Acara S.O.P Supir</h4>
                    </div>

                    {{-- Tombol Hapus Permanen Semua --}}
                    <div class="col-12 col-md-2 text-center text-md-end">
                        @if ($basopsupir->count() > 0)
                            <a style="display: none;" href="{{ route('listsupir.hapus_permanen_semua') }}"
                                onclick="return confirm('Apakah anda yakin ingin menghapus permanen semua data BA S.O.P Supir ini?');"
                                class="btn btn-outline-danger w-100 w-md-auto">
                                <i class="ri-delete-bin-2-fill me-1"></i> Hapus Permanen Semua
                            </a>
                        @endif
                    </div>

                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="trashbasopsupir" class="table table-md table-bordered border-secondary table-nowrap"
                        style="width:100%">
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
                                        <a href="{{ route('listsupir.kembalikan', ['id' => $item->id]) }}"
                                            onclick="return confirm('Apakah anda yakin ingin mengembalikan data ba s.o.p supir ini yang sudah dihapus ?');"
                                            class="btn btn-md btn-outline-success" style="margin-bottom: 10px;"><i
                                                class="ri-leaf-fill"></i></a>
                                        <a style="display: none"
                                            href="{{ route('listsupir.hapus_permanen', ['id' => $item->id]) }}"
                                            onclick="return confirm('Apakah anda yakin ingin menghapus data ba s.o.p supir ini yang sudah dihapus secara permanen ?');"
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
                    <a href="{{ route('ba-sop-list-supir') }}" class="btn btn-outline-dark btn-md">Kembali</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#trashbasopsupir').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [-1]
                }]
            });
        });
    </script>
@endpush
