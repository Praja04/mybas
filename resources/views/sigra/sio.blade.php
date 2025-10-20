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
                            <h3 class="card-label">SIGRA SIO (Surat Izin Operator)
                                <span class="d-block text-muted pt-2 font-size-sm">Manage data Surat Izin Operator
                                    (SIO)</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder"
                                onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Tambah Perizinan</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="sio" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="2%">#</th>
                                    <th>PT</th>
                                    <th>NAMA PERIZINAN</th>
                                    <th>NAMA KARYAWAN</th>
                                    <th>NIK KARYAWAN</th>
                                    <th>NOMOR PERIZINAN</th>
                                    <th>STATUS</th>
                                    <th>TANGGAL TERBIT</th>
                                    <th>TANGGAL EXPIRED</th>
                                    <th>HARGA</th>
                                    <th>REMARKS</th>
                                    <th width="5%"><i class="fa fa-tools text-dark-75"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    <div class="modal fade" id="sertifikasi-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span id="sio-title"></span> <input id="perizinan-status" data-size="small" data-switch="true"
                            type="checkbox" checked="checked" data-on-text="Active...." data-handle-width="50"
                            data-off-text="Inactive" data-on-color="success" />
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="openCreateSertifikat()"><i
                            class="fa fa-plus-circle"></i> Tambah SIO</a>
                    <hr>
                    <div id="container-create-sertifikat" class="hide">
                        <div class="card card-custom border border-black" data-card="true">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label"><span class="sertification-form-title"></span></h3>
                                </div>
                                <div class="card-toolbar">
                                    <a href="javascript:" onClick="closeCreateSertifikat()"
                                        class="btn btn-icon btn-sm btn-hover-light-primary">
                                        <i class="ki ki-close icon-nm"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('/') }}" id="form-create-sertifikat">
                                    <input type="hidden" id="id" name="id">
                                    <input type="hidden" id="transaction-type">
                                    <input type="hidden" readonly id="sio-id" name="sio_id">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label text-right" for="tanggal-sertifikasi">Tanggal
                                            Terbit <span class="text-danger">*</span></label>
                                        <div class="col-4">
                                            <input name="tanggal_sertifikasi" required placeholder="Tanggal Sertifikasi"
                                                class="form-control" type="date" value="" id="tanggal-sertifikasi">
                                        </div>
                                        <label class="col-2 col-form-label text-right" for="tanggal-expired">Tanggal
                                            Expired</label>
                                        <div class="col-4">
                                            <input name="tanggal_expired" placeholder="Tanggal Expired" class="form-control"
                                                type="date" value="" id="tanggal-expired">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label text-right" for="no-dokumen">Nomor Izin <span
                                                class="text-danger">*</span></label>
                                        <div class="col-4">
                                            <input required name="nomor_izin" placeholder="Nomor Izin"
                                                class="form-control" type="text" value="" id="nomor-izin">
                                        </div>
                                        <label class="col-2 col-form-label text-right" for="harga-izin">Harga <span
                                                class="text-danger">*</span></label>
                                        <div class="col-4">
                                            <input required name="harga" placeholder="Harga" class="form-control"
                                                type="number" value="" id="harga-perizinan">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label text-right" for="remarks">Keterangan</label>
                                        <div class="col-4">
                                            <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control"></textarea>
                                        </div>
                                        <label class="col-2 col-form-label text-right"
                                            for="attachments">Attachments</label>
                                        <div class="col-4">
                                            <div class="dropzone dropzone-multi" id="dropzone">
                                                <div class="dropzone-panel mb-lg-0 mb-2">
                                                    <a
                                                        class="dropzone-select btn btn-light-secondary font-weight-bold text-dark btn-sm">Attach
                                                        files</a>
                                                </div>
                                                <div class="dropzone-items">
                                                    <div class="dropzone-item" style="display:none">
                                                        <div class="dropzone-file">
                                                            <div class="dropzone-filename"
                                                                title="some_image_file_name.jpg">
                                                                <span data-dz-name="">some_image_file_name.jpg</span>
                                                                <strong>(
                                                                    <span data-dz-size="">340kb</span>)</strong>
                                                            </div>
                                                            <div class="dropzone-error" data-dz-errormessage=""></div>
                                                        </div>
                                                        <div class="dropzone-progress">
                                                            <div class="progress">
                                                                <div class="progress-bar bg-primary" role="progressbar"
                                                                    aria-valuemin="0" aria-valuemax="100"
                                                                    aria-valuenow="0" data-dz-uploadprogress=""></div>
                                                            </div>
                                                        </div>
                                                        <div class="dropzone-toolbar">
                                                            <span class="dropzone-delete" data-dz-remove="">
                                                                <i class="flaticon2-cross"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" readonly name="create_sertifikat_transaction_id"
                                        id="create_sertifikat_transaction_id">
                                    <div class="form-group row">
                                        <div class="col-2"></div>
                                        <div class="col-4">
                                            <button class="submit-button btn btn-primary"><i
                                                    class="fa fa-paper-plane"></i> Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <hr>
                    </div>

                    <div id="sertifikasi">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NOMOR IZIN</th>
                                    <th>HARGA</th>
                                    <th>TANGGAL TERBIT</th>
                                    <th>TANGGAL EXPIRED</th>
                                    <th>REMARKS</th>
                                    <th><i class="fa fa-tools text-dark-75"></i></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="document-attachment-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalSizeSm" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-dark border border-1">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="exampleModalLabel"><i class="fa fa-folder-open"></i>
                        <span>File file attachment</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close text-white"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-hover table-dark" id="table-attachments">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th>Nama File</th>
                                <th width="5%">
                                    {{-- <i class="la la-download"></i> --}}
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="create-new-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i> Tambah Perizinan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="create-perizinan-form">
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="perusahaan">Perusahaan <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <select required name="perusahaan" id="perusahaan" class="form-control">
                                    <option value="" selected disabled>Pilih Perusahaan</option>
                                    @foreach ($perusahaan as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="nama-perizinan">Nama Perizinan <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <input name="nama_perizinan" required placeholder="Nama Perizinan" class="form-control"
                                    type="text" value="" id="nama-perizinan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="nama-karyawan">Nama Karyawan <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <input name="nama_karyawan" required placeholder="Nama Karyawan" class="form-control"
                                    type="text" value="" id="nama-karyawan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="nama-perizinan">NIK Karyawan <span
                                    class="text-danger">*</span></label>
                            <div class="col-9">
                                <input name="nik_karyawan" required placeholder="NIK Karyawan" class="form-control"
                                    type="text" value="" id="nik-karyawan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="submitButton" type="submit" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i> Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="editModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><i class="fa fa-tools text-dark-75"></i> Edit Surat Izin
                        Operator
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-perizinan-form">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="edit-perusahaan">Perusahaan</label>
                            <div class="col-9">
                                <select required name="perusahaan" id="edit-perusahaan" class="form-control">
                                    <option value=""></option>
                                    @foreach ($perusahaan as $p)
                                        <option value="{{ $p->id }}">{{ $p->nama_perusahaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="nama-perizinan">Nama Perizinan</label>
                            <div class="col-9">
                                <input name="nama_perizinan" required placeholder="Nama Perizinan" class="form-control"
                                    type="text" value="" id="edit-nama-perizinan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="nama-karyawan">Nama Karyawan</label>
                            <div class="col-9">
                                <input name="nama_karyawan" required placeholder="Nama Karyawan" class="form-control"
                                    type="text" value="" id="edit-nama-karyawan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="nik-karyawan">NIK Karyawan</label>
                            <div class="col-9">
                                <input name="nik_karyawan" required placeholder="NIK Karyawan" class="form-control"
                                    type="text" value="" id="edit-nik-karyawan">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="editSubmitButton" type="submit" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i> Edit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    <script type="text/javascript">
        var sio_table;

        function openCreateSertifikat() {
            $('.sertification-form-title').text('Tambah Surat Izin Operator (SIO)');
            $('#transaction-type').val('create');

            // Generate new transaction_id
            $.ajax({
                url: "{{ route('attachment.generate-transaction-id') }}",
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    $('#create_sertifikat_transaction_id').val(response.data);
                },
                error: function(error) {
                    console.log(error);
                }
            })
            closeCreateSertifikat();
            $('#container-create-sertifikat').slideDown();
        }

        function editSertifikasi(id) {
            $('.sertification-form-title').text('Edit Surat Izin Operator');
            $('#transaction-type').val('edit');
            closeCreateSertifikat();
            // Get current data to ajax
            $.ajax({
                // ambil data sertifikat
                url: "{{ url('/sigra/sio/sertifikasi/get') }}/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    let data = response.data;
                    console.log(response)
                    $('#id').val(data.id);
                    $('#nomor-izin').val(data.nomor_izin);
                    $('#harga-perizinan').val(data.harga);
                    $('#tanggal-sertifikasi').val(data.tanggal_terbit);
                    $('#tanggal-expired').val(data.tanggal_habis);
                    $('#remarks').val(data.keterangan);
                    $('#create_sertifikat_transaction_id').val(data.transaction_id);
                },
                error: function(error) {
                    console.log(error);
                }
            })
            $('#container-create-sertifikat').slideDown();
        }

        function deleteSertifikasi(id) {
            closeCreateSertifikat();
            // Get current data to ajax
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
                        url: "{{ url('/sigra/sio/sertifikasi/delete') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
                            Swal.fire(
                                'Yeay!',
                                'Data berhasil dihapus.',
                                'success'
                            );
                            getSertifikasi($("#sio-id").val());
                            sio_table.ajax.reload();
                        }
                    })
                }
            });
        }

        // update close create sertifikat
        function closeCreateSertifikat() {
            $('#id').val('');
            $('#nomor-izin').val('');
            $('#tanggal-sertifikasi').val('');
            $('#tanggal-expired').val('');
            $('#instansi').val('');
            $('#harga-perizinan').val('');
            $('#remarks').val('');

            myDropzone5.removeAllFiles();

            $('#container-create-sertifikat').slideUp();
        }

        var id = '#dropzone';

        // set the preview element template
        var previewNode = $(id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();

        var myDropzone5 = new Dropzone(id, {
            url: "{{ url('/attachment/upload') }}",
            parallelUploads: 20,
            maxFilesize: 50,
            timeout: 180000,
            previewTemplate: previewTemplate,
            previewsContainer: id + " .dropzone-items",
            clickable: id + " .dropzone-select",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        myDropzone5.on("sending", function(file, xhr, formData) {
            formData.append('transaction_id', $('#create_sertifikat_transaction_id').val());
            formData.append('transaction_type', 'perizinan_sio');
        });

        myDropzone5.on("addedfile", function(file) {
            // Hookup the start button
            $(document).find(id + ' .dropzone-item').css('display', '');
        });

        // Update the total progress bar
        myDropzone5.on("totaluploadprogress", function(progress) {
            $(id + " .progress-bar").css('width', progress + "%");
        });

        myDropzone5.on("sending", function(file) {
            // Show the total progress bar when upload starts
            $(id + " .progress-bar").css('opacity', "1");
        });

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone5.on("complete", function(progress) {
            var thisProgressBar = id + " .dz-complete";
            setTimeout(function() {
                $(thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress").css('opacity',
                    '0');
            }, 300)
        });

        $('#form-create-sertifikat').on('submit', function(e) {
            e.preventDefault();

            // munculkan validasi ketika submit form
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah data sertifikasi sudah lengkap?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, submit!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        });

        function submitForm() {
            $('#form-create-sertifikat .submit-button').html("<i class='fa fa-spin fa-spinner'></i> Submiting...");
            $('#form-create-sertifikat .submit-button').attr('disabled', true);

            if ($('#transaction-type').val() == 'create') {
                var url = "{{ route('sigra.sio.buat-sertifikat') }}";
                var success_message = 'Sertifikasi berhasil dibuat';
                var failed_message = 'Sertifikasi gagal dibuat, coba lagi';
            } else {
                var url = "{{ route('sigra.sio.ubah-sertifikat') }}";
                var success_message = 'Sertifikasi berhasil diubah';
                var failed_message = 'Sertifikasi gagal diubah, coba lagi';
            }

            // Do ajax submit
            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                data: $('#form-create-sertifikat').serialize(),
                success: function(response) {
                    if (response.success == '1') {
                        Swal.fire('Berhasil!', success_message, 'success')
                            .then(function() {
                                $('#form-create-sertifikat .submit-button').html(
                                    "<i class='fa fa-paper-plane'></i> Submit");
                                $('#form-create-sertifikat .submit-button').removeAttr('disabled');
                                getSertifikasi($("#sio-id").val());
                                closeCreateSertifikat();
                                sio_table.ajax.reload();
                            });
                    } else {
                        Swal.fire('Gagal!', failed_message, 'error')
                            .then(function() {
                                $('#form-create-sertifikat .submit-button').html(
                                    "<i class='fa fa-paper-plane'></i> Submit");
                                $('#form-create-sertifikat .submit-button').removeAttr('disabled');
                            });
                    }
                },
                error: function(error) {
                    Swal.fire('Gagal!', failed_message, 'error')
                        .then(function() {
                            $('#form-create-sertifikat .submit-button').html(
                                "<i class='fa fa-paper-plane'></i> Submit");
                            $('#form-create-sertifikat .submit-button').removeAttr('disabled');
                        });
                    console.log(error);
                }
            });
        }


        $('#create-perizinan-form').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton i').removeClass('fa-paper-plane');
            $('#submitButton i').addClass('fa-spinner');
            $('#submitButton i').addClass('fa-spin');

            // console log create-perizinan-form
            // var datas = $(this).serialize();
            // console.log(datas);

            $.ajax({
                url: '{{ url('sigra/sio/tambah-perizinan') }}',
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success == '1') {
                        Swal.fire('Berhasil!', 'Perizinan berhasil dibuat', 'success')
                            .then(function() {
                                $('#submitButton i').addClass('fa-paper-plane');
                                $('#submitButton i').removeClass('fa-spinner');
                                $('#submitButton i').removeClass('fa-spin');
                                location.reload();
                            });
                    } else {
                        Swal.fire('Gagal!', 'Perizinan gagal dibuat, coba lagi', 'error')
                            .then(function() {
                                $('#submitButton i').addClass('fa-paper-plane');
                                $('#submitButton i').removeClass('fa-spinner');
                                $('#submitButton i').removeClass('fa-spin');
                            });
                    }
                },
                error: function(e) {
                    Swal.fire('Gagal!', 'Sertifikasi gagal dibuat, coba lagi', 'error')
                        .then(function() {
                            $('#submitButton i').addClass('fa-paper-plane');
                            $('#submitButton i').removeClass('fa-spinner');
                            $('#submitButton i').removeClass('fa-spin');
                        });
                }
            })
        });

        $('#edit-perizinan-form').on('submit', function(e) {
            e.preventDefault();
            $('#editSubmitButton i').removeClass('fa-paper-plane');
            $('#editSubmitButton i').addClass('fa-spinner');
            $('#editSubmitButton i').addClass('fa-spin');
            $.ajax({
                url: "{{ url('sigra/sio/update') }}",
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success == '1') {
                        Swal.fire('Berhasil!', 'Perizinan berhasil diubah', 'success')
                            .then(function() {
                                $('#editSubmitButton i').addClass('fa-paper-plane');
                                $('#editSubmitButton i').removeClass('fa-spinner');
                                $('#editSubmitButton i').removeClass('fa-spin');
                                location.reload();
                            });
                    } else {
                        Swal.fire('Gagal!', 'Perizinan gagal diubah, coba lagi', 'error')
                            .then(function() {
                                $('#editSubmitButton i').addClass('fa-paper-plane');
                                $('#editSubmitButton i').removeClass('fa-spinner');
                                $('#editSubmitButton i').removeClass('fa-spin');
                            });
                    }
                },
                error: function(e) {
                    Swal.fire('Gagal!', 'Sertifikasi gagal diubah, coba lagi', 'error')
                        .then(function() {
                            $('#editSubmitButton i').addClass('fa-paper-plane');
                            $('#editSubmitButton i').removeClass('fa-spinner');
                            $('#editSubmitButton i').removeClass('fa-spin');
                        });
                    // console.log('test')
                }
            })
        });

        function showDocuments(id) {
            var table = $('#table-attachments tbody');
            // Clear the table content
            table.html('');
            // Do get attachment by the id
            $.ajax({
                url: "{{ url('sigra/sio/get-attachments') }}/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    if (response.data.length > 0) {
                        $.each(response.data, function(key, val) {
                            var row = '' +
                                '<tr>' +
                                '<td>' + (key + 1) + '</td>' +
                                '<td>' + val.original_file_name + '</td>' +
                                '<td>' +
                                '<a title="Download" onClick="downloadAttachment(\'' + val.id +
                                '\')" href="javascript:" class="text-hover-dark">' +
                                '<i class="fa fa-cloud-download-alt text-hover-dark text-success"></i>' +
                                '</a>' +
                                '</td>' +
                                '</tr>';
                            table.append(row);
                        });
                    } else {
                        var row = '' +
                            '<tr>' +
                            '<td colspan="3" class="text-center"><span class="label label-inline">Tidak ada attachment</span></td>' +
                            '</tr>';
                        table.append(row);
                    }
                },
                errir: function(error) {
                    console.log(error);
                }
            });
            $('#document-attachment-modal').modal('show');
        }

        function showSertifikasi(id, title, status) {
            $('#sio-title').text(title);
            $("#sio-id").val(id);

            if (status == 'inactive') {
                $('#perizinan-status').bootstrapSwitch('state', false, true);
            } else {
                $('#perizinan-status').bootstrapSwitch('state', true, true);
            }

            getSertifikasi(id);

            closeCreateSertifikat();

            $('#sertifikasi-modal').modal('show');
        }

        function getSertifikasi(id) {
            var table = $('#sertifikasi table tbody');
            table.html('');

            $.ajax({
                url: '{{ url('sigra/sio/get-sertifikat') }}/' + id,
                dataType: 'JSON',
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    if (response.data.length == 0) {
                        var row = '' +
                            '<tr>' +
                            '<td colspan="9" class="text-center"><span class="label label-secondary label-inline">Belum ada sertifikasi</span></td>' +
                            '</tr>';
                        table.append(row);
                    }
                    $.each(response.data, function(key, val) {
                        var count = val.attachments.length;
                        var tanggal_habis = val.tanggal_habis == null ? '-' : formatTanggalIndonesia(val
                            .tanggal_habis);
                        var row = '' +
                            '<tr>' +
                            '<td>' + (key + 1) + '</td>' +
                            '<td>' + val.nomor_izin + '</td>' +
                            '<td>' + val.harga + '</td>' +
                            '<td>' + formatTanggalIndonesia(val.tanggal_terbit) + '</td>' +
                            '<td>' + tanggal_habis + '</td>' +
                            '<td>' + val.keterangan + '</td>' +
                            // '<td>' + val.masa_berlaku + '</td>' +
                            // '<td>' + val.keterangan + '</td>' +
                            '<td>' +
                            '<a title="Tampilkan attachment" onClick="showDocuments(' + val.id +
                            ')" href="javascript:" class="mr-10 position-relative text-hover-dark mr-1">' +
                            '<span style="z-index: 99; position: absolute; right: -30px; top: -9px" class="label label-rounded label-light-dark label-sm mr-2">' +
                            count + '</span>' +
                            '<span style="z-index: 100; position: absolute"><i class="fa fa-folder-open"></i></span>' +
                            '</a>' +
                            '<a title="Edit" onClick="editSertifikasi(' + val.id +
                            ')" href="javascript:" class="text-hover-dark mr-1">' +
                            '<i class="fa fa-edit"></i>' +
                            '</a>' +
                            '<a title="Hapus" onClick="deleteSertifikasi(' + val.id +
                            ')" href="javascript:" class="text-hover-dark">' +
                            '<i class="fa fa-trash"></i>' +
                            '</a>' +
                            '</td>' +
                            '</tr>';
                        table.append(row);
                    });
                },
                error: function(e) {
                    console.log('error');
                    console.log(e);
                }
            });
        }

        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        function getPerizinansio() {
            sio_table = $('#sio').DataTable({
                "ajax": "{{ route('sigra.sio.get-all') }}",
                "paging": false,
                "responsive": true,
                "dom": '<"toolbar">frtip',
                "order": [
                    // ganti 5 ke 0
                    [0, "asc"]
                ],
                "columnDefs": [{
                    "targets": 5,
                    "type": "date-eu"
                }]
            });
            // perbaiki export sio dan implementasi sweet alert kedalam nya
            $('#sio_filter').append(
                "<a href='" + "{{ route('sigra.export.sio') }}" +
                "' class='btn btn-outline-success float-left btn-sm' onclick='exportExcel()'>" +
                "<i class='la la-file-excel-o'></i> Export excel <i class='la la-download'></i>" +
                "</a>"
            )
        }

        getPerizinansio();

        function showModalCreateNew() {
            $('#modal-title').text('Tambah SIO');
            $('#create-new-modal').modal('show');
        }

        function edit(id) {
            $.ajax({
                url: "{{ url('/sigra/sio/get') }}/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    // console.log("Fetched data:", response.data);
                    $('#edit-id').val(response.data.id);
                    $('#edit-perusahaan').val(response.data.id_perusahaan);
                    $('#edit-nama-perizinan').val(response.data.nama_perizinan);
                    $('#edit-nama-karyawan').val(response.data.nama_karyawan);
                    $('#edit-nik-karyawan').val(response.data.nik_karyawan);
                    console.log('yrdy', response.data);
                },
                error: function(error) {
                    Swal.fire('Hmmm!', 'Ada masalah, coba lagi', 'error');
                    console.log(error);
                }
            });
            $('#edit-modal').modal('show');
        }

        function deleteItem(id) {
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
                        url: "{{ url('/sigra/sio/delete') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
                            Swal.fire(
                                'Yeay!',
                                'Data berhasil dihapus.',
                                'success'
                            );
                            sio_table.ajax.reload();
                        }
                    })
                }
            });
        }

        function exportExcel() {
            Swal.fire(
                'Yeay',
                'Berhasil mendownload file sio.',
                'success',
            );
        }

        function downloadAttachment(id) {
            window.location.href = "{{ url('/attachment/download') }}/" + id
        }

        $('[data-switch=true]').bootstrapSwitch();

        $('#perizinan-status').on('switchChange.bootstrapSwitch', function(e, data) {
            let status;
            let id = $('#sio-id').val();
            if ($('#perizinan-status').bootstrapSwitch('state') == true) {
                status = 'active';
            } else {
                status = 'inactive';
            }
            Swal.fire({
                title: 'Yakin mau dirubah ke ' + status + '?',
                text: "Kamu bisa merubah nya kembali nanti!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1BC5BD',
                cancelButtonText: 'Gak jadi',
                confirmButtonText: 'Ya!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ubah status di server
                    $.ajax({
                        url: "{{ url('/sigra/sio/set-status') }}",
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            id: id,
                            status: status
                        },
                        success: function(response) {
                            if (response.success == 1) {
                                sio_table.ajax.reload();
                            } else {
                                Swal.fire(
                                    'Hmmm!',
                                    'Gagal mengubah statue, coba lagi.',
                                    'error'
                                );
                                $('#perizinan-status').bootstrapSwitch('state', !data, true);
                            }

                        },
                        error: function(error) {
                            Swal.fire(
                                'Hmmm!',
                                'Gagal mengubah statue, coba lagi.',
                                'error'
                            );
                            $('#perizinan-status').bootstrapSwitch('state', !data, true);
                        }
                    });
                } else {
                    $('#perizinan-status').bootstrapSwitch('state', !data, true);
                }
            });
        });
    </script>
@endpush
