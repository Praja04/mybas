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
                        href="/bas_logger/spv/index">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>
        
             <div class="row gutters-sm">
                  <div class="col-md-12 mb-3">
                       <div class="card">
                            <div class="card-body">
                              <a href="javascript:history.back()" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                               
                                   <form action="/bas_logger/spv/update_sampel_varian/{{$detail->id}}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">No. Standar</label>
                                                   <input type="text" name="no_standar" class="form-control"
                                                    placeholder="Masukan No. Standar" value="{{$detail->no_standar}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Jenis Sampel</label>
                                                <select class="form-control Sampel" name="jenis_sampel" required>
                                                    <option selected value="{{$detail->jenis_sampel}}">{{$detail->jenis_sampel}}</option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    <option value="KECAP">KECAP</option>
                                                    <option value="GGA">GGA</option>
                                                    <option value="GGAS">GGAS</option>
                                                    <option value="LG">LG</option>
                                                    <option value="SKJ">SKJ</option>
                                                    <option value=" ">OTHER</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Jenis Varian</label>
                                                <select class="form-control Varian" name="jenis_varian" required>
                                                  <option selected value="{{$detail->jenis_varian}}">{{$detail->jenis_varian}}</option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    <option value="MSD">MSD</option>
                                                    <option value="SS">SS</option>
                                                    <option value="JB">JB</option>
                                                    <option value=" ">OTHER</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                               <div class="form-group" style="display: none" id="other_sampel">
                                                <label for="exampleFormControlInput1">Jenis Sampel</label>
                                                    <input type="text" name="jenis_sampel_other" class="form-control"
                                                    placeholder="Masukan Jenis sampel">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                               <div class="form-group" style="display: none" id="other_varian">
                                                <label for="exampleFormControlInput1">Jenis varian</label>
                                                    <input type="text" name="jenis_varian_other" class="form-control"
                                                    placeholder="Masukan Jenis varian">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary mb-2 btn-block"
                                            style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                        </form>
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

           $('.Sampel').on('change', function() {
            if (this.value == ' ') {
                $("#other_sampel").show();
            } else {
                $("#other_sampel").hide();
            }
        });
         
        $('.Varian').on('change', function() {
            if (this.value == ' ') {
                $("#other_varian").show();
            } else {
                $("#other_varian").hide();
            }
        });
</script>


@endpush