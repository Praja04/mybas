@extends('pages.halo-security.layout.base')

@section('title', 'Cek Kartu Izin Karyawan')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3">
            <div class="card card-height-100" style="border:1px solid #eee; width: 100%; border-radius: 5px">
                <div class="card-header text-center">
                    <h4 class="card-title mb-0 flex-grow-1">Cek Kartu Izin Karyawan</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="text-center">
                        <img id="gambar" style="width: 100px; margin-bottom: 15px;" class="opacity-90" src="{{ asset('/assets/media/icons/id-card.jpg') }}" alt="Tidak ada foto ID Card">
                        <div id="text-gambar"></div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div id="scanner-container" class="login-form login-signin" style="width: 200px">
                            <div class="text-center mb-10 mb-lg-20">
                                <p class="text-muted font-weight-bold">Silahkan Scan IDCard Karyawan</p>
                            </div>
                            <div class="form-group py-3 m-0">
                                <input class="form-control" type="text" placeholder="Scan Kartu" id="scanner" name="rfid" autocomplete="off" autofocus />
                            </div>
                            <div class="col-md-12" style="text-align: center">
                                <button data-bs-toggle="modal" data-bs-target="#modalCariManualIzin" class="btn btn-light waves-effect shadow-none">
                                    <i class="mdi mdi-magnify"></i> Cari Manual
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- .card-->
        </div> <!-- .col-->

        <div class="col-xl-9">
            <div class="card" style="border:1px solid #eee; width: 100%; border-radius: 5px">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1 text-center px-2">Data Izin Karyawan</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="p-1" style="border:1px solid #eee; width: 100%; border-radius: 5px">
                        <table>
                            <tbody>
                                <tr>
                                    <td><strong>NIK</strong></td>
                                    <td class="px-2">:</td>
                                    <td><span id="view-nik"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>NAMA</strong></td>
                                    <td class="px-2">:</td>
                                    <td><span id="view-nama"></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-1">
                    <div class="card-header text-center">
                        <h5>Detail Izin Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <table id="cekizin" class="table table-md table-bordered border-secondary table-nowrap">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">No Kartu</th>
                                    <th scope="col" class="text-center">Jenis Izin</th>
                                </tr>
                            </thead>
                            <tbody id="pengajuanizin">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- .card-->
        </div> <!-- .col-->
    </div> <!-- end row-->
</div>

