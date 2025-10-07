    @extends('layouts.base')

    @section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Advance Table Widget 4-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0 py-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label font-weight-bolder text-dark">Master Approval</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <div class="card-body">
                            <main class="container">
                                <div class="my-3 p-3 bg-body rounded shadow-sm">
                                    <!-- FORM PENCARIAN -->
                                    <div class="pb-3">
                                        <form class="d-flex" action="" method="get">
                                            <input class="form-control me-1" type="search" name="katakunci"
                                                value="{{ Request::get('katakunci') }}" style="width: 40%;"
                                                placeholder="Masukkan nik admin" aria-label="Search">
                                            <button class="btn btn-secondary" type="submit">Cari</button>
                                        </form>
                                    </div>
                                    <div class="pb-3">
                                        <a href="{{ url('/masukharilibur/tambah_data') }}" class="btn btn-primary">+ Tambah
                                            Data</a>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped" style="max-width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="col-md-1">id</th>
                                                    {{-- <th class="col-md-1">dept</th> --}}
                                                    <th class="col-md-2">nik_admin</th>
                                                    <th class="col-md-2">nama_admin</th>
                                                    <th class="col-md-2">nik_approval</th>
                                                    <th class="col-md-2">nama_approval</th>
                                                    <th class="col-md-2">status</th>
                                                    <th class="col-md-2">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @php
                                                    $no = ($datas->currentPage() - 1) * $datas->perPage() + 1;
                                                @endphp

                                                @foreach ($datas as $item)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        {{-- <td>{{ $item->dept }}</td> --}}
                                                        <td>{{ $item->nik_admin }}</td>
                                                        <td>{{ $item->nama_admin }}</td>
                                                        <td>{{ $item->nik_approval }}</td>
                                                        <td>{{ $item->nama_approval }}</td>
                                                        <td>{{ $item->status }}</td>
                                                        <td>
                                                            <div class="d-flex">
                                                                <a href="{{ route('editData', ['id' => $item->id]) }}"
                                                                    class="btn btn-warning btn-sm mr-2">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                                {{-- <a href="{{ route('deleteData', ['id' => $item->id]) }}"
                                                                    class="btn btn-danger btn-delete">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a> --}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        {{ $datas->links() }}
                                    </div>
                                </div>
                            </main>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.btn-delete');
                deleteButtons.forEach(function(button) {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: 'Anda yakin ingin menghapus baris ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            reverseButtons: true,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const url = this.getAttribute('href');
                                $.ajax({
                                    url: url,
                                    type: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    },
                                    success: function(response) {
                                        Swal.fire('Sukses',
                                                'Data berhasil dihapus.', 'success')
                                            .then((result) => {
                                                window.location.href =
                                                    '{{ url('/masukharilibur/master_approval') }}';
                                            });
                                    },
                                    error: function(xhr, status, error) {
                                        Swal.fire('Sukses',
                                                'Data berhasil dihapus.', 'success')
                                            .then((result) => {
                                                window.location.href =
                                                    '{{ url('/masukharilibur/master_approval') }}';
                                            });
                                    },
                                });
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
