@extends('layouts.base-sidebar')

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .message {
            transition-duration: 0.7ms;
        }

        .fixTableHead {
            overflow-y: auto;
            height: 400px;
        }

        .fixTableHead thead th {
            position: sticky;
            top: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 6px 10.5px !important;
        }

        .btn {
            padding: 2px !important
        }

        th {
            background: #dbdbdb;
        }
    </style>
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')
    <div class="container">
        <div class="main-body">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-header card-header-tabs-line">
                            <div class="card-title">
                                <h3 class="card-label">History @if (!in_array('security', $permissions)) Edoc Dari <u>{{ Auth::user()->name }}</u> /
                                        Untuk dept <u>{{ $dept }}</u> @endif
                                </h3>
                            </div>
                            <div class="card-toolbar">
                                <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1_3">
                                            <span class="nav-icon"><i class="fas fa-truck-loading"></i></span>
                                            <span class="nav-text"> Kedatangan</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_3">
                                            <span class="nav-icon"><i class="fas fa-truck"></i></span>
                                            <span class="nav-text"> Pengiriman</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_tab_pane_1_3" role="tabpanel"
                                    aria-labelledby="kt_tab_pane_1_3">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="exampleSelect1">Filter Status</label>
                                                <select class="form-control" id="filter">
                                                    {{-- filter status sudah diambil dan belum diambil --}}
                                                    <option
                                                        @if (!isset($_GET['filter'])) selected @else @if (isset($_GET['filter'])) @if ($_GET['filter'] == 'all') selected @endif
                                                        @endif
                                                        @endif value="all">Semua</option>
                                                    <option
                                                        @if (isset($_GET['filter'])) @if ($_GET['filter'] == '1') selected @endif
                                                        @endif value="1">Belum Diambil</option>
                                                    <option
                                                        @if (isset($_GET['filter'])) @if ($_GET['filter'] == '0') selected @endif
                                                        @endif value="0">Sudah Diambil</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="table-kedatangan" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>No</th>
                                                        <th>#</th>
                                                        <th>DEPT PENERIMA</th>
                                                        <th>NAMA PENERIMA</th>
                                                        <th>NAMA PT PENGIRIM</th>
                                                        <th>TANGGAL KEDATANGAN</th>
                                                        <th>JENIS</th>
                                                        <th>KETERANGAN</th>
                                                        <th>DIBUAT</th>
                                                        <th>STATUS</th>
                                                        @if (in_array('edoc_security', $permissions))
                                                            <th>AKSI</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="kt_tab_pane_2_3" role="tabpanel"
                                    aria-labelledby="kt_tab_pane_2_3">
                                    <table id="table-pengiriman" class="table table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th>No</th>
                                                <th>#</th>
                                                <th>DEPT PENERIMA</th>
                                                <th>NAMA PENERIMA</th>
                                                <th>NAMA PT PENGIRIM</th>
                                                <th>TANGGAL KEDATANGAN</th>
                                                <th>JENIS</th>
                                                <th>KETERANGAN</th>
                                                <th>DIBUAT</th>
                                                <th>STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($pengiriman as $edoc)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)"
                                                            onclick="detailPengiriman('{{ $edoc->id }}')"
                                                            class="btn btn-dark"><i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">{{ $edoc->dept_pengirim }}</td>
                                                    <td class="text-center">{{ $edoc->nama_penerima }}</td>
                                                    <td class="text-center">{{ $edoc->nama_pt_penerima }}</td>
                                                    <td class="text-center">
                                                        {{ Carbon\carbon::parse($edoc->tanggal_pengiriman)->format('d-M-Y') }}
                                                    </td>
                                                    <td class="text-center">{{ $edoc->jenis }}</td>
                                                    <td class="text-center">{{ $edoc->keterangan }}</td>
                                                    <td class="text-center">{{ $edoc->created_at }}</td>
                                                    <td class="text-center">
                                                        @if ($edoc->status == 1)
                                                            <span class="badge badge-warning">Proses</span>
                                                        @else
                                                            <span class="badge badge-info"> Selesai</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalkedatangan" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">DETAIL KEDATANGAN</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="appendBody">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalpengiriman" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">DETAIL PENGIRIMAN</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="appendBodyPengiriman">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalChangeDept" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">UBAH DEPARTMENT PENERIMA</h5>
                </div>
                <div class="modal-body">
                    <form id="formChangeDept" action="{{ url('edoc/postChangeDeprtPenerima') }}" method="POST">
                        @csrf
                        <input type="hidden" id="id_barang_penerima_baru" name="id_barang">
                        <div class="form-group">
                            <label for="">Department Penerima</label>
                            <select name="dept_penerima_baru" id="dept_penerima_baru" class="form-control">
                                @foreach ($list_dept as $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="formChangeDept" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReturnBarang" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">RETURN BARANG YANG SUDAH DITERIMA</h5>
                </div>
                <div class="modal-body">
                    <form id="formReturnBarang" action="{{ url('edoc/postReturnBarang') }}" method="POST">
                        @csrf
                        <input type="hidden" id="id_barang_return" name="id_barang">
                        <div class="form-group">
                            <label for="">Department Penerima</label>
                            <select name="dept_return" id="dept_return" class="form-control">
                                @foreach ($list_dept as $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="formReturnBarang" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        function returnBarang(id_barang, dept) {
            $('#id_barang_return').val(id_barang)
            $('#dept_return').val(dept)
            $('#modalReturnBarang').modal('show')
        }

        function changeDepartment(id_barang, dept) {
            $('#id_barang_penerima_baru').val(id_barang)
            $('#dept_penerima_baru').val(dept)
            $('#modalChangeDept').modal('show')
        }

        var initTooltip = function(el) {
            var theme = el.data('theme') ? 'tooltip-' + el.data('theme') : '';
            var width = el.data('width') == 'auto' ? 'tooltop-auto-width' : '';
            var trigger = el.data('trigger') ? el.data('trigger') : 'hover';

            $(el).tooltip({
                trigger: trigger,
                template: '<div class="tooltip ' + theme + ' ' + width +
                    '" role="tooltip">\
                                                                                                                                                                                                                                    <div class="arrow"></div>\
                                                                                                                                                                                                                                    <div class="tooltip-inner"></div>\
                                                                                                                                                                                                                                </div>'
            });
        }

        $('[data-toggle="tooltip"]').each(function() {
            initTooltip($(this));
        });

        $('#table-pengiriman').DataTable()

        var filter = 1;

        @if (isset($_GET['filter']))
            filter = "{{ $_GET['filter'] }}";
        @endif

        $('#table-kedatangan').DataTable({
            ajax: "{{ url('edoc/history/data-kedatangan') }}?filter=" + filter,
            type: "GET",
            serverSide: true,
            processing: true,
            columns: [{
                    data: 'DT_RowIndex',
                    "searchable": false,
                    "orderable": false,
                    name: 'DT_RowIndex'
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return `<a href="javascript:void(0)" onclick="detailKedatangan('${data}')"
                    class="btn btn-dark btn-sm"><i class="fas fa-eye"></i>
                </a>`
                    }
                },
                {
                    data: 'dept_penerima',
                    name: 'dept_penerima'
                },
                {
                    data: 'nama_penerima',
                    name: 'nama_penerima'
                },
                {
                    data: 'nama_pt_pengirim',
                    name: 'nama_pt_pengirim'
                },
                {
                    data: 'tanggal_kedatangan',
                    name: 'tanggal_kedatangan'
                },
                {
                    data: 'jenis',
                    name: 'jenis'
                },
                {
                    data: 'keterangan',
                    name: 'keterangan'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        if (data == 1) {
                            return `<span class="badge badge-warning"> Belum Diambil</span>`
                        } else {
                            return `<span class="badge badge-info"> Sudah Diambil</span>`
                        }
                    }
                },
                @if (in_array('edoc_security', $permissions))
                    {
                        data: null,
                        "sortable": false,
                        "searchable": false,
                        render: function(data, type, row) {
                            // belum diambil
                            if (row.status == 1) {
                                return `<a 
                                            href="javascript:void(0)" 
                                            onClick="changeDepartment('${row.id_barang}', '${row.dept_penerima}')" 
                                            data-toggle="tooltip" 
                                            title="Ganti department penerima" 
                                            class="btn btn-dark btn-sm"
                                            >
                                                <i class="las la-edit"></i>Ganti departemen
                                        </a>`
                            }
                            // sudah diambil
                            return `<a 
                                        href="javascript:void(0)" 
                                        onClick="returnBarang('${row.id_barang}', '${row.dept_penerima}')" 
                                        data-toggle="tooltip" 
                                        title="Return Barang / Dokumen" 
                                        class="btn btn-primary btn-sm">
                                            <i class="las la-undo-alt"></i> Return Barang
                                    </a>`
                        }
                    },
                @endif
            ]
        })

        $("#filter").on("change", function() {
            var filter = $(this).val()
            var url = "{{ url('edoc/history') }}?filter=" + filter;
            window.location.href = url;
        })

        function detailKedatangan(id) {
            $.ajax({
                url: "{{ url('edoc/detailKedatangan') }}" + '/' + id,
                type: "GET",
                data: {
                    id: id,
                },
                dataType: 'JSON',
                success: function(response) {
                    $('#modalkedatangan').modal('show');
                    $('.appendBody').html("");
                    $('.appendBody').append(`
                <div class="timeline timeline-4">
                    <div class="timeline-bar"></div>
                    <div class="timeline-items">
                        <div class="timeline-item timeline-item-left">
                            <div class="timeline-badge">
                                <div class="bg-danger"></div>
                            </div>
                            <div class="timeline-label">
                                <span class="text-primary font-weight-bold">${response.data.data.created_at} WIB</span>
                            </div>
                            <div class="timeline-content">
                                <b>${response.data.user.name}</b> Menginput form kedatangan
                            </div>
                        </div>
                        <div class="timeline-item timeline-item-right">
                            <div class="timeline-badge">
                                <div class="bg-success"></div>
                            </div>
                            <div class="timeline-label text-primary">
                                <span class="text-primary font-weight-bold">${response.data.data.updated_at ? response.data.data.updated_at + " WIB" : "Belum Diambil"} </span>
                            </div>
                            <div class="timeline-content">
                                 ${
                                    response.data.data.updated_by
                                    ? `<b>${response.data.data.updated_by}</b> Mengambil Barang/Dokumen
                                                                                                                    
                                                                                                                        <br>Bukti Foto:<hr>
                                                                                                                        ${response.data.data.foto 
                                                                                                                            ? `<img src="{{ url('e-doc/pengambilan') }}/${response.data.data.foto}" width="100%">`
                                                                                                                            : '<i>Tidak ada foto</i>'}`
                                            
                                    : '<span class="text-muted">Belum Diambil</span>'
                                }
                            </div>
                        </div>
                    </div>
                </div>`);
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan, silahkan coba lagi!',
                    });
                }
            });
        }

        function detailPengiriman(id) {
            $.ajax({
                url: "{{ url('edoc/detailPengiriman') }}" + '/' + id,
                type: "GET",
                data: {
                    id: id,
                },
                dataType: 'JSON',
                success: function(response) {
                    $('#modalpengiriman').modal('show');
                    $('.appendBodyPengiriman').html("");
                    console.log(response.data);
                    var namaConfirmBy = response.data.konfirm_by == null ? 'Belum Konfirmasi' : '<b>' + response
                        .data.konfirm_by.name + '</b> Mengkonfirmasi Barang/Dokumen Pengiriman Di POS';
                    var waktuConfirm = response.data.data.confirm_at == null ? 'Belum Konfirmasi' : response
                        .data.data.confirm_at;
                    var fotoKonfirmasi = response.data.data.foto == null ? '...' :
                        "<img src='{{ url('e-doc/konfirmasi-pengiriman') }}/" + response.data.data.foto +
                        "' style='width:100%'>";
                    var namaPetugas = response.data.petugas == null ? 'Belum Serah Terima' : '<b>' + response
                        .data.petugas.name + '</b> Menginput Form Serah Terima Kurir';
                    var waktuTransfer = response.data.data.transfer_at == null ? 'Belum Serah Terima' : response
                        .data.data.transfer_at;
                    var fotoSerahTerima = response.data.data.foto_serah_terima == null ? '...' :
                        "<img src='{{ url('e-doc/serah-terima') }}/" + response.data.data.foto_serah_terima +
                        "' style='width:100%'>";

                    $('.appendBodyPengiriman').append(`
                <div class="timeline timeline-4">
                    <div class="timeline-bar"></div>
                    <div class="timeline-items">
                        <div class="timeline-item timeline-item-left">
                            <div class="timeline-badge">
                                <div class="bg-danger"></div>
                            </div>
                            <div class="timeline-label">
                                <span class="text-primary font-weight-bold">${response.data.data.created_at} WIB</span>
                            </div>
                            <div class="timeline-content">
                                <b>${response.data.user.name}</b> Menginput Form Pengiriman
                            </div>
                        </div>
                        <div class="timeline-item timeline-item-right">
                            <div class="timeline-badge">
                                <div class="bg-success"></div>
                            </div>
                            <div class="timeline-label text-primary">
                                <span class="text-primary font-weight-bold">${waktuConfirm}</span>
                            </div>
                            <div class="timeline-content">
                                ${namaConfirmBy}
                                <hr>
                                Bukti Foto : <br />
                                ${fotoKonfirmasi}
                            </div>
                        </div>
                        <div class="timeline-item timeline-item-left">
                            <div class="timeline-badge">
                                <div class="bg-danger"></div>
                            </div>
                            <div class="timeline-label">
                                <span class="text-primary font-weight-bold">${waktuTransfer}</span>
                            </div>
                            <div class="timeline-content">
                                ${namaPetugas}
                                <hr>
                                Bukti Foto : <br />
                                ${fotoSerahTerima}
                            </div>
                        </div>
                    </div>
                </div>`);
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan, silahkan coba lagi!',
                    });
                }
            });
        }
    </script>
@endpush
