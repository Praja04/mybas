@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">

    <style>
        .import-excel {
            background-color: #00BFA6;
            padding: 14px 40px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            border-radius: 10px;
            border: 2px dashed #00BFA6;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
            transition: .4s;
        }

        .import-excel span:last-child {
            display: none;
        }

        .import-excel:hover {
            transition: .4s;
            border: 2px dashed #00BFA6;
            background-color: #fff;
            color: #00BFA6;
        }

        .import-excel:active {
            background-color: #87dbd0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">SIGRA KONTRAK VENDOR
                                <span class="d-block text-muted pt-2 font-size-sm">Manage Kontrak Vendor</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder"
                                onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Create New Vendor</a>
                        </div>
                    </div>
                    <div class="p-4 export-button">
                        <input type="hidden" id="current-id-pembagian">
                        <div class="card card-custom card-fit card-border mb-5">
                            {{-- masih progres upload file excel kontrak vendor --}}
                            {{-- <div class="card-body">
                                <form action="{{ url('/sigra/kontrak-vendor/import-excel') }}" method="POST"
                                    enctype="multipart/form-data" id="form-import-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="file" class="form-control btn-import" name="file">
                                        </div>
                                        <div class="col-md-2">
                                            <button id="upload-button" type="submit" class="import-excel">Upload</button>
                                        </div>
                                    </div>
                                </form>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="kontrak-vendor" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="2%">#</th>
                                    <th>PT</th>
                                    <th>NAMA VENDOR</th>
                                    <th>JENIS PEKERJAAN</th>
                                    <th>TANGGAL MULAI</th>
                                    <th>TANGGAL SELESAI</th>
                                    <th>VALUE</th>
                                    <th>KETERANGAN</th>
                                    <th>STATUS</th>
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

    <div class="modal fade" id="kontrak-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span id="kontrak-vendor-title"></span> <input id="perizinan-status" data-size="small"
                            data-switch="true" type="checkbox" checked="checked" data-on-text="Active...."
                            data-handle-width="50" data-off-text="Inactive" data-on-color="success" />
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="openCreateKontrak()"><i
                            class="fa fa-plus-circle"></i> Buat Kontrak Baru</a>
                    <hr>
                    <div id="container-create-kontrak" class="hide">
                        <div class="card card-custom border border-black" data-card="true">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label"><span class="sertification-form-title"></span></h3>
                                </div>
                                <div class="card-toolbar">
                                    <a href="javascript:" onClick="closeCreateKontrak()"
                                        class="btn btn-icon btn-sm btn-hover-light-primary">
                                        <i class="ki ki-close icon-nm"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ url('/') }}" id="form-create-kontrak">
                                    <input type="hidden" id="id" name="id">
                                    <input type="hidden" id="transaction-type">
                                    <input type="hidden" readonly id="kontrak-vendor-id" name="vendor_id">
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label text-right" for="tanggal-mulai">Tanggal
                                            Mulai</label>
                                        <div class="col-4">
                                            <input name="tanggal_mulai" required placeholder="Tanggal kontrak"
                                                class="form-control" type="date" value="" id="tanggal-mulai">
                                        </div>
                                        <label class="col-2 col-form-label text-right" for="tanggal-selesai">Tanggal
                                            Selesai</label>
                                        <div class="col-4">
                                            <input required name="tanggal_selesai" placeholder="Tanggal Expired"
                                                class="form-control" type="date" value="" id="tanggal-selesai">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label text-right" for="value">Value</label>
                                        <div class="col-4">
                                            <input required name="value" placeholder="Value" class="form-control"
                                                type="text" value="" id="value">
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
                                    <div class="form-group row">
                                        <label class="col-2 col-form-label text-right" for="remarks">Keterangan</label>
                                        <div class="col-4">
                                            <textarea name="remarks" id="remarks" cols="30" rows="3" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" readonly name="create_kontrak_transaction_id"
                                        id="create_kontrak_transaction_id">
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

                    <div id="kontrak">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>TANGGAL MULAI</th>
                                    <th>TANGGAL SELESAI</th>
                                    <th>VALUE</th>
                                    <th>KETERANGAN</th>
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

    <div class="modal fade" id="create-new-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
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
                            <input required id="nama-vendor" name="nama_vendor" type="text" class="form-control"
                                placeholder="Nama Vendor">
                        </div>
                        <div class="form-group">
                            <label for="jenis-pekerjaan">Jenis Pekerjaan</label>
                            <div></div>
                            <select required class="form-control" name="jenis_pekerjaan" id="jenis-pekerjaan">
                                <option value=""></option>
                                <option value="Lainnya">Lainnya</option>
                                <option value="Cleaning Service">Cleaning Service</option>
                                <option value="Security">Security</option>
                                <option value="Bongkar Muat">Bongkar Muat</option>
                                <option value="Angkut Sampah">Angkut Sampah</option>
                                <option value="Klinik Internal">Klinik Internal</option>
                                <option value="Konsumsi Karyawan">Konsumsi Karyawan</option>
                                <option value="Lingkungan Perusahaan">Lingkungan Perusahaan</option>
                            </select>
                        </div>
                        <div class="form-group" id="lainnyaModal" style="display: none;">
                            <input type="text" id="jenisPerusahaanInput" class="form-control"
                                placeholder="Masukkan jenis perusahaan baru">
                            <button type="button" id="tambahOpsiBtn" class="btn btn-primary mt-2">Tambah Opsi +</button>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <div></div>
                            <select required class="form-control" name="status" id="status">
                                <option value="active">active</option>
                                <option value="inactive">inactive</option>
                            </select>
                        </div>
                        <button id="submitButton" type="submit" class="btn btn-success"><i
                                class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
        aria-hidden="true">
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
                        <input type="hidden" name="id" id="id-vendor">
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
                            <input required id="edit-nama-vendor" name="nama_vendor" type="text" class="form-control"
                                placeholder="Nama Vendor">
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
                        <button id="editSubmitButton" type="submit" class="btn btn-success"><i
                                class="fa fa-paper-plane"></i>Submit</button>
                        <br><br>
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
        var kontrak_vendor_table;

        function openCreateKontrak() {
            $('.sertification-form-title').text('Create new contract');
            $('#transaction-type').val('create');

            // Generate new transaction_id
            $.ajax({
                url: "{{ route('attachment.generate-transaction-id') }}",
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    $('#create_kontrak_transaction_id').val(response.data);
                },
                error: function(error) {
                    console.log(error);
                }
            })
            closeCreateKontrak();
            $('#container-create-kontrak').slideDown();
        }

        // onchange input
        $(document).ready(function() {
            $("#jenis-pekerjaan").change(function() {
                if ($(this).val() === "Lainnya") {
                    $("#lainnyaModal").show();
                } else {
                    $("#lainnyaModal").hide();
                }
            });

            $("#tambahOpsiBtn").click(function() {
                const jenisPerusahaanBaru = $("#jenisPerusahaanInput").val();
                if (jenisPerusahaanBaru.trim() !== "") {
                    $("#jenis-pekerjaan").append(
                        `<option value="${jenisPerusahaanBaru}">${jenisPerusahaanBaru}</option>`);
                    $("#jenisPerusahaanInput").val("");
                    $("#lainnyaModal").hide();

                    // Tampilkan SweetAlert sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses!',
                        text: 'Jenis pekerjaan baru telah ditambahkan.',
                    });
                }
            });
        });

        function editkontrak(id) {
            $('.sertification-form-title').text('Edit kontrak');
            $('#transaction-type').val('edit');
            closeCreateKontrak();
            // Get current data to ajax
            $.ajax({
                url: "{{ url('/sigra/kontrak-vendor/kontrak/get') }}/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    let data = response.data;
                    $('#id').val(data.id);
                    $('#tanggal-mulai').val(data.tanggal_mulai);
                    $('#tanggal-selesai').val(data.tanggal_selesai);
                    $('#value').val(data.value);
                    $('#remarks').val(data.keterangan);
                    $('#create_kontrak_transaction_id').val(data.transaction_id);
                },
                error: function(error) {
                    console.log(error);
                }
            })
            $('#container-create-kontrak').slideDown();
        }

        function deletekontrak(id) {
            closeCreateKontrak();
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
                        url: "{{ url('/sigra/kontrak-vendor/kontrak/delete') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
                            Swal.fire(
                                'Yeay!',
                                'Data berhasil dihapus.',
                                'success'
                            );
                            getKontrak($("#kontrak-vendor-id").val());
                            kontrak_vendor_table.ajax.reload();
                        }
                    })
                }
            });
        }

        function closeCreateKontrak() {
            $('#id').val('');
            $('#tanggal-mulai').val('');
            $('#tanggal-selesai').val('');
            $('#value').val('');
            $('#instansi').val('');
            $('#masa-berlaku').val('');
            $('#remarks').val('');
            myDropzone5.removeAllFiles();

            $('#container-create-kontrak').slideUp();
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
            // update maxFilesize
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
            formData.append('transaction_id', $('#create_kontrak_transaction_id').val());
            formData.append('transaction_type', 'kontrak_vendor');
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

        $('#form-create-kontrak').on('submit', function(e) {
            e.preventDefault();
            $('#form-create-kontrak .submit-button').html("<i class='fa fa-spin fa-spinner'></i> Submiting...");
            $('#form-create-kontrak .submit-button').attr('disabled', true);

            if ($('#transaction-type').val() == 'create') {
                var url = "{{ route('sigra.kontrak-vendor.create') }}";
                var success_message = 'Kontrak berhasil dibuat';
                var failed_message = 'Kontrak gagal dibuat, coba lagi';
            } else {
                var url = "{{ route('sigra.kontrak-vendor.update') }}";
                var success_message = 'Kontrak berhasil diubah';
                var failed_message = 'Kontrak gagal diubah, coba lagi';
            }

            // Do ajax submit
            $.ajax({
                url: url,
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success == '1') {
                        Swal.fire('Berhasil!', success_message, 'success')
                            .then(function() {
                                $('#form-create-kontrak .submit-button').html(
                                    "<i class='fa fa-paper-plane'></i> Submit");
                                $('#form-create-kontrak .submit-button').removeAttr('disabled');
                                getKontrak($("#kontrak-vendor-id").val());
                                closeCreateKontrak();
                                kontrak_vendor_table.ajax.reload();
                            });
                    } else {
                        Swal.fire('Gagal!', failed_message, 'error')
                            .then(function() {
                                $('#form-create-kontrak .submit-button').html(
                                    "<i class='fa fa-paper-plane'></i> Submit");
                                $('#form-create-kontrak .submit-button').removeAttr('disabled');
                            });
                    }
                },
                error: function(error) {
                    Swal.fire('Gagal!', failed_message, 'error')
                        .then(function() {
                            $('#form-create-kontrak .submit-button').html(
                                "<i class='fa fa-paper-plane'></i> Submit");
                            $('#form-create-kontrak .submit-button').removeAttr('disabled');
                        });
                    $('#form-create-kontrak .submit-button').html(
                        "<i class='fa fa-paper-plane'></i> Submit");
                    $('#form-create-kontrak .submit-button').removeAttr('disabled');
                    console.log(error);
                }
            })

        })

        $('#create-form').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton i').removeClass('fa-paper-plane');
            $('#submitButton i').addClass('fa-spinner');
            $('#submitButton i').addClass('fa-spin');
            $.ajax({
                url: "{{ url('sigra/master-vendor/store') }}",
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success == '1') {
                        Swal.fire('Berhasil!', 'Master Vendor berhasil dibuat', 'success')
                            .then(function() {
                                $('#submitButton i').addClass('fa-paper-plane');
                                $('#submitButton i').removeClass('fa-spinner');
                                $('#submitButton i').removeClass('fa-spin');
                                location.reload();
                            });
                    } else {
                        Swal.fire('Gagal!', 'Master Vendor gagal dibuat, coba lagi', 'error')
                            .then(function() {
                                $('#submitButton i').addClass('fa-paper-plane');
                                $('#submitButton i').removeClass('fa-spinner');
                                $('#submitButton i').removeClass('fa-spin');
                            });
                    }
                },
                error: function(e) {
                    Swal.fire('Gagal!', 'Master Vendor gagal dibuat, coba lagi', 'error')
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
                url: "{{ url('sigra/kontrak-vendor/update') }}",
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
                    Swal.fire('Gagal!', 'kontrak gagal diubah, coba lagi', 'error')
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
                url: "{{ url('sigra/kontrak-vendor/get-attachments') }}/" + id,
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

        function showKontrak(id, title, status) {
            $('#kontrak-vendor-title').text(title);
            $("#kontrak-vendor-id").val(id);

            if (status == 'inactive') {
                sigra / kontrak - vendor /
                    $('#perizinan-status').bootstrapSwitch('state', false, true);
            } else {
                $('#perizinan-status').bootstrapSwitch('state', true, true);
            }

            getKontrak(id);

            closeCreateKontrak();

            $('#kontrak-modal').modal('show');
        }

        function formatRupiah(number) {
            var formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            return formatter.format(number);
        }

        function getKontrak(id) {
            var table = $('#kontrak table tbody');
            table.html('');

            $.ajax({
                url: "{{ url('sigra/kontrak-vendor/get-kontrak') }}/" + id,
                dataType: 'JSON',
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    if (response.data.length == 0) {
                        var row = '' +
                            '<tr>' +
                            '<td colspan="9" class="text-center"><span class="label label-secondary label-inline">Belum ada kontrak</span></td>' +
                            '</tr>';
                        table.append(row);
                    }
                    $.each(response.data, function(key, val) {
                        var count = val.attachments.length;
                        var hargaFormatted = formatRupiah(val.value);
                        var row = '' +
                            '<tr>' +
                            '<td>' + (key + 1) + '</td>' +
                            '<td>' + formatTanggalIndonesia(val.tanggal_mulai) + '</td>' +
                            '<td>' + formatTanggalIndonesia(val.tanggal_selesai) + '</td>' +
                            '<td>' + hargaFormatted + '</td>' +
                            '<td>' + val.keterangan + '</td>' +
                            '<td>' +
                            '<a title="Tampilkan attachment" onClick="showDocuments(' + val.id +
                            ')" href="javascript:" class="mr-10 position-relative text-hover-dark mr-1">' +
                            '<span style="z-index: 99; position: absolute; right: -30px; top: -9px" class="label label-rounded label-light-dark label-sm mr-2">' +
                            count + '</span>' +
                            '<span style="z-index: 100; position: absolute"><i class="fa fa-folder-open"></i></span>' +
                            '</a>' +
                            '<a title="Edit" onClick="editkontrak(' + val.id +
                            ')" href="javascript:" class="text-hover-dark mr-1">' +
                            '<i class="fa fa-edit"></i>' +
                            '</a>' +
                            '<a title="Hapus" onClick="deletekontrak(' + val.id +
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

        function getKontrakVendor() {
            kontrak_vendor_table = $('#kontrak-vendor').DataTable({
                "ajax": "{{ route('sigra.kontrak-vendor.get-all') }}",
                "paging": false,
                "responsive": true,
                "dom": '<"toolbar">frtip',
                "order": [
                    [5, "asc"]
                ],
                "columnDefs": [{
                    "targets": 5,
                    "type": "date-eu"
                }]
            });
            $(document).ready(function() {
                $('#kontrak-vendor_filter').append(
                    "<a href='" + "{{ route('sigra.kontrak-vendor.export') }}" +
                    "' class='btn btn-outline-success float-left btn-sm' onclick='exportExcel()'>" +
                    "<i class='la la-file-excel-o'></i> Export excel <i class='la la-download'></i>" +
                    "</a>"
                );
            });
        }

        getKontrakVendor();

        function showModalCreateNew() {
            $('#modal-title').text('Buat Perizinan Baru');
            $('#create-new-modal').modal('show');
        }

        function edit(id) {
            $.ajax({
                url: "{{ url('/sigra/master-vendor/get') }}/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(response) {
                    $('#id-vendor').val(response.data.id);
                    $('#edit-perusahaan').val(response.data.id_perusahaan);
                    $('#edit-nama-vendor').val(response.data.nama_vendor);
                    $('#edit-jenis-pekerjaan').val(response.data.jenis_pekerjaan);
                    $('#edit-status').val(response.data.status);
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
                        url: "{{ url('/sigra/master-vendor/delete') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
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

        function exportExcel() {
            Swal.fire(
                'Yeay',
                'Berhasil mendownload file kontrak vendor.',
                'success',
            );
        }

        function importExcel() {
            swal.fire(
                'hore',
                'berhasil mengimport data excel nih.',
                'success'
            )
        }

        function downloadAttachment(id) {
            window.location.href = "{{ url('/attachment/download') }}/" + id
        }

        $('[data-switch=true]').bootstrapSwitch();

        $('#perizinan-status').on('switchChange.bootstrapSwitch', function(e, data) {
            let status;
            let id = $('#kontrak-vendor-id').val();
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
                        url: "{{ url('/sigra/kontrak-vendor/set-status') }}",
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            id: id,
                            status: status
                        },
                        success: function(response) {
                            if (response.success == 1) {
                                kontrak_vendor_table.ajax.reload();
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

        $('#edit-form').on('submit', function(e) {
            e.preventDefault();
            $('#editSubmitButton i').removeClass('fa-paper-plane');
            $('#editSubmitButton i').addClass('fa-spinner');
            $('#editSubmitButton i').addClass('fa-spin');
            $.ajax({
                url: "{{ url('sigra/master-vendor/update') }}",
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success == '1') {
                        Swal.fire('Berhasil!', 'Master Vendor berhasil diubah', 'success')
                            .then(function() {
                                $('#editSubmitButton i').addClass('fa-paper-plane');
                                $('#editSubmitButton i').removeClass('fa-spinner');
                                $('#editSubmitButton i').removeClass('fa-spin');
                                location.reload();
                            });
                    } else {
                        Swal.fire('Gagal!', 'Master Vendor gagal diubah, coba lagi', 'error')
                            .then(function() {
                                $('#editSubmitButton i').addClass('fa-paper-plane');
                                $('#editSubmitButton i').removeClass('fa-spinner');
                                $('#editSubmitButton i').removeClass('fa-spin');
                            });
                    }
                },
                error: function(e) {
                    Swal.fire('Gagal!', 'Master Vendor gagal diubah, coba lagi', 'error')
                        .then(function() {
                            $('#editSubmitButton i').addClass('fa-paper-plane');
                            $('#editSubmitButton i').removeClass('fa-spinner');
                            $('#editSubmitButton i').removeClass('fa-spin');
                        });
                }
            })
        });
    </script>
@endpush
