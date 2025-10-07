@extends('layouts.base-display')

@section('title', 'REPORT ADMIN PRODUKSI')

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
            <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos" data-placement="right">
              <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="/">
                <i class="fas fa-arrow-left"></i>
              </a>
            </li>
            </li>
          </ul>

              <div class="row d-flex justify-content-center">
                <div class="col-sm-8">
                  <center>
                <div class="accordion" id="accordionExample">
                  <div class="card mb-3">
                    <div class="card-body">

                      <div class="accordion" id="accordionExample">
                        <div class="card">
                          <div class="card-header" id="headingOne">

                            <div class="row">
                              <div class="col-sm-12">
                                <!--begin::Tiles Widget 11-->
                                <div class="card card-custom bg-info gutter-b" style="height: 100;  border-radius: 15px">
                                  <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#packing" aria-expanded="false" aria-controls="collapseTwo">
                                  <div class="card-body">
                                      <i class="fas fa-clipboard-list fa-icon-white" style="color: white;">
                                      </i>
                                      <div class="text-inverse-info  mt-3">REPORT PACKING</div>
                                    </button>

                                  </div>
                                </div>
                                <!--end::Tiles Widget 11-->
                              </div>
                           

                              <div id="packing" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                  <div class="row">
                                    <div class="col-sm-4">
                                      <div class="card card-custom bg-info gutter-b" style="height: 100;  border-radius: 15px">
                                        <button class="btn btn-info"> <a href="/logging_machine/adm_prod/report_packing_day">
                                        <div class="card-body">
                                            <i class="fas fa-clipboard-check fa-icon-white" style="color: white;">
                                            </i>
                                            <div class="text-inverse-info  mt-3">REPORT HARIAN</div>
                                          </a>
                                          </button>
      
                                        </div>

                                    </div>
                                    <div class="col-sm-4">
                                      <div class="card card-custom bg-info gutter-b" style="height: 100;  border-radius: 15px">
                                        <button class="btn btn-info"> <a href="/logging_machine/adm_prod/report_varian">
                                        <div class="card-body">
                                            <i class="fas fa-search fa-icon-white" style="color: white;">
                                            </i>
                                            <div class="text-inverse-info  mt-3">TRACKING BY VARIAN</div>
                                          </a>
                                          </button>
      
                                        </div>

                                    </div>
                                    <div class="col-sm-4">
                                      <div class="card card-custom bg-info gutter-b" style="height: 100;  border-radius: 15px">
                                        <button class="btn btn-info"> <a href="/logging_machine/adm_prod/report_packing_all">
                                        <div class="card-body">
                                            <i class="fas fa-clipboard-list fa-icon-white" style="color: white;">
                                            </i>
                                            <div class="text-inverse-info  mt-3">TRACKING REPORT ALL</div>
                                          </a>
                                          </button>
      
                                        </div>

                                    </div>
                                  </div>
                                </div>
                              </div>


                            <div class="row">
                              <div class="col-sm-12 mt-4">
                                <!--begin::Tiles Widget 11-->
                                <div class="card card-custom bg-primary gutter-b" style="height: 100;  border-radius: 15px">
                                  <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseTwo">
                                  <div class="card-body">
                                      <i class="fas fa-clipboard-list fa-icon-white" style="color: white;">
                                      </i>
                                      <div class="text-inverse-primary  mt-3">REPORT DOWNTIME</div>
                                    </button>

                                  </div>
                                </div>
                                <!--end::Tiles Widget 11-->
                              </div>
                            </div>
                          </div>
                      
                          <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                            <div class="card-body">
                              <div class="row">
                                <div class="col-sm-4">
                                  <!--begin::Tiles Widget 11-->
                                  <div class="card card-custom bg-primary gutter-b" style="height: 100;  border-radius: 15px">
                                    <button class="btn btn-primary"> <a href="/logging_machine/adm_prod/report_downtime">
                                    <div class="card-body">
                                        <i class="fas fa-clipboard-check fa-icon-white" style="color: white;">
                                        </i>
                                        <div class="text-inverse-primary  mt-3">ALL LIST REQUEST</div>
                                      </a>
                                      </button>
  
                                    </div>
                                  </div>
                                <div class="col-sm-4">
                                  <!--begin::Tiles Widget 11-->
                                  <div class="card card-custom bg-primary gutter-b" style="height: 100;  border-radius: 15px">
                                    <button class="btn btn-primary"> <a href="/logging_machine/adm_prod/report_mesin">
                                    <div class="card-body">
                                        <i class="fas fa-cogs fa-icon-white" style="color: white;">
                                        </i>
                                        <div class="text-inverse-primary  mt-3">TRACKING MESIN</div>
                                      </button>
                                    </a>
  
                                    </div>
                                  </div>
                                <div class="col-sm-4">
                                  <!--begin::Tiles Widget 11-->
                                  <div class="card card-custom bg-primary gutter-b" style="height: 100;  border-radius: 15px">
                                    <button class="btn btn-link"><a href="/logging_machine/adm_prod/report_operator">
                                    <div class="card-body">
                                        <i class="fas fa-user fa-icon-white" style="color: white;">
                                        </i>
                                        <div class="text-inverse-primary  mt-3">TRACKING OPERATOR</div>
                                      </button>
                                    </a>
  
                                    </div>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="col-sm-2"></div>
                                  <div class="col-sm-8 mt-4">
                                  <!--begin::Tiles Widget 11-->
                                  <div class="card card-custom bg-primary gutter-b" style="height: 100px;  border-radius: 15px">
                                    <button class="btn btn-link"> <a href="/logging_machine/adm_prod/report_maintenance">
                                    <div class="card-body">
                                        <i class="fas fa-hard-hat fa-icon-white" style="color: white;">
                                        </i>
                                        <div class="text-inverse-primary mt-3">TRACKING MAINTENANCE</div>
                                      </button>
                                    </a>
  
                                    </div>
                                  </div>
                                  <div class="col-sm-2"></div>

                                  <!--end::Tiles Widget 11-->
                                </div>
                            </div>
                          </div>

                          <br>
                           <div class="row">
                              <div class="col-sm-12">
                                <!--begin::Tiles Widget 11-->
                                <div class="card card-custom gutter-b" style="height: 100;  border-radius: 15px; background-color: green;">
                                  <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#sap" aria-expanded="false" aria-controls="collapseTwo">
                                  <div class="card-body">
                                      <i class="fas fa-upload fa-icon-white" style="color: white;">
                                      </i>
                                      <div class="text-inverse-info  mt-3">UPLOAD PRODUCTION ORDER SAP</div>
                                    </button>

                                  </div>
                                </div>
                                <!--end::Tiles Widget 11-->
                              </div>

                               <div id="sap" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                  <div class="row">
                                    <div class="col-sm-6">
                                      <div class="card" style="height: 100; border-radius: 15px; background-color: green;">
                                        <button class="btn" style="background-color: green;"> <a href="#upload_file" data-toggle="modal">
                                        <div class="card-body">
                                            <i class="fas fa-file-excel fa-icon-white" style="color: white;">
                                            </i>
                                            <div class="text-inverse-info  mt-3">UPLOAD FILE EXCEL</div>
                                          </a>
                                          </button>
      
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                      <div class="card" style="height: 100;  border-radius: 15px">
                                        <button class="btn" style="background-color: green;">
                                           <a href="/logging_machine/adm_prod/tracking_file">
                                        <div class="card-body">
                                            <i class="fas fa-search fa-icon-white" style="color: white;">
                                            </i>
                                            <div class="text-inverse-info  mt-3">TRACKING FILE EXCEL</div>
                                          </a>
                                          </button>
      
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
                </center>
              </div>
            </div>
          </div>

<!-- Modal -->
<div class="modal fade" id="upload_file" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">MASUKAN FILE</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
      </div>
      
      <form action="{{ url('/logging_machine/adm_prod/upload_prod_order')}}" method="post" enctype="multipart/form-data">
        @csrf

      <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">
              <input type="file" class="form-control-file" name="file" id="" required>
              <hr>
               <div class="col-sm-12 mt-4">
                    <a href="/master_import/UPLOAD_PROD_ORDER MASTER.xlsx"
                        class="btn mb-2 btn-block text-white"
                        style="border-radius: 8px; background-color: green"><i
                            class="fas fa-file-excel text-white"></i> Download Master Excel</a>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 13px"><i class="fas fa-upload"></i> Upload</button>
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
