@extends('layouts.base-display')

@section('title', 'REPORT TRACKING VARIAN')


    @push('styles')
        <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
    @endpush

@section('content')

    <div class="container-fluid">
        <div class="row">

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success"
                        href="/logging_machine/adm_prod/report">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
                </li>
            </ul>
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-tittle">
                            <form action="{{url('/logging_machine/adm_prod/report_varian')}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <label for="exampleFormControlSelect1">Varian</label>
                                        <select class="form-control Varian ml-4" name="varian_rasa">
                                            <option disabled selected>Silahkan Pilih</option>
                                            @foreach ($list_varian as $val)
                                                <option value="{{ $val->varian_rasa }}">{{ $val->varian_rasa }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-6">
                                        <input type="date" class="form-control" placeholder="Tanggal Mulai"
                                            name="tgl_mulai">
                                    </div>
                                    <div class="col mt-4">
                                        <input type="date" class="form-control" placeholder="Tanggal Selesai"
                                            name="tgl_selesai">
                                    </div>
                                </div>
                                 <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info btn-sm btn-block mt-2"><i
                                                class="fas fa-search"></i>
                                                Cari</button>
                                            </div>
                                        </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="grafik">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No.</th>
                                                <th class="text-center">Nama Operator</th>
                                                <th class="text-center">No. Mesin</th>
                                                <th class="text-center">Tanggal Produksi</th>
                                                <th class="text-center">Shift/Group</th>
                                                <th class="text-center">Produksi Box</th>
                                                <th class="text-center">Produksi Pcs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($group as $item)
                                                <tr>
                                                    <td class="text-center">
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->nama }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->no_mesin }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ \Carbon\Carbon::parse($item->tgl_pengisian)->format('d-M-Y') }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->shift_group }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->total_produksi_box }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $item->hasil_produksi_pcs }}
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

    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script src="{{ url('/') }}/assets/js/highcharts/highcharts.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/data.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/drilldown.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/exporting.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/export-data.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/accessibility.js"></script>

    <script type="text/javascript">
        $('.table').DataTable();

        $('.Varian').select2({
            placeholder: 'Select an option'
        });


        Highcharts.chart('grafik', {
            title: {
                text: 'Grafik Performa Produksi Tanggal {{ \Carbon\Carbon::parse($tgl_mulai)->format('d-M-Y') }} Sampai {{ \Carbon\Carbon::parse($tgl_selesai)->format('d-M-Y') }} '
            },

            legend: {
                title: {
                    text: 'Total Keseluruhan : {{ $total_box }} BOX<br/><span style="font-size: 9px; color: #666; font-weight: normal"></span>',
                    style: {
                        fontStyle: 'italic'
                    }
                },
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                layout: 'vertical',
                backgroundColor: '#FCFFC5',
                borderColor: '#C98657',
                borderWidth: 1
            },
            subtitle: {
                text: '{{ $req_rasa }}'
            },
            xAxis: {
                categories: {!! json_encode($rasa_varian) !!}
            },
            series: [{
                    type: 'column',
                    name: 'VARIAN RASA',
                    data: {!! json_encode($data_hasil_produksi) !!}

                },
            ]
        });

    </script>

@endpush
