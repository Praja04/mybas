@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">SIGRA MASTER VENDOR
                                <span class="d-block text-muted pt-2 font-size-sm">Manage Data Vendor</span></h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Create New</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Perusahaan</th>
                                <th>Nama Vendor</th>
                                <th>Jenis Pekerjaan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($vendors as $key => $vendor)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <th>{{ $vendor->perusahaan->nama_perusahaan }}</th>
                                    <td>{{ $vendor->nama_vendor }}</td>
                                    <td>{{ $vendor->jenis_pekerjaan }}</td>
                                    <td>{{ $vendor->status }}</td>
                                    <td>
                                        <a onClick="edit('{{ $vendor->id }}')" title="Edit" href="javascript:" class="fa fa-edit text-hover-dark mr-2"></a>
                                        <a onClick="deleteItem('{{ $vendor->id }}')" title="Hapus" href="javascript:" class="fa fa-trash text-hover-dark"></a>'
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="create-form">
                        @csrf
                        <div class="form-group">
                            <label for="perusahaan">Perusahaan</label>
                            <div></div>
                            <select required class="form-control" name="perusahaan" id="perusahaan">
                                <option value=""></option>
                                @foreach ($perusahaan as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama-vendor">Nama Vendor</label>
                            <div></div>
                            <input required id="nama-vendor" name="nama_vendor" type="text" class="form-control" placeholder="Nama Vendor">
                        </div>
                        <div class="form-group">
                            <label for="jenis-pekerjaan">Jenis Pekerjaan</label>
                            <div></div>
                            <select required class="form-control" name="jenis_pekerjaan" id="jenis-pekerjaan">
                                <option value=""></option>
                                <option value="Cleaning Service">Cleaning Service</option>
                                <option value="Security">Security</option>
                                <option value="Bongkar Muat">Bongkar Muat</option>
                                <option value="Angkut Sampah">Angkut Sampah</option>
                                <option value="Klinik Internal">Klinik Internal</option>
                                <option value="Konsumsi Karyawan">Konsumsi Karyawan</option>
                                <option value="Lingkungan Perusahaan">Lingkungan Perusahaan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <div></div>
                            <select required class="form-control" name="status" id="status">
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                            </select>
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="edit-form">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="perusahaan">Perusahaan</label>
                            <div></div>
                            <select required class="form-control" name="perusahaan" id="edit-perusahaan">
                                <option value=""></option>
                                @foreach ($perusahaan as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nama-vendor">Nama Vendor</label>
                            <div></div>
                            <input required id="edit-nama-vendor" name="nama_vendor" type="text" class="form-control" placeholder="Nama Vendor">
                        </div>
                        <div class="form-group">
                            <label for="jenis-pekerjaan">Jenis Pekerjaan</label>
                            <div></div>
                            <select required class="form-control" name="jenis_pekerjaan" id="edit-jenis-pekerjaan">
                                <option value=""></option>
                                <option value="Cleaning Service">Cleaning Service</option>
                                <option value="Security">Security</option>
                                <option value="Bongkar Muat">Bongkar Muat</option>
                                <option value="Angkut Sampah">Angkut Sampah</option>
                                <option value="Klinik Internal">Klinik Internal</option>
                                <option value="Konsumsi Karyawan">Konsumsi Karyawan</option>
                                <option value="Lingkungan Perusahaan">Lingkungan Perusahaan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <div></div>
                            <select required class="form-control" name="status" id="edit-status">
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                            </select>
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-success"><i class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">

        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('.table').DataTable();

        function showModalCreateNew()
        {
            $('#modal-title').text('Create New Data');
            $('#modal').modal('show');
        }

        $('#submitButton').on('click', function () {
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Submiting...');
            setTimeout(function () {
                $('#submitButton').html('<i class="fa fa-paper-plane"></i> Submit');
            }, 1000)
            // $(this).attr('disabled', true);
        })

        $('#create-form').on('submit', function (e) {
            e.preventDefault();
            $('#submitButton i').removeClass('fa-paper-plane');
            $('#submitButton i').addClass('fa-spinner');
            $('#submitButton i').addClass('fa-spin');
            $.ajax({
                url: "{{ url('sigra/master-vendor/store') }}",
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function ( response ) {
                    if(response.success == '1') {
                        Swal.fire('Berhasil!', 'Master Vendor berhasil dibuat', 'success')
                        .then(function () {
                            $('#submitButton i').addClass('fa-paper-plane');
                            $('#submitButton i').removeClass('fa-spinner');
                            $('#submitButton i').removeClass('fa-spin');
                            location.reload();
                        });
                    }else{
                        Swal.fire('Gagal!', 'Master Vendor gagal dibuat, coba lagi', 'error')
                        .then(function () {
                            $('#submitButton i').addClass('fa-paper-plane');
                            $('#submitButton i').removeClass('fa-spinner');
                            $('#submitButton i').removeClass('fa-spin');
                        });
                    }
                },
                error: function ( e ) {
                    Swal.fire('Gagal!', 'Master Vendor gagal dibuat, coba lagi', 'error')
                    .then(function () {
                        $('#submitButton i').addClass('fa-paper-plane');
                        $('#submitButton i').removeClass('fa-spinner');
                        $('#submitButton i').removeClass('fa-spin');
                    });
                }
            })
        });

        function edit(id)
        {
            $.ajax({
                url: "{{ url('/sigra/master-vendor/get') }}/"+id,
                type: "GET",
                dataType: "JSON",
                success: function ( response ) {
                    $('#id').val(response.data.id);
                    $('#edit-perusahaan').val(response.data.id_perusahaan);
                    $('#edit-nama-vendor').val(response.data.nama_vendor);
                    $('#edit-jenis-pekerjaan').val(response.data.jenis_pekerjaan);
                    $('#edit-status').val(response.data.status);
                },
                error: function ( error ) {
                    Swal.fire('Hmmm!', 'Ada masalah, coba lagi', 'error');
                    console.log( error );
                }
            });
            $('#edit-modal').modal('show');
        }

        $('#edit-form').on('submit', function (e) {
            e.preventDefault();
            $('#submitButton i').removeClass('fa-paper-plane');
            $('#submitButton i').addClass('fa-spinner');
            $('#submitButton i').addClass('fa-spin');
            $.ajax({
                url: "{{ url('sigra/master-vendor/update') }}",
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function ( response ) {
                    if(response.success == '1') {
                        Swal.fire('Berhasil!', 'Master Vendor berhasil diubah', 'success')
                        .then(function () {
                            $('#submitButton i').addClass('fa-paper-plane');
                            $('#submitButton i').removeClass('fa-spinner');
                            $('#submitButton i').removeClass('fa-spin');
                            location.reload();
                        });
                    }else{
                        Swal.fire('Gagal!', 'Master Vendor gagal diubah, coba lagi', 'error')
                        .then(function () {
                            $('#submitButton i').addClass('fa-paper-plane');
                            $('#submitButton i').removeClass('fa-spinner');
                            $('#submitButton i').removeClass('fa-spin');
                        });
                    }
                },
                error: function ( e ) {
                    Swal.fire('Gagal!', 'Master Vendor gagal diubah, coba lagi', 'error')
                    .then(function () {
                        $('#submitButton i').addClass('fa-paper-plane');
                        $('#submitButton i').removeClass('fa-spinner');
                        $('#submitButton i').removeClass('fa-spin');
                    });
                }
            })
        });

        function deleteItem(id)
        {
            Swal.fire({
                title: 'Yakin mau dihapus?',
                text: "Data mungkin tidak bisa dikembalikan lagi setelah dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Gak jadi',
                confirmButtonText: 'Hapus aja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus data dari server
                    $.ajax({
                        url: "{{ url('/sigra/master-vendor/delete') }}/"+id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function ( response ) {
                            Swal.fire(
                                'Yeay!',
                                'Data berhasil dihapus.',
                                'success'
                            ).then(function() {
                                location.reload();
                            });
                            
                        }
                    })
                }
            });
        }

    </script>

@endpush
