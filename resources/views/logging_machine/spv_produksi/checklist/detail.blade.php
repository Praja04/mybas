@extends('layouts.base-display')

@section('title', 'DASHBOARD DETAIL REPORT PACKING')

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }
        .message {
            transition-duration: 0.7ms;
        }


        /**THE SAME CSS IS USED IN ALL 3 DEMOS**/    
/**gallery margins**/  
ul.gallery{    
margin-left: 3vw;     
margin-right:3vw;  
}    

.zoom {      
-webkit-transition: all 0.35s ease-in-out;    
-moz-transition: all 0.35s ease-in-out;    
transition: all 0.35s ease-in-out;     
cursor: -webkit-zoom-in;      
cursor: -moz-zoom-in;      
cursor: zoom-in;  
}     

.zoom:hover,  
.zoom:active,   
.zoom:focus {
/**adjust scale to desired size, 
add browser prefixes**/
-ms-transform: scale(2.5);    
-moz-transform: scale(2.5);  
-webkit-transform: scale(2.5);  
-o-transform: scale(2.5);  
transform: scale(2.5);    
position:relative;      
z-index:100;  
}

/**To keep upscaled images visible on mobile, 
increase left & right margins a bit**/  
@media only screen and (max-width: 768px) {   
ul.gallery {      
margin-left: 15vw;       
margin-right: 15vw;
}

/**TIP: Easy escape for touch screens,
give gallery's parent container a cursor: pointer.**/
.DivName {cursor: pointer}
}
    </style>
@endpush

