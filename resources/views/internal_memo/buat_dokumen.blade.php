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
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    
    
    @endpush
@section('content')

    <div class="container">
        <div class="main-body">

            
            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 120%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Dashboard"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/internal_memo/menu/index">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-tittle text-center">
                                FORM CREATE INTERNAL MEMO
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <form action="{{ url('/internal_memo/menu/post_dokumen')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">No Dokumen :
                                        </label>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="no_dokumen" class="form-control" id="" value="{{$no_dokumen}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label">Perihal :
                                        </label>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input type="text" name="perihal" class="form-control mb-4" id="" placeholder="Masukan Perihal" autofocus="on" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <textarea id="summernote" name="konten"></textarea>

                                            <hr style="background-color: black">
                                                {{-- <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group row">
                                                                <label for="staticEmail" class="col-sm-2 col-form-label">Pembuat</label>
                                                                <div class="col-sm-9">
                                                                    <select class="form-control select2" name="pembuat" id="pembuat">
                                                                        <option value="{{Auth::user()->username}}" selected>{{Auth::user()->name}}</option>
                                                                        @foreach ($user as $val)
                                                                        <option value="{{$val->username}}">{{$val->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div> --}}
                                                    <div class="row">
                                                        <div class="col-sm-12 mt-2">
                                                            <div class="form-group row">
                                                                <label for="staticEmail" class="col-sm-3 col-form-label">Pembuat</label>
                                                                <div class="col-sm-6">
                                                                    <select class="form-control" name="jml_pembuat">
                                                                        <option disabled selected>Silahkan Pilih</option>
                                                                        <option value="1">1</option>
                                                                        <option value="2">2</option>
                                                                        <option value="3">3</option>
                                                                        <option value="4">4</option>
                                                                        <option value="5">5</option>
                                                                        <option value="7">7</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div id="Pembuat" class="row"></div>
                                                </div>
                                            </div>

                                            {{-- @if($approver_utama)
                                            <div class="row row_approver_utama">
                                                <div class="col-sm-12 mt-2">
                                                    <div class="form-group row">
                                                                <label for="staticEmail" class="col-sm-3 col-form-label">Approver Utama</label>
                                                                <div class="col-sm-6">
                                                                    <select class="form-control Nama_Approver" name="approver_utama" disabled>
                                                                        <option value="{{$nama_approver->EMPNM}}" selected>{{$nama_approver->EMPNM}} | {{$nama_approver->DEPTID}} </option>
                                                                    </select>
                                                                    <button type="button" class="btn btn-sm btn-primary mt-4 ApproverUtama"><i class="fas fa-trash-alt"></i> Hapus</button>

                                                                    <input type="hidden" name="nik_approver[]" value="{{$nama_approver->NIK}}" id="Nik_Approver">

                                                                    <input type="hidden" name="kategori_approver[]" value="Approver" id="">
                                                                    
                                                                    <input type="hidden" name="sub_kategori_approver[]" value="Approver_Utama" id="">

                                                                    <input type="hidden" name="dept_approver[]" value="{{$nama_approver->DEPTID}}" id="">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif --}}
                                                    <div class="row">
                                                        <div class="col-sm-12 mt-2">
                                                            <div class="form-group row">
                                                                <label for="staticEmail" class="col-sm-3 col-form-label">Jumlah Approver</label>
                                                                <div class="col-sm-6">
                                                                    <select class="form-control" name="jml_approver">
                                                                        <option disabled selected>Silahkan Pilih</option>
                                                                        <option value="0">Tidak Ada</option>
                                                                        <option value="1">1</option>
                                                                        <option value="2">2</option>
                                                                        <option value="3">3</option>
                                                                        <option value="4">4</option>
                                                                        <option value="5">5</option>
                                                                        <option value="7">7</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div id="Approver" class="row"></div>
                                                </div>
                                            </div>
                                                    <div class="row">
                                                        <div class="col-sm-12 mt-2">
                                                            <div class="form-group row">
                                                                <label for="staticEmail" class="col-sm-3 col-form-label">Jumlah Mengetahui</label>
                                                                <div class="col-sm-6">
                                                                    <select class="form-control" name="jml_mengetahui">
                                                                        <option disabled selected>Silahkan Pilih</option>
                                                                        <option value="0">Tidak Ada</option>
                                                                        <option value="1">1</option>
                                                                        <option value="2">2</option>
                                                                        <option value="3">3</option>
                                                                        <option value="4">4</option>
                                                                        <option value="5">5</option>
                                                                        <option value="7">7</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div id="Mengetahui" class="row"></div>
                                                </div>
                                            </div>
                                                    <div class="row">
                                                        <div class="col-sm-12 mt-2">
                                                            <div class="form-group row">
                                                                <label for="staticEmail" class="col-sm-3 col-form-label">Jumlah Penerima</label>
                                                                <div class="col-sm-6">
                                                                    <select class="form-control" name="jml_penerima">
                                                                        <option disabled selected>Silahkan Pilih</option>
                                                                        <option value="0">Tidak Ada</option>
                                                                        <option value="1">1</option>
                                                                        <option value="2">2</option>
                                                                        <option value="3">3</option>
                                                                        <option value="4">4</option>
                                                                        <option value="5">5</option>
                                                                        <option value="7">7</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div id="Penerima" class="row"></div>
                                                </div>
                                            </div>
                                        </div>
                                
                                    <div class="card-footer">
                                            <div class="float-right">
                                                        <button type="submit" class="btn btn-md btn-primary btn-block BtnSave"><i class="fas fa-save"></i> Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
            </div>


@endsection


@push('scripts')

<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{url ('/assets/js/summernote.js')}}"></script>

<script src="{{url ('/assets/js/pages/crud/forms/widgets/select2.js?v=7.2.8') }} "></script>

<script type="text/javascript">


  $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: 'Dengan Hormat,',
                    height: 200,
                    popatmouse: true,
                    toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['picture']],
                    ],
            });
        });
        $('.BtnSave').click(function(){
            $('.BtnSave').hide();
        });
       
        $('.ApproverUtama').click(function(){
            $('.row_approver_utama').html("");
        });

    var col_sm_ = [
        "",
        "col-sm-12", 
        "col-sm-6", 
        "col-sm-4", 
        "col-sm-3", 
        "col-sm-3", 
        "col-sm-2", 
        "col-sm-1", 
    ];

      jQuery('select[name="jml_pembuat"]').on('change', function() {
            $("#Pembuat").html("")
            var jml_pembuat = this.value;
                for (var o = 1; o <= jml_pembuat; o++){
                    $("#Pembuat").append(
                        '<div class="'+ col_sm_[jml_pembuat] +' border">\
                            <table class="table">\
                            <thead>\
                                <tr class="text-center">\
                                <th scope="col">pembuat Ke '+ o +'</th>\
                                </tr>\
                                <tr class="text-center">\
                                <th>\
                                <a href="#add_pembuat_'+o+'" data-toggle="modal" class="btn btn-sm btn-primary" data-toggle="modal"><i class="fas fa-search"></i> Pilih pembuat</a>\
                                </th>\
                                </tr>\
                                <th>\
                                <b><input type="text" value="" class="form-control nama_pembuat_'+o+' text-center" readonly></b>\
                                 <b><input type="hidden" value="" name="dept_pembuat[]" class="form-control dept_pembuat_'+o+' text-center" readonly></b>\
                                </th>\
                            </thead>\
                            </tbody>\
                            </table>\
                            </div>\
                            <div class="modal fade" id="add_pembuat_'+ o +'" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">\
                                <div class="modal-dialog modal-dialog-centered" role="document">\
                                    <div class="modal-content">\
                                        <div class="modal-header">\
                                            <h5 class="modal-title">PEMBUAT KE '+o+' </h5>\
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                                    <span aria-hidden="true">&times;</span>\
                                                </button>\
                                        </div>\
                                        <div class="modal-body">\
                                            <div class="row">\
                                                <div class="col-sm-12">\
                                                 <select class="form-control select2" name="nik_pembuat[]" id="pembuat_'+o+'" style="width: 100%">\
                                                <option value="{{Auth::user()->username}}"" selected>{{Auth::user()->name}}</option>\
                                                @foreach($user as $daftar)\
                                                <option value="{{$daftar->username}}">{{$daftar->name}}</option>\
                                                @endforeach\
                                            </select>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="modal-footer">\
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>\
                                    <button type="button" class="btn btn-primary btn-sm" onclick="SavePembuat('+o+');" data-dismiss="modal"><i class="fas fa-save"></i> Simpan</button>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                        '
                    );
                     $('#pembuat_' + o).select2({
                    placeholder: "Select a state"
                    });
                }

                SavePembuat =  function(t){
                    var isi = $("#pembuat_" + t).val();

                    jQuery.ajax({
                url: '/internal_memo/ajax/post_nama/' + isi,
                type: "POST",
                data: {
                    isi: isi
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        $(".nama_pembuat_" + t).val(response.data.name);
                        $(".dept_pembuat_" + t).val(response.data.dept_id);
                    }
                }
            });
                }
        });

      jQuery('select[name="jml_approver"]').on('change', function() {
            $("#Approver").html("")
            var jml_approver = this.value;
                for (var i = 1; i <= jml_approver; i++){
                    $("#Approver").append(
                        '<div class="'+ col_sm_[jml_approver] +' border">\
                            <table class="table">\
                            <thead>\
                                <tr class="text-center">\
                                <th scope="col">Approver Ke '+ i +'</th>\
                                </tr>\
                                <tr class="text-center">\
                                <th>\
                                <a href="#add_approver_'+i+'" data-toggle="modal" class="btn btn-sm btn-primary" data-toggle="modal"><i class="fas fa-search"></i> Pilih Approver</a>\
                                </th>\
                                </tr>\
                                <tr class="text-center">\
                                <th>\
                                <b><input type="text" value="" class="form-control nama_approver_'+i+' text-center" readonly></b>\
                                <b><input type="hidden" value="" class="form-control dept_approver_'+i+' text-center" name="dept_approver[]"></b>\
                                <b><input type="hidden" value="Approver" name="kategori_approver[]" class="form-control text-center" readonly></b>\
                                <b><input type="hidden" value="Approver_'+i+'" name="sub_kategori_approver[]" class="form-control text-center" readonly></b>\
                                </th>\
                                </tr>\
                            </thead>\
                            </tbody>\
                            </table>\
                            </div>\
                            <div class="modal fade" id="add_approver_'+ i +'" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">\
                                <div class="modal-dialog modal-dialog-centered" role="document">\
                                    <div class="modal-content">\
                                        <div class="modal-header">\
                                            <h5 class="modal-title">APPROVER KE '+i+' </h5>\
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                                    <span aria-hidden="true">&times;</span>\
                                                </button>\
                                        </div>\
                                        <div class="modal-body">\
                                            <div class="row">\
                                                <div class="col-sm-12">\
                                            <select class="form-control select2" name="nik_approver[]" id="approver_'+i+'" style="width: 100%">\
                                                <option disabled selected>Silahkan Pilih Approver</option>\
                                                @foreach($user as $value)\
                                                <option value="{{$value->username}}">{{$value->name}}</option>\
                                                @endforeach\
                                            </select>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="modal-footer">\
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>\
                                    <button type="button" class="btn btn-primary btn-sm" onclick="SaveApprover('+i+');" data-dismiss="modal"><i class="fas fa-save"></i> Simpan</button>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                        '
                    );

                     $('#approver_' + i).select2({
                    placeholder: "Select a state"
                    });
                }

                SaveApprover =  function(t){
                    var isi = $("#approver_" + t).val();

                    jQuery.ajax({
                url: '/internal_memo/ajax/post_nama/' + isi,
                type: "POST",
                data: {
                    isi: isi
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        $(".nama_approver_" + t).val(response.data.name);
                        $(".dept_approver_" + t).val(response.data.dept_id);
                    }
                }
            });

                }
        });
      
      jQuery('select[name="jml_mengetahui"]').on('change', function() {
             $("#Mengetahui").html("")
            var jml_mengetahui = this.value;
                for (var i = 1; i <= jml_mengetahui; i++){
                    $("#Mengetahui").append(
                        '<div class="'+ col_sm_[jml_mengetahui] +' border">\
                            <table class="table">\
                            <thead>\
                                <tr class="text-center">\
                                <th scope="col">Mengetahui Ke '+ i +'</th>\
                                </tr>\
                                <tr class="text-center">\
                                <th>\
                                <a href="#add_mengetahui_'+i+'" data-toggle="modal" class="btn btn-sm btn-primary" data-toggle="modal"><i class="fas fa-search"></i> Pilih mengetahui</a>\
                                </th>\
                                </tr>\
                                 <th>\
                                <b><input type="text" value="" class="form-control nama_mengetahui_'+i+' text-center" readonly></b>\
                                <b><input type="hidden" value="" class="form-control dept_mengetahui_'+i+' text-center" name="dept_mengetahui[]"></b>\
                                <b><input type="hidden" value="Mengetahui" name="kategori_mengetahui[]" class="form-control text-center" readonly></b>\
                                <b><input type="hidden" value="Mengetahui_'+i+'" name="sub_kategori_mengetahui[]" class="form-control text-center" readonly></b>\
                                </th>\
                            </thead>\
                            </tbody>\
                            </table>\
                            </div>\
                            <div class="modal fade" id="add_mengetahui_'+ i +'" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">\
                                <div class="modal-dialog modal-dialog-centered" role="document">\
                                    <div class="modal-content">\
                                        <div class="modal-header">\
                                            <h5 class="modal-title">Mengetahui KE '+i+' </h5>\
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                                    <span aria-hidden="true">&times;</span>\
                                                </button>\
                                        </div>\
                                        <div class="modal-body">\
                                            <div class="row">\
                                                <div class="col-sm-12">\
                                            <select class="form-control select2" name="nik_mengetahui[]" id="mengetahui_'+i+'" style="width: 100%">\
                                                <option disabled selected>Silahkan Pilih Mengetahui</option>\
                                                @foreach($user as $val)\
                                                <option value="{{$val->username}}">{{$val->name}}</option>\
                                                @endforeach\
                                            </select>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="modal-footer">\
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>\
                                    <button type="button" class="btn btn-primary btn-sm" onclick="SaveMengetahui('+i+');" data-dismiss="modal"><i class="fas fa-save"></i> Simpan</button>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                        '
                    );
                     $('#mengetahui_' + i).select2({
                    placeholder: "Select a state"
                    });
                }

                SaveMengetahui =  function(t){
                    var isi = $("#mengetahui_" + t).val();

                   jQuery.ajax({
                url: '/internal_memo/ajax/post_nama/' + isi,
                type: "POST",
                data: {
                    isi: isi
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        $(".nama_mengetahui_" + t).val(response.data.name);
                        $(".dept_mengetahui_" + t).val(response.data.dept_id);
                    }
                }
            });
                }
            });
     
      jQuery('select[name="jml_penerima"]').on('change', function() {
          $("#Penerima").html("")
            var jml_penerima = this.value;
                for (var i = 1; i <= jml_penerima; i++){
                    $("#Penerima").append(
                        '<div class="'+ col_sm_[jml_penerima] +' border">\
                            <table class="table">\
                            <thead>\
                                <tr class="text-center">\
                                <th>Penerima Ke '+ i +'</th>\
                                </tr>\
                                <tr class="text-center">\
                                <th>\
                                <a href="#add_penerima_'+i+'" data-toggle="modal" class="btn btn-sm btn-primary" data-toggle="modal"><i class="fas fa-search"></i> Pilih penerima</a>\
                                </th>\
                                </tr>\
                                 <th>\
                                <b><input type="text" value="" class="form-control nama_penerima_'+i+' text-center" readonly></b>\
                                <b><input type="hidden" value="" class="form-control dept_penerima_'+i+' text-center" name="dept_penerima[]"></b>\
                                <b><input type="hidden" value="Penerima" name="kategori_penerima[]" class="form-control text-center" readonly></b>\
                                <b><input type="hidden" value="Penerima_'+i+'" name="sub_kategori_penerima[]" class="form-control text-center" readonly></b>\
                                </th>\
                            </thead>\
                            </tbody>\
                            </table>\
                            </div>\
                            <div class="modal fade" id="add_penerima_'+ i +'" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">\
                                <div class="modal-dialog modal-dialog-centered" role="document">\
                                    <div class="modal-content">\
                                        <div class="modal-header">\
                                            <h5 class="modal-title">penerima KE '+i+' </h5>\
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                                    <span aria-hidden="true">&times;</span>\
                                                </button>\
                                        </div>\
                                        <div class="modal-body">\
                                            <div class="row">\
                                                <div class="col-sm-12">\
                                            <select class="form-control select2" name="nik_penerima[]" id="penerima_'+i+'" style="width: 100%">\
                                                <option disabled selected>Silahkan Pilih penerima</option>\
                                                @foreach($user as $val)\
                                                <option value="{{$val->username}}">{{$val->name}}</option>\
                                                @endforeach\
                                            </select>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="modal-footer">\
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>\
                                    <button type="button" class="btn btn-primary btn-sm"  onclick="SavePenerima('+i+');" data-dismiss="modal">><i class="fas fa-save"></i> Simpan</button>\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                        '
                    );
                     $('#penerima_' + i).select2({
                    placeholder: "Select a state"
                    });
                }

                 SavePenerima =  function(t){
                    var isi = $("#penerima_" + t).val();
                    
            jQuery.ajax({
                url: '/internal_memo/ajax/post_nama/' + isi,
                type: "POST",
                data: {
                    isi: isi
                },
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        $(".nama_penerima_" + t).val(response.data.name);
                        $(".dept_penerima_" + t).val(response.data.dept_id);
                    }
                }
            });
                }
            });

    </script>

@endpush
