@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        <div class="card card-custom mb-4">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="card-label">Laporan Pemeriksaan Klinik</h3>
                </div>
                <div class="card-toolbar">
                    <ul class="nav nav-light-success nav-bold nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-skd">
                                <span class="nav-icon"><i class="flaticon2-chat-1"></i></span>
                                <span class="nav-text">SKD</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-permintaan-obat">
                                <span class="nav-icon"><i class="flaticon2-drop"></i></span>
                                <span class="nav-text">Permintaan Obat</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-1">
                        <label for="filter-start-date" class="pt-2">Start Date</label>
                    </div>
                    <div class="col-md-2">
                        <input id="filter-start-date" type="text" class="form-control form-control-sm date">
                    </div>
                    <div class="col-md-1">
                        <label for="filter-end-date" class="pt-2">End Date</label>
                    </div>
                    <div class="col-md-2">
                        <input id="filter-end-date" type="text" class="form-control form-control-sm date">
                    </div>
                </div>
                <hr>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-skd" role="tabpanel" aria-labelledby="tab-skd">
                        <div class="row">
                            <div class="col-md-5">
                                <div id="skd-diagnosa-chart"></div>
                            </div>
                            <div class="col-md-5">
                                <div id="skd-faskes-chart"></div>
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%">#</th>
                                                <th style="white-space: nowrap">NIK</th>
                                                <th style="white-space: nowrap">NAMA</th>
                                                {{-- <th style="white-space: nowrap">JK</th> --}}
                                                <th style="white-space: nowrap">BAGIAN</th>
                                                <th style="white-space: nowrap">Tgl SKD Mulai</th>
                                                <th style="white-space: nowrap">Tgl SKD Selesai</th>
                                                <th style="white-space: nowrap">Faskes</th>
                                                <th style="white-space: nowrap">Tanggal Pemeriksaan</th>
                                                <th style="white-space: nowrap">Waktu Pemeriksaan</th>
                                                {{-- <th style="white-space: nowrap">PIC</th> --}}
                                                <th style="white-space: nowrap">Dokter</th>
                                                <th style="white-space: nowrap">Keluhan</th>
                                                <th style="white-space: nowrap">Diagnosa</th>
                                                <th style="white-space: nowrap">Komorbid</th>
                                                <th style="white-space: nowrap">Tindakan</th>
                                                <th style="white-space: nowrap">Keterangan</th>
                                                <th style="white-space: nowrap">Bukti SKD</th>
                                                {{-- <th style="white-space: nowrap">Tanggal Validasi</th>
                                            <th style="white-space: nowrap">Validasi Oleh</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($skd_raw as $key => $skd)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->nik }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->nama }}</td>
                                                    {{-- <td style="white-space: nowrap">{{ $skd->jenis_kelamin }}</td> --}}
                                                    <td style="white-space: nowrap">{{ $skd->bagian }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->tanggal_skd_mulai }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->tanggal_skd_selesai }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->nama_faskes }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->tanggal_pemeriksaan }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->waktu_pemeriksaan }}</td>
                                                    {{-- <td style="white-space: nowrap">{{ $skd->pic }}</td> --}}
                                                    <td style="white-space: nowrap">{{ $skd->dokter }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->keluhan }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->nama_diagnosa }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->komorbid }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->tindakan }}</td>
                                                    <td style="white-space: nowrap">{{ $skd->keterangan }}</td>
                                                    <td style="white-space: nowrap">
                                                        <a type="button" class="pop">
                                                            <img style="width:40px;"
                                                                src="{{ asset('bukti_skd/' . $skd->bukti_skd) }}">
                                                        </a>

                                                        <div class="modal fade" id="imagemodal" tabindex="-1"
                                                            role="dialog" aria-labelledby="myModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">

                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr- 
                                                            only">Close</span></button>
                                                                        <img src=""{{ asset('bukti_skd/' . $skd->bukti_skd) }}
                                                                            class="imagepreview" style="width: 100%;">

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <img src="{{asset('bukti_skd/'.$skd->bukti_skd)}}" alt="" style="width:40px;"> --}}
                                                    </td>
                                                    {{-- <td style="white-space: nowrap">{{ $skd->tanggal_validasi ? formatTanggalIndonesia($skd->tanggal_validasi) : '' }}</td>
                                                <td style="white-space: nowrap">{{ $skd->validasi_oleh }}</td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- edit-modal.blade.php -->
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog"
                        aria-labelledby="editModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Tindakan dan Keterangan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm" method="post"
                                        action="{{ route('update.tindakan.keterangan') }}">
                                        @csrf
                                        <!-- Input hidden untuk menyimpan id -->
                                        <input type="" name="id" value="" id="id_permintaan_obat">
                                        <!--end input hidden id -->
                                        <div class="form-group">
                                            <label for="tindakan">Tindakan</label>
                                            <input type="text" class="form-control" name="tindakan" id="tindakan"
                                                value="tindakan">
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea class="form-control" value="keterangan" name="keterangan" id="keterangan" rows="3"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-permintaan-obat" role="tabpanel"
                        aria-labelledby="tab-permintaan-obat">
                        <div class="row">
                            <div class="col-md-5">
                                <div id="permintaan-obat-diagnosa-chart"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 2%">#</th>
                                                <th style="white-space: nowrap">NIK</th>
                                                <th style="white-space: nowrap">NAMA</th>
                                                {{-- <th style="white-space: nowrap">JK</th> --}}
                                                <th style="white-space: nowrap">BAGIAN</th>
                                                <th style="white-space: nowrap">Tanggal Pemeriksaan</th>
                                                <th style="white-space: nowrap">Waktu Pemeriksaan</th>
                                                {{-- <th style="white-space: nowrap">PIC</th> --}}
                                                <th style="white-space: nowrap">Dokter</th>
                                                <th style="white-space: nowrap">Keluhan</th>
                                                <th style="white-space: nowrap">Diagnosa</th>
                                                <th style="white-space: nowrap">Komorbid</th>
                                                <th style="white-space: nowrap">Tindakan</th>
                                                <th style="white-space: nowrap">Keterangan</th>
                                                <th style="white-space: nowrap">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach ($permintaan_obat_raw as $key => $permintaan_obat)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->nik }}</td>
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->nama }}</td>
                                                    {{-- <td style="white-space: nowrap">{{ $permintaan_obat->jenis_kelamin }}</td> --}}
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->bagian }}</td>
                                                    <td style="white-space: nowrap">
                                                        {{ $permintaan_obat->tanggal_pemeriksaan }}</td>
                                                    <td style="white-space: nowrap">
                                                        {{ $permintaan_obat->waktu_pemeriksaan }}</td>
                                                    {{-- <td style="white-space: nowrap">{{ $permintaan_obat->pic }}</td> --}}
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->dokter }}</td>
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->keluhan }}</td>
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->nama_diagnosa }}
                                                    </td>
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->komorbid }}</td>
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->kp_tindakan }}
                                                    </td>
                                                    <td style="white-space: nowrap">{{ $permintaan_obat->kp_keterangan }}
                                                    </td>
                                                    <td style="white-space: nowrap">
                                                        <a href="#" class="editBtn"
                                                            data-id="{{ $permintaan_obat->kp_id }}"
                                                            data-tindakan="{{ $permintaan_obat->kp_tindakan }}"
                                                            data-keterangan="{{ $permintaan_obat->kp_keterangan }}">Edit</a>
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
@endsection

