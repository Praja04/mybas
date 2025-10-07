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

            <div class="row">
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                      <div class="form-group">
                                        <label for="exampleFormControlInput1">Jenis Import Data</label>
                                        <select class="form-control jenis_import">
                                            <option disabled selected>Silahkan Pilih</option>
                                            <option value="1">Import Excel</option>
                                            <option value="2">Manual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="import">
                                <div class="row">
                                    <form action="/bas_logger/spv/import_parameter_pengecekan" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Pilih File</label>
                                                <input accept=".xlsx" type="file" name="file" class="form-control">
                                            </div>
                                            <div class="col-sm-12">
                                                <a href="/master_import/MASTER_PARAMETER_PENGECEKAN.xlsx"
                                                    class="btn mb-2 btn-block text-white"
                                                    style="border-radius: 8px; background-color: green"><i
                                                        class="fas fa-file-excel text-white"></i> Download Master Excel</a>
                                                <button type="submit" class="btn btn-primary mb-2 btn-block"
                                                    style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                             <div id="manual">
                                 <div class="row">
                                        <form action="/bas_logger/spv/post_parameter_pengecekan" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">No. Standar</label>
                                                   <select class="form-control Nostandar" name="no_standar" required>
                                                    <option disabled selected>Silahkan Pilih</option>
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
                                                <select class="form-control Sampel" name="jenis_sampel" id="jenis_sampel">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Varian</label>
                                                <select class="form-control Varian" name="jenis_varian" readonly>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Parameter</label>
                                                   <select class="form-control" name="parameter" required id="parameter">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Satuan Parameter</label>
                                                <select class="form-control" name="satuan_parameter" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <input type="hidden" name="min" class="form-control"
                                                    placeholder="Min" required>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Standar Value</label>
                                                <select class="form-control" id="nilai">
                                                <option disabled selected>Silahkan Pilih</option>
                                                <option value="min">Lower</option>
                                                <option value="max">Upper</option>
                                                <option value="minmax">Upper & Lower</option>
                                                <option value="standar">Standar</option>
                                                <option value="warna">Warna</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="col-sm-4 mt-2">
                                            <br>
                                            <a href="#lihat_standar" class="btn btn-sm btn-info" data-toggle="modal" data-toggle="tooltip" title="Lihat Standar"><i class="fas fa-eye"></i></a>
                                        </div>
                                    </div>
                                    <div class="row" style="display: none" id="minmax">
                                        <div class="col-sm-5">
                                            <label>Nilai</label>
                                              <input type="text" name="minmax_min" class="form-control"
                                                    placeholder="Min">
                                        </div>
                                        <div class="col-sm-2 mt-4">
                                            <br>
                                            -
                                        </div>
                                        <div class="col-sm-5 mt-2">
                                            <br>
                                            <input type="text" name="minmax_max" class="form-control"
                                                    placeholder="Max">
                                        </div>
                                    </div>
                                    <div class="row" style="display: none" id="warna">
                                        <div class="col-sm-12">
                                            <label>Kode Warna</label>
                                             <select class="form-control Warna" name="kode_warna">
                                                 <option disabled selected>Silahkan Pilih</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row" style="display: none" id="max">
                                        <div class="col-sm-12">
                                            <label>Nilai</label>
                                            <input type="text" name="max" placeholder="Nilai Max" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row" style="display: none" id="min">
                                        <div class="col-sm-12">
                                            <label>Nilai</label>
                                            <input type="text" name="min" placeholder="Nilai Min" class="form-control">
                                        </div>
                                    </div>
                                        <button type="submit" class="btn btn-primary mb-2 btn-block mt-4"
                                        style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                </form>
                            </div>
                        </div>
                     </div>
                  </div>

                 <div class="col-sm-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <form action="/bas_logger/spv/delete_parameter_pengecekan" method="post">
                                            @csrf

                                        <table class="table table-hover" style="zoom: 90%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No.</th>
                                                    <th class="text-center">Aksi</th>
                                                    <th class="text-center">No Standar</th>
                                                    <th class="text-center">Jenis Sampel</th>
                                                    <th class="text-center">Varian</th>
                                                    <th class="text-center">Parameter</th>
                                                    <th class="text-center">Nilai</th>
                                                    <th class="text-center">Satuan Parameter</th>
                                                    <th class="text-center">Tools</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $list)
                                                <tr>
                                                    <td class="text-center">
                                                        {{$loop->iteration}}
                                                    </td>
                                                      <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input Cbox" name="id[]"
                                                                value="{{ $list->id }}">
                                                            <label class="form-check-label"
                                                                for="exampleCheck1">Pilih</label>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        {{$list->no_standar}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$list->jenis_sampel}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$list->jenis_varian}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$list->parameter}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$list->nilai}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$list->satuan_parameter}}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="/bas_logger/spv/edit_parameter_pengecekan/{{$list->id}}" class="btn btn-sm btn-success"><i class="fas fa-edit fa-sm"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                <button type="submit" class="btn btn-primary btn-sm mb-3 mt-4" id="btn_hapus" disabled
                                                   style="border-radius: 10px"> <i class="fas fa-trash-alt"></i>
                                                   Hapus
                                               </button>
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


