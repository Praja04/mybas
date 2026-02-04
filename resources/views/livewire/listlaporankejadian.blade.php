<div class="container-fluid">
    {{-- EXPORT EXCEL --}}
    <form method="get" action="{{ route('excel-report-kejadian') }}" id="excelkejadian">
        {{ csrf_field() }}

        <div class="card mb-4">
            <div class="card-header text-center">
                <h4 class="card-title mb-0">Export Excel</h4>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <label class="input-group-text">From Export</label>
                            <input id="startDate" name="startDate" type="date" class="form-control" />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <label class="input-group-text">To Export</label>
                            <input id="endDate" name="endDate" type="date" class="form-control" />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="ri-file-excel-2-fill me-1"></i> Export Excel
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    {{-- FILTER DATA --}}
    <form action="{{ route('ba-list-laporankejadian') }}" method="get">
        {{ csrf_field() }}

        <div class="card mb-4">
            <div class="card-header text-center">
                <h4 class="card-title mb-0">Filter Data List Berita Acara Laporan Kejadian</h4>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <label class="input-group-text">Tanggal</label>
                            <input type="date" name="created_at" value="{{ request('created_at') }}"
                                class="form-control">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <label class="input-group-text">Jenis Kejadian</label>
                            <select class="form-select" name="jenis_kejadian">
                                <option value="">Semua Jenis Kejadian</option>
                                @foreach (['kecelakaan lalu lintas', 'penemuan barang', 'kecelakaan kerja', 'pencurian', 'perkelahian', 'tindak kekerasan', 'kebakaran', 'demonstrasi', 'tindakan asusila', 'pengerusakan', 'tindakan indispliner'] as $jk)
                                    <option value="{{ $jk }}"
                                        {{ request('jenis_kejadian') == $jk ? 'selected' : '' }}>
                                        {{ ucwords($jk) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <label class="input-group-text">Status Terlapor</label>
                            <select class="form-select" name="status_terlapor">
                                <option value="">Semua Status Terlapor</option>
                                @foreach (['sudah kawin', 'belum kawin', 'janda/duda'] as $s)
                                    <option value="{{ $s }}"
                                        {{ request('status_terlapor') == $s ? 'selected' : '' }}>
                                        {{ ucwords($s) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-filter-3-line me-1"></i> Filter
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-12 col-md-8 text-center text-md-start">
                    <h4 class="card-title mb-0">List Berita Acara Laporan Kejadian</h4>
                </div>
                <div class="col-12 col-md-4 text-center text-md-end mt-2 mt-md-0">
                    <a href="{{ route('listlaporankejadian.trash') }}" class="btn btn-success btn-md">
                        <i class="ri-recycle-fill me-1"></i> Recycling
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="bakejadian" class="table table-md table-bordered border-secondary table-nowrap"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">ID Kejadian</th>
                            <th scope="col" class="text-center">Tanggal Kejadian</th>
                            <th scope="col" class="text-center">Jenis Kejadian</th>
                            <th scope="col" class="text-center">Nama Korban</th>
                            <th scope="col" class="text-center">Nik Korban</th>
                            <th scope="col" class="text-center">Perusahaan Korban</th>
                            <th scope="col" class="text-center">Bagian Korban</th>
                            {{-- <th scope="col">Fakta Kejadian</th>
                            <th scope="col">Saksi Kejadian</th> --}}
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($balaporankejadian as $item)
                            <tr>
                                <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                                <td scope="row" class="text-center">{{ $item->lk_id }}</td>
                                <td class="text-center">
                                    {{ $item->created_at->locale('id')->translatedFormat('d F Y') }}
                                </td>
                                <td scope="row" class="text-center">{{ $item->jenis_kejadian }}</td>
                                <td scope="row" class="text-center">{{ $item->nama_korban }}</td>
                                <td scope="row" class="text-center">{{ $item->nik_korban }}</td>
                                <td scope="row" class="text-center">{{ $item->perusahaan_korban }}</td>
                                <td scope="row" class="text-center">{{ $item->bagian_korban }}</td>
                                {{-- <td>
                            @foreach ($item->faktas as $data)
                            <ul>
                                <li>{{ $data->keterangan_fakta }}</li>
                            </ul>
                            @endforeach
                            </td>
                            <td>
                            @foreach ($item->saksis as $data2)
                            <ul>
                                <li>
                                    <p>Nama : {{ $data2->nama_saksi }}</p>
                                    <p>Nik : {{ $data2->nik_saksi }}</p>
                                </li>
                            </ul>
                            @endforeach
                            </td> --}}
                                <td scope="row" class="text-center">
                                    <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                        @if (in_array('hs_edit_lk', $permissions))
                                            <a href="{{ route('edit-laporan-kejadian', ['lk_id' => $item->lk_id]) }}"
                                                class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                        @endif
                                        @if (in_array('hs_hapus_lk', $permissions))
                                            <form action="{{ route('hapus-kejadian', ['lk_id' => $item->lk_id]) }}"
                                                method="post">
                                                <button class="btn btn-outline-danger"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data BA Laporan Kejadian ini?');"
                                                    type="submit"><i class="ri-delete-bin-2-line"></i></button>
                                                @csrf
                                                @method('delete')
                                            </form>
                                        @endif
                                        <a href="{{ route('printpdf.laporankejadian', $item->lk_id) }}"
                                            class="btn btn-outline-success"><i class=" ri-file-download-line"></i></a>
                                    </div>
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
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Validasi Data Input Export Excel
        $("#excelkejadian").submit(function() {
            // Mengambil data
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();

            // Validasi
            if (startDate == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Berita Acara Laporan Kejadian dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            } else if (endDate == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'To export wajib di isi, untuk export data Berita Acara Laporan Kejadian sampai tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }
        })
    </script>

    <script>
        $(document).ready(function() {
            $('#bakejadian').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [-1]
                }]
            });
        });
    </script>
@endpush
