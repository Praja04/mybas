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
                               
                                   <form action="/bas_logger/spv/update_parameter_pengecekan/{{$detail->id}}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                        <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">No. Standar</label>
                                                   <select class="form-control Nostandar" name="no_standar" required>
                                                    <option selected value="{{$detail->no_standar}}">{{$detail->no_standar}}</option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    @foreach ($no_standar as $val)
                                                    <option value="{{$val->no_standar}}">{{$val->no_standar}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Jenis Sampel</label>
                                                <select class="form-control Sampel" name="jenis_sampel" readonly>
                                                    <option selected value="{{$detail->jenis_sampel}}">{{$detail->jenis_sampel}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Varian</label>
                                                <select class="form-control Varian" name="jenis_varian" readonly>
                                                     <option selected value="{{$detail->jenis_varian}}">{{$detail->jenis_varian}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Parameter</label>
                                                   <select class="form-control Nostandar" name="parameter" required>
                                                    <option value selected="{{$detail->parameter}}">{{$detail->parameter}}</option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    @foreach ($parameter as $val)
                                                    <option value="{{$val->parameter}}">{{$val->parameter}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Satuan Parameter</label>
                                                   <select class="form-control Nostandar" name="parameter" required>
                                                    <option value selected="{{$detail->satuan_parameter}}">{{$detail->satuan_parameter}}</option>
                                                    <option disabled>Silahkan Pilih</option>
                                                    @foreach ($parameter as $list)
                                                    <option value="{{$list->satuan}}">{{$list->satuan}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Nilai</label>
                                                <input type="text" name="nilai" class="form-control"
                                                    placeholder="Masukan Nilai" value="{{$detail->nilai}}" required>
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

        $('.Nostandar').select2();


        jQuery('select[name="no_standar"]').on('change', function() {
            var no_standar = this.value;
            jQuery.ajax({
                url: '/bas_logger/spv/get_sampel_parameter/' + no_standar,
                type: "GET",
                data: {
                    no_standar: no_standar
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        // console.log(response);
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="jenis_sampel"]').append('<option value="' + value
                            .jenis_sampel + '">' + value.jenis_sampel + '</option>');
                        });
                        jQuery('select[name="jenis_varian"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="jenis_varian"]').append('<option value="' + value
                                .jenis_varian + '">' + value.jenis_varian + '</option>');
                        });
                    }
                }
            });
        });

</script>


@endpush