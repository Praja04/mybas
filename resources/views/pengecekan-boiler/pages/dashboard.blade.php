@extends('pengecekan-boiler.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
    <style>
        /* hover list menu */
        .list-group-item:hover {
            background-color: #0d5eb5;
            color: white;
        }

        /* atur button show and hide speedometer dan linechart*/
        #showChartspeedmeter {
            --color: #0d5eb5;
            font-family: inherit;
            display: inline-block;
            width: 8em;
            height: 2.6em;
            line-height: 2.5em;
            margin: 20px;
            position: relative;
            overflow: hidden;
            border: 2px solid var(--color);
            transition: color .5s;
            z-index: 1;
            font-size: 17px;
            border-radius: 6px;
            font-weight: 500;
            color: var(--color);
            }

            #showChartspeedmeter:before {
            content: "";
            position: absolute;
            z-index: -1;
            background: var(--color);
            height: 150px;
            width: 200px;
            border-radius: 50%;
            }

            #showChartspeedmeter:hover {
            color: #fff;
            }

            #showChartspeedmeter:before {
            top: 100%;
            left: 100%;
            transition: all .7s;
            }

            #showChartspeedmeter:hover:before {
            top: -30px;
            left: -30px;
            }

            #showChartspeedmeter:active:before {
            background: #3a0ca3;
            transition: background 0s;
            }

            /* cursor pointer */
            #speedmeter, 
            #feedwatermeter, 
            #lhtempmeter, 
            #rhtempmeter, 
            #LHFDFans, 
            #RHFDFans, 
            #IDFans, 
            #LHStokers, 
            #RHStockers,
            #LHGuiloutines {
                cursor: pointer;
            }

            .list-group-numbered {
                cursor: pointer;
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

        <div class="page-heading d-flex justify-content-between align-items-center">
            <h3>Data Trend</h3>
            <div class="user-info" style="flex-grow: 1; text-align: right;">
                <i class="fa fa-user-circle" aria-hidden="true" style="font-size: 48px;"></i>
                <div class="user-email" style="margin-top: 8px;">
                    <h5> Hallo, {{ Auth::user()->name }}</h5>
                </div>
            </div>
        </div>

        <div class="page-content">
            <div class="col-12">

                <section class="pt-5 pb-5">
                    <div class="container" id="chartspeedmeter">
                        <div class="row">
                            <div class="col-12">
                                <div id="carouselExampleIndicators2" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner data-speedmeter">
                                        <div class="carousel-item active">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <figure class="highcharts-figure">
                                                                    <div id="speedmeter"></div>
                                                                </figure>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:25px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <figure class="highcharts-figure">
                                                                    <div id="feedwatermeter"></div>
                                                                </figure>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <figure class="highcharts-figure">
                                                                    <div id="lhtempmeter"></div>
                                                                </figure>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <figure class="highcharts-figure">
                                                                    <div id="rhtempmeter"></div>
                                                                </figure>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <figure class="highcharts-figure">
                                                                    <div id="LHFDFans"></div>
                                                                </figure>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <figure class="highcharts-figure">
                                                                    <div id="RHFDFans"></div>
                                                                </figure>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <div class="row">

                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <figure class="highcharts-figure">
                                                                    <div id="IDFans"></div>
                                                                </figure>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div class="card-content">
                                                                <div
                                                                    style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                    <figure class="highcharts-figure">
                                                                        <div id="LHStokers"></div>
                                                                    </figure>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                            <figure class="highcharts-figure">
                                                                <div id="RHStockers"></div>
                                                            </figure>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                            <figure class="highcharts-figure">
                                                                <div id="LHGuiloutines"></div>
                                                            </figure>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <i class="bi bi-speedometer"></i>
                                                            </div>
                                                            <div class="card-body text-center" style="padding-top: 45px;">
                                                                <h5 class="card-title"> INLET WATER FLOW </h5>
                                                                <p class="card-text"
                                                                    style="font-size: 14px; position:relative; top:20px; color:#3498db;">
                                                                    3.0 M3/H
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card" style="padding-left:20px; padding-right:20px;">
                                                        <div class="card-content">
                                                            <div
                                                                style="text-align: center; font-size: 36px; margin-top:30px;">
                                                                <i class="bi bi-speedometer"></i>
                                                            </div>
                                                            <div class="card-body text-center" style="padding-top: 45px;">
                                                                <h5 class="card-title"> OUTLET STEAM FLOW </h5>
                                                                <p class="card-text"
                                                                    style="font-size: 14px; position:relative; top:20px; color:#3498db;">
                                                                    3.0 M3/H
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <a class="btn btn-primary" style="margin-right: 20px;" href="#carouselExampleIndicators2"
                                role="button" data-slide="prev">
                                <i class="fa fa-arrow-left"></i>
                            </a>
                            <a class="btn btn-primary" href="#carouselExampleIndicators2" role="button"
                                data-slide="next">
                                <i class="fa fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Existing Alarm History card -->
                <section class="linechart pt-4">
                    <div>
                        <button id="showChartspeedmeter" style="display: none;">Kembali</button>
                    </div>
                    <div class="container">
                        <div class="row">
                            <!-- Alarm History Column -->
                            <div class="col-md-4">
                                <div class="card alarm-history-card" style="position: relative; display: none;">
                                    <div class="card-content">
                                        <div class="card-body" style="text-align: left;">
                                            <h3 class="card-title" style="font-size: 24px;">Data</h3>
                                            <ol class="list-group list-group-numbered">
                                                <li class="list-group-item">Steam Pressure</li>
                                                <li class="list-group-item">Level Feedwater</li>
                                                <li class="list-group-item">LH Temp</li>
                                                <li class="list-group-item">RH Temp</li>
                                                <li class="list-group-item">LH FD Fan</li>
                                                <li class="list-group-item">RH FD Fan</li>
                                                <li class="list-group-item">ID Fan</li>
                                                <li class="list-group-item">LH Stocker</li>
                                                <li class="list-group-item">RH Stocker</li>
                                                    {{-- <li class="list-group-item">Feedtank Temp</li>
                                                    <li class="list-group-item">Inlet Water Flow</li>
                                                    <li class="list-group-item">Outlet Steam Flow</li>  --}}
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Line Chart Column -->
                            <div class="col-md-8" id="daftar-line-chart">
                                <div class="card daftar-chart" style="display: none;">
                                    <div class="card-body">
                                        <!-- Button group for time range -->
                                        {{-- <div class="btn-group" role="group" aria-label="Time range">
                                            <button type="button" class="btn btn-outline-secondary" onclick="updateChart('1H')">1H</button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="updateChart('24H')">24H</button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="updateChart('1W')">1W</button>
                                        </div> --}}
                                        <!-- Highcharts container -->
                                        <div id="steamPressureChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="levelFeedwaterChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="LHTempChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="RHTempChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="LHFDFanChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="RHFDFanChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="IDFanChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="LHStokersChart" style="height: 200px; margin-top: 1rem;"></div>
                                        <div id="RHStockersChart" style="height: 200px; margin-top: 1rem;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
@endsection


@push('after-script')
    {{-- <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script> --}}
    <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
    {{-- selection id hide and show cardlist --}}
    <script>
        $(document).ready(function() {
            function showRelatedContent(cardId) {
                $('#chartspeedmeter').hide("slide", { direction: "up" }, 500);

                $('.alarm-history-card, .daftar-chart, #steamPressureChart, #levelFeedwaterChart, #LHTempChart, #RHTempChart, #LHFDFanChart, #RHFDFanChart, #IDFanChart, #LHStokersChart, #RHStockersChart')
                    .hide();
                $('.list-group-item').removeClass('active-line-chart');

                $('.alarm-history-card').show("slide", { direction: "down" }, 500);
                $('.daftar-chart').show("slide", { direction: "down" }, 500);

                switch (cardId) {
                    case 'speedmeter':
                        $('#steamPressureChart').show();
                        $('.list-group-item:contains("Steam Pressure")').addClass('active-line-chart');
                        break;
                    case 'feedwatermeter':
                        $('#levelFeedwaterChart').show();
                        $('.list-group-item:contains("Level Feedwater")').addClass('active-line-chart');
                        break;
                    case 'lhtempmeter':
                        $('#LHTempChart').show();
                        $('.list-group-item:contains("LH Temp")').addClass('active-line-chart');
                        break;
                    case 'rhtempmeter':
                        $('#RHTempChart').show();
                        $('.list-group-item:contains("RH Temp")').addClass('active-line-chart');
                        break;
                    case 'LHFDFans':
                        $('#LHFDFanChart').show();
                        $('.list-group-item:contains("LH FD Fan")').addClass('active-line-chart');
                        break;
                    case 'RHFDFans':
                        $('#IDFanChart').show();
                        $('.list-group-item:contains("RH FD Fan")').addClass('active-line-chart');
                        break;
                    case 'IDFans':
                        $('#IDFanChart').show();
                        $('.list-group-item:contains("ID Fan")').addClass('active-line-chart');
                        break;
                    case 'LHStokers':
                        $('#LHStokersChart').show();
                        $('.list-group-item:contains("LH Stocker")').addClass('active-line-chart');
                        break;
                    case 'RHStockers':
                        $('#RHStockersChart').show();
                        $('.list-group-item:contains("RH Stocker")').addClass('active-line-chart');
                        break;
                }

                $('#showChartspeedmeter').show("slide", { direction: "down" }, 500);
            }

            $('#showChartspeedmeter').click(function() {
                $('#chartspeedmeter').show("slide", { direction: "up" }, 500);
                $('.alarm-history-card, .daftar-chart').hide("slide", { direction: "up" }, 500);
                $('#showChartspeedmeter').hide("slide", { direction: "up" }, 500);
            });

            $('#speedmeter, #feedwatermeter, #lhtempmeter, #rhtempmeter, #LHFDFans, #RHFDFans, #IDFans, #LHStokers, #RHStockers').click(function() {
                showRelatedContent(this.id);
            });
            
        });
    </script>

    {{-- munculkan notifikasi pengembangan data sensor --}}
    <script>
        var image = new Image();
        image.src = 'assets/mazer/dist/assets/compiled/png/statistics.png';
    
        function showUpdateFiturLHGuiloutines() {
            Swal.fire({
                title: 'Info',
                text: 'Linechart LHGuiloutines masih dalam tahap pengembangan',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        function showUpdateFiturRHGuiloutine() {
            swal.fire({
                title: 'Info',
                text: 'Linechart RHGuiloutines masih dalam tahap pengembangan',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }
    
        document.getElementById('LHGuiloutines').addEventListener('click', showUpdateFiturLHGuiloutines);
        document.getElementById('RHGuiloutines').addEventListener('click', showUpdateFiturRHGuiloutine);
    </script>      
    
@endpush