@push('scripts')
    <script src="{{ asset('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('/assets/js/highcharts/highcharts.js') }}"></script>
    <script>
        $("#filter-start-date").val(
            "{{ isset($_GET['filter_start_date']) ? $_GET['filter_start_date'] : date('Y-m-d', strtotime('-7 days')) }}"
        )

        $("#filter-end-date").val("{{ isset($_GET['filter_end_date']) ? $_GET['filter_end_date'] : date('Y-m-d') }}")

        @if (!isset($_GET['filter_start_date']) || !isset($_GET['filter_start_date']))

            window.location.href = "{{ url('/klinik/laporan-pemeriksaan') }}?filter_start_date=" + $("#filter-start-date")
                .val() + "&filter_end_date=" + $("#filter-end-date").val()
        @endif

        $("#filter-start-date, #filter-end-date").on("change", function() {
            window.location.href = "{{ url('/klinik/laporan-pemeriksaan') }}?filter_start_date=" + $(
                "#filter-start-date").val() + "&filter_end_date=" + $("#filter-end-date").val()
        })

        $(".date").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true
        })
        $('.table').DataTable({
            dom: "<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                			<'row'<'col-sm-12'tr>>\
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                			<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
            buttons: [
                'copy', 'excel', 'pdf'
            ],
            // paging: false
        });
    </script>

    <script>
        Highcharts.chart('skd-diagnosa-chart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Trend Diagnosa'
            },
            // tooltip: {
            //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            // },
            // accessibility: {
            //     point: {
            //         valueSuffix: '%'
            //     }
            // },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                    }
                }
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                data: {!! $skd_chart !!}
            }]
        });

        Highcharts.chart('skd-faskes-chart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Trend Faskes'
            },
            // tooltip: {
            //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            // },
            // accessibility: {
            //     point: {
            //         valueSuffix: '%'
            //     }
            // },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                    }
                }
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                data: {!! $faskes_chart !!}
            }]
        });

        Highcharts.chart('permintaan-obat-diagnosa-chart', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Trend Diagnosa'
            },
            // tooltip: {
            //     pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            // },
            // accessibility: {
            //     point: {
            //         valueSuffix: '%'
            //     }
            // },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                    }
                }
            },
            series: [{
                name: 'Jumlah',
                colorByPoint: true,
                data: {!! $permintaan_obat_chart !!}
            }]
        });
    </script>
    <script>
        $(function() {
            $('.pop').on('click', function() {
                $('.imagepreview').attr('src', $(this).find('img').attr('src'));
                $('#imagemodal').modal('show');
            });
        });
    </script>
@endpush

{{-- my task --}}
@push('scripts')
    <script>
        $(document).ready(function() {
            $('.editBtn').on('click', function(e) {
                e.preventDefault();
                const rowId = $(this).data('id');
                const tindakan = $(this).data('tindakan');
                const keterangan = $(this).data('keterangan');

                $('#id_permintaan_obat').val(rowId);
                $('#tindakan').val(tindakan);
                $('#keterangan').val(keterangan);
                $('#editModal').modal('show');
            });
        });
    </script>
@endpush
