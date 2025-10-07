@extends('layouts.base-display')

@section('title', 'DASHBOARD TABLET')

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

    <div class="container">
        <div class="main-body">

            @if (!$cek_logging_machine)
            <div class="row gutters-sm">
                    <div class="col-sm-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="alert alert-primary" role="alert">
                                    Opps.. Anda Belum Mengisi Master Laporan Produksi Packing, Harap Mengisi Master Laporan..
                                    <a href="#input" data-toggle="modal" class="btn btn-warning btn-sm mt-3"><i
                                        class="fas fa-plus"></i> Input Form Sekarang</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            
                @else
                <div class="row gutters-sm">
                    <div class="col-sm-4">
                        <div class="card mb-3">
                            <div class="card-body">
                               <div class="d-flex flex-column align-items-center text-center">
                                  <img src=" data:image/jpg;base64, {{ $foto }}" alt="Admin"
                                    class="rounded mx-auto d-block" width="80px">
                                     <div class="mt-3">
                                        <h4>{{ $user->EMPNM }}</h4>
                                        <p class="text-secondary mb-1">NIK: {{ $user->NIK }}</p>
                                        <input type="hidden" name="nik" value="{{ $user->NIK }}" id="Nik">
                                        <p class="font-size-sm">Dept: {{ $user->DEPTID }}</p>
                                        <hr>
                                    </div>
                                    <hr>
                                @if ($cek_logging_machine->approval_spv_downtime == 0)
                                <div class="row">
                                    <div class="col-sm-12">
                                     
                                    </div>
                                </div>
                                @else
                                    <div class="row">
                                        <div class="col-sm-12">
                                               <a href="#input" data-toggle="modal" class="btn btn-info btn-sm mb-4" style="border-radius: 13px;"><i
                                                class="fas fa-plus"></i> Add Mesin
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <a href="#ganti_varian" data-toggle="modal"
                                                    class="btn btn-info btn-sm" style="border-radius: 13px;"><i
                                                        class="fas fa-edit"></i> Pindah Varian 
                                                    </a>
                                                </div>
                                            </div>
                                   @endif
                                    <hr>
                                    <p>Detail Mesin Hari Ini</p>
                                    <div class="table-responsive">
                                            <table class="table">
                                        <thead class="thead-dark">
                                            <tr>
                                            <th scope="col">Line</th>
                                            <th scope="col">No.mesin</th>
                                            <th scope="col">Rasa</th>
                                            <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mesin_saya as $item)
                                            <tr>
                                                <td>{{$item->line}}</td>
                                                <td>{{$item->no_mesin}}</td>
                                                <td>{{$item->rasa}}</td>
                                                <td>
                                                    @if($item->pindah_varian == 2)
                                                    <span class="badge badge-pill badge-dark"> Ganti Varian</span>
                                                    @elseif($item->pindah_varian == 0)
                                                    <span class="badge badge-pill badge-info"> Selesai</span>
                                                    @else
                                                    <span class="badge badge-pill badge-primary"> Running</span>
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

                    <div class="col-sm-8">
                          <div class="card mb-3">
                            <div class="card-toolbar">
                                <div class="float-right">
                                    <button onclick="logout()"
                                        class="btn btn-dark font-weight-bolder font-size-sm logout mt-2 mr-4" style="border-radius: 19px"><i
                                            class="fa fa-sign-out-alt"> </i> Log Out</button>
                                </div>
                            </div>
                                <div class="card-body">

                                @if ($cek_logging_machine->approval_spv_downtime == 0)
                                    <div class="alert alert-warning" role="alert">
                                        <div class="progress" style="zoom: 160%">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated "
                                                role="progressbar" style="width: 100%;" aria-valuenow="100"
                                                aria-valuemin="100" aria-valuemax="100"> MATI LAMPU
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @foreach ($mesin_running as $val)
                                    @php

                                        $cek_downtime = DB::table('downtime')
                                        ->join('logging_machine', 'logging_machine.id', '=', 'downtime.id_logging_machine')
                                        ->where('downtime.nik', $val->nik)
                                        ->where('downtime.no_mesin', $val->no_mesin)
                                        ->where('downtime.tgl_pengisian', date('Y-m-d'))
                                        ->where('logging_machine.pindah_varian', $val->pindah_varian)
                                        ->where('downtime.close_operator', 0)
                                        ->first();
                                        
                                        $cek_wip = DB::table('logging_machine_wip')
                                        ->join('logging_machine', 'logging_machine.id', '=', 'logging_machine_wip.id_logging_machine')
                                        ->where('logging_machine_wip.nik', $val->nik)
                                        ->where('logging_machine.no_mesin', $val->no_mesin)
                                        ->where('logging_machine.pindah_varian', $val->pindah_varian)
                                        ->where('logging_machine_wip.tgl_pengisian', date('Y-m-d'))
                                        ->first();
                                        
                                        $cek_kebersihan = DB::table('logging_machine_kebersihan')
                                        ->join('logging_machine', 'logging_machine.id', '=', 'logging_machine_kebersihan.id_logging_machine')
                                        ->where('logging_machine_kebersihan.nik', $val->nik)
                                        ->where('logging_machine.no_mesin', $val->no_mesin)
                                        ->where('logging_machine.pindah_varian', $val->pindah_varian)
                                        ->where('logging_machine_kebersihan.tgl_pengisian', date('Y-m-d'))
                                        ->first();

                                         $gramatur = DB::table('gramatur_logging_machine')
                                         ->join('logging_machine', 'logging_machine.id', '=', 'gramatur_logging_machine.id_logging_machine')
                                         ->where('gramatur_logging_machine.nik', $val->nik)
                                         ->where('logging_machine.pindah_varian', $val->pindah_varian)
                                         ->where('gramatur_logging_machine.tgl_pengisian', date('Y-m-d'))
                                         ->where('logging_machine.no_mesin', $val->no_mesin)
                                         ->get();
                                         $cek_gramatur = count($gramatur) / 8;

                                         $inner = DB::table('inner_logging_machine')
                                         ->join('logging_machine', 'logging_machine.id', '=', 'inner_logging_machine.id_logging_machine')
                                         ->where('inner_logging_machine.nik', $val->nik)
                                         ->where('logging_machine.pindah_varian', $val->pindah_varian)
                                         ->where('inner_logging_machine.tgl_pengisian', date('Y-m-d'))
                                         ->where('logging_machine.no_mesin', $val->no_mesin)
                                         ->get();

                                         $cek_inner = count($inner);

                                         $close_operator = DB::table('downtime')
                                         ->where('nik', $val->nik)
                                         ->where('no_mesin', $val->no_mesin)
                                         ->where('close_operator', 1)
                                         ->groupBy('jam_pengisian')
                                         ->where('status', 0)
                                         ->get();
                                         $notif_close = count($close_operator);

                                    @endphp
                                      <div class="card-custom bg-gray-100 card-stretch gutter-b" style="height: 100;  border-radius: 15px">
                                        
                                        <div class="card-header bg-primary" style="border-radius: 30px">
                                         <a class="collapsed d-block" data-toggle="collapse" href="#{{$val->no_mesin}}" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                                            <i class="fas fa-angle-double-down text-white mr-4"> 
                                                {{$val->no_mesin}}
                                                @if ($notif_close)
                                                    <span class="badge badge-danger"><b>
                                                        {{ $notif_close }}</b></span>
                                                    @endif
                                            </i>
                                        @if ($cek_kebersihan )
                                            <a href="/logging_machine/get_hasil_produksi/{{$val->no_mesin}}/{{ $user->NIK }}/{{$val->pindah_varian}}"
                                            class="btn btn-dark Output mt-2" style="border-radius: 10px"> <i
                                            class="fas fa-plus"></i> Masukan Ouput Produksi</a>
                                        @endif
                                               <div class="float-right text-white" style="margin-bottom: 20px">
                                                    <div class="dropdown dropdown-inline bg-info" style="border-radius: 35px;">
														<a href="#" class="btn btn-transparent-white btn-sm font-weight-bolder dropdown-toggle px-5" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-toggle="tooltip" tittle="Edit Mesin Disini">Status</a>
														<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
															<!--begin::Navigation-->
															<ul class="navi navi-hover">
																<li class="navi-item">
																	<p class="navi-link">
																		<span class="navi-icon">
																			<i class="fas fa-hard-hat"></i>
																		</span>
																		<span class="navi-text">Downtime</span>
																		<span class="navi-text">
                                                                            @if ($cek_downtime)
                                                                                <i class="fas fa-check" style="color:green; zoom: 180%">
                                                                                </i>
                                                                            @endif
                                                                        </span>
																	</p>
																</li>
																<li class="navi-item">
																	<p class="navi-link">
																		<span class="navi-icon">
																			<i class="fa fa-balance-scale"></i>
																		</span>
																		<span class="navi-text">Sampling</span>
                                                                    @if ($cek_gramatur)
																		<span class="badge badge-primary">{{ $cek_gramatur }}</span>
                                                                    @endif
																	</p>
																</li>
																	<li class="navi-item">
																	<p class="navi-link">
																		<span class="navi-icon">
																			<i class="fa fa-balance-scale"></i>
																		</span>
																		<span class="navi-text">Inner</span>
                                                                    @if ($cek_inner)
																		<span class="badge badge-primary">{{ $cek_inner }}</span>
                                                                    @endif
																	</p>
																</li>
                                                                <li class="navi-item">
																	<p class="navi-link">
																		<span class="navi-icon">
																			<i class="fas fa-signature"></i>
																		</span>
																		<span class="navi-text">WIP</span>
																		<span class="navi-text">
                                                                            @if ($cek_wip)
                                                                                <i class="fas fa-check" style="color:green; zoom: 180%">
                                                                                </i>
                                                                            @endif
                                                                        </span>
																	</p>
																</li>
                                                                <li class="navi-item">
																	<p class="navi-link">
																		<span class="navi-icon">
																			<i class="fas fa-trash-alt"></i>
																		</span>
																		<span class="navi-text">Kebersihan</span>
																		<span class="navi-text">
                                                                            @if ($cek_kebersihan)
                                                                                <i class="fas fa-check" style="color:green; zoom: 180%">
                                                                                </i>
                                                                            @endif
                                                                        </span>
																	</p>
																</li>
                                                                <li class="navi-item">
                                                                    <a href="/logging_machine/edit_logging_master/{{$val->pindah_varian}}/{{$val->no_mesin}}/{{ Crypt::encrypt($val->nik)}}" class="navi-link">
																		<span class="navi-icon">
                                                                            <i class="fas fa-edit"></i>
																		</span>
																		<span class="navi-text">Edit Mesin</span>
																	</a>
																</li>
															</ul>
														</div>
													</div>
                                                </div>
                                            </a>
                                            </div>
                                          
                                    <div id="{{$val->no_mesin}}" class="collapse" aria-labelledby="heading-collapsed">
                                      <div class="card-body">
                                          <div class="row">
                                              <div class="col-sm-6">
                                                       <div class="card card-custom bg-primary gutter-b"
                                                        style="height: 100;  border-radius: 15px">
                                                            <div class="card-body">
                                                                 @if ($cek_downtime)
                                                                    <div class="float-right">
                                                                        <i class="fas fa-check" style="color:whitesmoke; zoom: 180%">
                                                                        </i>
                                                                    </div>
                                                                @endif
                                                                <i class="fas fa-hard-hat fa-icon-white"
                                                                    style="color: white; zoom: 180%">
                                                                </i>
                                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                                </div>
                                                                <a href="/logging_machine/downtime/{{$val->pindah_varian}}/{{$val->no_mesin}}/{{ Crypt::encrypt($user->NIK) }}"
                                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">{{$val->no_mesin}} DOWNTIME
                                                                    REQUEST</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="card card-custom bg-primary gutter-b"
                                                        style="height: 100;  border-radius: 15px">
                                                            <div class="card-body">
                                                                @if ($notif_close)
                                                                    <div class="float-right">
                                                                        <span class="badge badge-danger"><b>Pending Close, New
                                                                            {{ $notif_close }}</b></span>
                                                                    </div>
                                                                @endif
                                                                <i class="fas fa-user-clock" style="color: white; zoom: 180%">
                                                                </i>
                                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                                </div>
                                                                <a href="/logging_machine/history_downtime/{{$val->no_mesin}}/{{ Crypt::encrypt($user->NIK) }}"
                                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} HISTORY
                                                                    DOWNTIME</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <hr>

                                                <div class="row">
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-info gutter-b"
                                                    style="height: 100;  border-radius: 15px">
                                                    <div class="card-body">
                                                          @if ($cek_gramatur)
                                                            <div class="float-right">
                                                                <span class="badge badge-secondary">{{ $cek_gramatur }}
                                                                    Sampling</span>
                                                            </div>
                                                        @endif
                                                        <i class="fa fa-balance-scale" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/gramatur/{{$val->pindah_varian}}/{{$val->no_mesin}}/{{ Crypt::encrypt($cek_logging_machine->nik) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} SAMPLING
                                                            GRAMATUR</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-info gutter-b"
                                                    style="height: 100;  border-radius: 15px">
                                                    <div class="card-body">
                                                        <i class="fas fa-user-clock" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/gramatur/list_history/{{$val->pindah_varian}}/{{$val->id}}/{{ Crypt::encrypt($user->NIK) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} HISTORY
                                                            GRAMATUR</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                        </div>

                                              <hr>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-danger gutter-b"
                                                    style="height: 100; border-radius: 15px">
                                                    <div class="card-body">
                                                        @if ($cek_inner)
                                                            <div class="float-right">
                                                                <span class="badge badge-secondary">{{ $cek_inner }}
                                                                    Foto</span>
                                                            </div>
                                                        @endif
                                                        <i class="fas fa-clipboard" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/inner/{{$val->id}}/{{ Crypt::encrypt($user->NIK) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} TRACEBILITY
                                                            INNER</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-danger gutter-b"
                                                    style="height: 100; border-radius: 15px">
                                                    <div class="card-body">
                                                        <i class="fas fa-user-clock" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/history_inner/{{ Crypt::encrypt($user->NIK) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} HISTORY
                                                            TRACEBILITY</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                        </div>

                                        <hr>

                                                   <div class="row">
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-success gutter-b"
                                                    style="height: 100; border-radius: 15px">
                                                    <div class="card-body">
                                                    @if ($cek_wip)
                                                            <div class="float-right">
                                                                <i class="fas fa-check" style="color:whitesmoke; zoom: 180%">
                                                                </i>
                                                            </div>
                                                        @endif
                                                        <i class="fas fa-signature" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/wip/{{$val->pindah_varian}}/{{$val->id}}/{{ Crypt::encrypt($user->NIK) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} WORK
                                                            IN
                                                            PROCESS(WIP)</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-success gutter-b"
                                                    style="height: 100; border-radius: 15px">
                                                    <div class="card-body">
                                                        <i class="fas fa-user-clock" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/history_wip/{{$val->no_mesin}}/{{ Crypt::encrypt($user->NIK) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} HISTORY
                                                            WIP</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                        </div>

                                                  <div class="row">
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-dark gutter-b"
                                                    style="height: 100; border-radius: 15px">
                                                    <div class="card-body">
                                                        @if ($cek_kebersihan)
                                                                <div class="float-right">
                                                                    <i class="fas fa-check" style="color:whitesmoke; zoom: 180%">
                                                                    </i>
                                                                </div>
                                                        @endif
                                                        <i class="fas fa-trash" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/kebersihan/{{$val->pindah_varian}}/{{$val->id}}/{{ Crypt::encrypt($user->NIK) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} Checksheet
                                                            Kebersihan</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                            <div class="col-sm-6">
                                                <!--begin::Tiles Widget 11-->
                                                <div class="card card-custom bg-dark gutter-b"
                                                    style="height: 100; border-radius: 15px">
                                                    <div class="card-body">
                                                        <i class="fas fa-user-clock" style="color: white; zoom: 135%">
                                                        </i>
                                                        <div
                                                            class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                        </div>
                                                        <a href="/logging_machine/history_kebersihan/{{$val->no_mesin}}/{{ Crypt::encrypt($user->NIK) }}"
                                                            class="text-inverse-primary font-weight-bold font-size-lg mt-1"> {{$val->no_mesin}} HISTORY
                                                            Checksheet</a>
                                                    </div>
                                                </div>
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                        </div>

                                                </div>
                                             </div>
                                        </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="downtime_operator" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background-color: yellow;">
            <div class="modal-header">
                <h4 class="modal-title">INFORMASI DOWNTIME</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h2>PERMINTAAN DOWNTIME KAMU BELUM DI CLOSE</h2>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-dismiss="modal">Nanti,</button>
                <button id="Proses" class="btn btn-primary"><i class="fas fa-check"></i> Proses</a>
            </div>
            </div>
        </div>
    </div>
                    
                                
    <div class="modal fade" id="input" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Form Input Master Checksheet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/logging_machine/post_logging_master') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">Nama Lengkap</label>
                                        <input type="text" class="form-control" value="{{ $user->EMPNM }}" name="nama"
                                            readonly>
                                    </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="exampleFormControlInput1">NIK</label>
                                    <input type="text" class="form-control" value="{{ $user->NIK }}" name="nik" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Line</label>
                                    <select class="form-control" name="line" required>
                                        <option disabled selected>Silahkan Pilih</option>
                                        @foreach ($no_mesin as $item)
                                            <option value="{{ $item->group }}">{{ $item->line }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">No. Mesin</label>
                                    <select class="form-control" name="no_mesin" required style="width: 105%">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Varian</label>
                                    <select class="form-control" name="varian" required>
                                        <option value="Garnish">Garnish</option>
                                        <option value="Sayuran">Sayuran</option>
                                    </select>
                                </div>
                                @if(date('l') != $hari_sabtu)
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Shift</label>
                                    <select class="form-control" name="shift" required style="width: 105%">
                                        @for ($i = 1; $i < 4; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @else
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Shift</label><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i>
                                    <select class="form-control bg-primary text-white" name="shift" required style="width: 105%">
                                            <option disabled selected>Silahkan Dipilih</option>
                                            <option value="1">1 </option>
                                            <option value="2">2 </option>
                                            <option value="3">3 </option>
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Rasa</label>
                                    <select class="form-control" name="varian_rasa" style="width: 105%" required>
                                    </select>
                                </div>
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Group</label>
                                        <select class="form-control" name="group" required>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                        </select>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" style="border-radius: 10px"><i
                                    class="fas fa-save"></i>
                                Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ganti_varian" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Form Ganti Varian </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{ url('/logging_machine/pindah_varian_logging_master') }}" method="POST">
                        @csrf
                          <input type="text" class="form-control" value="{{ $user->EMPNM }}" name="nama"
                                            hidden>
                          <input type="text" class="form-control" value="{{ $user->NIK }}" name="nik"
                                            hidden>

                          <input type="text" class="form-control" value="Garnish" name="varian"
                                            hidden>
                        @if ($cek_logging_machine != NULL)
                        <input type="text" class="form-control" value="{{$cek_logging_machine->id}}" name="id"
                        hidden>
                        @endif
                          
                        <input type="text" class="form-control" value="2" name="pindah_varian"
                                            hidden>
                        
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Jenis Ganti Varian</label>
                                    <select class="form-control" name="jenis_ganti" id="jenis_ganti">
                                        <option disabled selected>Silahkan Di Pilih</option>
                                        <option value="1">Sesuai Mesin Running</option>
                                        <option value="2" disabled>Pindah Mesin/Line/Group</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div id="normal" style="display: none">
                          <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Pilih Mesin</label>
                                    <select class="form-control" name="no_mesin" required>
                                        <option disabled selected>Silahkan Di Pilih</option>
                                        @foreach ($mesin_running as $val)
                                        <option value="{{$val->no_mesin}}">{{$val->no_mesin}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Varian/Rasa Sebelmunya</label>
                                    <select class="form-control" name="rasa_before">

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                    @if(date('l') != $hari_sabtu)
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Shift</label>
                                    <select class="form-control" name="shift" required style="width: 105%">
                                         @for ($i = 1; $i < 4; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                @else
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Shift</label><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i><i class="fas fa-arrow-down alert_shift text-danger ml-2"></i>
                                    <select class="form-control bg-primary text-white" name="shift" required style="width: 105%">
                                            <option disabled selected>Silahkan Dipilih</option>
                                            <option value="1">1 </option>
                                            <option value="2">2 </option>
                                            <option value="3">3 </option>
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Group</label>
                                        <select class="form-control" name="group" required>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <hr>
                                <h1 class="text-center"><b>PINDAH VARIAN KE <i class="fas fa-arrow-down fa-lg text-dark"></i> </b></h1>
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Pilih Varian Baru</label>
                                        <select class="form-control" name="varian_rasa" required>
                                        <option disabled selected>Silahkan Di Pilih</option>
                                        @foreach($rasa_after as $item)
                                        <option value="{{$item->varian_rasa}}">{{$item->varian_rasa}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5"></div>
                                <div class="col-sm-4"></div>
                                    <div class="col-sm-3">
                                        <button type="submit" class="btn btn-sm btn-primary btn-block"><i class="fas fa-save"></i> Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

@endsection

@push('scripts')
    <script type="text/javascript">

        function logout() {
          sessionStorage.clear();
          location.href = "{{ url('/display/logging_machine') }}";
        }

          if(sessionStorage.length == 0 )
            {
                    location.href = "{{ url('/display/logging_machine') }}";
            }

        function blink_text() {
            $('.Output').fadeOut(700);
            $('.Output').fadeIn(700);
        }
        setInterval(blink_text, 2000);

        function alert_shift() {
            $('.alert_shift').fadeOut(700);
            $('.alert_shift').fadeIn(700);
        }
        setInterval(alert_shift, 2000);

    jQuery(document).ready(function() {
            var nik = $('#Nik').val();
            jQuery.ajax({
                url: '/logging_machine/get_modal_operator/' + nik,
                type: "POST",
                data: {
                    nik: nik
                },
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    if (data.data != null) {
                            Swal.fire({
                            title: "PERMITNAAN DOWNTIME BELUM DI CLOSE",
                            text: "Segera Close Permintaan Downtime Kamu Jika Sudah Selesai..",
                            icon: "warning",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='fas fa-check'></i> Proses!",
                            showCancelButton: true,
                            cancelButtonText: "<i class='fas fa-times'></i> Nanti",
                            customClass: {
                            confirmButton: "btn btn-danger",
                            cancelButton: "btn btn-default"
                            }
                            }).then((result) => {
                                if (result.value) {
                                location.href =
                                    "{{ url('/logging_machine/list_for_operator') }}/" + nik;
                                }
                            });
                        // $('#downtime_operator').modal('show');
                        // document.getElementById("Proses").onclick = function() {
                        //     location.href =
                        //         "{{ url('/logging_machine/list_for_operator') }}/" +
                        //         nik;
                        // };
                    } else {
                        console.log(data);
                    }
                },
                error: function(error) {
                    // console.log(error);
                }
            });
        });
        
        jQuery('select[name="line"]').on('change', function() {
            var group = this.value;
            jQuery.ajax({
                url: '/logging_machine/get_mesin/' + group,
                type: "GET",
                data: {
                    group: group
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        jQuery('select[name="no_mesin"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="no_mesin"]').append('<option value="' + value
                                .no_mesin + '">' + value.no_mesin + '</option>');
                        });
                    }
                }
            });
        });

        jQuery('select[name="line"]').on('change', function() {
            var group = this.value;
            jQuery.ajax({
                url: '/logging_machine/get_rasa/' + group,
                type: "GET",
                data: {
                    group: group
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        jQuery('select[name="varian_rasa"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="varian_rasa"]').append('<option value="' + value
                                .varian_rasa + '">' + value.varian_rasa + '</option>');
                        });
                    }
                }
            });
        });
        
        jQuery('select[name="no_mesin"]').on('change', function() {
            var no_mesin = this.value;
            jQuery.ajax({
                url: '/logging_machine/get_rasa_before/' + no_mesin,
                type: "GET",
                data: {
                    no_mesin: no_mesin
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        console.log(response.data);
                        jQuery('select[name="rasa_before"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="rasa_before"]').append('<option value="' + value
                                .rasa + '">' + value.rasa + '</option>');
                        });
                    }
                }
            });
        });

      $('#jenis_ganti').on('change', function() {
            if (this.value == '1') {
                $("#normal").show();
            } else {
                $("#normal").hide();
            }
        });
    
      $('#jenis_ganti').on('change', function() {
            if (this.value == '2') {
                $("#tdk_normal").show();
            } else {
                $("#tdk_normal").hide();
            }
        });



    </script>

@endpush
