@extends('layouts.base-display')

@section('title', 'MONITORING DOWNTIME')

    @push('styles')
        <style type="text/css">
            .hide {
                display: none;
            }

            .message {
                transition-duration: 0.7ms;
            }

        </style>
    @endpush

@section('content')

    {{-- <meta http-equiv="refresh" content="300" /> --}}

    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            @foreach ($data as $list)
                                <div class="col-sm-2">
                                    <div id="CardDowntime-{{ $list['id'] }}" class="card m-1 CardDowntime p-0 rounded"
                                        style="background-color: @if ($list['downtime'])  @if ($list['status']==2) red
                                    @else goldenrod @endif
                                    @else green @endif;">
                                        <div class="card-body pt-1 pb-0">
                                            <a href="#"
                                                class="text-inverse-primary font-weight-bold font-size-lg mt-1 text-center">
                                                <h1 class="text-white font-semibolt">{{ $list['mesin']->no_mesin }}</h1>
                                            </a>
                                        </div>
                                    </div>
                                    <!--end::Tiles Widget 11-->
                                </div>
                            @endforeach
                            <hr />
                            <!--end::Tiles Widget 11-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table_monitoring table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No.</th>
                                        <th class="text-center">Nama Pengaju</th>
                                        <th class="text-center">Shift/Group</th>
                                        <th class="text-center">No.Mesin</th>
                                        <th class="text-center">Kerusakan</th>
                                        <th class="text-center">Hari Permintaan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Remaining Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tabel as $val)
                                        <tr>
                                            <td class="text-center">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="text-center">
                                                {{ $val->nama }}
                                            </td>
                                            <td class="text-center">
                                                {{ $val->shift_group }}
                                            </td>
                                            <td class="text-center">
                                                {{ $val->no_mesin }}
                                            </td>
                                            <td class="text-center">
                                                {{ $val->kerusakan }}
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($val->tgl_pengisian)->format('d-M-Y') . ' ' . $val->jam_pengisian }}
                                            </td>
                                            <td class="text-center">
                                                @if ($val->status == 2)
                                                    <span class="badge badge-pill badge-primary"> Belum Di Respon
                                                        Maintenance</span>
                                                @else
                                                    <span class="badge badge-pill badge-warning"> Proses
                                                        Perbaikan</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $waktuawal = date_create($val->jam_pengisian); //waktu di setting
                                                    
                                                    $waktuakhir = date_create(); //2019-02-21 09:35 waktu sekarang
                                                    
                                                    $diff = date_diff($waktuawal, $waktuakhir);
                                                    // echo $diff->h . 'JAM' . ' ' . $diff->i . 'MENIT';
                                                @endphp
                                                <span class="badge badge-pill badge-primary">
                                                    {{ $diff->h . ' ' . 'JAM' . ' ' . $diff->i . ' ' . 'MENIT' }}
                                                </span>
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


@endsection

@push('scripts')

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            get_data_monitoring();
            setInterval(function() {
                get_data_monitoring()
            }, 300000);
        });

        function get_data_monitoring() {
            $.ajax({
                url: '{{ URL::to('/maintenance/get_ajax_monitoring/') }}',
                type: 'get',
                success: function(response) {
                    if (response.status == 1) {
                        $(".CardDowntime").css("background-color", "green")
                        response.data.forEach(function(value, index) {
                            console.log(value.no_mesin);
                            if (value.status == 2) {
                                $("#CardDowntime-" + value.id + ".CardDowntime").css("background-color",
                                    "red")
                                $("#CardDowntime-" + value.id + ".CardDowntime").each(function() {
                                    var elem = $(this);
                                    setInterval(function() {
                                        if (elem.css('visibility') == 'hidden') {
                                            elem.css('visibility', 'visible');
                                        } else {
                                            elem.css('visibility', 'hidden');
                                        }
                                    }, 800);
                                });
                            }
                            if (value.status == 1) {
                                $("#CardDowntime-" + value.id + ".CardDowntime").css("background-color",
                                    "#ff9900")
                                $("#CardDowntime-" + value.id + ".CardDowntime").each(function() {
                                    var yellow = $(this);
                                    setInterval(function() {
                                        if (yellow.css('visibility') == 'hidden') {
                                            yellow.css('visibility', 'visible');
                                        } else {
                                            yellow.css('visibility', 'hidden');
                                        }
                                    }, 800);
                                });
                            }
                        });
                    }
                }
            });
        }

    </script>

@endpush