<!-- Default Modals -->
<div id="modalCariManualIzin" class="modal fade" tabindex="-1" aria-labelledby="modalCariManualLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCariManualLabel">Cari Kartu Izin Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="nik" name="nik" placeholder="Masukan NIK Karyawan">
                    <button class="btn btn-outline-info shadow-none" type="button" id="reset">Reset</button>
                    <button class="btn btn-outline-info shadow-none" type="button" id="search-karyawan">Search</button>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatables" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align: center">NIK</th>
                            <th style="text-align: center">Nama</th>
                            <th style="text-align: center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#cekizin').DataTable();

        cari_manual();

        function cari_manual(nik = '')
        {
            var dataTable = $('#datatables').DataTable({
                    processing: true,
                    serverSide: true,
                    searching:false,
                    info:true,
                    ajax:{
                        url: "{{ route('get-all-karyawan.data') }}",
                        data:{nik:nik}
                    },
                    columns: [
                        {
                                data: "NIK",
                                name: "NIK",
                                render: function(data, type, row) {
                                    return `
                                    <div style="white-space: normal; text-align: center;">${row.NIK}
                                    </div>
                                    `;
                                }
                        },
                        { 
                                data: "EMPNM",
                                name: "EMPNM",
                                render: function(data, type, row) {
                                    return `
                                    <div style="white-space: normal; text-align: center;">${row.EMPNM}
                                    </div>
                                    `;
                                }
                        },
                        { 
                                data: "NIK",
                                name: "NIK",
                                render: function(data, type, row) {
                                    return `
                                    <div class="text-center">
                                        <button class="btn btn-md btn-success waves-effect waves-light" onClick="scanByNIK('${row.NIK}')"><i class="mdi mdi-account-check"></i> Pilih</button>
                                    </div>
                                    `;
                                }
                        },
                    ]
            });
        }

        $('#search-karyawan').click(function(){
            var nik = $('#nik').val();

            if(nik != '')
            {
                $('#datatables').DataTable().destroy();
                cari_manual(nik);
            }
            else
            {
                Swal.fire({            
                    icon: 'warning',                   
                    title: 'Gagal',    
                    text: 'NIK Karyawan wajib di isi terlebih dahulu',                        
                    timer: 2000,                                
                    showConfirmButton: false
                })
            }
        });

        $('#reset').click(function(){
            $('#nik').val('');
            $('#datatables').DataTable().destroy();
            cari_manual();
        });
    });

    $(document).on('shown.bs.modal', function (e) {
        $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
    });

    $("#scanner").on("keypress", function (e) {
        if(e.key == "Enter")
        {
            Swal.fire({
                title: 'Loading',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                },
                timer: 1000
            })
            
            if($("#scanner").val() == "")
            {
                return false
            }
            
            var rfid = $("#scanner").val()
            $("#scanner").val("")
            $.ajax({
                url: "{{ url('/halo-security/cek_pengajuan_izin/scan') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    rfid : rfid
                },
                success: function (response) {
                    if(response.success == 0) {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })

                            return
                        }

                        $('#gambar').attr('src', 'data:image/jpg;base64,'+response.data_karyawan.FOTOBLOB);
                        $("#view-nik").text(response.data_karyawan.NIK)
                        $("#view-nama").text(response.data_karyawan.EMPNM)

                        $("#pengajuanizin").html('')

                        if (response.data_perizinan.length == 0) {
                            $("#pengajuanizin").append(`
                                <tr>
                                    <td colspan="2" style="text-align: center;">Tidak Memiliki Izin Apapun</td>
                                </tr>
                            `)
                        }

                        response.data_perizinan.forEach((item, key) => {
                                    i = key+1
                                    $('#pengajuanizin').append(
                                    `<tr>
                                        <td>
                                            <p style="font-weight: bold; text-transform: uppercase; text-align: center;">${item.id_pengajuan}</p>
                                        </td>
                                        <td>
                                            <p style="font-weight: bold; text-transform: uppercase; text-align: center;">${item.jenis.nama_jenis}</p>
                                        </td>
                                    </tr>`);
                        })
                    },
                    error: function (e) {
                        console.log( e )
                    }
                })
        }
    })

    function scanByNIK(nik)
    {
        // Close modal
        $('#modalCariManualIzin').modal('hide')

        var url = "{{ route('get-pengajuan-izin.get-by-nik', ['nik' => '_nik']) }}"
        $(this).val('')
        // Swal loading
        Swal.fire({
            title: 'Loading',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            },
            timer: 1000
        })

        $.ajax({
            url: url.replace('_nik', nik),
            type: 'GET',
            dataType: 'JSON',
            success: function (response) {
                
                $('#gambar').attr('src', 'data:image/jpg;base64,'+response.data_karyawan.FOTOBLOB);
                $("#view-nik").text(response.data_karyawan.NIK)
                $("#view-nama").text(response.data_karyawan.EMPNM)

                $("#pengajuanizin").html('')

                if (response.data == null) {
                    $("#pengajuanizin").append(`
                        <tr>
                            <td colspan="2" style="text-align: center;">Tidak Memiliki Izin Apapun</td>
                        </tr>
                    `)
                }

                $('#pengajuanizin').append(`
                    <tr>
                        <td>
                            <p style="font-weight: bold; text-transform: uppercase; text-align: center;">${response.data.id_pengajuan}</p>
                        </td>
                        <td>
                            <p style="font-weight: bold; text-transform: uppercase; text-align: center;">${response.data.jenis.nama_jenis}</p>
                        </td>
                    </tr>
                `)

                Swal.close()
            },
            error: function (error) {
                console.log(error)
                // Swal error
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error, coba lagi!',
                    // Timer close 2 seconds
                    timer: 1000,
                })
            }
        })
    }
</script>
@endpush