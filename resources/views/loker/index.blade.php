@extends('layouts.base')

@push('styles')
<style type="text/css">
    .item-nih {
        overflow: hidden;
    }
</style>
@endpush

@section('content')
<div class="print-area" style="width: 100%; padding-left: 20px; padding-right: 20px">
    <div id="print-karyawan-loker" class="row">
    </div>
</div>
<div class="container-fluid hide-print">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom bg-light-warning" style="border-radius: 26px;">
                <div class="card-header">
                    <div class="card-title">
                        <h3>Loker Online BAS</h3>
                    </div>
                    {{-- <div class="card-toolbar"> --}}
                         {{-- <a href="javascript:void(0)" class="btn btn-sm btn-info font-weight-bolder text-white" style="border-radius: 26px;" data-toggle="modal" aria-haspopup="true" aria-expanded="false"><i class="fas fa-clipboard-list text-white"></i> List Kebutuhan Loker From FPTK</a>
                         <a onClick="showDataKaryawanMasuk()" href="javascript:void(0)" class="btn btn-sm btn-warning font-weight-bolder ml-4" style="border-radius: 26px;" aria-haspopup="true" aria-expanded="false"><i class="fas fa-users"></i> Karyawan Belum Punya Loker</a>
                         <a onClick="showDataResignDanKabur()" href="javascript:void(0)" class="btn btn-sm btn-dark font-weight-bolder text-white ml-4" style="border-radius: 26px;" aria-haspopup="true" aria-expanded="false"><i class="fas fa-users text-white"></i> List Karyawan Keluar & Kabur</a> --}}

                    {{-- </div> --}}
                  
                    {{-- <div class="card-toolbar">
                        <a href="#modal_upload" class="btn btn-sm font-weight-bolder text-white ml-4" style="background-color: green; border-radius: 26px;" data-toggle="modal" aria-haspopup="true" aria-expanded="false"><i class="fas fa-file-excel text-white"></i> Export Excel Loker</a>
                    </div> --}}
                    <div class="card-toolbar">
                        <a href="#modal_loker_kosong" class="btn btn-success btn-sm ml-4 font-weight-bolder" data-toggle="modal" aria-haspopup="true" aria-expanded="false" style="border-radius: 26px;"><i class="fas fa-search"></i> Cek Stok Loker Kosong</a>
                    </div>
                    
                </div>
                <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger" style="border-radius: 13px;">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                        </ul>
                    </div>
                @endif
                    <div class="row">
                        @foreach ($pilih_loker as $loker)
                        <div class="col-sm-6">
                            <div class="card card-custom card-stretch bg-light-warning gutter-b Card_Loker Card_{{$loker->kode_area}}" style="border-radius: 26px;">
                                <div class="card-header">
                                    <div class="card-toolbar">
                                    </div>

                                    <div class="card-toolbar">
                                        <div class="dropdown dropdown-inline">
                                            <a href="#" onclick="return false;" class="btn btn-primary btn-sm font-weight-bolder dropdown-toggle " data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 26px;"><i class="fas fa-tools"></i> Tools</a>
                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <ul class="navi navi-hover">
                                                    <li class="navi-header pb-1">
                                                        <span class="text-primary text-uppercase font-weight-bold font-size-sm">Tools:</span>
                                                    </li>
                                                    <li class="navi-item">
                                                        <a href="{{url('loker/history_loker/' . $loker->kode_area)}}" class="navi-link">
                                                            <span class="navi-icon">
                                                                <i class="fas fa-hourglass text-dark"></i>
                                                            </span>
                                                            <span class="navi-text">History Loker 
                                                                @if($loker->kode_area == 'ps1')
                                                                Pria Sepatu 1
                                                                @elseif($loker->kode_area == 'ps2')
                                                                Pria Sepatu 2
                                                                @elseif($loker->kode_area == 'pb1')
                                                                Pria Baju 1
                                                                @elseif($loker->kode_area == 'pb2')
                                                                Pria Baju 2
                                                                @elseif($loker->kode_area == 'ws1')
                                                                Wanita Sepatu 1
                                                                @elseif($loker->kode_area == 'ws2')
                                                                Wanita Sepatu 2
                                                                @elseif($loker->kode_area == 'wb1')
                                                                Wanita Baju 1
                                                                @else
                                                                Wanita Baju 2
                                                                @endif
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="navi-item">
                                                        <a href="{{url('loker/database/' . $loker->kode_area)}}" class="navi-link">
                                                            <span class="navi-icon">
                                                                <i class="fas fa-database text-dark"></i>
                                                            </span>
                                                            <span class="navi-text">DATABASE</span>
                                                        </a>
                                                    </li>
                                                    <li class="navi-item">
                                                        <a href="javascript:void(0)" onclick="upload_excel('{{$loker->kode_area}}')" class="navi-link">
                                                            <span class="navi-icon">
                                                                <i class="fas fa-file-excel text-dark"></i>
                                                            </span>
                                                            <span class="navi-text">UPLOAD EXCEL</span>
                                                        </a>
                                                    </li>
                                                    <li class="navi-item">
                                                        <a href="javascript:void(0)" onclick="export_excel('{{$loker->kode_area}}')" class="navi-link">
                                                            <span class="navi-icon">
                                                                <i class="fas fa-file-excel text-dark"></i>
                                                            </span>
                                                            <span class="navi-text">EXPORT TO EXCEL</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body d-flex align-items-center py-0 mt-8">
                                    <a href="#" onclick="return false;" class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-2 text-hover-primary Click_area_card" data-kode-area="{{$loker->kode_area}}">{{$loker->nama_area}}</a>
                                    <div class="d-flex flex-column flex-grow-1 py-2 py-lg-5">
                                    </div>
                                    @if($loker->kode_area == 'wb1')
                                     <img src="assets/media/svg/avatars/003-girl-1.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'wb2')
                                     <img src="assets/media/svg/avatars/003-girl-1.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'ws1')
                                     <img src="assets/media/svg/avatars/023-girl-13.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'ws2')
                                     <img src="assets/media/svg/avatars/023-girl-13.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'pb1')
                                     <img src="assets/media/svg/avatars/009-boy-4.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'pb2')
                                     <img src="assets/media/svg/avatars/009-boy-4.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'ps1')
                                     <img src="assets/media/svg/avatars/008-boy-3.svg" alt="" class="align-self-end h-100px" />
                                     @else
                                     <img src="assets/media/svg/avatars/008-boy-3.svg" alt="" class="align-self-end h-100px" />
                                     @endif
                                     {{-- @if($loker->kode_area == 'wb1')
                                     <img src="assets/media/svg/avatars/003-girl-1.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'wb2')
                                     <img src="assets/media/svg/avatars/023-girl-13.svg" alt="" class="align-self-end h-100px" />
                                     @elseif($loker->kode_area == 'p_lama')
                                     <img src="assets/media/svg/avatars/009-boy-4.svg" alt="" class="align-self-end h-100px" />
                                     @else
                                     <img src="assets/media/svg/avatars/008-boy-3.svg" alt="" class="align-self-end h-100px" />
                                     @endif --}}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @foreach ($pilih_loker as $loker)
                    <div class="row" id="{{$loker->kode_area}}" style="display: none;">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body Body_{{$loker->kode_area}}">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                               
                    <div class="row">
                        <div class="col-sm-12 CardBlok" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="symbol symbol-80 symbol-light-warning mr-4 flex-shrink-0 mt-4">
                                                <div class="symbol-label">
                                                    <a href="javascript:void(0)" onclick="ToggleValueBlok()">
                                                        <span class="svg-icon svg-icon-lg svg-icon-warning ValueBlok">
                                                        </span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
           
                    <div class="row">
                        <div class="col-sm-12 CardNomer" style="display: none;">
                            <div class="card">
                                <div class="card-body BodyListNomer">
                                    <div class="row">
                                        <div class="col-sm-12">
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="input_loker" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                     <div class="row">
                                         <div class="col-sm-12">
                                             <div class="card card-custom">
                                                 <form action="{{ url('loker/post_user_loker')}}" method="post">
                                                    @csrf

                                                    <input type="hidden" name="no_loker" class="input_value_no_loker" value="">
                                                    <input type="hidden" name="kode_area" class="input_value_kode_area" value="">
                                                    <input type="hidden" name="kode_blok" class="input_value_kode_blok" value="">

                                                    <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group mb-8">
                                                                <div class="alert alert-custom alert-default" role="alert">
                                                                    <div class="alert-icon"><i class="flaticon-warning text-primary"></i></div>
                                                                    <div class="alert-text">
                                                                        FORM INPUT LOKER <a href="javascript:void(0)" class="ml-4 btn btn-dark TandaiRusak" style="border-radius: 13px;" onclick="tandai_rusak()"><i class="fas fa-bookmark"></i> Tandai Rusak</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                            <div class="form-group">
                                                                <label for="exampleSelect1">Pilih Karyawan <span class="text-danger">*</span></label>
                                                                <select class="form-control select2 Karyawan" id="exampleSelect1" name="nik_karyawan_baru" style="width: 100%;">
                                                                    <option selected disabled>Silahkan Pilih</option>
                                                                        @foreach ($karyawan_baru as  $user)
                                                                        <option value="{{$user->NIK}}">{{$user->EMPNM}} | {{$user->NIK}} | {{$user->DEPTID}}</option>
                                                                        @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea name="keterangan" class="form-control" placeholder="Masukan Keterangan" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary BtnSave"><i class="fas fa-save"></i> Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

                <div class="modal fade" id="modal_history_karyawan" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="card card-custom">
                                                <div class="card-header">
                                                    <div class="card-toolbar"></div>
                                                      <div class="card-toolbar">
                                                        <a href="javascript:void(0)" class="btn btn-sm" onclick="BtnExport()" style="background-color: rgb(143, 238, 0)"><i class="fas fa-file-excel text-dark"></i> Export To Excel </a>
                                                    </div>
                                                    </div>
                                                <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group mb-8">
                                                            <div class="alert alert-custom alert-default" role="alert">
                                                                <div class="alert-icon"><i class="flaticon-warning text-primary"></i></div>
                                                                <div class="alert-text">
                                                                    DETAIL HISTORY KEPEMILIKAN LOKER KARYAWAN 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="table-responsive">
                                                        <table class="table datatable table-bordered">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th>No.</th>
                                                                    <th>Status</th>
                                                                    <th>No. Loker</th>
                                                                    <th>Keterangan</th>
                                                                    <th>Kode Area</th>
                                                                    <th>Kode Blok</th>
                                                                    <th>Tanggal Pengisian</th>
                                                                    <th>Input By</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="body_history_karyawan">

                                                            </tbody>
                                                            
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary BtnCloseHistory" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modal_loker_kosong" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card card-custom gutter-b">
                                        <div class="card-header card-header-tabs-line">
                                            <div class="card-toolbar">
                                                <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                                    <li class="nav-item">
                                                        <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1_4">
                                                            <span class="nav-icon"><i class="fas fa-list"></i></span>
                                                            <span class="nav-text">Stok Loker Kosong</span>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2_4">
                                                            <span class="nav-icon"><i class="fas fa-user"></i></span>
                                                            <span class="nav-text">List Loker Karyawan Aktif</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="kt_tab_pane_1_4" role="tabpanel" aria-labelledby="kt_tab_pane_1_4">
                                                             <div class="card card-custom">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample6">
                                                        
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#pb1">
                                                                    <i class="fa fa-male mr-4"></i> Loker Pria Baju 1
                                                                    @if($pb1 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif 
                                                                     @if(count($pb1) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($pb1)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="pb1" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($pb1 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#pb2">
                                                                    <i class="fa fa-male mr-4"></i> Loker Pria Baju 2
                                                                    @if($pb2 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif 
                                                                     @if(count($pb2) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($pb2)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="pb2" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($pb2 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#ps1">
                                                                    <i class="fa fa-male mr-4"></i> Loker Pria Sepatu 1
                                                                    @if($ps1 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif 
                                                                     @if(count($ps1) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($ps1)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="ps1" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($ps1 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#ps2">
                                                                    <i class="fa fa-male mr-4"></i> Loker Pria Sepatu 2
                                                                    @if($ps2 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif 
                                                                     @if(count($ps2) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($ps2)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="ps2" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($ps2 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#wb1">
                                                                    <i class="fa fa-female mr-4"></i> Loker Wanita Baju 1 
                                                                    @if($wb1 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif
                                                                       @if(count($wb1) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($wb1)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="wb1" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($wb1 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#wb2">
                                                                    <i class="fa fa-female mr-4"></i> Loker Wanita Baju 2
                                                                    @if($wb2 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif 
                                                                     @if(count($wb2) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($wb2)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="wb2" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($wb2 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#ws1">
                                                                    <i class="fa fa-male mr-4"></i> Loker Wanita Sepatu 1
                                                                    @if($ws1 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif 
                                                                     @if(count($ws1) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($ws1)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="ws1" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($ws1 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-header" id="headingOne6">
                                                                <div class="card-title" data-toggle="collapse" data-target="#ws2">
                                                                    <i class="fa fa-female mr-4"></i> Loker Wanita Sepatu 2
                                                                    @if($ws2 == null)
                                                                    <span class="badge badge-dark ml-4"> Full</span>
                                                                    @endif 
                                                                     @if(count($ws2) > 0)
                                                                        <span class="badge badge-pill badge-primary ml-4">{{count($ws2)}} Loker Free</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div id="ws2" class="collapse" data-parent="#accordionExample6">
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach($ws2 as $list)
                                                                        <div class="col-sm-2">
                                                                            <div class="symbol symbol-80 symbol-light-info mr-4 flex-shrink-0 mt-4">
                                                                                <div class="symbol-label">
                                                                                    <i class="symbol-badge bg-success"></i>
                                                                                    <a href="javascript:void(0)" >
                                                                                        <span class="svg-icon svg-icon-lg svg-icon-dark">
                                                                                        </span>
                                                                                        {{$list['no_loker'] ?? ''}}
                                                                                    </a>
                                                                                </div>
                                                                                <center> <span class="badge badge-info bagde-pill mt-1"> Free </span> </center>
                                                                            </div> 
                                                                        </div>
                                                                        @endforeach
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
                                    <div class="tab-pane fade" id="kt_tab_pane_2_4" role="tabpanel" aria-labelledby="kt_tab_pane_2_4">
                                        <div class="row">
                                            <div class="col-sm-12 mb-5">
                                                <button class="btn btn-primary" onClick="window.location.reload()"><i class="fa fa-refresh"></i> Refresh Data</button>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="table-responsive">
                                                    <table class="table datatable table-bordered">
                                                        <thead>
                                                            <tr class="text-center">
                                                                <th>No.</th>
                                                                <th>Nama</th>
                                                                <th>NIK</th>
                                                                <th>Jenis Kelamin</th>
                                                                <th>Divisi</th>
                                                                <th>Area Loker</th>
                                      
                                                                <th>Kode Blok</th>
                                                                <th>No Loker</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($master_user->where('nik', '!=', '') as $list)
                                                            <tr class="text-center">
                                                                <td>{{$loop->iteration}}</td>
                                                                <td> <a href="javascript:void(0)" onclick="history_karyawan('{{$list->nik}}')">
                                                                    {{$list->nama}} </a>
                                                                </td>
                                                                <td>{{$list->nik}}</td>
                                                                <td>{{$list->jk}}</td>
                                                                <td>{{$list->divisi}}</td>
                                                                <td>
                                                                    @if($list->kode_area == 'ps1')
                                                                    Pria Sepatu 1
                                                                    @elseif($list->kode_area == 'ps2')
                                                                    Pria Sepatu 2
                                                                    @elseif($list->kode_area == 'pb1')
                                                                    Pria Baju 1
                                                                    @elseif($list->kode_area == 'pb2')
                                                                    Pria Baju 2
                                                                    @elseif($list->kode_area == 'ws1')
                                                                    Wanita Sepatu 1
                                                                    @elseif($list->kode_area == 'ws2')
                                                                    Wanita Sepatu 2
                                                                    @elseif($list->kode_area == 'wb1')
                                                                    Wanita Baju 1
                                                                    @elseif($list->kode_area == 'wb2')
                                                                    Wanita Baju 2
                                                                    @else
                                                                    Loker Area
                                                                    @endif  
                                                                </td>
                                                                <td>{{$list->kode_blok}}</td>
                                                                <td>
                                                                    @if($list->no_loker == '')
                                                                    <span class="badge badge-pill badge-dark">Kosong</span>
                                                                    @else
                                                                    {{$list->no_loker}}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
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
                <div class="row">
                    <div class="col-sm-4">
                        <a href="javascript:void(0)" class="badge badge-primary mt-3" style="width: 100%;"> Total Loker Kosong : {{count($cek_loker_kosong)}} Pintu</a>
                        <a href="javascript:void(0)" class="badge badge-info mt-3" style="width: 100%;"> Total Loker Terisi : {{$cek_loker_terisi}} Pintu</a>
                        <a href="javascript:void(0)" class="badge badge-dark mt-3" style="width: 100%;"> Total Loker Rusak : {{$cek_loker_rusak}} Pintu</a>

                    </div>
                    <div class="col-sm-8">
                        <h2 class="mt-4">Total Loker : {{$cek_loker_terisi + count($cek_loker_kosong) + $cek_loker_rusak}} PINTU</h2>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>

            <div class="modal fade" id="detail_loker" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card card-custom">
                                          <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group mb-8">
                                                        <div class="alert alert-custom alert-default" role="alert">
                                                            <div class="alert-text">
                                                                DETAIL LOKER NO. <b class="no_loker"></b>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card card-custom gutter-b">
                                                    <div class="card-body">
                                                        <div class="d-flex mb-9">
                                                            <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                                                <div class="symbol symbol-50 symbol-lg-120">
                                                                    <img id="picture" class="mr-4" src="/assets/media/users/blank.png" style="width: 150%;border-radius: 10px; margin-right: 23px;" alt="image" />
                                                                </div>
                                                                <div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
                                                                    <span class="font-size-h3 symbol-label font-weight-boldest"></span>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex justify-content-between flex-wrap mt-1">
                                                                    <div class="d-flex mr-3">
                                                                        <a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 Nama" style="margin-left: 20px;" ></a>
                                                                        <a href="#" onclick="return false;">
                                                                            <i class="flaticon2-correct text-success font-size-h5"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="my-lg-0 my-3 BtnEdit">
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex flex-wrap justify-content-between mt-1">
                                                                    <div class="d-flex flex-column flex-grow-1 pr-8">
                                                                        <div class="d-flex flex-wrap mb-4">
                                                                            <a class="text-dark font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 " style="margin-left: 20px;">
                                                                            DEPT : <b class="DeptValue"></b> </a>
                                                                            <a class="text-dark font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 " style="margin-left: 20px;">
                                                                            NIK: <b class="NIKValue"></b></a>
                                                                            <a class="text-dark font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 " style="margin-left: 20px;">
                                                                            KODE BLOK : <b class="KodeBlokValue"></b></a>
                                                                            <a class="text-dark font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 " style="margin-left: 20px;">
                                                                            NO.LOKER : <b class="NoLokerValue"></b></a>
                                                                        </div>
                                                                        <span class="font-weight-bold text-dark KodeKontrakValue" style="margin-left: 20px;"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="separator separator-solid"></div>
                                                    </div>
                                                </div>
                                                                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="tarik_kunci" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                          <label for="">Keterangan</label>
                                          <textarea class="form-control Keterangan" name="keterangan" id="" rows="3" placeholder="Resign"></textarea>
                                        </div>
                                        <div id="reader" style="width:500px; height:500px; border-radius:20px; display: none;"></div>
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

  <div class="modal fade" id="modal_upload" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-custom">
                                <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group mb-8">
                                            <div class="alert alert-custom alert-default" role="alert">
                                                <div class="alert-text">
                                                  FORM UPLOAD EXCEL PENGGUNA <b class="JudulUploadFIle"></b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card card-custom gutter-b">
                                        <form action="{{url('loker/import_loker_user')}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for=""></label>
                                                        <input type="file" class="form-control-file" name="file" id="" placeholder="Masukan" aria-describedby="fileHelpId">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="BodyUpload">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="float-right">
                                                    <button type="submit" class="btn btn-primary btn-sm BtnUploadFile" style="border-radius: 13px;"><i class="fas fa-save"></i> Simpan</button>
                                                     <button type="button" class="btn btn-outline-danger spinner spinner-darker-danger spinner-left mr-3 Proses" style="display: none;" disabled>
                                                    Proses Upload..
                                                    </button>
                                                </div>
                                            </div>
                                          </form>
                                        </div>
                                    </div>
                                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

  <div class="modal fade" id="sudah_benar" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                           <a id="" class="btn btn-success btn-md" href="javascript:void(0)" onclick="sudah_benar()" role="button"><i class="fas fa-check mr-4"></i>  Tandai Loker Sudah Tidak Rusak</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>

  <div class="modal fade" id="list_kabur" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-custom gutter-b">
                            <div class="card-header card-header-tabs-line">
                                <div class="card-toolbar">
                                    <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#kabur">
                                                <span class="nav-icon"><i class="fas fa-user"></i></span>
                                                <span class="nav-text">Karyawan Kabur</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#phk">
                                                <span class="nav-icon"><i class="fas fa-users"></i></span>
                                                <span class="nav-text">Karyawan sudah keluar masih punya loker</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="kabur" role="tabpanel" aria-labelledby="kabur">
                                        ...
                                    </div>
                                    <div class="tab-pane fade show" id="phk" role="tabpanel" aria-labelledby="phk">
                                        <button id="phk-submit-button" type="submit" class="btn btn-primary btn-sm mb-2" style="display: none"><span id="phk-submit-button-text">Tarik Loker</span> (<span id="phk-submit-count"></span>)</button>
                                        <table id="phk-table" class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                            <thead>
                                                <tr class="text-left text-uppercase">
                                                    <th style="width: 40px"><input type="checkbox" id="phk-check-all" class="form-control"/></th>
                                                    <th style="min-width: 100px">NIK</th>
                                                    <th style="min-width: 100px">NAMA</th>
                                                    <th style="min-width: 100px">KODE AREA</th>
                                                    <th style="min-width: 130px">KODE BLOK</th>
                                                    <th style="min-width: 130px">NOMOR LOKER</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="float-right">
                        <button type="button" data-dismiss="modal"class="btn btn-dark btn-sm" style="border-radius: 13px;"><i class="fas fa-times"></i> Tutup</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade hide-print" id="list_pkw" tabindex="-1" role="dialog" aria-labelledby="list_pkw" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>List karyawan belum punya loker</h3>
                </div>
                <div class="modal-body">
                    <div class="card card-custom gutter-b">
                        <div class="card-header card-header-tabs-line">
                            <div class="card-toolbar">
                                <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#pkw-laki-laki">
                                            <span class="nav-icon"><i class="fas fa-male"></i></span>
                                            <span class="nav-text">Karyawan Laki-laki</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#pkw-perempuan">
                                            <span class="nav-icon"><i class="fas fa-female"></i></span>
                                            <span class="nav-text">Karyawan Perempuan</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pkw-laki-laki" role="tabpanel" aria-labelledby="kabur">
                                    <button id="laki-laki-submit-button" type="submit" class="btn btn-success btn-sm mb-2" style="display: none"><span id="laki-laki-submit-button-text">Save</span> (<span id="laki-laki-submit-count"></span>)</button>
                                    <div class="row" id="laki-laki-store-error" style="display: none">
                                        <div class="col-12">
                                            <div class="alert alert-danger">
                                                <span id="laki-laki-store-error-message"></span> : <strong id="laki-laki-store-error-content"></strong>.
                                            </div>
                                        </div>
                                    </div>
                                    <table id="pkw-laki-laki-table" class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                        <thead>
                                            <tr class="text-left text-uppercase">
                                                <th style="width: 40px"><input type="checkbox" id="laki-laki-check-all" class="form-control"/></th>
                                                <th style="min-width: 100px">NIK</th>
                                                <th style="min-width: 100px">NAMA</th>
                                                <th style="min-width: 100px">KODE AREA</th>
                                                <th style="min-width: 130px">KODE BLOK</th>
                                                <th style="min-width: 130px">NOMOR LOKER</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="tab-pane fade show" id="pkw-perempuan" role="tabpanel" aria-labelledby="kabur">
                                    <button id="perempuan-submit-button" type="submit" class="btn btn-success btn-sm mb-2" style="display: none"><span id="perempuan-submit-button-text">Save</span> (<span id="perempuan-submit-count"></span>)</button>
                                    <div class="row" id="perempuan-store-error" style="display: none">
                                        <div class="col-12">
                                            <div class="alert alert-danger">
                                                <span id="perempuan-store-error-message"></span> : <strong id="perempuan-store-error-content"></strong>.
                                            </div>
                                        </div>
                                    </div>
                                    <table id="pkw-perempuan-table" class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                        <thead>
                                            <tr class="text-left text-uppercase">
                                                <th style="width: 40px"><input type="checkbox" id="perempuan-check-all" class="form-control"/></th>
                                                <th style="min-width: 100px">NIK</th>
                                                <th style="min-width: 100px">NAMA</th>
                                                <th style="min-width: 100px">KODE AREA</th>
                                                <th style="min-width: 130px">KODE BLOK</th>
                                                <th style="min-width: 130px">NOMOR LOKER</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
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
<div class="modal-footer">
{{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
</div>
</div>
</div>
</div>
</div>


                   

@endsection

@push('scripts')

<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ url('/assets/plugins/custom/qrcode/html5-qrcode.js') }}"></script>
<script src="{{ url('/assets/plugins/custom/qrcode/jsqrcode-combined.min.js') }}"></script>

<script type="text/javascript">

    function copotLokerPHK()
    {
        $("#phk-submit-button").attr('disabled', true)
        $("#phk-submit-button-text").text('Processing..')
        

        var nik_phk = JSON.parse(localStorage.getItem('checked_loker_phk'))
        var data_phk  = []

        nik_phk.forEach(function (nik) {
            data_phk.push({
                nik: nik,
                kode_area: $(`#phk-kode_area-${nik}`).val(),
                nama: $(`#phk-nama-${nik}`).val(),
                kode_blok: $(`#phk-kode_blok-${nik}`).val(),
                no_loker: $(`#phk-no_loker-${nik}`).val()
            })
        })

        $.ajax({
            url: "{{ url('/loker/data-karyawan-phk/copot') }}",
            type: "POST",
            dataType: "JSON",
            data: {
                data_phk: data_phk
            },
            success: function (response) {
                $("#phk-submit-button").removeAttr('disabled')
                $("#phk-submit-button-text").text('Tarik Loker')

                if(response.success == 1) {
                    localStorage.removeItem('checked_loker_phk')
                    toastr.success('Tarik Loker succeed')

                    // $("#list_pkw").modal("hide")
                    $("#phk-submit-button").hide()

                    getKaryawanKeluar()
                }

            },
            error: function (error) {
                $("#phk-submit-button").removeAttr('disabled')
                $("#phk-submit-button-text").text('Tarik Loker')
            }
        })
    }

    $("#phk-submit-button").on("click", function () {
        // Tarik Loker
        if(!confirm('Yakin loker mau dicopot?')) {
            return
        }

        copotLokerPHK()
    })

    function updateChecklistPHK()
    {
        if($(".phk-checkbox:checked").length < 1) {
            localStorage.removeItem('checked_loker_phk')
            return
        }

        var checked_loker_phk = []

        $(".phk-checkbox:checked").each(function () {
            var nik = $(this).val()
            checked_loker_phk.push(nik)
        })

        localStorage.setItem("checked_loker_phk", JSON.stringify(checked_loker_phk))
    }

    function checkKaryawanPHK()
    {
        updateChecklistPHK()

        $(".phk-checkbox").closest("tr").removeClass('bg-secondary')
        $(".phk-checkbox:checked").closest("tr").addClass('bg-secondary')

        var checked_nik = $(".phk-checkbox:checked").length;

        $("#phk-submit-count").text(checked_nik)
        if(checked_nik > 0)
        {
            $("#phk-submit-button").slideDown()
        }

        if(checked_nik < 1)
        {
            $("#phk-submit-button").slideUp()
        }
    }

    $("#phk-check-all").on("change", function () {
        var checked = $(this).prop('checked');
        $(".phk-checkbox").prop("checked", checked);
        checkKaryawanPHK()
    })

    function getKaryawanKeluar()
    {
        $.ajax({
            url: "{{ url('/loker/data-karyawan-keluar-masih-punya-loker') }}/",
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                $("#phk-table tbody").html('')

                if(response.data.length == 0) {
                    $("#phk-table tbody").html("<tr><td colspan='6'><span class='text-center'>No data found..</span></td></tr>")
                }

                response.data.forEach(function (item) {
                    $("#phk-table tbody").append(`
                        <tr>
                            <td><input onChange="checkKaryawanPHK()" id="phk-checkbox-${item.nik}" name="nik_keluar_punya_loker[]" value="${item.nik}" class="phk-checkbox form-control" type="checkbox" /></td>
                            <td><label class="pt-2" for="checkbox-${item.nik}">${item.nik}</label></td>
                            <td>
                                <span>${item.nama}</span>
                            </td>
                            <td>
                                <input id="phk-kode_area-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.kode_area}">
                                <input id="phk-nama-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.nama}">
                                <input id="phk-kode_blok-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.kode_blok}">
                                <input id="phk-no_loker-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.no_loker}">                                
                                <span>${item.kode_area}</span></td>
                            <td><span>${item.kode_blok}</span></td>
                            <td><span>${item.no_loker}</span></td>
                        </tr>
                    `);
                });
            },
            error: function (error) {
                console.log(error)
            }
        }) 
    }

    function showDataResignDanKabur()
    {
        getKaryawanKeluar()
        $("#list_kabur").modal("show")
    }
</script>

<script type="text/javascript">

    function submitStoreLoker(prefix)
    {
        $("#"+ prefix +"-store-error").slideUp()
        $("#"+ prefix +"-submit-button").attr('disabled', true)
        $("#"+ prefix +"-submit-button-text").text('Processing..')
        

        var nik_belum_punya_loker = JSON.parse(localStorage.getItem(prefix+'-checked_belum_punya_loker'))
        var data_belum_punya_loker  = []

        $("#print-karyawan-loker").html('')

        nik_belum_punya_loker.forEach(function (nik) {
            data_belum_punya_loker.push({
                nik: nik,
                nama: $(`#${prefix}-belum-punya-loker-nama-${nik}`).val(),
                kode_area: $(`#${prefix}-belum-punya-loker-kode_area-${nik}`).val(),
                kode_blok: $(`#${prefix}-belum-punya-loker-kode_blok-${nik}`).val(),
                no_loker: $(`#${prefix}-belum-punya-loker-no_loker-${nik}`).val(),
                kode_divisi: $(`#${prefix}-belum-punya-loker-kode_divisi-${nik}`).val(),
                kode_bagian: $(`#${prefix}-belum-punya-loker-kode_bagian-${nik}`).val(),
                kode_group: $(`#${prefix}-belum-punya-loker-kode_group-${nik}`).val(),
                kode_kontrak: $(`#${prefix}-belum-punya-loker-kode_kontrak-${nik}`).val(),
            })

            $("#print-karyawan-loker").append(`
                <div class="col-3 p-1">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td style="width: 10px">NIK</td>
                                <td>${nik}</td>
                            </tr>
                            <tr>
                                <td>NAMA</td>
                                <td>${$(`#${prefix}-belum-punya-loker-nama-${nik}`).val().substring(0,15)}</td>
                            </tr>
                            <tr>
                                <td>AREA</td>
                                <td>${$(`#${prefix}-belum-punya-loker-kode_area-${nik}`).val()} > ${$(`#${prefix}-belum-punya-loker-kode_blok-${nik}`).val()} > ${$(`#${prefix}-belum-punya-loker-no_loker-${nik}`).val()}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `)
        })

        $.ajax({
            url: "{{ url('/loker/data-karyawan-belum-punya-loker/store') }}",
            type: "POST",
            dataType: "JSON",
            data: {
                data_belum_punya_loker: data_belum_punya_loker
            },
            success: function (response) {
                $("#"+prefix+"-submit-button").removeAttr('disabled')
                $("#"+prefix+"-submit-button-text").text('Save')
                
                if(response.success == 0) {
                    // ada yang gak beres
                    $("#"+prefix+"-store-error-content").text(response.data)
                    $("#"+prefix+"-store-error-message").text(response.message)
                    $("#"+prefix+"-store-error").slideDown()
                    return
                }

                if(response.success == 1) {
                    window.print()
                    localStorage.removeItem(prefix+'-checked_belum_punya_loker')
                    toastr.success('Save data succeed')

                    // $("#list_pkw").modal("hide")
                    $("#"+ prefix +"-submit-button").hide()
                    getPKW(prefix)
                }

            },
            error: function (error) {
                $("#"+ prefix +"-submit-button").removeAttr('disabled')
                $("#"+ prefix +"-submit-button-text").text('Save')
            }
        })
    }

    $("#laki-laki-submit-button").on("click", function () {
        if(!confirm('Yakin mau simpan ini?')) {
            return
        }
        submitStoreLoker('laki-laki')
    })

    $("#perempuan-submit-button").on("click", function () {
        if(!confirm('Yakin mau simpan ini?')) {
            return
        }
        submitStoreLoker('perempuan')
    })

    function updateChecklist(prefix)
    {
        if($("."+prefix+"-loker-checkbox:checked").length < 1) {
            localStorage.removeItem(prefix+'-checked_belum_punya_loker')
            return
        }

        var checked_belum_punya_loker = []

        $("."+prefix+"-loker-checkbox:checked").each(function () {
            var nik = $(this).val()
            checked_belum_punya_loker.push(nik)
        })

        localStorage.setItem(prefix+"-checked_belum_punya_loker", JSON.stringify(checked_belum_punya_loker))
        
    }

    function checkKaryawanBelumPunyaLoker(prefix)
    {
        updateChecklist(prefix)

        $("."+prefix+"-loker-checkbox").closest("tr").removeClass('bg-secondary')
        $("."+prefix+"-loker-checkbox:checked").closest("tr").addClass('bg-secondary')

        var checked_nik = $("."+prefix+"-loker-checkbox:checked").length;

        $("#"+prefix+"-submit-count").text(checked_nik)
        if(checked_nik > 0)
        {
            $("#"+prefix+"-submit-button").slideDown()
        }

        if(checked_nik < 1)
        {
            $("#"+prefix+"-submit-button").slideUp()
        }
    }

    $("#laki-laki-check-all").on("change", function () {
        var checked = $(this).prop('checked');
        $(".laki-laki-loker-checkbox").prop("checked", checked);
        checkKaryawanBelumPunyaLoker('laki-laki')
    })

    $("#perempuan-check-all").on("change", function () {
        var checked = $(this).prop('checked');
        $(".perempuan-loker-checkbox").prop("checked", checked);
        checkKaryawanBelumPunyaLoker('perempuan')
    })

    function showDataKaryawanMasuk() {
        getPKW('laki-laki')
        getPKW('perempuan')
        $("#list_pkw").modal("show")
    }

    function getPKW(prefix)
    {
        if(prefix == 'laki-laki') {
            var jk = 'L'
        }else{
            var jk = 'P'
        }
        $("#pkw-" + prefix + "-table tbody").html("<tr><td colspan='6'><span class='text-center'>Loading..</span></td></tr>")

        // Get data karyawan yang belum memiliki loker
        $.ajax({
            url: "{{ url('/loker/data-karyawan-belum-punya-loker') }}/"+jk,
            type: "GET",
            dataType: "JSON",
            success: function (response) {
                $("#pkw-" + prefix + "-table tbody").html('')

                if(response.data.length == 0) {
                    $("#pkw-" + prefix + "-table tbody").html("<tr><td colspan='6'><span class='text-center'>No data found..</span></td></tr>")
                }

                response.data.forEach(function (item) {
                    $("#pkw-" + prefix + "-table tbody").append(`
                        <tr>
                            <td><input id="${prefix}-checkbox-${item.nik}" onChange="checkKaryawanBelumPunyaLoker('${prefix}')" name="nik_belum_punya_loker[]" value="${item.nik}" class="${prefix}-loker-checkbox form-control" type="checkbox" /></td>
                            <td><label class="pt-2" for="checkbox-${item.nik}">${item.nik}</label></td>
                            <td>
                                <input readonly id="${prefix}-belum-punya-loker-nama-${item.nik}" type="text" class="form-control form-control-sm" value="${item.nama}">
                                <input id="${prefix}-belum-punya-loker-kode_divisi-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.kode_divisi}">
                                <input id="${prefix}-belum-punya-loker-kode_bagian-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.kode_bagian}">
                                <input id="${prefix}-belum-punya-loker-kode_group-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.kode_group}">
                                <input id="${prefix}-belum-punya-loker-kode_kontrak-${item.nik}" type="hidden" class="form-control form-control-sm" value="${item.kode_kontrak}">
                            </td>
                            <td><input id="${prefix}-belum-punya-loker-kode_area-${item.nik}" type="text" class="form-control form-control-sm" value="${item.kode_area}"></td>
                            <td><input id="${prefix}-belum-punya-loker-kode_blok-${item.nik}" type="text" class="form-control form-control-sm" value="${item.kode_blok}"></td>
                            <td><input id="${prefix}-belum-punya-loker-no_loker-${item.nik}" type="text" class="form-control form-control-sm" value="${item.no_loker}"></td>
                        </tr>
                    `);
                });
            },
            error: function (error) {
                console.log(error)
            }
        })
    }
</script>

<script type="text/javascript">
 
    $('.datatable').DataTable();
    $('.Karyawan').select2();
    $('.BtnSave').hide();

    setInterval(function(){blink()}, 1200);

    function blink() {
        $('.TandaiRusak').fadeOut(500).fadeIn(500);
    }

    function upload_excel(kode_area)
    {

        $('.BodyUpload').html("");
        $('.BtnUploadFile').on("click", function(){
        $('.BtnUploadFile').hide("slow");
        $('.Proses').show("slow");

        });

        sessionStorage.setItem('kode_area', kode_area);
        $('.JudulUploadFIle').text(sessionStorage.getItem('kode_area'));
        $('.BodyUpload').append('<a href="/master_import/upload_loker_'+kode_area+'.xlsx" class="btn btn-sm text-white" style="background-color: green; border-radius: 15px;"><i class="fas fa-file-excel text-white"></i> Download Master Excel</a>');

        $('#modal_upload').modal("show");
    }

    function tandai_rusak()
    {
        var kode_blok = sessionStorage.getItem('kode_blok');
        var kode_area = sessionStorage.getItem('kode_area');
        var no_loker = sessionStorage.getItem('no_loker');

        location.href = "{{ url('/loker/tandai_rusak/') }}/" + kode_blok + '/' + kode_area + '/' + no_loker;
    }
   
    function sudah_benar()
    {
        var kode_blok = sessionStorage.getItem('kode_blok');
        var kode_area = sessionStorage.getItem('kode_area');
        var no_loker = sessionStorage.getItem('no_loker');

        location.href = "{{ url('/loker/sudah_benar/') }}/" + kode_blok + '/' + kode_area + '/' + no_loker;
    }

    $('.Keterangan').keyup(function(){
        $('#reader').show('slow');
    });
       

    $('.Karyawan').on('change', function(){
        var nama = this.value;
        $('.BtnSave').show();
    });

    function BtnExport()
    {
        var nik = sessionStorage.getItem('nik_history_karyawan');
        location.href = "{{ url('/loker/export_history_karyawan') }}/" + nik ;
    }
    
   function cari_no_loker(kode_blok, kode_area)
   {
        sessionStorage.setItem('kode_area', kode_area)
        sessionStorage.setItem('kode_blok', kode_blok)
        $('.CardBlok').toggle('fast');
        $('#'+kode_area).toggle('fast');
        $('.ValueBlok').text(kode_blok);
        $('.CardNomer').toggle('fast');
        $('.input_value_kode_blok').val(kode_blok);
        $('.input_value_kode_area').val(kode_area);

        jQuery.ajax({
        url: '/loker/cari_no_loker/' + kode_area + '/' + kode_blok,
        type: "GET",
        data: {
            kode_blok: kode_blok
        },
        dataType: "json",
        success: function(response) {
            if (response.status == 1) {
                console.log(response.data);
                $('.BodyListNomer').html("");
                jQuery.each(response.data, function(id, value) 
                {
                    var bg_color = '';
                    var show_badge = '';
                    var textfull = '';
                    if(value.status == 0)
                    {
                        bg_color = 'symbol-light-info';
                        show_badge = 'bg-success';
                        textfull = '<span class="badge badge-pill badge-info text-center">Free</span>';
                    } 
                    if(value.status == 2)
                    {
                        bg_color = 'symbol-light-dark';
                        show_badge = 'bg-dark';
                        textfull = '<span class="badge badge-pill badge-dark text-center">Rusak</span>';
                    } 
                    if(value.status == 1)
                    {
                        bg_color = 'symbol-light-danger';
                        show_badge = 'bg-danger';
                        textfull = '<span class="badge badge-pill badge-danger text-center">Full</span>';
                    } 

                    $('.BodyListNomer').append('<div class="symbol symbol-80 '+ bg_color+' mr-4 flex-shrink-0 mt-4">\
                            <div class="symbol-label">\
                                <i class="symbol-badge '+show_badge+'"></i>\
                                <a href="javascript:void(0)" onclick="detail_loker(\''+value.no_loker+'\')" >\
                                    <span class="svg-icon svg-icon-lg svg-icon-dark">\
                                        '+ value.no_loker +' \
                                    </span>\
                                </a>\
                            </div>\
                           <center>'+textfull+' </center>\
                        </div>\
                    ');
                });
            }
        }
    });
   }
   function detail_loker(no_loker)
   {
       var kode_area = sessionStorage.getItem('kode_area');
       var kode_blok = sessionStorage.getItem('kode_blok');
       sessionStorage.setItem('no_loker', no_loker);
       jQuery.ajax({
            url: '/loker/cek_penghuni_loker/' + no_loker + '/'+ kode_area + '/' + kode_blok,
            type: "GET",
            data: {
                no_loker: no_loker,
                kode_area: kode_area,
                kode_blok: kode_blok,
            },
            dataType: "json",
            success: function(response)
            {
                if (response.status == 1)
                {
                  if (response.data.status.status == 1) {
                    var nik = response.data.user.nik;
                    sessionStorage.setItem('nik', response.data.user.nik);

                    $('#detail_loker').modal("show"); 
                        jQuery.ajax({
                            url: '/loker/get_foto_user/' + nik,
                            type: "GET",
                            data: {
                                nik: nik
                            },
                            dataType: "json",
                            success: function(response) {
                                    $('#picture').attr('src', 'data:image/jpg;base64,' + response.data.image);
                                    $('.DeptValue').text(response.data.divisi);
                                    $('.NIKValue').text(nik);
                                    $('.KodeKontrakValue').text(response.data.kode_kontrak);
                                    $('.KodeBlokValue').text(response.data.kode_blok);
                                    $('.NoLokerValue').text(response.data.no_loker);
                                    $('.Nama').text(response.data.nama);
                                    $('.no_loker').text(response.data.no_loker);

                                    $('.BtnEdit').html("");
                                    $('.BtnEdit').append('<a href="javascript:void(0)" onclick="tarik_kunci_manual()" class="btn btn-primary btn-sm TarikKunci"> <i class="fas fa-key"></i> Tarik Kunci</a>\
                                    <button type="button" class="btn btn-outline-danger spinner spinner-darker-danger spinner-left mr-3 Proses" style="display: none;" disabled>\
                                                Tunggu Ya, Lagi Proses..\
                                            </button>' );
                                    }
                            });
                    }
                    if(response.data.status.status == 2)
                    {
                    

                        $('#sudah_benar').modal("show"); 
                    }
                if(response.data.status.status == 0)
                {
                        $('#input_loker').modal("show"); 
                        $('.input_value_no_loker').val(no_loker); 
                }
              }
            }
        });
    }

    function history_karyawan(nik)
    {
        $("#modal_loker_kosong").modal("hide");
        $("#modal_history_karyawan").modal("show");
        sessionStorage.setItem("nik_history_karyawan", nik);

         jQuery.ajax({
            url: '/loker/history_loker_karyawan/' + nik,
            type: "GET",
            data: {
                nik: nik
            },
            dataType: "json",
            success: function(response) {
                if (response.status == 1) {
                        console.log(response.data);
                        $('.body_history_karyawan').html("");
                         jQuery.each(response.data, function(id, value) 
                        {
                        var number = id+ parseInt(1); 
                        var status = '';
                        if(value.status == 'IN')
                        {
                            status = '<span class="badge badge-pill badge-info">IN</span>';
                        }
                        else
                        {
                            status = '<span class="badge badge-pill badge-dark">OUT</span>';
                        }
                        $('.body_history_karyawan').append('<tr class="text-center">\
                            <td>'+ number +'</td>\
                            <td class="text-center">'+ status +'</td>\
                            <td>'+ value.no_loker +'</td>\
                            <td>'+ value.keterangan +'</td>\
                            <td>'+ value.kode_area +'</td>\
                            <td>'+ value.kode_blok +'</td>\
                            <td>'+ value.tgl_pengisi +'</td>\
                            <td>'+ value.nama_pengisi +'</td>\
                        </tr>\ ')
                        });
                    }
                }
            });
    }

   function tarik_kunci_manual()
   {
       $('.TarikKunci').hide('slow');
       $('.Proses').show('slow');
        var no_loker = sessionStorage.getItem('no_loker');
        var kode_area = sessionStorage.getItem('kode_area');
        var kode_blok = sessionStorage.getItem('kode_blok');
        var nik = sessionStorage.getItem('nik');

        let keterangan = prompt("Alasan Penarikan Kunci:", "Resign");
        if (keterangan == null || keterangan == "") {
        } else {
               $.ajax({
                    type: "GET",
                    url: '/loker/tarik_kunci_manual/' + no_loker + '/' + kode_blok + '/' + kode_area + '/' + nik + '/' + keterangan,
                    data: {
                        no_loker: no_loker,
                        kode_area: kode_area,
                        kode_blok: kode_blok,
                        nik: nik,
                        keterangan: keterangan,
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 1)
                        {                            
                            $('#detail_loker').modal("hide");
                            location.reload();
                                Swal.fire({
                                    position: "top-right",
                                    icon: "success",
                                    title: "Kunci Loker Berhasil Di Tarik..",
                                    showConfirmButton: false,
                                    timer: 4500
                                });
                        }
                    }
                });
        }

   }
   function tarik_kunci(nik_pengisi, no_loker, kode_blok, kode_area)
   {
    //    alert(kode_area);
       sessionStorage.setItem('nik_pengisi', nik_pengisi);
       sessionStorage.setItem('no_loker', no_loker);
        $('#detail_loker').modal("hide");
        $('#tarik_kunci').modal('show');

           $('#reader').html5_qrcode(function(no_loker, nik, keterangan){
            var nik = sessionStorage.getItem('nik_pengisi');
            var keterangan = $('.Keterangan').val();
            console.log(nik);
            console.log(no_loker);
            $.ajax({
                    type: "GET",
                    url: '/loker/tarik_kunci/' + no_loker + '/' + nik + '/' + keterangan + '/' + kode_blok + '/' + kode_area,
                    data: {
                        no_loker: no_loker,
                        nik: nik,
                        keterangan: keterangan,
                        kode_area: kode_area,
                        kode_blok: kode_blok,
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 1)
                        {                            
                            $('#tarik_kunci').modal("hide");
                            location.reload();
                                Swal.fire({
                                    position: "top-right",
                                    icon: "success",
                                    title: "Kunci Loker Berhasil Di Tarik..",
                                    showConfirmButton: false,
                                    timer: 4500
                                });
                        }
                    }
                });
            },
            function(error){

            }, function(videoError){
                //the video stream could be opened
            }
        );
      
   }
   function ToggleValueBlok()
   {
       var textblok =  $('.ValueBlok').text();
        $('#'+sessionStorage.getItem('kode_area')).toggle('fast');
        $('.CardBlok').toggle('fast');
        $('.CardNomer').toggle('fast');
   }

$('.Click_area_card').on('click', function() {
    $('.Card_Loker').toggle('fast');
    var kode_area = $(this).attr('data-kode-area');

    $('.Card_'+kode_area).toggle('fast');
    $('#'+kode_area).toggle('fast');

    jQuery.ajax({
        url: '/loker/cari_blok/' + kode_area,
        type: "GET",
        data: {
            kode_area: kode_area
        },
        dataType: "json",
        success: function(response) {
            if (response.status == 1) {
                $('.Body_'+kode_area).html("");
                console.log(response.data);
                jQuery.each(response.data, function(id, value) 
                {
                    $('.Body_'+kode_area).append('<div class="symbol symbol-80 symbol-light-warning mr-4 flex-shrink-0 mt-4">\
                                <div class="symbol-label">\
                                    <a href="javascript:void(0)" onclick="cari_no_loker(\''+value.kode_blok+'\',\''+value.kode_area+'\')" >\
                                        <span class="svg-icon svg-icon-lg svg-icon-warning">\
                                            '+ value.kode_blok +'\
                                        </span>\
                                    </a>\
                                </div>\
                            </div>\
                    ');
                });
            }
        }
    });
});

function export_excel(kode_area)
{
    window.open('loker/export_excel/'+kode_area);
}



    </script>
@endpush
