@extends('layouts.base')

@push('styles')
    <style>
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .summary-row {
            font-weight: bold;
        }

        .summary-row .total-qty {
            color: red;
        }

        .enlarged-text {
            font-size: 1.2em;
        }

        .enlarged-checkbox {
            transform: scale(1.2);
            margin-right: 5px;
        }

        .input-radio {
            box-shadow: 0px 0px 0px 1px #6d6d6d;
            font-size: 3em;
            width: 12px;
            height: 12px;
            margin-right: 7px;

            border: 4px solid #fff;
            background-clip: border-box;
            border-radius: 50%;
            appearance: none;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        .input-radio.on:checked {
            box-shadow: 0px 0px 0px 4px #00eb27;
            background-color: #51ff6e;
        }

        .input-radio.off:checked {
            box-shadow: 0px 0px 0px 4px #eb0000;
            background-color: #ff5151;
        }

        .form-check-input:disabled+.form-check-label {
            color: #000;
        }

        /* style approve all */
        .comic-button {
            width: 100%;
            display: inline-block;
            padding: 10px 20px;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #ff5252;
            border: 2px solid #000;
            border-radius: 10px;
            box-shadow: 5px 5px 0px #000;
            transition: all 0.3s ease;
        }

        .comic-button:hover {
            background-color: #fff;
            color: #ff5252;
            border: 2px solid #ff5252;
            box-shadow: 5px 5px 0px #ff5252;
        }

        .comic-button:active {
            background-color: #fcf414;
            box-shadow: none;
            transform: translateY(4px);
        }
    </style>
@endpush


@section('content')
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">
            <div class="col-md-3">
                <h3 style="margin-bottom: 13px">Data Catering</h3>
                <div class="card border">
                    <div class="card-body">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <td>{{ $data->id_transaksi }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>{{ $data->tanggal }}</td>
                                </tr>
                                <tr>
                                    <th>Catering</th>
                                    <td>{{ $data->catering }}</td>
                                </tr>
                                <tr>
                                    <th>Shift</th>
                                    <td>{{ $data->shift }}</td>
                                </tr>
                                <tr>
                                    <th>Status Pengambilan Sampel</th>
                                    <td>
                                        @if ($data->status_pengambilan_sampel === 'sudah')
                                            <span class="badge text-bg-success"
                                                style="background-color: #00a816; color: white;">Sudah</span>
                                        @else
                                            @if ($data->status_pengambilan_sampel === 'menunggu approval')
                                                <span class="badge text-bg-success"
                                                    style="background-color: #d4ce13; color: white;">Menunggu
                                                    Approval</span>
                                            @else
                                                <span class="badge text-bg-danger"
                                                    style="background-color: #a80000; color: white;">Belum</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <th>Status Cek Kendaraan</th>
                                    <td>
                                        @if ($data->status_cek_kedatangan === 'sudah')
                                            <span class="badge text-bg-success"
                                                style="background-color: #00a816; color: white;">Sudah</span>
                                        @else
                                            <span class="badge text-bg-danger"
                                                style="background-color: #a80000; color: white;">Belum</span>
                                        @endif
                                    </td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <button onClick="showModalCreateCatering()" type="button"
                    class="btn btn-primary waves-effect btn-block w-100" data-bs-toggle="modal"
                    data-bs-target="#modalCreateGroup">
                    <i class="mdi mdi-plus"></i>
                    Tambah Catering
                </button> --}}
            </div>
            <div class="col-lg-9">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Data Pengambilan Sampel
                                <span class="d-block text-muted pt-2 font-size-sm">Atur Pengambilan Sampel</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                            <a id="lihat-jenis-menu" href="javascript:" class="btn btn-primary font-weight-bolder"
                                onClick="showJenisMenuModal()"><i class="fas fa-eye"></i> Lihat Detail Menu</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-start">
                            <div class="form-group">
                                <a id="SubmitApproveSampel"
                                    href="{{ url('/reporting/approve-sampel/' . $data->id_transaksi) }}"
                                    class="comic-button"
                                    onclick="return confirm('Apakah Anda Yakin Akan Approve Data Ini?');">
                                    Approve Semua Data Sampel <i class="fas fa-check ml-4 text-white"></i>
                                </a>
                            </div>
                            <div class="form-group">
                                <a id="dataApproveSampel" class="btn btn-success btn-lg" style="display: none;">
                                    <div class="p-2">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>{{ $data->approval_cek_sampel_at }}</span>
                                    </div>
                                    <div class="p-2">
                                        <i class="fas fa-user"></i>
                                        <span>{{ $data->approval_cek_sampel_by }}</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="jumlahPesanan" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-md-3">Tanggal Jam Masuk</th>
                                        <th class="col-sm-2">Foto Sampel Masuk </th>
                                        {{-- <th class="col-sm-2">Keterangan Menu Masuk</th> --}}
                                        <th class="col-sm-2">Tanggal Jam Keluar</th>
                                        <th class="col-sm-2">Foto Sampel Keluar</th>
                                        <th class="col-sm-1">Keterangan</th>
                                        <th class="col-sm-2">Masa Simpan</th>
                                        <th class="col-sm-2">Status</th>
                                        {{-- <th class="col-sm-2">Kategori Staff</th> --}}
                                        {{-- <th width="8%"><i class="fa fa-tools text-dark-75"></i></th> <!-- Kolom Aksi --> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- data table kedatangan lauk --}}
                                </tbody>
                            </table>
                        </div>
                        <div class="button-group pt-4" style="display: flex; gap: 20px;">
                            <a href="/cateringbas/reporting-GA" id="kembalikehome" class="btn btn-danger">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    {{-- melihat modal jenis menu makanan --}}
    <div class="modal fade" id="jenismenuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hasil Keluar Sampel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Kategori Staff</th>
                                <th>Jenis Menu</th>
                                <th>Nama Menu</th>
                                <th>Kondisi Sampel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($MenuMakanan as $kategoriStaff => $menusGrouped)
                                @php
                                    $utamaWithPengambilan = array_filter($menusGrouped['utama'], function ($item) {
                                        return $item['pengambilan'] == 1;
                                    });
                                    $pendampingWithPengambilan = array_filter($menusGrouped['pendamping'], function ($item) {
                                        return $item['pengambilan'] == 1;
                                    });
                                    $totalUtama = count($utamaWithPengambilan);
                                    $totalPendamping = count($pendampingWithPengambilan);
                                    $firstRowUtama = true;
                                    $firstRowPendamping = true;
                                @endphp
                                @foreach ($utamaWithPengambilan as $itemUtama)
                                    <tr>
                                        @if ($firstRowUtama)
                                            <td rowspan="{{ $totalUtama + $totalPendamping }}">{{ $kategoriStaff }}</td>
                                            <td rowspan="{{ $totalUtama }}">Menu Utama</td>
                                        @endif
                                        <td>{{ $itemUtama['nama_menu'] }}</td>
                                        <td>
                                            <div style="display: flex; gap: 25px; justify-content: center;">
                                                <div class="form-check">
                                                    <input class="form-check-input enlarged-checkbox input-radio on" type="radio"
                                                        name="kondisi_sampel_utama_{{ $itemUtama['id'] }}"
                                                        id="baikUtama{{ $itemUtama['id'] }}"
                                                        value="1" {{ $itemUtama['baik'] == 1 ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label ml-4" for="baikUtama{{ $itemUtama['id'] }}">
                                                        Baik
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input enlarged-checkbox input-radio off" type="radio"
                                                        name="kondisi_sampel_utama_{{ $itemUtama['id'] }}"
                                                        id="tidakBaikUtama{{ $itemUtama['id'] }}"
                                                        value="0" {{ $itemUtama['baik'] == 0 ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label ml-4" for="tidakBaikUtama{{ $itemUtama['id'] }}">
                                                        Tidak Baik
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $firstRowUtama = false; @endphp
                                @endforeach
                                @foreach ($pendampingWithPengambilan as $itemPendamping)
                                    <tr>
                                        @if ($firstRowPendamping)
                                            <td rowspan="{{ $totalPendamping }}">Menu Pendamping</td>
                                        @endif
                                        <td>{{ $itemPendamping['nama_menu'] }}</td>
                                        <td>
                                            <div style="display: flex; gap: 25px; justify-content: center;">
                                                <div class="form-check">
                                                    <input class="form-check-input enlarged-checkbox input-radio on" type="radio"
                                                        name="kondisi_sampel_pendamping_{{ $itemPendamping['id'] }}"
                                                        id="baikPendamping{{ $itemPendamping['id'] }}"
                                                        value="1" {{ $itemPendamping['baik'] == 1 ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label ml-4" for="baikPendamping{{ $itemPendamping['id'] }}">
                                                        Baik
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input enlarged-checkbox input-radio off" type="radio"
                                                        name="kondisi_sampel_pendamping_{{ $itemPendamping['id'] }}"
                                                        id="tidakBaikPendamping{{ $itemPendamping['id'] }}"
                                                        value="0" {{ $itemPendamping['baik'] == 0 ? 'checked' : '' }} disabled>
                                                    <label class="form-check-label ml-4" for="tidakBaikPendamping{{ $itemPendamping['id'] }}">
                                                        Tidak Baik
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @php $firstRowPendamping = false; @endphp
                                @endforeach
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    {{-- modal delete jumlah pesanan catering --}}
@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    {{-- filepon --}}
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script type="text/javascript">
        function showJenisMenuModal() {
            $('#jenismenuModal').modal('show');
        }

        function showModalCreateNew() {
            $('#modal-title-pesanan').text('Upload Jumlah Pesanan Catering');
            $('#modalPesananCatering').modal('show');
        }

        function openEditModal(id, foto_before, foto_after, id_transaksi, keterangan, keterangan_menu, kategori_staff) {
            $('#edit-id').val(id);
            $('#edit-id-transaksi').val(id_transaksi);
            $('#edit-image-preview-before').attr('src', foto_before);
            $('#edit-image-preview-after').attr('src', foto_after);
            $('#edit-keterangan-menu').val(keterangan_menu);
            // console.log(status_pengambilan_sampel);
            $('#edit-keterangan-sampel option[value="' + keterangan + '"]').prop('selected', true);
            $('#edit-kategori-staff option[value="' + kategori_staff + '"]').prop('selected', true);
            $('#editmodalPesananCatering').modal('show');
        }

        // edit pesanan
        $(document).ready(function() {
            // Initialize FilePond for foto_after1
            const inputElementAfter1 = document.querySelector('input[name="foto_after1"]');
            FilePond.registerPlugin(FilePondPluginImagePreview);
            const pondAfter1 = FilePond.create(inputElementAfter1);

            // Initialize FilePond for foto_after2
            const inputElementAfter2 = document.querySelector('input[name="foto_after2"]');
            const pondAfter2 = FilePond.create(inputElementAfter2);

            // Click event for submit button
            $('#editSubmitbutton').click(function(e) {
                e.preventDefault();

                var data = new FormData($('#editPesananCatering')[0]);
                var filesAfter1 = pondAfter1.getFiles();
                var filesAfter2 = pondAfter2.getFiles();

                // Append files from FilePond instances to FormData
                if (filesAfter1.length > 0) {
                    filesAfter1.forEach((file) => {
                        data.append('files_after1[]', file.file);
                    });
                }
                if (filesAfter2.length > 0) {
                    filesAfter2.forEach((file) => {
                        data.append('files_after2[]', file.file);
                    });
                }

                var id = $('#edit-id').val();

                // AJAX request to server
                $.ajax({
                    url: '/cateringbas/sampel/edit/' + id,
                    type: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success === 1) {
                            var table = $('#jumlahPesanan').DataTable();
                            table.ajax.reload();
                            $('#editmodalPesananCatering').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: response.message,
                            });
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
                });
            });
        });


        // get pesanan
        $(document).ready(function() {
            var table = $('#jumlahPesanan').DataTable({
                paging: true,
                responsive: true,
                order: [
                    [0, 'desc']
                ],
                ajax: {
                    url: '/cateringbas/sample-lauk/get-all/' + encodeURIComponent(
                        '{{ $data->id_transaksi }}'),
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'tanggal_jam_masuk'
                    },
                    {
                        // Combined column for foto_before_1 and foto_before_2
                        data: function(row) {
                            return [row.foto_before_1, row.foto_before_2];
                        },
                        render: function(data) {
                            return data.map(function(imageUrl) {
                                if (imageUrl) {
                                    return `<div class="image-container" style="margin-bottom: 10px;"><a href="${imageUrl}" class="venobox"><img src="${imageUrl}" class="clickable-image" style="max-width: 80px; height: auto;" /></a></div>`;
                                }
                                return '';
                            }).join('');
                        }
                    },
                    /* {
                        data: 'keterangan_menu',
                        render: function(data, type, row) {
                            if (data === 'baik') {
                                return '<span class="badge badge-success text-white">Baik</span>';
                            } else if (data === 'tidak  ') {
                                return '<span class="badge badge-danger text-white">Tidak Baik</span>';
                            }
                            return data;
                        }
                    }, */
                    {
                        data: 'tanggal_jam_keluar',
                        render: function(data) {
                            if (data === null) {
                                return '<span class="badge badge-warning text-white">Data<br> Belum Ditambahkan</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: function(row) {
                            return [row.foto_after_1, row.foto_after_2];
                        },
                        render: function(data) {
                            if (!data[0] && !data[1]) {
                                return '<span class="badge badge-warning text-white">Data<br> foto belum Ditambahkan</span>';
                            }

                            return data.map(function(imageUrl) {
                                if (imageUrl) {
                                    return `<div class="image-container" style="margin-bottom: 10px;"><a href="${imageUrl}" class="venobox"><img src="${imageUrl}" class="clickable-image" style="max-width: 80px; height: auto;" /></a></div>`;
                                }
                                return '';
                            }).join('');
                        }
                    },
                    {
                        data: 'keterangan_menu_keluar',
                        render: function(data, type, row) {
                            if (data === 'baik') {
                                return '<span class="badge badge-success text-white">Baik</span>';
                            } else if (data === 'tidak') {
                                return '<span class="badge badge-danger text-white">Tidak Baik</span>';
                            } else if (data === '') {
                                return '<span class="badge badge-warning text-white">Data <br> Belum Ditambahkan</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'masa_simpan',
                        render: function(data) {
                            if (data == null) {
                                return '<span class="badge badge-warning text-white">Data <br>belum ditambahkan</span>';
                            } else {
                                return `<span class="badge badge-info text-white">${data}</span>`;
                            }
                        }
                    },
                    // {
                    //     data: 'kategori_staff'
                    // },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if ('{{ $data->status_pengambilan_sampel }}' == 'belum') {
                                return `<p class="badge badge-warning">Silahkan Hubungi PIC Sampel</p>`;
                            } else {
                                return `<p class="badge badge-success">Data Sampel<br> Sudah Dikirim</p>`;
                            }
                        }
                    }
                ]
            });

            // Initialize VenoBox for images
            new VenoBox({
                selector: '.venobox',
                overlayClose: true,
            });
        });





        // delete pesanan
        function deletePesanan(id) {
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
                        url: "{{ url('/cateringbas/sampel/delete/') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response) {
                            if (response.success === 1) {
                                var table = $('#jumlahPesanan').DataTable();
                                $('#editmodalPesananCatering').modal(
                                    'hide');
                                table.ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                })

                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);

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

        $(document).ready(function() {
            $('#checkAllBaik').click(function() {
                if (this.checked) {
                    $('.baik-checkbox').each(function() {
                        this.checked = true;
                    });
                } else {
                    $('.baik-checkbox').each(function() {
                        this.checked = false;
                    });
                }
            });

            $('#checkAllTidakBaik').click(function() {
                if (this.checked) {
                    $('.tidak-baik-checkbox').each(function() {
                        this.checked = true;
                    });
                } else {
                    $('.tidak-baik-checkbox').each(function() {
                        this.checked = false;
                    });
                }
            });
        });

        $(document).ready(function() {
            // Checkbox handlers
            $('#checkAllBaik, #checkAllTidakBaik').click(function() {
                var targetClass = $(this).attr('id') === 'checkAllBaik' ? '.baik-checkbox' :
                    '.tidak-baik-checkbox';
                $(targetClass).prop('checked', this.checked);
                updateKeteranganMenu();
            });

            $('.baik-checkbox, .tidak-baik-checkbox').change(function() {
                updateKeteranganMenu();
            });

            function updateKeteranganMenu() {
                var jumlahBaik = $('.baik-checkbox:checked').length;
                var jumlahTidakBaik = $('.tidak-baik-checkbox:checked').length;
                var keterangan;

                if (jumlahBaik > jumlahTidakBaik) {
                    keterangan = 'baik';
                } else if (jumlahBaik < jumlahTidakBaik) {
                    keterangan = 'tidak';
                } else {
                    keterangan = '';
                }

                // $('#edit-keterangan-menu').val(keterangan);
                $('#keterangan-menu').val(keterangan);
                // $('#keterangan-menu-keluar').val(keterangan);
            }
        });

        $(document).ready(function() {
            function showConfirmationModal() {
                Swal.fire({
                    icon: 'info',
                    title: 'Hanya Reminder',
                    text: 'Pastikan Sampel Sudah Sesuai.',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Tutup'
                });
            }
            showConfirmationModal();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusCekKendaraan = "{{ $data->status_pengambilan_sampel }}";
            console.log(statusCekKendaraan);

            if (statusCekKendaraan === 'menunggu approval') {
                document.getElementById("submitPenilaian").style.display = "none";
                // document.getElementById("uploadJumlahSample").style.display = "none";
                document.getElementById("penilaianTerkirim").style.display = "inline-block";
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var statusCekKendaraan = "{{ $data->approval_cek_sampel }}";
            console.log(statusCekKendaraan);

            if (statusCekKendaraan === 'Y') {
                document.getElementById("SubmitApproveSampel").style.display = "none";
                document.getElementById("dataApproveSampel").style.display = "inline-block";
            }
        });
    </script>
@endpush
