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
                            <h3 class="card-label">Report Checklist Fly Catcher
                                <span class="d-block text-muted pt-2 font-size-sm">Report hasil pengecekan fly catcher all area PT. PAS</span></h3>
                        </div>
                        <div class="card-toolbar">
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>Filter</h4>
                        <div class="row filter">
                            <div class="col-md-1">
                                <input type="number" class="form-control" name="filter_year" id="filter-year">
                            </div>
                            <div class="col-md-2">
                                <select name="filter_month" id="filter-month" class="form-control">
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="filter_dept" id="filter-dept" class="form-control">
                                    <option value="">Pilih Department</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-md-2">
                                <a id="generate-button" href="{{ url('/') }}" class="btn btn-success">Generate</a>
                            </div> --}}
                        </div>
                        <br>
                        <div class="example-preview">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" onclick="triggerApex()">
                                        <span class="nav-icon">
                                            <i class="flaticon2-graphic"></i>
                                        </span>
                                        <span class="nav-text">Chart</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile">
                                        <span class="nav-icon">
                                            <i class="flaticon2-list-2"></i>
                                        </span>
                                        <span class="nav-text">Raw Data</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <div id="chart-report"></div>
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <section class="filter col-md-1">
                                        <select name="filter_week" id="filter-week" class="form-control">
                                            <option value="0">Week</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                        </select>
                                    </section>
                                    <br>
                                    <section id="table-data" class="col-md-12">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Dept</th>
                                                    <th>ID</th>
                                                    <th>Week</th>
                                                    <th width="400">Lokasi</th>
                                                    <th>Kondisi</th>
                                                    <th>Jumlah Pest</th>
                                                    <th>Keterangan</th>
                                                    <th>Cek Oleh</th>
                                                    <th>Waktu Cek</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

@endsection

@push('scripts')
<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

<script>
    var date = new Date();
    $(".filter #filter-year").val(moment(date).format('Y'));
    $(".filter #filter-month").val(moment(date).format('MM'));
    // $(".filter #filter-month").val('06');
    $(".filter #filter-year").datepicker({
        minViewMode: 2,
        format: 'yyyy',
        autoclose: true
    });

    function triggerApex()
    {
        setTimeout(() => {
            $(".filter #filter-month").change();
        }, 1000);
    }

    var table;
    var options = {
        legend: {
            show: true,
            position: 'top'
        },
        colors:['#111d5e', '#c70039', '#f37121', '#ffbd69'],
        series: [],
        chart: {
            height: 500,
            type: 'bar',
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    position: 'top', // top, center, bottom
                },
            }
        },
        dataLabels: {
            enabled: true,
            offsetY: -20,
            style: {
                fontSize: '10px',
                colors: ["#304758"]
            }
        },
    
        xaxis: {
            categories: [],
            position: 'bottom',
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                    }
                }
            },
            tooltip: {
                enabled: false,
            }
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false,
            },
            labels: {
                show: true,
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart-report"), options);
    chart.render();

    function getData()
    {
        var year 	= $(".filter #filter-year").val();
		var month 	= $(".filter #filter-month").val();
		var week 	= $(".filter #filter-week").val();
		var dept    = $(".filter #filter-dept").val();
		table       = $("#table-data table").DataTable({
			ajax : "{{ URL::to('/checklist/fly_catcher/report/data') }}/"+year+"/"+month+"/"+week+"/"+dept
		});
        getChart(year, month, dept);
    }

    getData();

    function updateData()
    {
        var month = $(".filter #filter-month").val();
        var week = $(".filter #filter-week").val();
        var year = $(".filter #filter-year").val();
        var dept = $(".filter #filter-dept").val();
        table.ajax.url("{{ URL::to('/checklist/fly_catcher/report/data') }}/"+year+"/"+month+"/"+week+"/"+dept).load();
        getChart(year, month, dept);
    }

    function getChart(year, month, dept)
    {
        // console.log(month);
        var url = "{{ route('fly-catcher-report-chart', ['month' => ':month', 'year' => ':year', 'dept' => ':dept']) }}";
        url = url.replace(':month', month);
        url = url.replace(':year', year);
        url = url.replace(':dept', dept);
        $.ajax({
            url: url,
            type: "GET",
            dataType: "JSON",
            success: function ( response ) {
                // console.log( response.data );
                chart.updateSeries(response.data, true)
            },
            error: function ( error ) {
                console.log( error );
            }
        });
    }

    $(".filter #filter-month").change(function() {
        updateData();
    });

    $(".filter #filter-dept").change(function() {
        updateData();
    });
    $(".filter #filter-week").change(function() {
        updateData();
    });
    $(".filter #filter-year").change(function() {
        updateData();
    });

</script>

@endpush