@extends('internal_memo.master.layout')

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
                                    <strong >DASHBOARD</strong>
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
                                                <div class="col-sm-4">
                                                    <a href="/internal_memo/menu/buat_dokumen/">
                                                      <div class="card bg-primary" style="border-radius: 20px">
                                                            <div class="card-body">
                                                                <center><i class="fas fa-plus menu-icon text-white"></i></center>
                                                                <span class="card-title font-weight-bolder text-center text-white mb-0 d-block pt-4 pb-4">Buat Dokumen</span>
                                                            </div>
                                                        </div>
                                                        </a>
                                                   </div>
                                                <div class="col-sm-4">
                                                    <a href="/internal_memo/menu/history_dokumen/{{Crypt::encrypt(Auth::user()->nik)}}">
                                                      <div class="card bg-info" style="border-radius: 20px">
                                                            <div class="card-body">
                                                                <center><i class="fas fa-history menu-icon text-white"></i></center>
                                                                <span class="card-title font-weight-bolder text-center text-white mb-0 d-block pt-4 pb-4">History Dokumen</span>
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