<!-- Modal-->
<div class="modal fade" id="lihat_standar" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <center><h5>MASTER PARAMETER ANALISA KIMIA</h5></center>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card card-custom">
                    <div class="card-body">
                        <!--begin: Datatable-->
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Jenis Sampel</th>
                                    <th>Parameter</th>
                                    <th>Satuan</th>
                                    <th>Standar Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parameter as $val)
                                <tr class="text-center">
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$val->jenis_sampel}}</td>
                                    <td>{{$val->parameter}}</td>
                                    <td>{{$val->satuan}}</td>
                                    <td>{{$val->standar_value}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
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
        });

        $('.Varian').select2();
        $('.Warna').select2();
        $('.Nostandar').select2();
        $('.Sampel').select2();
        
        $('#import').hide();
        // $('#manual').hide();

                $('.jenis_import').on('change', function() {
            if (this.value == '1') {
                $("#import").show();
            } else {
                $("#import").hide();
            }
        });

        $('.jenis_import').on('change', function() {
            if (this.value == '2') {
                $("#manual").show();
            } else {
                $("#manual").hide();
            }
        });

        $('#nilai').on('change', function() {
            if (this.value == 'minmax') {
                $("#minmax").show();
            } else {
                $("#minmax").hide();
            }
        });
   
        $('#nilai').on('change', function() {
            if (this.value == 'min') {
                $("#min").show();
            } else {
                $("#min").hide();
            }
        });

        $('#nilai').on('change', function() {
            if (this.value == 'max') {
                $("#max").show();
            } else {
                $("#max").hide();
            }
        });
    
        $('#nilai').on('change', function() {
            if (this.value == 'warna') {
                $("#warna").show();
            } else {
                $("#warna").hide();
            }
        });
        
            $(".Cbox").click(function() {
                $("#btn_hapus").attr("disabled", !this.checked);
            });

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
                        // console.log(response.data);
                            $('select[name="jenis_sampel"]').append('<option value="' + response.data[0].jenis_sampel + '">' + response.data[0].jenis_sampel + '</option>');

                            $('select[name="jenis_varian"]').append('<option value="' +  response.data[0]
                                .jenis_varian + '">' +  response.data[0].jenis_varian + '</option>');

                        jQuery('select[name="parameter"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="parameter"]').append('<option value="' + value
                                .parameter + '">' + value.parameter + '</option>');
                        });

                        jQuery('select[name="satuan_parameter"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="satuan_parameter"]').append('<option value="' + value
                                .satuan + '">' + value.satuan + '</option>');
                        });
                    }
                }
            });
        });

          jQuery('select[name="no_standar"]').on('change', function() {
            var no_standar = this.value;
            jQuery.ajax({
                url: '/bas_logger/spv/get_kode_warna/' + no_standar,
                type: "GET",
                data: {
                    no_standar: no_standar
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        console.log(response.data);

                         jQuery('select[name="kode_warna"]').empty();
                        jQuery.each(response.data, function(id, value) {
                            $('select[name="kode_warna"]').append('<option value="' + value
                                .kode_warna + '">' + value.kode_warna + '</option>');
                        });
                    }
                }
            });
        });

        
    </script>

@endpush
