@extends('pages.halo-security.layout.base')

@section('title', 'Security User GA')

@section('content')

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12 col-md-8 text-center text-md-start">
                        <h4 class="card-title mb-0">List Security User GA</h4>
                    </div>

                    <div class="col-12 col-md-4 text-center text-md-end mt-2 mt-md-0">
                        <a href="{{ route('create-security') }}" class="btn btn-primary btn-md">
                            <i class="ri-add-fill me-1"></i> Tambah Data
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="securityuserga" class="table table-md table-bordered border-secondary table-nowrap"
                        style="width:100%">
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
                                    <td scope="row" class="text-center">{{ $item->nik }}</td>
                                    <td scope="row" class="text-center">{{ $item->nama }}</td>
                                    <td scope="row" class="text-center">{{ $item->keterangan }}</td>
                                    <td>
                                        <div class="hstack gap-3 fs-15 d-flex justify-content-center">
                                            <a href="{{ route('edit-security', ['user_id' => $item->user_id]) }}"
                                                class="btn btn-outline-warning"><i class="ri-pencil-fill"></i></a>
                                            <form action="{{ route('destroy-security', ['user_id' => $item->user_id]) }}"
                                                method="post">
                                                <button class="btn btn-outline-danger"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus Data Security User GA ini?');"
                                                    type="submit"><i class="ri-delete-bin-2-line"></i></button>
                                                @csrf
                                                @method('delete')
                                            </form>
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

@endsection

@push('scripts')
    <script>
        // Validasi Data Input Export Excel
        $("#excelsecurity").submit(function() {
            // Mengambil data
            var startDate = $("#startDate").val();
            var endDate = $("#endDate").val();

            // Validasi
            if (startDate == "") {
                Swal.fire({
                    title: 'Oops !',
                    text: 'From export wajib di isi, untuk export data Security User GA dari tanggal berapa ke dalam report excel',
                    icon: 'warning',
                });
                return false;
            } else if (endDate == "") {
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
        $(document).ready(function() {
            $('#securityuserga').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [-1, -2]
                }]
            });
        });
    </script>
@endpush
