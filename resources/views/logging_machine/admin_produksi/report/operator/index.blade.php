@extends('layouts.base-display')

@section('title', 'REPORT DOWNTIME OPERATOR')


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
                              <form action="{{ url('/logging_machine/adm_prod/report_operator') }}" method="POST">
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
                       <div id="grafik">
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
                                      <th class="text-center">Kerusakan</th>
                                      <th class="text-center">Jam Mulai Perbaikan</th>
                                      <th class="text-center">Jam Selesai Perbaikan</th>
                                      <th class="text-center">Remaining Time</th>
                                  </tr>
                              </thead>
                              <tbody>
                                   @foreach ($list as $item)
                                     <tr>
                                          <td class="text-center">
                                               {{$loop->iteration}}
                                          </td>
                                          <td class="text-center">
                                               {{$item->approval_maintenance_nama}}
                                          </td>
                                          <td class="text-center">
                                               {{$item->no_mesin}}
                                          </td>
                                          <td class="text-center">
                                               {{$item->kerusakan}}
                                          </td>
                                          <td class="text-center">
                                               {{$item->jam_pengisian. " ". "WIB"}}
                                          </td>
                                          <td class="text-center">
                                               {{$item->jam_selesai_maintenance. " ". "WIB"}}
                                          </td>
                                           <td class="text-center">
                                                @php
                                                    $waktuawal = date_create($item->jam_pengisian); //waktu di setting
                                                    
                                                    $waktuakhir = date_create($item->jam_selesai_maintenance); //2019-02-21 09:35 waktu sekarang
                                                    
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

//     Highcharts.chart('grafik', {
//     chart: {
//         type: 'column'
//     },
//     title: {
//         text: 'GRAFIK DATA PENGERJAAN DOWNTIME MAINTENANCE'
//     },
//     subtitle: {
//         text: 'Seassoning 2'
//     },
//     xAxis: {
//         min: 0,
        
//         categories: 
//             {!! json_encode($nama_maintenance) !!},
//     },
//     yAxis: {
//         min: 0,
       
//     },
//     tooltip: {
//         headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
//         pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
//             '<td style="padding:0"><b></b></td></tr>',
//         footerFormat: '</table>',
//         shared: true,
//         useHTML: true
//     },
//     plotOptions: {
//         column: {
//             borderWidth: 0
//         }
//     },
//     series: [{
//         name: 'NAMA MAINTENANCE',
//         data: {!! json_encode($total)!!}
//     }]
// });

  Highcharts.chart('grafik', {
            title: {
                text: 'Grafik Downtime Operator {{ date('d-M-Y') }}'
            },
            xAxis: {
                categories: {!! json_encode($nama_maintenance) !!}
            },
            series: [{
                    type: 'column',
                    name: 'NAMA ',
                    data: {!! json_encode($total) !!}

                },
            ]
        });
</script>

@endpush
