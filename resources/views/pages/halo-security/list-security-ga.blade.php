@extends('pages.halo-security.layout.base')

@section('title', 'Security User GA')

@section('content')

<div class="container-fluid">
    <form method="get" action="{{ route('excel-report-security') }}" id="excelsecurity">
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
    <div class="card">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1 text-center">List Security User GA</h4>
            <a href="{{ route('create-security') }}" class="btn btn-primary btn-md"><i class="ri-add-fill" style="margin-top: 10px; margin-right: 4px;"></i> Tambah Data</a>
        </div>
        <div class="card-body">
            <table id="securityuserga" class="table table-md table-bordered border-secondary table-nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">No</th>
                            <th scope="col" class="text-center">NIK</th>
                            <th scope="col" class="text-center">Nama</th>
                            <th scope="col" class="text-center">Keterangan</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($securitys as $item)
                        <tr>
                            <td scope="row" class="text-center">{{ $loop->iteration }}</td>
                            <td scope="row" class="text-center">{{  $item->nik  }}</td>
                            <td scope="row" class="text-center">{{  $item->nama }}</td>
                            <td scope="row" class="text-center">{{  $item->keterangan }}</td>
                            <td>
                                <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                    <a href="{{ route('edit-security', ['user_id' => $item->user_id]) }}" class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                    <form action="{{ route('destroy-security', ['user_id'=>$item->user_id]) }}" method="post">
                                        <button class="btn btn-outline-danger" onclick="return confirm('Apakah anda yakin ingin menghapus Data Security User GA ini?');" type="submit"><i class="ri-delete-bin-2-line"></i></button>
                                        @csrf
                                        @method('delete')
                                    </form>
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

@endsection

@push('scripts')
<script>
    // Validasi Data Input Export Excel
    $("#excelsecurity").submit(function() {
            // Mengambil data
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();

            // Validasi
            if(startDate == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Security User GA dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }else if(endDate == ""){
                Swal.fire({
                    title: 'Oops !',
                    text: 'To export wajib di isi, untuk export data Security User GA sampai tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            }
    })
</script>

<script>
    $(document).ready(function () {
        $('#securityuserga').DataTable();
    });
</script>
@endpush