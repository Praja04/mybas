<div class="container-fluid">
    {{-- EXPORT EXCEL --}}
    <form method="get" action="{{ route('excel-reportbai') }}" id="range">
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
                            <i class="ri-file-excel-2-fill me-1"></i> Export Excel
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </form>

    {{-- FILTER DATA --}}
    <form action="{{ route('ba-list-introgasi') }}" method="get">
        {{ csrf_field() }}

        <div class="col-12">
            <div class="card">

                <div class="card-header text-center">
                    <h4 class="card-title mb-0">Filter Data List Berita Acara Introgasi</h4>
                </div>

                <div class="card-body">

                    <div class="row g-3">

                        <!-- Tanggal -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="input-group">
                                <label class="input-group-text">Tanggal</label>
                                <input type="date" name="created_at" value="{{ request('created_at') }}"
                                    class="form-control">
                            </div>
                        </div>

                        <!-- Jenis Kejadian -->
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

                        <!-- Status Pelaku -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="input-group">
                                <label class="input-group-text">Status Pelaku</label>
                                <select class="form-select" name="status_pelaku">
                                    <option value="">Semua Status Pelaku</option>
                                    @foreach (['sudah kawin', 'belum kawin', 'janda/duda'] as $status)
                                        <option value="{{ $status }}"
                                            {{ request('status_pelaku') == $status ? 'selected' : '' }}>
                                            {{ ucwords($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Shift -->
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

                        <!-- Filter Button -->
                        <div class="col-12 col-md-6 col-lg-2">
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
                    <h4 class="card-title mb-0">List Berita Acara Introgasi</h4>
                </div>
                <div class="col-12 col-md-4 text-center text-md-end mt-2 mt-md-0">
                    <a href="{{ route('listbai.trash') }}" class="btn btn-md btn-success"><i class="ri-recycle-fill"
                            style="margin-top: 8px; margin-right: 4px;"></i> Recycling</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="baintrogasi" class="table table-md table-bordered border-secondary table-nowrap"
                    style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">ID Introgasi</th>
                            <th scope="col" class="text-center">Jenis Kejadian</th>
                            <th scope="col" class="text-center">Nama Introgasi</th>
                            <th scope="col" class="text-center">Nama Pelapor</th>
                            <th scope="col" class="text-center">Nama Pelaku</th>
                            <th scope="col" class="text-center">Nama Korban</th>
                            <th scope="col" class="text-center">Motif Kejadian</th>
                            <th scope="col" class="text-center">Tempat Kejadian</th>
                            <th scope="col" class="text-center">Dokumen TTD</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($baintrogasi as $item)
                            <tr id="bid{{ $item->bai_id }}">
                                <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                                <td scope="row" class="text-center">{{ $item->bai_id }}</td>
                                <td scope="row" class="text-center">{{ $item->jenis_kejadian }}</td>
                                <td scope="row" class="text-center">{{ $item->nama_introgasi }}</td>
                                <td scope="row" class="text-center">{{ $item->nama_pelapor }}</td>
                                <td scope="row" class="text-center">{{ $item->nama_pelaku }}</td>
                                <td scope="row" class="text-center">{{ $item->nama_korban }}</td>
                                <td scope="row" class="text-center">{{ $item->detail_barang_kejadian }}</td>
                                <td scope="row" class="text-center">{{ $item->tempat_kejadian }}</td>
                                <td scope="row" class="text-center">
                                    @if (empty($item->dokumen_ttd))
                                        <a href="javascript:void(0)" onClick="uploaddokumen('{{ $item->bai_id }}')"
                                            class="btn btn-outline-info"><i class="ri-upload-2-line"></i> Upload</a>
                                    @else
                                        <a href="{{ route('printdokumenttd.introgasi', $item->bai_id) }}"
                                            class="btn btn-outline-success"><i class="ri-download-2-line"></i>
                                            Download</a>
                                    @endif
                                </td>
                                <td scope="row" class="text-center">
                                    <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                        @if (in_array('hs_edit_bai', $permissions))
                                            <a href="{{ route('edit-introgasi', ['bai_id' => $item->bai_id]) }}"
                                                class="btn btn-outline-warning"><i class="ri-pencil-fill"></i>
                                                Ubah</a>
                                        @endif
                                        @if (in_array('hs_hapus_bai', $permissions))
                                            <form action="{{ route('hapus-introgasi', ['bai_id' => $item->bai_id]) }}"
                                                method="post">
                                                <button class="btn btn-outline-danger"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data BA Introgasi ini?');"
                                                    type="submit"><i class="ri-delete-bin-2-line"></i> Hapus</button>
                                                @csrf
                                                @method('delete')
                                            </form>
                                        @endif
                                        <a href="{{ route('printpdf.introgasi', $item->bai_id) }}"
                                            class="btn btn-outline-success"><i class=" ri-file-download-line"></i>
                                            Full</a>
                                        <a href="{{ route('printpdfonepage.introgasi', $item->bai_id) }}"
                                            class="btn btn-outline-info"><i class=" ri-file-download-line"></i>
                                            Satu</a>
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

<!-- Default Modals -->
<div id="dokumenEditModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Upload Gambar Satu Halaman Berita Acara Introgasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <form id="dokumenEditForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="bai_id" name="bai_id" />
                    <input class="form-control mb-3" name="dokumen_ttd" type="file" id="dokumen_ttd">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@push('scripts')
    <script>
        // Validasi Data Input Export Excel
        $("#range").submit(function() {
            // Mengambil data
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();

            // Validasi
            if (startDate == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Berita Acara Introgasi dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            } else if (endDate == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'To export wajib di isi, untuk export data Berita Acara Introgasi sampai tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }
        })

        $("#dokumenEditForm").submit(function() {
            // Mengambil data
            var dokumen_ttd = $("#dokumen_ttd").val();

            // Validasi
            if (dokumen_ttd == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'Form Upload Dokumen Satu Halaman Yang Sudah Di Tanda Tangan Wajib Di Isi',
                    icon: 'warning',
                });
                return false;
            }
        })
    </script>

    <script>
        function uploaddokumen(bai_id) {
            $.get('/halo-security/bai/introgasi/' + bai_id, function(introgasi) {
                $("#bai_id").val(introgasi.bai_id);
                $("#dokumenEditModal").modal('toggle');
            });
        }

        $("#dokumenEditForm").submit(function(e) {
            e.preventDefault();

            let bai_id = $("#bai_id").val();
            let dokumen_ttd = $("#dokumen_ttd").val();
            let _token = $("input[name=_token]").val();

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('upload-dokumen-ttd') }}",
                type: "POST",
                processData: false,
                contentType: false,
                data: formData,
                success: function(response) {
                    $("#dokumenEditModal").modal('toggle');
                    $("#dokumenEditForm")[0].reset();
                    $('#main').html(
                        '<div class="alert alert-success alert-dismissible alert-solid alert-label-icon shadow fade show col-sm-12 mb-2" role="alert"><i class="ri-upload-2-line"></i> <strong>Success</strong> - ' +
                        response.success +
                        '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                    );
                    setTimeout(function(response) {
                        window.location = `{{ url('/halo-security/bai/listintrogasi') }}`;
                    }, 2000);
                }
            })
        })
    </script>

    <script>
        $(document).ready(function() {
            $('#baintrogasi').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [-1, -2]
                }]
            });
        });
    </script>
@endpush
