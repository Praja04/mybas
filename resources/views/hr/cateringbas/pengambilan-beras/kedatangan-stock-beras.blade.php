@extends('hr.cateringbas.pengambilan-beras.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
    <style>
        /* styling loading spinner */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .custom-border {
        border: 2px solid #676a6c;
    }

    /* styling input */
    input:focus, input:hover {
        outline: none;
        border-color: rgba(76, 79, 234, 0.4);
        background-color: #fff;
        box-shadow: 0 0 0 4px rgb(234 76 137 / 10%);
    }

    </style>
@endpush

@section('content')
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        {{-- <div class="page-heading">
            <h3>Master Data Beras</h3>
        </div> --}}
        <div class="page-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header flex-wrap border-0 pt-6 pb-0">
                            <div class="card-title">
                                <h3 class="card-label">Kedatangan<span style="color: red"> Beras</span></h3>
                            </div>
                            <div class="card-toolbar pt-4 pb-4">
                                <a href="javascript:" class="btn btn-primary font-weight-bolder"
                                    onClick="showModalCreateStock()"><i class="fa fa-plus-square"></i> Tambah Stock
                                    Beras</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive datatable-minimal">
                                <table id="kedatanganStock" class="table table-hover">
                                    <thead>
                                        <tr>
                                            {{-- <th width="4%">no</th> --}}
                                            {{-- <th class="col-md-1" style="color: red;">Nomor Stock</th> --}}
                                            <th class="col-md-1">Tanggal</th>
                                            <th class="col-md-1">Jumlah Kedatangan</th>
                                            <th class="col-md-1">Satuan</th>
                                            {{-- <th class="col-md-1">Status Approval</th> --}}
                                            {{-- <th width="8%"><i class="fa fa-tools text-dark-75"></i></th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- foreach data stock --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Total Kedatangan beras Bulan Ini</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-profile-visit"></div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        </section>
    </div>
    </div>

    <div class="modal fade" id="create-modal-stock" tabindex="-1" role="dialog" aria-labelledby="exampleModalSizeSm"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i> Tambah Stock
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="tambahKedatanganStock">
                        @csrf
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right" for="kedatangan-stock">Jumlah Kedatangan
                                Beras <span style="color: red">*</span></label>
                                <div class="col-9">
                                    <div class="col-9">
                                        <div class="input-group mt-2">
                                            <input type="number" name="kedatangan_stock" class="form-control custom-border" required>
                                        </div>
                                    </div>                     
                                </div>                         
                            <label hidden class="col-3 col-form-label text-right pt-4" for="satuan-berat">Satuan
                                Berat</label>
                            <div class="col-9 pt-4">
                                <div class="input-group gap-2">
                                    <select hidden name="satuan_berat" required class="form-control" id="satuan-berat">
                                        {{-- di hidden balikin aja kalo perlu --}}
                                        {{-- <option value="">Pilih Satuan</option> --}}
                                        <option value="sak" {{ old('satuan_berat') == 'sak' ? 'selected' : '' }}>SAK
                                        </option>
                                        {{-- <option value="kg" {{ old('satuan_berat') == 'kg' ? 'selected' : '' }}>KG
                                        </option>
                                        <option value="hg" {{ old('satuan_berat') == 'kg' ? 'selected' : '' }}>HG
                                        </option>
                                        <option value="dag" {{ old('satuan_berat') == 'kg' ? 'selected' : '' }}>DAG
                                        </option>
                                        <option value="g" {{ old('satuan_berat') == 'kg' ? 'selected' : '' }}>G
                                        </option>
                                        <option value="dg" {{ old('satuan_berat') == 'kg' ? 'selected' : '' }}>DG
                                        </option>
                                        <option value="cm" {{ old('satuan_berat') == 'kg' ? 'selected' : '' }}>CM
                                        </option>
                                        <option value="mm" {{ old('satuan_berat') == 'kg' ? 'selected' : '' }}>MM
                                        </option> --}}
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="form-group row pt-2">
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
@endsection
@push('after-script')
    {{-- <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> --}}
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    <script type="text/javascript">
        let stock_table;

        function showModalCreateStock() {
            $('#modal-title').text('Buat Stock Baru');
            $('#create-modal-stock').modal('show');
        }

        // create anggota juri
        $(document).ready(function() {
        $('#tambahKedatanganStock').submit(function(e) {
            e.preventDefault();

        // SweetAlert confirmation dialog
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah anda yakin kedatangan beras sudah sesuai?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, yakin!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Continue with AJAX request if confirmed
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        type: "POST",
                        url: '{{ route("kedatangan-beras.tambah.stock") }}',
                        data: $('#tambahKedatanganStock').serialize(),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken 
                        },
                        success: function(response) {
                            if (response.success === 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message,
                                }).then(function() {
                                    var table = $('#kedatanganStock').DataTable();
                                    $('#create-modal-stock').modal('hide');
                                    table.ajax.reload();
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
                }
            });
        });
    });


        // get data kedatangan stock
        $(document).ready(function() {
            var table = $('#kedatanganStock').DataTable({
                paging: false,
                responsive: true,
                dom: '<"toolbar">frtip',
                order: [
                    [0,
                        "desc"
                    ]
                ],
                columnDefs: [{
                    targets: 1,
                    type: "date-eu"
                }],
                ajax: {
                    url: '{{ route('kedatangan-beras.data.stock') }}',
                    dataSrc: 'data'
                },
                columns: [
                    /* 'no' column
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            var pageNum = table.page.info().page;
                            var index = meta.row + (pageNum * table.page.info().length) + 1;
                            return index;
                        }
                    },
                    */
                    /* 'id_stock' column
                        {
                        data: 'id_stock'
                    },
                        */
                    {
                        data: "tanggal",
                        render: function(data, type, row) {
                            var dateTimeParts = data.split(' '); // Assuming format is 'YYYY-MM-DD HH:MM:SS'
                            var datePart = dateTimeParts[0];
                            var timePart = dateTimeParts[1];

                            return `<div style="display: flex; flex-direction: column; align-items: start;">
                                        <span><i class="fas fa-calendar-alt"></i> ${datePart}</span>
                                        <span><i class="fas fa-clock"></i> ${timePart}</span>
                                    </div>`;
                        }
                    },
                    {
                        data: 'qty_kedatangan_stock'
                    },
                    {
                        data: 'satuan_berat',
                        render: function(data, type, row) {
                            return ' <span class="badge bg-secondary">' + data + '</span>';
                        }
                    }
                ],
                lengthMenu: [10, 25, 50, 100]
            });

            $("div.toolbar").html(
                '<div class="dataTables_length" style="display:inline-block;margin-right:10px;">Show entries <select class="form-control input-sm" id="pageLength"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select></div>'
            );
        });

        // komen dulu
        // {
        //                 data: null,
        //                 render: function(data, type, row) {
        //                     return `
        //             <a class="btn btn-outline-danger" onClick="deleteStock('${data.id}')" href="javascript:;">
        //                 Delete
        //             </a>
        //             `;
        //                 }
        //             }

        // delete data stock
        function deleteStock(id) {
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
                        url: "{{ url('/dashboard/delete-data-stock/') }}/" + id,
                        type: "DELETE",
                        dataType: "JSON",
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response.success === 1) {
                                var table = $('#kedatanganStock').DataTable();
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
    </script>
@endpush
