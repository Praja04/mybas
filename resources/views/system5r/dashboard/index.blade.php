@extends('system5r.layouts.base')




@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.5/main.min.js"></script>
    <script
        src="
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin-top: 20px;
            background-color: #f7f7ff;
        }

        .radius-10 {
            border-radius: 10px !important;
        }

        .border-info {
            border-left: 5px solid #0dcaf0 !important;
        }

        .border-danger {
            border-left: 5px solid #fd3550 !important;
        }

        .border-success {
            border-left: 5px solid #15ca20 !important;
        }

        .border-warning {
            border-left: 5px solid #ffc107 !important;
        }


        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 0px solid rgba(0, 0, 0, 0);
            border-radius: .25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 6px 0 rgb(218 218 253 / 65%), 0 2px 6px 0 rgb(206 206 238 / 54%);
        }

        .bg-gradient-scooter {
            background: #17ead9;
            background: -webkit-linear-gradient(45deg, #17ead9, #6078ea) !important;
            background: linear-gradient(45deg, #17ead9, #6078ea) !important;
        }

        .widgets-icons-2 {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #ededed;
            font-size: 27px;
            border-radius: 10px;
        }

        .rounded-circle {
            border-radius: 50% !important;
        }

        .text-white {
            color: #fff !important;
        }

        .ms-auto {
            margin-left: auto !important;
        }

        .bg-gradient-bloody {
            background: #f54ea2;
            background: -webkit-linear-gradient(45deg, #f54ea2, #ff7676) !important;
            background: linear-gradient(45deg, #f54ea2, #ff7676) !important;
        }

        .bg-gradient-ohhappiness {
            background: #00b09b;
            background: -webkit-linear-gradient(45deg, #00b09b, #96c93d) !important;
            background: linear-gradient(45deg, #00b09b, #96c93d) !important;
        }

        .bg-gradient-blooker {
            background: #ffdf40;
            background: -webkit-linear-gradient(45deg, #ffdf40, #ff8359) !important;
            background: linear-gradient(45deg, #ffdf40, #ff8359) !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid ">
       
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <!-- card -->
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total Departemen</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-success fs-14 mb-0">
                                    <i class="ri-arrow-right-up-line fs-13 align-middle"></i> 100%
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="12">0</span></h4>
                                <a href="" class="text-decoration-underline">Lihat detail</a>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success rounded fs-3">
                                    <i class="bx bx-dollar-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <!-- card -->
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Office</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-danger fs-14 mb-0">
                                    <i class="ri-arrow-right-down-line fs-13 align-middle"></i> 100%
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="12">0</span></h4>
                                <a href="" class="text-decoration-underline">Lihat detail</a>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info rounded fs-3">
                                    <i class="bx bx-shopping-bag"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <!-- card -->
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Manufaktur</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-success fs-14 mb-0">
                                    <i class="ri-arrow-right-up-line fs-13 align-middle"></i> 100%
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="12">0</span> </h4>
                                <a href="" class="text-decoration-underline">Lihat detail</a>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded fs-3">
                                    <i class="bx bx-user-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <!-- card -->
                <div class="card card-animate">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1 overflow-hidden">
                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Periode 5R</p>
                            </div>
                            <div class="flex-shrink-0">
                                <h5 class="text-muted fs-14 mb-0">
                                  50%
                                </h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-end justify-content-between mt-4">
                            <div>
                                <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="1">Periode 1</span> </h4>
                                <a href="" class="text-decoration-underline">Lihat detail</a>
                            </div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-danger rounded fs-3">
                                    <i class="bx bx-wallet"></i>
                                </span>
                            </div>
                        </div>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div> <!-- end row-->
    </div>



    <div>
        <div class="container-fluid">
            <div class="row">


               



                <div class="col-md-8">
                    <div class="card card-custom shadow-sm border-light rounded-3">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bolder text-primary fs-5 mb-0" >
                                Nilai per Departemen
                            </h3>
                        </div>
                
                        <div class="card-body">
                            <!-- Filter Jadwal -->
                            <form action="" method="GET" class="mb-4">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-8">
                                        <select name="filter_jadwal" id="filter_jadwal" class="form-select">
                                            <option value="---">Pilih Jadwal</option>
                                            @foreach ($allJadwal as $_jadwal)
                                                <option value="{{ $_jadwal->id_jadwal }}"
                                                    {{ isset($_GET['filter_jadwal']) && $_GET['filter_jadwal'] == $_jadwal->id_jadwal ? 'selected' : '' }}>
                                                    {{ $_jadwal->tahun }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            Lihat Laporan
                                        </button>
                                    </div>
                                </div>
                            </form>
                
                            <!-- Chart Container -->
                            <div class="chart-container position-relative" style="height: 400px;">
                                @if(isset($departmentLabels) && count($departmentLabels) > 0)
                                    <canvas id="departmentChart" class="w-100 h-100"></canvas>
                                @else
                                    <div class="text-center text-muted mt-5">
                                        <p class="mb-0">Belum ada data nilai departemen.</p>
                                        <small>Silakan pilih jadwal untuk melihat hasil.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

               

                <div class="col-md-4">
                    <div class="card card-custom shadow-sm border-light rounded-3">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h3 class="card-title font-weight-bolder text-primary fs-5 mb-0" >
                                Perbandingan Nilai Departemen
                            </h3>
                        </div>
                
                        <div class="card-body">
                            <!-- Filter Jadwal -->
                            <form action="" method="GET" class="mb-4">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-6">
                                        <select name="filter_jadwal" id="filter_jadwal" class="form-select">
                                            <option value="---">Pilih Jadwal</option>
                                            @foreach ($allJadwal as $_jadwal)
                                                <option value="{{ $_jadwal->id_jadwal }}"
                                                    {{ isset($_GET['filter_jadwal']) && $_GET['filter_jadwal'] == $_jadwal->id_jadwal ? 'selected' : '' }}>
                                                    {{ $_jadwal->tahun }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            Lihat Laporan
                                        </button>
                                    </div>
                                </div>
                            </form>
                
                            <!-- Chart Container -->
                            <div class="chart-container position-relative" style="height: 400px;">
                                @if(isset($departmentLabels) && count($departmentLabels) > 0)
                                    <canvas id="" class="w-100 h-100"></canvas>
                                @else
                                    <div class="text-center text-muted mt-5">
                                        <p class="mb-0">Belum ada data nilai departemen.</p>
                                        <small>Silakan pilih jadwal untuk melihat hasil.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                


             

                <div class="col-md-12 mt-4">
                    <div class="card shadow-sm border-light rounded-3">
                        <div class="card-header bg-white py-3">
                            <h3 class="card-title font-weight-bolder text-primary fs-5 mb-0">📊 Track Perbandingan Nilai Antar Periode</h3>
                        </div>
                
                        <div class="card-body">
                
                            <!-- Dropdown Pilih Departemen -->
                            <form method="GET" action="{{ url()->current() }}" class="mb-4">
                                <div class="row">
                                    <!-- Filter Jadwal -->
                                    <div class="col-md-4">
                                        <label for="filter_jadwal">Pilih Jadwal:</label>
                                        <select name="filter_jadwal" id="filter_jadwal" class="form-control">
                                            <option value="">-- Pilih Jadwal --</option>
                                            @foreach ($allJadwal as $jadwal)
                                                <option value="{{ $jadwal->id_jadwal }}" {{ request('filter_jadwal') == $jadwal->id_jadwal ? 'selected' : '' }}>
                                                    {{ $jadwal->nama_jadwal }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                            
                                    <!-- Filter Departemen -->
                                    <div class="col-md-4">
                                        <label for="filter_department">Pilih Departemen:</label>
                                        <select name="filter_department" id="filter_department" class="form-control">
                                            <option value="">-- Pilih Departemen --</option>
                                            @foreach ($allDepartments as $department)
                                                <option value="{{ $department->id_department }}" {{ request('filter_department') == $department->id_department ? 'selected' : '' }}>
                                                    {{ $department->nama_department }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                            
                                    <!-- Tombol Submit -->
                                    <div class="col-md-4 align-self-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                
                            <!-- Tempat Chart -->
                            <div class="chart-container" style="height: 400px;">
                                <canvas id="departmentPeriodBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>


            </div>



           


    </div>

   
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var ctx = document.getElementById('departmentChart').getContext('2d');
            
            var departmentData = {
                labels: {!! json_encode($departmentLabels) !!},
                datasets: [{
                    label: 'Nilai Total Departemen',
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    data: {!! json_encode($departmentValues) !!},
                    hoverBackgroundColor: 'rgba(75, 192, 192, 0.8)',
                }]
            };
    
            new Chart(ctx, {
                type: 'bar',
                data: departmentData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Nilai Total: ' + context.raw.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Nilai Total'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Departemen'
                            }
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(isset($chartData) && count($chartData) > 0 && isset($periodLabels))
                const ctx = document.getElementById('PerbandinganNilai').getContext('2d');
    
                const datasets = {!! json_encode(array_values($chartData)) !!};
    
                const backgroundColors = [
                    'rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ];
    
                const borderColors = [
                    'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ];
    
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($periodLabels) !!},
                        datasets: datasets.map((dept, i) => ({
                            label: dept.label,
                            data: dept.data,
                            backgroundColor: backgroundColors[i % backgroundColors.length],
                            borderColor: borderColors[i % borderColors.length],
                            borderWidth: 1,
                            borderRadius: 5
                        }))
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Nilai Total' }
                            },
                            x: {
                                title: { display: true, text: 'Periode' }
                            }
                        }
                    }
                });
            @endif
        });
    </script>

<script>
    const departmentPeriodLabels = @json($departmentPeriodLabels);
    const departmentPeriodValues = @json($departmentPeriodValues);

    const ctx = document.getElementById('departmentPeriodBarChart').getContext('2d');
    const departmentPeriodBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: departmentPeriodLabels,
            datasets: [{
                label: 'Nilai Per Periode',
                data: departmentPeriodValues,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Nilai Departemen Per Periode',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
</script>

    

   
 

   


 @endsection
  







