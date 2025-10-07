@extends('layouts.base-display')

@section('title', 'REPORT DOWNTIME MESIN')


@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')

    <div class="container-fluid">
        <div class="row">

          <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
               <!--begin::Item-->
               <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos" data-placement="right">
                 <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="/logging_machine/adm_prod/report">
                   <i class="fas fa-arrow-left"></i>
                 </a>
               </li>
               </li>
             </ul>
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                         <div class="card-tittle">
                              <form action="{{ url('/logging_machine/adm_prod/report_downtime') }}" method="POST">
                                   @csrf
                                   <div class="row">
                                     <div class="col">
                                       <input type="date" class="form-control" placeholder="Tanggal Mulai" name="tgl_mulai">
                                     </div>
                                     <div class="col">
                                       <input type="date" class="form-control" placeholder="Tanggal Selesai" name="tgl_selesai">
                                     </div>
                                   </div>
                                   <div class="form-group">
                                        <button type="submit" class="btn btn-info btn-sm mt-2"><i class="fas fa-search"></i> Cari</button>
                                   </div>
                                 </form>
                              </div>
                         </div>
                    <div class="card-body">
                       
                       
                         <div class="row">
                              <div class="col-sm-12">
                                   <div id="grafik">
     
                                   </div>
                              </div>
                         </div>

                         <div class="table-responsive">
                              <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th class="text-center">Nama Pengaju</th>
                                    <th class="text-center">No. Mesin</th>
                                    <th class="text-center">Tanggal Permintaan</th>
                                    <th class="text-center">Jam Permintaan</th>
                                    <th class="text-center">Kerusakan</th>
                                    <th class="text-center">Jenis Maintenance</th>
                                    <th class="text-center">Nama Maintenance</th>
                                    <th class="text-center">Jam Mulai Perbaikan</th>
                                    <th class="text-center">Jam Selesai Perbaikan</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                 @foreach ($list as $item)
                                   <tr>
                                        <td class="text-center">
                                             {{$loop->iteration}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->nama}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->no_mesin}}
                                        </td>
                                        <td class="text-center">
                                             {{\Carbon\Carbon::parse($item->tgl_pengisian)->format('d-M-Y')}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->jam_pengisian. " ". "WIB"}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->kerusakan}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->jenis_maintenance}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->approval_maintenance_nama}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->jam_mulai_maintenance. " ". "WIB"}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->jam_selesai_maintenance. " ". "WIB"}}
                                        </td>
                                        <td class="text-center">
                                             {{$item->progress}}
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

<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

     <script src="{{ url('/') }}/assets/js/highcharts/highcharts.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/data.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/drilldown.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/exporting.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/export-data.js"></script>
    <script src="{{ url('/') }}/assets/js/highcharts/accessibility.js"></script>

<script type="text/javascript">
    $('.table').DataTable();

    Highcharts.chart('grafik', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'GRAFIK REPORT DOWNTIME MESIN'
    },
    subtitle: {
        text: 'Seassoning 2'
    },
    xAxis: {
        categories: ['Start','Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
    },
    yAxis: {
        title: {
            text: ''
        }
    },
    plotOptions: {
        line: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: false
        }
    },
    series: [{
        name: 'GRAFIK PERMINTAAN DOWNTIME',
        data: {!! json_encode($data) !!}
     }]
});

</script>

@endpush
