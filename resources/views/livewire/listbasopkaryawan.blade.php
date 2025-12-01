<div class="container-fluid">
    {{-- EXPORT EXCEL --}}
    <form method="get" action="{{ route('excel-report-karyawan') }}" id="excelkaryawan">
        {{ csrf_field() }}

        <div class="card">
            <div class="card-header text-center">
                <h4 class="card-title mb-0">Export Excel</h4>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <label class="input-group-text">From Export</label>
                            <input id="startDate" name="startDate" type="date" class="form-control">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="input-group">
                            <label class="input-group-text">To Export</label>
                            <input id="endDate" name="endDate" type="date" class="form-control">
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="ri-file-excel-2-fill me-1"></i>
                            Export Excel
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </form>

    {{-- FILTER DATA --}}
    <form action="{{ route('ba-sop-list-karyawan') }}" method="get">
        {{ csrf_field() }}

        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <h4 class="card-title mb-0">Filter Data List Berita Acara S.O.P Karyawan</h4>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-12 col-md-3">
                            <div class="input-group">
                                <label class="input-group-text">Tanggal</label>
                                <input type="date" name="created_at" value="{{ request('created_at') }}"
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
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

                        <div class="col-12 col-md-3">
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

                        <div class="col-12 col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ri-filter-3-line me-1"></i> Filter
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
            <div class="align-items-center row">
                <div class="col-12 col-md-8 text-center text-md-start">
                    <h4 class="card-title mb-0">List Berita Acara S.O.P Karyawan</h4>
                </div>
                <div class="col-12 col-md-4 text-center text-md-end mt-2 mt-md-0">
                    <a href="{{ route('listkaryawan.trash') }}" class="btn btn-md btn-success"><i
                            class="ri-recycle-fill" style="margin-top: 8px; margin-right: 4px;"></i> Recycling
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="basopkaryawan" class="table table-md table-bordered border-secondary table-nowrap"
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
                            <th scope="col" class="text-center">Pelanggaran</th>
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
                                    <div class="hstack gap-3 fs-15">
                                        @if (in_array('hs_edit_sop_karyawan', $permissions))
                                            <a href="{{ route('edit-ba-sop-karyawan', ['basopkaryawan_id' => $item->id]) }}"
                                                class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                        @endif
                                        @if (in_array('hs_hapus_sop_karyawan', $permissions))
                                            <a href="#" class="btn btn-outline-danger"
                                                wire:click.prevent="confirmKaryawanRemoval({{ $item->id }})">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('download.pdf.karyawan', $item->id) }}"
                                            class="btn btn-outline-success" title="Download PDF" id="btn-download">
                                            <i class="ri-download-line"></i>
                                        </a>

                                        <a href="{{ route('preview.pdf.karyawan', $item->id) }}"
                                            class="btn btn-outline-primary" target="_blank" title="Preview PDF"
                                            id="btn-preview">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
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
        window.addEventListener('show-delete-confirmation', event => {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin menghapus data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, saya setuju!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('deleteConfirmed');
                }
            })
        });

        window.addEventListener('deleted', event => {
            Swal.fire(
                'Deleted!',
                event.detail.message,
                'success'
            )
        })
    </script>

    <script>
        // Validasi Data Input Export Excel
        $("#excelkaryawan").submit(function() {
            // Mengambil data
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();

            // Validasi
            if (startDate == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Berita Acara S.O.P Karyawan dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            } else if (endDate == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'To export wajib di isi, untuk export data Berita Acara S.O.P Karyawan sampai tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }
        })
    </script>

    <script>
        $(document).ready(function() {
            $('#basopkaryawan').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [-1]
                }]

            });
        });
    </script>
@endpush
