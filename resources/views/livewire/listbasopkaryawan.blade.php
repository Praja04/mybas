<div class="container-fluid">
        <form method="get" action="{{ route('excel-report-karyawan') }}" id="excelkaryawan">
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
        <form action="{{route('ba-sop-list-karyawan')}}" method="get">
            {{csrf_field()}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0 flex-grow-1 text-center">Filter Data List Berita Acara S.O.P Karyawan</h4>
                    </div>
                    <div class="card-body">
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
                                <button type="submit" style="width: 100%;" class="btn btn-md btn-primary"><i class="ri-filter-3-line" style="margin-top: 10px; margin-right: 4px;"></i> Filter</button>
                            </div>
                        </div>
                    </div>
            </div>
        </form>
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1 text-center">List Berita Acara S.O.P Karyawan</h4>
                <a href="{{ route('listkaryawan.trash') }}" class="btn btn-md btn-success"><i class="ri-recycle-fill" style="margin-top: 8px; margin-right: 4px;"></i> Recycling</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="basopkaryawan" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
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
                                    <div class="hstack gap-3 fs-15">
                                        @if (in_array('hs_edit_sop_karyawan', $permissions))
                                        <a href="{{ route('edit-ba-sop-karyawan', ['basopkaryawan_id'=>$item->id]) }}" class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                        @endif
                                        @if (in_array('hs_hapus_sop_karyawan', $permissions))
                                        <a href="#" class="btn btn-outline-danger" wire:click.prevent="confirmKaryawanRemoval({{ $item->id }})">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </a>
                                        @endif
                                        <a href="{{ route('printpdf.karyawan',$item->id) }}" class="btn btn-outline-success"><i class=" ri-file-download-line"></i></a>
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
            if(startDate == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Berita Acara S.O.P Karyawan dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }else if(endDate == ""){
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
    $(document).ready(function () {
        $('#basopkaryawan').DataTable();
    });
</script>
@endpush