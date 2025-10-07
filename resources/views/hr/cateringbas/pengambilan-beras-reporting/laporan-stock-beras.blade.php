@extends('hr.cateringbas.pengambilan-beras.layouts.app')

@section('title', 'Dashboard')

@push('after-style')
    <style>
        @media (max-width: 767px) {
            .karung-image {
                display: none;
            }

            .image-content {
                display: none;
            }
        }

        .karung-image {
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
        }

        .karung-image:hover {
            transform: scale(1.1);
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            60% {
                transform: translateY(-10px);
            }
        }

        #chart {
            max-width: 90%;
            max-height: 60%;
            margin: 35px auto;
        }

        /* spin */
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
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 80px;
            height: 80px;
            animation: spin 1s linear infinite;
        }

        /* Animation for the loader */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endpush

@section('content')
    {{-- <div id="loading-overlay">
        <div class="loader"></div>
    </div> --}}
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Stock Beras</h3>
        </div>
        <div class="page-content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header flex-wrap border-0 pt-6 pb-0">
                            <div class="card-title pt-2 pb-2">
                                <h3 class="card-label">Statistik Data<span style="color: red"> Beras</span></h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="chartContainer" style="height: 350px;"></div>
                            {{-- disini chart nya --}}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> --}}
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script type="text/javascript">
        var rawData = {!! json_encode($chartData) !!};
    
        function processChartData(rawData) {
            var processedDataIn = [];
            var processedDataOut = [];
            var processedDataStockBefore = [];
            var processedDataStockAfter = [];
    
            rawData.forEach(function(item) {
                var timestamp = (new Date(item.tanggal)).getTime();
                
                if(item.status === 'in' && item.transaksi_masuk !== null) {
                    processedDataIn.push([timestamp, parseFloat(item.transaksi_masuk)]);
                }
    
                if(item.status === 'out' && item.transaksi_keluar !== null) {
                    processedDataOut.push([timestamp, -parseFloat(item.transaksi_keluar)]);
                }
    
                processedDataStockBefore.push([timestamp, parseFloat(item.jumlah_stock)]);
                processedDataStockAfter.push([timestamp, parseFloat(item.jumlah_stock_sesudah)]);
            });
            
            return { 
                in: processedDataIn,
                out: processedDataOut,
                stockBefore: processedDataStockBefore,
                stockAfter: processedDataStockAfter
            };
        }
        
        var chartData = processChartData(rawData);
    
        Highcharts.chart('chartContainer', {
            chart: {
                type: 'line',
                zoomType: 'x'
            },
            title: {
                text: 'Grafik Kedatangan Beras'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    hour: '%H:%M:%S'
                },
                title: {
                    text: 'Tanggal dan Waktu'
                }
            },
            yAxis: {
                title: {
                    text: 'Jumlah Beras (sak)'
                }
            },
            series: [{
                name: 'Transaksi Masuk',
                data: chartData.in,
                color: 'green'
            }, {
                name: 'Transaksi Keluar',
                data: chartData.out,
                color: 'red'
            }, {
                name: 'Jumlah Stock Sebelum',
                data: chartData.stockBefore,
                color: 'blue'
            }, {
                name: 'Jumlah Stock Sesudah',
                data: chartData.stockAfter,
                color: 'orange'
            }],
            tooltip: {
                xDateFormat: '%d %b %Y %H:%M:%S',
                pointFormat: '<span style="color:{series.color}">\u25CF</span> {series.name}: <b>{point.y}</b><br/>',
                shared: true
            }
        });
    </script>
    
    
    

    <script>
        $(document).ready(function() {
            $('#loading-overlay').show();
            $(window).on('load', function() {
                $('#loading-overlay').fadeOut('slow');
            });
        });
    </script>

    
@endpush
