@extends('layouts.base-display')

@section('title', 'DASHBOARD SPV. PRODUKSI SEASSONING 2')

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

            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="/">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
                </li>
            </ul>

            <div class="row d-flex justify-content-center">
                <div class="col-sm-8">
                    <center>
                        <div class="card mb-3">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <!--begin::Tiles Widget 11-->
                                        <div class="card card-custom bg-primary gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">
                                                <i class="fas fa-signature" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="/logging_machine/spv_prod/checklist_shift"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">CHECKLIST
                                                    SHIFT</a>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Tiles Widget 11-->
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card card-custom bg-primary gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">
                                                <i class="fas fa-search" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="/logging_machine/spv_prod/tracking_shift"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1">TRACKING
                                                    HISTORY SHIFT</a>
                                            </div>
                                        </div>
                                        <!--end::Tiles Widget 11-->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <!--begin::Tiles Widget 11-->
                                        <div class="card card-custom bg-dark gutter-b"
                                            style="height: 100;  border-radius: 15px">
                                            <div class="card-body">
                                                @if($data->approval_spv_downtime == 1)
                                                <i class="fas fa-exclamation-triangle" style="color: white; zoom: 180%">
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                <a href="#downtime" data-toggle="modal"
                                                    class="text-inverse-primary font-weight-bold font-size-lg mt-1"> FORM URGENT</a>
                                                    @else
                                                      <button class="btn btn-lg text-white" data-toggle="modal" data-target="#stop" style="background-color: green"><i class="fas fa-stop text-white"></i> STOP </button>
                                                </i>
                                                <div class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                </div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated " role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="100" aria-valuemax="100">  MATI LAMPU
                                                        </div>
                                                    </div>
                                                    @endif
                                            </div>
                                        </div>
                                        <!--end::Tiles Widget 11-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </center>
                </div>
            </div>
        </div>
    </div>


<!-- Modal -->
<div class="modal fade" id="downtime" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title"> <b><center>KONFIRMASI DOWNTIME MATI LAMPU</center></b></h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="/logging_machine/spv_downtime" method="POST">
        @csrf
        @method('PATCH')

        <input type="hidden" name="approval_spv_nama" id="" value="{{Auth::user()->name}}">
        <input type="hidden" name="approval_spv_nik" id="" value="{{Auth::user()->username}}">

      <div class="modal-body">  
        <h6>Saya Yang Bertanda Tangan Di Bawah Ini. </h6>
        <hr>
        <h6 class="mt-4">Nama : {{Auth::user()->name}} </h6>
        <h6 class="mt-2">NIK : {{Auth::user()->username}} </h6>
        <hr>
        <h6 class="mt-4">Dengan Ini Saya Menyatakan Bahwa Saat Ini Mati Lampu, Dan Proses Produksi Harus Terhenti Sejenak Terhitung Mulai: </h6>
        <hr>
        <h6 class="mt-4">Tanggal : {{date('d-M-Y')}} </h6>
        <h6 class="mt-2">Jam : {{date('h:i:s')}} WIB </h6>
        <h6 class="mt-2">Sampai : Belum Di Tentukan </h6>
        <hr>
        <h6 class="mt-2">Demikian Laporan Ini Saya Buat Dengan Sebenar-benarnya Dan Dapat Di Pertanggung Jawabkan</h6>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm" style="border: 10px"><i class="fas fa-check"></i> Buat Laporan</button>
    </div>
    </form>
    </div>
  </div>
</div>

<div class="modal fade" id="stop" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <form action="/logging_machine/stop_downtime" method="POST">
        @csrf
        @method('PATCH')
        <div class="modal-body">
            <h1 class="text-center"> <b>APAKAH ANDA YAKIN ?</b></h1>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary btn-sm"> <i class="fas fa-check"></i>  Ya, Stop</button>
        </div>
    </form>
    </div>
  </div>
</div>



@endsection

@push('scripts')
    <script type="text/javascript">


    </script>

@endpush
