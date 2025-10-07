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
                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                          DASHBOARD BAS LOGGER PRODUKSI
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
                                                    <div class="col-sm-2">
                                                        <div class="card-custom bg-light-warning">
                                                            <div class="card-body pt-1 pb-0">
                                                                
                                                            </div>
                                                        </div>
                                                        <!--end::Tiles Widget 11-->
                                                    </div>
                                                <hr />
                                                <!--end::Tiles Widget 11-->
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="row">
                                  <div class="col-sm-12">
                                    <div class="card">
                                        <a class="collapsed d-block" data-toggle="collapse" href="#monitoring" aria-expanded="true" aria-controls="collapse-collapsed" id="heading-collapsed">
                                        <div class="card-header bg-dark" style="zoom: 80%;">
                                            <i class="fas fa-tv text-white mr-2"> Monitoring</i> 
                                            </div>
                                        </a>
                                        <div id="monitoring" class="collapse show" aria-labelledby="heading-collapsed">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                   <div class="table-responsive">
                                                        <table class="table table-hover" style="zoom: 80%;">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Sampling</th>
                                                                    <th class="text-center">Jenis Sampel</th>
                                                                    <th class="text-center">Jam Sampling</th>
                                                                    <th class="text-center">Sampler</th>
                                                                    <th class="text-center">BJ (g/mL)</th>
                                                                    <th class="text-center">Brix</th>
                                                                    <th class="text-center">pH</th>
                                                                    <th class="text-center">% NaCl</th>
                                                                    <th class="text-center">Viskositas (ps)</th>
                                                                    <th class="text-center">Organo</th>
                                                                    <th class="text-center">Aroma</th>
                                                                    <th class="text-center">Warna</th>
                                                                    <th class="text-center">Buih</th>
                                                                    <th class="text-center">Endapan</th>
                                                                    <th class="text-center">AW1</th>
                                                                    <th class="text-center">AW2</th>
                                                                    <th class="text-center">Keterangan</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                    <tr>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                        <td class="txet-center"></td>
                                                                    </tr>
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