@section('content')

    <div class="container">
        <div class="main-body">
        
            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos" data-placement="right">
                  <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="logging_machine/spv_prod/checklist_shift">
                    <i class="fas fa-arrow-left"></i>
                  </a>
                </li>
                </li>
              </ul>

              <div class="row gutters-sm">
                <div class="col-sm-12 mb-3">
                  <div class="card-custom " style="zoom: 90%">
                   <div class="card-header bg-primary text-white"  style="border-radius: 30px;">
                      <a class="collapsed d-block" data-toggle="collapse" href="#inner" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                        <p class="text-center text-white"><i class="fas fa-angle-double-down text-white mr-4"></i> DETAIL INNER</p>
                      </a>
                    </div>
              
                  <div id="inner" class="collapse" aria-labelledby="heading-collapsed">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                          @if ($inner != NULL)
                          <div class="row">
                            @foreach ($inner_foto as $item)
                            <div class="col-sm-4">
                              <ul class="list-inline gallery">
                                <li><img class="img-thumbnail zoom" src="{{ asset('dokumen_lot/' . $item->foto) }}" style="width: 350%"></li>    
                              </ul>
                            </div>
                            @endforeach    
                          </div>
                          @else
                          <span class="badge badge-pill badge-info">BELUM UPLOAD FOTO</span>
                          @endif
                          </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                    <div class="card-custom  card-stretch gutter-b">
                       <div class="card-header bg-primary text-white"  style="border-radius: 30px; zoom:80%">
                         <a class="collapsed d-block" data-toggle="collapse" href="#hasil_produksi" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                          <p class="text-center text-white"><i class="fas fa-angle-double-up text-white mr-4"></i> HASIL PRODUKSI </p>
                         </a>
                        </div>
                        <div id="hasil_produksi" class="collapse show" aria-labelledby="heading-collapsed">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                @if ($cek_hasil->total_produksi_box == null)
                                    <center>
                                        <span class="badge badge-pill badge-info">BELUM INPUT HASIL</span>
                                    </center>
                                    @else
                                    <div class="row">
                                      <div class="col-sm-6">
                                        <table class="table table-bordered bg-success">
                                            <thead>
                                                <tr>
                                                <th colspan="2"><b>COUNTERPCS</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <td>HASIL BOX</td>
                                                <td>HASIL PCS</td>
                                                </tr>
                                                <tr>
                                                <td>{{$total_box}}</td>
                                                <td>{{$total_pcs}}</td>
                                                </tr>
                                            </tbody>
                                          </table>
                                      </div>
                                      <div class="col-sm-6">
                                        <table class="table table-bordered bg-warning">
                                            <thead>
                                                <tr>
                                                <th colspan="2"><b>PAS APPS</b></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <td>HASIL BOX</td>
                                                <td>HASIL PCS</td>
                                                </tr>
                                                <tr>
                                                <td>{{$cek_hasil->total_produksi_box}}</td>
                                                <td>{{$cek_hasil->hasil_produksi_pcs}}</td>
                                                </tr>
                                            </tbody>
                                          </table>
                                      </div>
                                    </div>
                                    <div class="row">
                                        <table class="table bg-white">
                                            <thead>
                                                <tr>
                                                <th scope="col">SELISIH BOX</th>
                                                <th scope="col">SELISIH PCS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <td>{{ ABS($total_box - $cek_hasil->total_produksi_box)}} BOX</td>
                                                <td>{{ABS($total_pcs - $cek_hasil->hasil_produksi_pcs)}} PCS</td>
                                                </tr>
                                            </tbody>
                                          </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
                <div class="col-sm-6">
                  <div class="card mb-3">
                    <div class="card-body">
                          <!--begin::Tiles Widget 11-->
                          @if ($gramatur != NULL)
                          <div class="table-responsive">
                             <table class="table table-bordered">
                                                <thead>
                                                    <div class="float-right">
                                                    </div>
                                                    <tr>
                                                        <th colspan="9" class="text-center"> HISTORY TIMBANGAN </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center">
                                                            JAM KE
                                                        </th>
                                                        @for ($i = 1; $i < 9; $i++)
                                                            <th class="text-center">
                                                                {{ $i }}
                                                            </th>
                                                       @endfor
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                            <td class="text-center">
                                                                1
                                                            </td>
                                                            {{-- LOGIK SAMPLING --}}
                                                            @foreach($jam_ke1 as $val)
                                                            <td class="text-center">
                                                                {{ $val->sampling_ke }}
                                                            </td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">2</td>
                                                            @foreach($jam_ke2 as $item)
                                                            <td class="text-center">
                                                                {{ $item->sampling_ke }}
                                                            </td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td class="text-center">3</td>
                                                            @foreach($jam_ke3 as $item)
                                                            <td class="text-center">
                                                                {{ $item->sampling_ke }}
                                                            </td>
                                                            @endforeach
                                                        </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                  @endif
                        </div>
                      </div>
                    </div>
                  <div class="row">
                  <div class="col-sm-12">
                    <div class="card-custom  card-stretch gutter-b">
                       <div class="card-header bg-primary text-white"  style="border-radius: 30px; zoom:80%">
                           <a class="collapsed d-block" data-toggle="collapse" href="#detail_downtime" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                          <p class="text-center text-white">  DETAIL DOWNTIME <i class="fas fa-angle-double-right text-white ml-4"></i> </p>
                         </a>
                    </div>
                    <div id="detail_downtime" class="collapse" aria-labelledby="heading-collapsed">
                      <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">No.</th>
                                            <th>Nama</th>
                                            <th>NIK</th>
                                            <th>Tanggal Permintaan</th>
                                            <th>Jam Permintaan</th>
                                            <th>Kerusakan</th>
                                            <th>Nama Maintenance</th>
                                            <th>Jam Respon Maintenance</th>
                                            <th>Jam Selesai Maintenance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($downtime as $item)                                            
                                        <tr>
                                            <td>
                                                {{$loop->iteration}}
                                            </td>
                                            <td>
                                                {{$item->nama}}
                                            </td>
                                            <td>
                                                {{$item->nik}}
                                            </td>
                                            <td>
                                                {{\Carbon\Carbon::parse($item->tgl_pengisian)->format('d-M-Y')}}
                                            </td>
                                            <td>
                                                {{$item->jam_pengisian}} WIB
                                            </td>
                                            <td>
                                                {{$item->kerusakan}}
                                            </td>
                                            <td>
                                                @if($item->jam_mulai_maintenance != NULL)
                                                {{$item->jam_mulai_maintenance}} WIB
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->jam_mulai_maintenance != NULL)
                                                {{$item->jam_selesai_maintenance}} WIB
                                                @else
                                                -
                                                @endif

                                            </td>
                                            <td>
                                                @if($item->status == 2)
                                                <span class="badge badge-warning">Belum Di Respon</span> 
                                                @elseif($item->status == 1)
                                                <span class="badge badge-info">Proses Perbaikan</span>
                                                @else 
                                                <span class="badge badge-success">Close Request</span>
                                                @endif
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
            </div>
                <div class="row">
                  <div class="col-sm-6 mt-4">
                    <div class="card-custom  card-stretch gutter-b">
                      <div class="card-header bg-primary text-white"  style="border-radius: 30px; zoom:80%">
                          <a class="collapsed d-block" data-toggle="collapse" href="#detail_wip" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                          <p class="text-center text-white"><i class="fas fa-angle-double-down text-white mr-4"></i>  DETAIL WIP </p>
                         </a>
                      </div>
                      <div id="detail_wip" class="collapse" aria-labelledby="heading-collapsed">
                      <div class="card-body">
                        <div class="table-responsive">
                          @if ($wip != NULL)
                          <table class="table table-striped">
                          <thead>
                              <tr>
                                  <th>Rasa</th>
                                  <th>Inner Reject(Kg)</th>
                                  <th>Sampah Inner</th>
                                  <th>Total WIP</th>
                                  <th>Sortir</th>
                                  <th>Sobek</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td>
                                    {{$wip->rasa}}
                                  </td>
                                  <td>
                                    {{$wip->inner_reject}}
                                  </td>
                                  <td>
                                    {{$wip->sampah_inner}}
                                  </td>
                                  <td>
                                    {{$wip->total_wip}}
                                  </td>
                                  <td>
                                    {{$wip->sortir}}
                                  </td>
                                  <td>
                                    {{$wip->sobek}}
                                  </td>
                              </tr>
                          </tbody>
                          </table>
                          @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                  <div class="col-sm-6 mt-4">
                    <div class="card-custom  card-stretch gutter-b">
                      <div class="card-header bg-primary text-white"  style="border-radius: 30px; zoom:80%">
                         <a class="collapsed d-block" data-toggle="collapse" href="#kebersihan" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                          <p class="text-center text-white"><i class="fas fa-angle-double-down text-white mr-4"></i>   DETAIL CHECKSHEET KEBERSIHAN </p>
                         </a>
                      </div>
                      <div id="kebersihan" class="collapse" aria-labelledby="heading-collapsed">
                      <div class="card-body">
                        <div class="table-responsive">
                          @if ($kebersihan != NULL)
                          <table class="table table-hover">
                          <thead>
                              <tr>
                                  <th>Lantai</th>
                                  <th>Bak</th>
                                  <th>Body Mesin</th>
                                  <th>Sealer</th>
                                  <th>Gayung</th>
                                  <th>Sodokan</th>
                                  <th>Tutup Hopper</th>
                                  <th>Serbet</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td>
                                    {{$kebersihan->lantai}}
                                  </td>
                                  <td>
                                    {{$kebersihan->bak}}
                                  </td>
                                  <td>
                                    {{$kebersihan->body_mesin}}
                                  </td>
                                  <td>
                                    {{$kebersihan->sealer}}
                                  </td>
                                  <td>
                                    {{$kebersihan->gayung}}
                                  </td>
                                  <td>
                                    {{$kebersihan->sodokan}}
                                  </td>
                                  <td>
                                    {{$kebersihan->tutup_hopper}}
                                  </td>
                                  <td>
                                    {{$kebersihan->serbet}}
                                  </td>
                              </tr>
                          </tbody>
                          </table>
                          @endif
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
<script type="text/javascript">

</script>
  
@endpush
