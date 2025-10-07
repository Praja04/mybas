@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@push('styles')
    <style type="text/css">
        .highcharts-figure, .highcharts-data-table table {
            min-width: 310px;
            margin: 1em auto;
        }
        #container {
            height: 600px;
        }
        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #EBEBEB;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }
        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }
        .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
            padding: 0.5em;
        }

    </style>
@endpush

@section('content')

    <div class="container-fluid">
        @if(in_array('report_cek_suhu_all', $permissions))
        <!--begin::Row-->
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Report Cek Suhu
                                <span class="d-block text-muted pt-2 font-size-sm">Report hasil pengecekan suhu seluruh department</span></h3>
                        </div>
                        <div class="card-toolbar">
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>Filter</h4>
                        <div class="row">
                            <div class="col-md-2">
                                <input class="form-control" type="date" id="filter-tanggal">
                            </div>
                            <div class="col-md-2">
                                <select id="filter-divisi" class="form-control">
                                    @foreach($divisi as $d)
                                        <option @if($d->{'Kode Divisi'} == 'PRO') selected @endif value="{{ $d->{'Kode Divisi'} }}">{{ $d->{'Kode Divisi'} }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filter-shift" class="form-control">
                                    <option value="N">Non Shift</option>
                                    <option value="S1">Shift 1</option>
                                    <option value="S2">Shift 2</option>
                                    <option value="S3">Shift 3</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a id="generate-button" href="{{ url('/') }}" class="btn btn-success">Generate</a>
                            </div>
                        </div>
                        <hr>
                        <figure class="highcharts-figure">
                            <div id="container"></div>
                        </figure>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(in_array('report_cek_suhu_department', $permissions))
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Report Cek Suhu
                                <span class="d-block text-muted pt-2 font-size-sm">Report hasil pengecekan suhu department</span>
                            </h3>
                        </div>
                        <div class="card-toolbar">
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>Filter</h4>
                        <div class="row">
                            <div class="col-md-2">
                                <input class="form-control" type="date" id="filter-tanggal-department">
                            </div>
                        </div>
                        <hr>
                        <table id="table-per-department" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Suhu</th>
                                    <th>Waktu Scan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_per_department as $key => $_data)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ formatTanggalIndonesia($_data->tanggal) }}</td>
                                        <td>{{ $_data->nik }}</td>
                                        <td>{{ $_data->nama }}</td>
                                        <td>{{ $_data->suhu }}</td>
                                        <td>{{ $_data->waktu_scan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a id="export-excel-link" href="javascript:" class="btn btn-outline-success"><i class="fa fa-file-excel"></i> Export Excel</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

@endsection

@push('scripts')

    @if(in_array('report_cek_suhu_all', $permissions))
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    @endif
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    @if(in_array('report_cek_suhu_department', $permissions))
    <script type="text/javascript">
        $('#filter-tanggal-department').on('change', function() {
            var tanggal = $(this).val();
            location.href = "{{ url('/cek-suhu/report') }}/"+tanggal;
            generateExportExcel(tanggal);
        });
        $('#filter-tanggal-department').val('{!! $filter[0] !!}');
        generateExportExcel('{!! $filter[0] !!}');
        $('#table-per-department').DataTable();

        function generateExportExcel(tanggal)
        {
            $("#export-excel-link").attr("href", "{{ url('/cek-suhu/report/export-excel') }}/"+tanggal);
        }
    </script>

    @endif

    @if(in_array('report_cek_suhu_all', $permissions))
    <script type="text/javascript">
        $("#table-per-department").dataTable();

        let filter_tanggal_default = '{!! $filter[0] !!}';
        let filter_divisi_default = '{!! $filter[1] !!}';
        let filter_shift_default = '{!! $filter[2] !!}';

        $('#filter-tanggal').val(filter_tanggal_default);
        $('#filter-divisi').val(filter_divisi_default);
        $('#filter-shift').val(filter_shift_default);

        changeGenereteParameter()

        function changeGenereteParameter() {
            let filter_tanggal = $('#filter-tanggal').val();
            let filter_divisi = $('#filter-divisi').val();
            let filter_shift = $('#filter-shift').val();
            $('#generate-button').attr('href','{{ url('/cek-suhu/report') }}/'+filter_tanggal+'/'+filter_divisi+'/'+filter_shift);
        }

        $('#filter-tanggal').on('change', function () {
            changeGenereteParameter();
        });

        $('#filter-divisi').on('change', function () {
            changeGenereteParameter();
        });
        
        $('#filter-shift').on('change', function () {
            changeGenereteParameter();
        });

        // Untuk style chart
        (function(a){
            "object" === typeof module&&module.exports ?
                (a["default"]=a,module.exports=a) :
                "function"===typeof define&&define.amd ?
                    define("highcharts/themes/sand-signika",["highcharts"],function(b){a(b);a.Highcharts=b;return a}) :
                    a("undefined"!==typeof Highcharts ?
                        Highcharts :
                        void 0)
        })
        (function(a){
            function b(a,c,b,d){
                a.hasOwnProperty(c)||(a[c]=d.apply(null,b))
            } a=a ?
                a._modules:
                {};
            b(a,"Extensions/Themes/SandSignika.js",[a["Core/Globals.js"],a["Core/Utilities.js"]], function(a,b){
                b=b.setOptions;
                a.addEvent(a.Chart,"afterGetContainer", function(){
                    this.container.style.background="url({{ asset('/assets/media/bg/sand.png') }})"
            });
            a.theme={
                colors:"#636363 #027d00 #87051f #000000".split(" "),
                chart:{
                    backgroundColor:null,
                    style:{
                        fontFamily:"Arial"
                    }
                },
                title:{
                    style:{
                        color:"black",
                        fontSize:"24px",
                        fontWeight:"bold"
                    }
                },
                subtitle:{
                    style:{
                        color:"black"
                    }
                },
                tooltip:{
                    borderWidth:0
                },
                labels:{
                    style:{
                        color:"#6e6e70"
                    }
                },
                legend:{
                    backgroundColor:"#E0E0E8",
                    itemStyle:{
                        fontWeight:"bold",
                        fontSize:"13px"
                    }
                },
                xAxis:{
                    labels:{
                        style:{
                            color:"#6e6e70"
                        }
                    }
                },
                yAxis:{
                    labels:{
                        style:{
                            color:"#6e6e70"
                        },
                    },
                },
                plotOptions:{
                    series:{
                        shadow:!0
                    },
                    candlestick:{
                        lineColor:"#404048"
                    },
                    map:{
                        shadow:!1
                    }
                },
                navigator:{
                    xAxis:{
                        gridLineColor:"#D0D0D8"
                    }
                },
                rangeSelector:{
                    buttonTheme:{
                        fill:"white",
                        stroke:"#C0C0C8",
                        "stroke-width":1,
                        states:{
                            select:{
                                fill:"#D0D0D8"
                            }
                        }
                    }
                },
                scrollbar:{
                    trackBorderColor:"#C0C0C8"
                }
            };
            b(a.theme)
        });
        b(a,"masters/themes/sand-signika.src.js",[],function(){})
    });
    </script>
    <script type="text/javascript">
        Highcharts.chart('container', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Laporan Hasil Pengecekan Suhu'
            },
            subtitle: {
                text: 'Pengecekan suhu tanggal : '+filter_tanggal_default+' shift : '+filter_shift_default+' divisi : '+filter_divisi_default
            },
            xAxis: {
                categories: {!! json_encode($bagian) !!},
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Orang',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ' orang'
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {},
            credits: {
                enabled: false
            },
            series: [{
                name: 'BELUM DI CEK',
                data: {!! json_encode($belum_scan) !!}
            }, {
                name: 'OK',
                data: {!! json_encode($ok) !!}
            }, {
                name: 'TIDAK OK',
                data: {!! json_encode($not_ok) !!}
            },
            {
                name: 'TOTAL',
                data: {!! json_encode($jumlah_karyawan) !!}
            }]
        });
    </script>
    @endif

@endpush
