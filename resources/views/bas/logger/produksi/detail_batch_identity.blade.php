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

                 <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 100%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Dashboard"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/bas_logger/operator/index">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>

            <div class="row gutters-sm">
                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-header text-center">
                            <i class="fas fa-eye text-dark"></i> <b>DETAIL BATCH IDENTITY </b>
                             <div class="float-right">
                                <a href="javascript:history.back()" class="btn btn-info btn-sm" style="border-radius: 10px"><i class="fas fa-arrow-left"></i> kembali</a>
                            </div>
                        </div>
                            <div class="card-body">
                                <div class="row">
                                  <div class="col-sm-12">
                                    <div class="card card-custom bg-light-primary ">
                                        <div class="card-header">
                                          <h3 class="card-title">
                                             <i class="fas fa-info-circle text-dark mr-2"></i> Informasi Batch
                                          </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                      <label  class="col-sm-3 col-form-label">Sampel</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{$detail->jenis_sampel}}" readonly/>
                                                        </div>
                                                  </div>
                                                    <div class="form-group row">
                                                      <label  class="col-sm-3 col-form-label">Varian</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{$detail->jenis_varian}} " readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                      <label  class="col-sm-3 col-form-label">Main Blending</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{$detail->main_blending}} " readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                      <label  class="col-sm-3 col-form-label">Tangal Produksi</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{\Carbon\Carbon::parse($detail->tgl_produksi)->format('d-M-Y')}}" readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                      <label class="col-sm-3 col-form-label">Tanggal Pasteurisasi</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{\Carbon\Carbon::parse($detail->tgl_pasteurisasi)->format('d-M-Y')}} " readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                      <label class="col-sm-3 col-form-label">Group</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{($detail->group)}} " readonly/>
                                                        </div>
                                                    </div>
                                                     <div class="form-group row">
                                                      <label  class="col-sm-3 col-form-label">Main Batch</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{$detail->main_batch}} " readonly/>
                                                        </div>
                                                    </div>
                                                     <div class="form-group row">
                                                      <label  class="col-sm-3 col-form-label">Production Order</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{$detail->production_order}} " readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group row">
                                                      <label  class="col-sm-1 col-form-label">Storage</label>
                                                      <div class="col-sm-10">
                                                          <b><input class="form-control" type="text" value="{{$detail->storage}} " readonly/></b>
                                                        </div>
                                                    </div>
                                                  <div class="form-group row mb-1">
                                                    <label for="exampleTextarea">Notes</label>
                                                    <textarea class="form-control" readonly>{{$detail->notes}}</textarea>
                                                   </div>
                                                </div>
                                             </div>
                                            
                                            <hr>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group row">
                                                      <label class="col-sm-3 col-form-label">Nama Pengisi</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{Auth::user()->name}} " readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                      <label class="col-sm-3 col-form-label">NIK</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{Auth::user()->username}} " readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                  <div class="form-group row">
                                                      <label class="col-sm-3 col-form-label">Tanggal Pembuatan</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{\Carbon\Carbon::parse($detail->tgl_pengisian)->format('d-M-Y')}} " readonly/>
                                                        </div>
                                                    </div>
                                                  <div class="form-group row">
                                                      <label class="col-sm-3 col-form-label">Jam Pembuatan</label>
                                                      <div class="col-sm-5">
                                                          <input class="form-control" type="text" value="{{$detail->jam_pengisian}} WIB " readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                     <div class="form-group row">
                                                      <label class="col-sm-3 col-form-label">Tanggal & Jam Edit</label>
                                                      <div class="col-sm-9">
                                                          <input class="form-control" type="text" value="{{\Carbon\Carbon::parse($detail->tgl_edit)->format('d-M-Y H:i:s')}} WIB " readonly/>
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
</div>
</div>

@endsection

@push('scripts')
       <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>


    <script type="text/javascript">
        $('.table').DataTable();
 
        $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

@endpush
