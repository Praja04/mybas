@extends('bas.layout.master')

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

            <div class="row gutters-sm">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-tittle text-center">
                                    <strong >DASHBOARD SPV.</strong>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                  <div class="col-sm-12">
                                    <div class="card">
                                        <a class="collapsed d-block" data-toggle="collapse" href="#quick_menu" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                                        <div class="card-header bg-dark" style="zoom: 80%;">
                                            <i class="fas fa-stream text-white"> Quick Menu</i> 
                                            </div>
                                        </a>
                                        <div id="quick_menu" class="collapse" aria-labelledby="heading-collapsed">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <a href="/bas_logger/spv/sampel_varian">
                                                      <div class="card card-custom bg-light-warning" style="border-radius: 20px">
                                                            <div class="card-body">
                                                                <center><i class="fas fa-database menu-icon text-warning"></i></center>
                                                                <span class="card-title font-weight-bolder text-center text-dark-75 mb-0 mt-6 d-block pt-3 pb-3">Jenis Sampel dan Varian</span>
                                                            </div>
                                                        </div>
                                                        </a>
                                                   </div>
                                                 <div class="col-sm-5">
                                                    <a href="/bas_logger/spv/parameter_pengecekan">
                                                       <div class="card card-custom bg-light-info" style="border-radius: 20px">
                                                            <div class="card-body">
                                                                <center><i class="fas fa-database menu-icon text-warning"></i></center>
                                                                <span class="card-title font-weight-bolder text-center text-dark-75 mb-0 mt-6 d-block pt-3 pb-3">Parameter Pengecekan</span>
                                                            </div>
                                                        </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
