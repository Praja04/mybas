<div class="container-fluid">
    <form method="get" action="{{ route('excel-reportbai') }}" id="range">
        {{csrf_field()}}
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 text-center">Export Excel</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="form-group">
                        <div class="input-group">
                            <label class="input-group-text" for="inputGroupSelect01">From Export</label>
                            <input id="startDate" name="startDate" class="form-control" id="exampleInputdate" type="date" />
                        </div>
                    </div>
                    <div class="form-group" style="margin-left: -120px;">
                        <div class="input-group">
                            <label class="input-group-text" for="inputGroupSelect01">To Export</label>
                            <input id="endDate"  name="endDate" type="date" class="form-control" id="exampleInputdate" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <button type="submit" style="width: 100%;" class="btn btn-success btn-md"><i class="ri-file-excel-2-fill" style="margin-top: 10px; margin-right: 4px;"></i> Export Excel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form action="{{route('ba-list-introgasi')}}" method="get">
        {{csrf_field()}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0 flex-grow-1 text-center">Filter Data List Berita Acara Introgasi</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Tanggal</label>
                                <input type="date" name="created_at" value="{{isset($_GET['created_at']) ? $_GET['created_at'] : ''}}" class="form-control" id="exampleInputdate">
                            </div>
                        </div>
                        <div class="col-6">
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
                        <div class="col-6 mt-3">
                            <div class="input-group">
                                <label class="input-group-text" for="inputGroupSelect01">Status Pelaku</label>
                                <select class="form-select" id="inputGroupSelect01" name="status_pelaku">
                                    <option value="">Semua Status Pelaku</option>
                                    <option {{ Request('status_pelaku') == 'sudah kawin' ? 'selected' : '' }} value="sudah kawin">Sudah Kawin</option>
                                    <option {{ Request('status_pelaku') == 'belum kawin' ? 'selected' : '' }} value="belum kawin">Belum Kawin</option>
                                    <option {{ Request('status_pelaku') == 'janda/duda' ? 'selected' : '' }} value="janda/duda">Janda/Duda</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4 mt-3">
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
                        <div class="col-2 mt-3">
                            <button type="submit" style="widows: 100%;" class="btn btn-md btn-primary"><i class="ri-filter-3-line" style="margin-top: 10px; margin-right: 4px;"></i> Filter</button>
                        </div>
                    </div>
                </div>
        </div>
    </form>
    <div class="card">
        <div id="main" class="mb-3">

        </div>
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1 text-center">List Berita Acara Introgasi</h4>
            <a href="{{ route('listbai.trash') }}" class="btn btn-md btn-success"><i class="ri-recycle-fill" style="margin-top: 8px; margin-right: 4px;"></i> Recycling</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="baintrogasi" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
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
                                    <a href="javascript:void(0)" onClick="uploaddokumen('{{ $item->bai_id }}')" class="btn btn-outline-info"><i class="ri-upload-2-line"></i> Upload</a>
                                @else
                                    <a href="{{ route('printdokumenttd.introgasi',$item->bai_id) }}" class="btn btn-outline-success"><i class="ri-download-2-line"></i> Download</a>
                                @endif
                            </td>
                            <td scope="row" class="text-center">
                                <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                    @if (in_array('hs_edit_bai', $permissions))
                                    <a href="{{ route('edit-introgasi', ['bai_id'=>$item->bai_id]) }}" class="btn btn-outline-warning"><i class="ri-pencil-fill"></i> Ubah</a>
                                    @endif
                                    @if (in_array('hs_hapus_bai', $permissions))
                                    <form action="{{ route('hapus-introgasi', ['bai_id'=>$item->bai_id]) }}" method="post">
                                        <button class="btn btn-outline-danger" onclick="return confirm('Apakah anda yakin ingin menghapus data BA Introgasi ini?');" type="submit"><i class="ri-delete-bin-2-line"></i> Hapus</button>
                                        @csrf
                                        @method('delete')
                                    </form>
                                    @endif
                                    <a href="{{ route('printpdf.introgasi',$item->bai_id) }}" class="btn btn-outline-success"><i class=" ri-file-download-line"></i> Full</a>
                                    <a href="{{ route('printpdfonepage.introgasi',$item->bai_id) }}" class="btn btn-outline-info"><i class=" ri-file-download-line"></i> Satu</a>
                                </div>
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
            if(startDate == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Berita Acara Introgasi dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }else if(endDate == ""){
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
        if(dokumen_ttd == ""){
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
    function uploaddokumen(bai_id)
    {
        $.get('/halo-security/bai/introgasi/'+bai_id,function(introgasi){
            $("#bai_id").val(introgasi.bai_id);
            $("#dokumenEditModal").modal('toggle');
        });
    }

    $("#dokumenEditForm").submit(function(e){
        e.preventDefault();

        let bai_id = $("#bai_id").val();
        let dokumen_ttd = $("#dokumen_ttd").val();
        let _token = $("input[name=_token]").val();

        var formData = new FormData(this);

        $.ajax({
                url:"{{ route('upload-dokumen-ttd') }}",
                type:"POST",
                processData: false,
                contentType: false,
                data: formData,
                success:function(response){
                    $("#dokumenEditModal").modal('toggle');
                    $("#dokumenEditForm")[0].reset();
                    $('#main').html('<div class="alert alert-success alert-dismissible alert-solid alert-label-icon shadow fade show col-sm-12 mb-2" role="alert"><i class="ri-upload-2-line"></i> <strong>Success</strong> - ' + response.success + '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                    setTimeout(function(response) { 
                        window.location = `{{ url('/halo-security/bai/listintrogasi') }}`;
                    }, 2000);
                }
        })
    })
</script>

<script>
    $(document).ready(function () {
        $('#baintrogasi').DataTable();
    });
</script>
@endpush