@extends('system5r.layouts.base')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">

        {{-- WIDGET KPI --}}
        <div class="row" id="widget-row">

            <div class="col-xl-3 col-md-6">
                <div data-aos="fade-up">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Workspace</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="total-workspace">0</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="ri-building-line text-white fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div data-aos="fade-up">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Department</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="total-department">0</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-success">
                                        <i class="ri-group-line text-white fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div data-aos="fade-up">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Group</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="total-group">0</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-warning">
                                        <i class="ri-stack-line text-white fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div data-aos="fade-up">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <p class="text-uppercase fw-medium text-muted mb-0">Area</p>
                                    <h4 class="fs-22 fw-semibold mb-0" id="total-area">0</h4>
                                </div>
                                <div class="avatar-sm">
                                    <span class="avatar-title rounded-circle bg-danger">
                                        <i class="ri-map-pin-line text-white fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart WorkSpace Periode --}}
        <div class="row">
            @foreach ($workspaces as $ws)
                <div data-aos="fade-up" data-aos-delay="200">
                    <div class="card card-animate">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"> Nilai 5R – {{ $ws->name }}</h5>

                            <select class="form-select form-select-sm w-auto jadwalChartPeriode" id="jadwalChartPeriode"
                                data-workspace="{{ $ws->id_workspace }}">
                                @foreach ($allJadwal as $j)
                                    <option value="{{ $j->id_jadwal }}">{{ $j->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="card-body">
                            <canvas id="chart-{{ $ws->id_workspace }}" height="250"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Chart Rank --}}
        <div class="row">
            @foreach ($workspaces as $ws)
                <div class="col-md-6" data-aos="fade-up">
                    <div class="card card-animate">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Ranking 5R – {{ $ws->name }}</h5>

                            <select class="form-select form-select-sm w-auto jadwal-rank"
                                data-workspace="{{ $ws->id_workspace }}">
                                @foreach ($allJadwal as $j)
                                    <option value="{{ $j->id_jadwal }}">{{ $j->tahun }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="card-body">
                            <canvas id="rank-{{ $ws->id_workspace }}" height="300"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        $(document).ready(function() {
            const chartPerPeriode = {};
            const rankCharts = {};
            loadWidget();
            @foreach ($workspaces as $ws)
                loadChartPerPeriode(
                    'chart-{{ $ws->id_workspace }}',
                    '{{ $ws->id_workspace }}',
                    ''
                );

                loadRankingChart(
                    'rank-{{ $ws->id_workspace }}',
                    '{{ $ws->id_workspace }}',
                    $('#jadwalChartPeriode').val()
                );
            @endforeach

            function loadWidget() {
                $.ajax({
                    url: "{{ route('5r-system.dashboard.data-widget') }}",
                    type: "GET",
                    dataType: "json",
                    success: function(res) {
                        if (res.status !== 'success') return;

                        $('#total-workspace').text(res.widget.workspace);
                        $('#total-department').text(res.widget.department);
                        $('#total-group').text(res.widget.group);
                        $('#total-area').text(res.widget.area);
                    },
                    error: function() {
                        console.error('Gagal load widget dashboard');
                    }
                });
            }

            function loadChartPerPeriode(canvasId, workspaceId, jadwalId) {
                console.log(jadwalId);
                $.ajax({
                    url: "{{ route('5r-system.dashboard.data-periode') }}",
                    type: 'GET',
                    data: {
                        id_workspace: workspaceId,
                        id_jadwal: jadwalId
                    },
                    success: function(res) {

                        if (res.status !== 'success') return;

                        const canvas = document.getElementById(canvasId);
                        if (!canvas) return;

                        const ctx = canvas.getContext('2d');

                        // destroy biar ga dobel render
                        if (chartPerPeriode[canvasId]) {
                            chartPerPeriode[canvasId].destroy();
                        }

                        chartPerPeriode[canvasId] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: res.labels,
                                datasets: res.datasets.map((ds, idx) => ({
                                    label: ds.label,
                                    data: ds.data,
                                    backgroundColor: getColor(idx),
                                    borderRadius: 6,
                                }))
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let value = context.raw;
                                                return value === 0 ?
                                                    'Belum Dinilai' :
                                                    'Nilai: ' + value;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        title: {
                                            display: true,
                                            text: 'Nilai'
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            }

            function getColor(index) {
                const colors = [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ];
                return colors[index % colors.length];
            }

            $('.jadwalChartPeriode').each(function() {
                let jadwalId = $(this).val();
                let workspace = $(this).data('workspace');

                loadChartPerPeriode(
                    'chart-' + workspace,
                    workspace,
                    jadwalId
                );
            });

            $(document).on('change', '.jadwalChartPeriode', function() {
                let jadwalId = $(this).val();
                let workspace = $(this).data('workspace');

                loadChartPerPeriode(
                    'chart-' + workspace,
                    workspace,
                    jadwalId
                );
            });

            function getRankColor(index) {
                if (index === 0) return 'rgba(255, 215, 0, 0.9)';
                if (index === 1) return 'rgba(192, 192, 192, 0.9)'; // 🥈 Silver
                if (index === 2) return 'rgba(205, 127, 50, 0.9)'; // 🥉 Bronze
                return 'rgba(54, 162, 235, 0.7)';
            }

            function loadRankingChart(canvasId, workspaceId, jadwalId) {
                $.ajax({
                    url: "{{ route('5r-system.dashboard.rank-periode-workspace') }}",
                    type: 'GET',
                    data: {
                        id_workspace: workspaceId,
                        id_jadwal: jadwalId
                    },
                    success: function(res) {

                        if (res.status !== 'success') return;

                        const canvas = document.getElementById(canvasId);
                        if (!canvas) return;

                        const ctx = canvas.getContext('2d');

                        if (rankCharts[canvasId]) {
                            rankCharts[canvasId].destroy();
                        }

                        rankCharts[canvasId] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: res.labels,
                                datasets: [{
                                    data: res.data,
                                    backgroundColor: res.data.map((_, i) =>
                                        getRankColor(i)),
                                    borderRadius: 8
                                }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: ctx =>
                                                ctx.raw === 0 ?
                                                'Belum Dinilai' : `Nilai: ${ctx.raw}`
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        max: 100
                                    }
                                }
                            }
                        });
                    }
                });
            }

            $('.jadwal-rank').each(function() {
                const ws = $(this).data('workspace');
                loadRankingChart('rank-' + ws, ws, '');
            });

            $('.jadwal-rank').on('change', function() {
                const ws = $(this).data('workspace');
                const jadwalId = $(this).val();

                loadRankingChart('rank-' + ws, ws, jadwalId);
            });
        });
    </script>
@endpush
