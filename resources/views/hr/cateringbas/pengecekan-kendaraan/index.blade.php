@extends('layouts.base')

@push('styles')
    <link href="{{ asset('assets/velzon/libs/filepond/filepond.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/velzon/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Data <span style="color: red">Pengecekan Kendaraan</span>
                                <span class="d-block text-muted pt-2 font-size-sm">Atur Data Pengirim Catering</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder"
                                onClick="showModalCreateCatering()"><i class="fa fa-plus-square"></i> Tambah Pengirim
                                Catering</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="pengirimCatering" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="4%">no</th>
                                    <th class="col-md-1" style="color: red;">Nomor Transaksi</th>
                                    <th class="col-md-1">Foto</th>
                                    <th class="col-md-1">Tanggal</th>
                                    <th class="col-md-1">Catering</th>
                                    <th class="col-md-1">Shift</th>
                                    <th class="col-md-1">Status Cek Kendaraan</th>
                                    {{-- <th class="col-md-1">Status Cek Kedatangan</th> --}}
                                    <th width="8%"><i class="fa fa-tools text-dark-75"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- data table kedatangan lauk --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    {{-- modal create jumlah pesanan catering --}}
    <div class="modal fade" id="modalPesananCatering" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title-pesanan" id="exampleModalLabel"><i class="fa fa-plus-square"></i> Upload Data
                        Pengirim Catering
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadPesananCatering">
                        @csrf
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="image">
                                Gambar Catering <span style="color: red;">*</span>
                            </label>
                            <div class="col-9">
                                <div class="form-group">
                                    <label for="image">Image</label>
                                    <img src="" id="edit-image-preview" class="clickable-image"
                                        style="max-width: 80px; height: auto;" />
                                    <input type="file" name="foto" id="image" multiple data-max-file-size="3MB"
                                        required>
                                </div>
                            </div>
                        </div>
                        <div class="col-9">
                            <input type="hidden" name="tanggal" required placeholder="Tanggal Kedatangan Catering"
                                class="form-control" type="date" value="" id="tanggal-catering">
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="nama-catering">
                                Nama Catering <span style="color: red;">*</span>
                            </label>
                            <div class="col-9">
                                <input name="catering" required placeholder="Nama Catering" class="form-control"
                                    type="text" value="" id="nama-catering">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kategori-shift">
                                Shift Karyawan <span style="color: red;">*</span>
                            </label>
                            <div class="col-9">
                                <select required name="shift" id="kategori-shift" class="form-control">
                                    <option value=""></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-3"></div>
                            <div class="col-9">
                                <button id="submitButton" type="button" class="btn btn-primary"><i
                                        class="fa fa-paper-plane"></i>Tambah Pengirim Catering</button>
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
    {{-- filepond --}}
    <script src="{{ asset('assets/velzon/libs/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('assets/velzon/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script type="text/javascript">
        // open modal pesanan catering
        function showModalCreateCatering() {
            $('#modal-title-pesanan').text('Upload Data Pengirim Catering');
            $('#modalPesananCatering').modal('show');
        }

        // getall data pengirim
        $(document).ready(function() {
            var table = $('#pengirimCatering').DataTable({
                paging: true,
                responsive: true,
                ajax: {
                    url: "{{ route('cateringbas.get.reporting-user') }}",
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'id_transaksi'
                    },
                    // {
                    //     data: 'foto',
                    //     render: function(data) {
                    //         return `<a href="${data}" class=""><img src="${data}" class="clickable-image" style="max-width: 80px; height: auto;" /></a>`;
                    //     }
                    // },
                    {
                        data: 'foto',
                        render: function(data, type, row) {
                            const blankSvg = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 24 24' fill='%23cccccc'><rect width='24' height='24' fill='%23f3f6f9'/><path d='M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z' fill='%23b5b5c3'/></svg>";
                            return `
                            <a href="javascript:void(0)" onclick="openImage(this)">
                                <img
                                    src="${row.foto}"
                                    onerror="
                                        if (this.src !== '${row.foto_backup}' && '${row.foto_backup}') {
                                            this.src='${row.foto_backup}';
                                        } else {
                                            this.onerror = null;
                                            this.src='${blankSvg}';
                                        }
                                    "
                                    class="clickable-image"
                                    style="max-width:80px;height:auto;"
                                />
                            </a>
                        `;
                        }
                    },
                    {
                        data: 'tanggal'
                    },
                    {
                        data: 'catering'
                    },
                    {
                        data: 'shift'
                    },
                    {
                        data: 'status_cek_kendaraan',
                        render: function(data, type, row) {
                            if (data === 'belum') {
                                return '<span class="badge badge-danger">Belum</span>';
                            } else if (data === 'menunggu approval') {
                                return '<span class="badge badge-warning text-white">menunggu Approval</span>';
                            } else if (data === 'sudah') {
                                return '<span class="badge badge-success">Sudah di Approve</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                            <div class="mt-4">
                                <a title="Hapus" onClick="deletePengirimcatering('${data}')" href="javascript:"
                                    class="btn btn-sm btn-danger text-white mx-2">
                                    <i class="fa fa-trash"></i>
                                </a>
                                <a href="{{ url('') }}/cateringbas/kuesioner-kendaraan/${row.id_transaksi}"
                                    class="btn btn-sm btn-info text-white mx-2">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>`;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
        });

        function openImage(el) {
            const img = el.querySelector('img');
            if (img && img.src) {
                window.open(img.src, '_blank');
            }
        }


        // delete pesanan
        function deletePengirimcatering(id) {
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
                        url: "{{ url('/cateringbas/pengirim-catering/delete/') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
                            if (response.success === 1) {
                                var table = $('#pengirimCatering').DataTable();
                                table.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message,
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengirim permintaan.',
                            });
                        }
                    })
                }
            });
        }

        // tambah pesanan 
        $(document).ready(function() {
            let pond = null;
            const inputElement = document.querySelector('input[name="foto"]');

            if (typeof FilePond !== 'undefined') {
                if (typeof FilePondPluginImagePreview !== 'undefined') {
                    FilePond.registerPlugin(FilePondPluginImagePreview);
                }
                pond = FilePond.create(inputElement);
            }

            $('#submitButton').click(function(e) {
                e.preventDefault();

                var catering = $('#nama-catering').val();
                var shift = $('#kategori-shift').val();

                if (!catering) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan isi Nama Catering terlebih dahulu.'
                    });
                    return;
                }

                if (!shift) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Silakan pilih Shift terlebih dahulu.'
                    });
                    return;
                }

                var $btn = $(this);
                var originalHtml = $btn.html();

                var data = new FormData($('#uploadPesananCatering')[0]);

                if (pond) {
                    var files = pond.getFiles();
                    if (files.length > 0) {
                        files.forEach((file) => {
                            data.append('files[]', file.file, file.file.name || 'foto.jpg');
                        });
                    }
                }

                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    type: "POST",
                    url: '{{ route('cateringbas.tambah.pesanan') }}',
                    data: data,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success === 1) {
                            var table = $('#pengirimCatering').DataTable();
                            table.ajax.reload();

                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            }).then(function() {
                                var id_transaksi = response.id_transaksi;
                                window.location.href =
                                    "{{ url('') }}/cateringbas/kuesioner-kendaraan/" +
                                    id_transaksi;
                            });
                        } else {
                            $btn.prop('disabled', false).html(originalHtml);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        $btn.prop('disabled', false).html(originalHtml);
                        var msg = 'Terjadi kesalahan saat mengirim permintaan.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.error === 0) {
                            msg = 'Harap masukkan foto kedatangan catering';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: msg,
                        });
                    }
                });

            });
        });
    </script>
@endpush
