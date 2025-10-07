@extends('layouts.base')

@push('styles')
    <style type="text/css">
        .control-label {
            font-weight: normal;
        }
        .btn-xs {
            font-size: 7px
        }
        .camera-container thead tr {
            background-color: #91150E !important;
            color: #fff;
        }
        .camera-container .nvr-label {
            border: 1px solid #EBEDF3;
            width: 100%;
            padding: 5px 5px 5px 10px;
            border-radius: 5px;
            /* border-top-right-radius: 5px; */
            /*text-decoration: underline;*/
        }
        .camera-container .nvr-label:hover {
            background-color: #f4f4f4
        }
        .camera-container a {
            color: #444
        }
        .camera-container a:hover {
            text-decoration: underline;
        }
        .collapse-button {
            cursor: pointer;
            float: right;
            margin-right: 5px;
            border: 1px solid #ccc;
            border-radius: 50px;
        }
        .collapse-button:hover {
            background-color: #f4f4f4
        }
        .collapse-button .collapse-icon {
            padding-top: 5px;
            padding-right: 3px;
            padding-bottom: 5px;
            padding-left: 3px;
        }
        .collapse-button .collapse-icon i {
            color: #333;
        }
        .item-option .color-label {
            position: relative;
            height: 30px;
            width: 30px;
            display: inline-block;
            cursor: pointer;
            box-shadow: 1px 1px 2px #333
        }
        .item-option .color-label:hover {
            opacity: 0.7;
            border: 2px solid #333;
        }
        .item-option .color-label.red {
            background-color: #FF5733;
            color: #fff;
        }
        .item-option .color-label.yellow {
            background-color: #DBFF33;
            color: #fff;
        }
        .item-option .color-label.green {
            background-color: #33FF57;
            color: #fff
        }
        .item-option .color-label.blue {
            background-color: #33FFBD;
            color: #fff
        }
        .item-option .color-label.none {
            background-color: white;
            /*border: 1px solid #000;*/
        }
        .item-option .edit {
            margin-top: 10px
        }
        .change-image {
            font-size: 12px
        }
        .form-edit .form-group {
            margin-bottom: 2px
        }
        @keyframes loading-inner {
        0% { transform: rotate(0deg) }
        50% { transform: rotate(180deg) }
        100% { transform: rotate(360deg) }
        }
        .loading-inner div {
        position: absolute;
        animation: loading-inner 1.56s linear infinite;
        width: 81.48px;
        height: 81.48px;
        /*top: 56.26px;
        left: 56.26px;*/
        border-radius: 50%;
        box-shadow: 0 3.492px 0 0 #91150e;
        transform-origin: 40.74px 42.486px;
        }
        .loading-outer {
        width: 100%;
        height: 100%;
        z-index: 100;
        background-color: rgba(255,255,255,0.8);
        /*display: inline-block;*/
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;
        overflow: hidden;
        /*background: none;*/
        }
        .loading-inner {
            margin: 200px auto;
            width: 100px;
            height: 100px;
            position: relative;
            transform: translateZ(0) scale(1);
            backface-visibility: hidden;
            transform-origin: 0 0; /* see note above */
        }
        .loading-inner div { box-sizing: content-box; }
        .nvr-container .table tbody tr td, #offlineCameras .table tbody tr td {
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 8px;
            padding-right: 8px;
        }
        .delete i:hover {
            color: #000
        }
        thead tr {
            border: 1px solid #999 !important
        }
        tr {
            border: 1px solid #eee !important
        }
        .nvr-link:hover {
            text-decoration: underline !important
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-5">
                        <div class="card-title">
                            <h3 class="card-label">Camera Manager
                                <span class="d-block text-muted pt-2 font-size-sm">Manage semua kamera CCTV PT. PAS</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom mt-5">
            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                <h3 class="card-label">Manage All camera PT. Prakarsa Alam Segar</h3>
            </div>
            <div class="card-body p-0">
                
                <div class="container-fluid camera-container">
                    <div class="tools-container">
                        <button id="offline-check-button" class="btn btn-outline-danger">
                            <span class="icon"><i class="la la-check-circle"></i></span>
                            <span class="text">Cek Offline Camera</span>
                        </button>
                        <button onclick="alert('coming soon')" id="export-excel-button" class="btn btn-outline-success btn-round">
                            <span class="icon"><i class="la la-file-excel-o"></i></span>
                            <span class="text">Export to Excel</span>
                        </button>
                        <button data-toggle="modal" data-target="#importExcel" id="export-excel-button" class="btn btn-outline-success btn-round">
                            <span class="icon"><i class="la la-file-import"></i></span>
                            <span class="text">Import from Excel</span>
                        </button>
                    </div>
                        <hr>
                    {{-- <code>@php print_r($nvrs) @endphp</code> --}}
                    @foreach ($nvrs as $nvr)
                        <section id="{{ str_replace('.', '-', $nvr['ip_address']) }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="nvr-label">
                                        <div class="row">
                                            <div class="col-md-1">
                                                <div class="">
                                                    <strong>
                                                        <a class="nvr-link" href="http://{{ $nvr['ip_address'] }}" target="_blank">{{ $nvr['ip_address'] }}</a>
                                                    </strong>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                {{-- <a href="alert:https://www.google.com">Yes</a> --}}
                                            </div>
                                            <div class="col-md-1">
                                                <div class="collapse-button" onClick="collapse('{{ $nvr["ip_address"] }}')">
                                                    <span class="collapse-icon"><i class="fa fa-chevron-down"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nvr-container">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="20">No</th>
                                            <th width="120">IP Address</th>
                                            <th width="500">Camera Name</th>
                                            <th width="100">Department</th>
                                            <th width="100">Merk</th>
                                            <th width="100">Type</th>
                                            <th>Model</th>
                                            <th width="100">Status</th>
                                            <th width="70"><i class="fa fa-cogs text-white"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- <code>@php print_r($nvr['cameras']) @endphp</code> --}}
                                        @foreach ($nvr['cameras'] as $camera)
                                            {{-- <code>@php print_r($key) @endphp</code> --}}
                                            <tr oncontextmenu="showOption('{{ str_replace('.', '-', $camera['ip_address']) }}');return false" id="{{ str_replace('.', '-', $camera['ip_address']) }}" ondblclick="showDetail('{{ $camera['id'] }}', '{{ $nvr['id'] }}', '{{ $camera['channel_number'] }}')">
                                                <td>{{ $camera['channel_number'] }}</td>
                                                <td><a href="http://{{ $camera['ip_address'] }}" target="_blank">{{ $camera['ip_address'] }}</a></td>
                                                <td>{{ $camera['name'] }}</td>
                                                <td>{{ $camera['department'] }}</td>
                                                <td>{{ $camera['merk'] }}</td>
                                                <td>{{ $camera['type'] }}</td>
                                                <td>{{ $camera['model'] }}</td>
                                                <td>
                                                    @if ($camera['current_condition'] != '')
                                                        <span class="status" id="{{ str_replace('.', '-', $camera['ip_address']) }}-status">
                                                            <span class="icon">
                                                                <i class="fa fa-circle {{ $camera['current_condition'] == 'online' ? 'text-success' : 'text-danger' }}"></i>
                                                            </span>{{ $camera['current_condition'] }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="btn btn-secondary btn-xs btn-icon" title="Live View" href="alert:{{ URL::to('/camera/view') }}/{{ $camera['id'] }}"><i class="la la-external-link"></i></a>
                                                    <a class="btn btn-danger btn-icon btn-xs delete" onClick="deleteItem('{{ $camera['id'] }}')" title="Delete" href="javascript:"><i class="la la-trash text-white"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    @endforeach
                </div>
        
                <br>
            </div>
        </div>
        
        <div class="modal" id="modalDetail">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="loading-outer hide">
                    <div class="loading-inner">
                        <div></div>
                    </div>
                </div>
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Default Modal <small class="current-status"><i class="fa fa-circle text-green"></i> <span>online</span></small></h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <div class="col-md-7">
                          <img class="camera-image" src="{{ URL::to('/storage/camera_images/no-image.jpg') }}" alt="" width="100%">
                          <fieldset>
                              <legend><a href="javascript:;" class="change-image">Change Image</a></legend>
                              <form class="form-change-image form-horizontal hide" action="" method="post" enctype="multipart/form-data">
                                <input type="file" class="upload-image" name="upload_image">
                                <label for="paste-image">Paste Here</label>
                                <textarea name="paste_image" id="paste-image" cols="10" class="form-control"></textarea>
                                <input type="hidden" name="camera_id" id="camera-id">
                            </form>
                          </fieldset>
                      </div>
                      {{-- <div class="col-md-12">
                          <div class="space-6"></div>
                      </div> --}}
                      <form class="form-horizontal col-md-5 form-edit" id="form-edit">
                          <input type="hidden" name="id" id="edit-id">
                          <div class="form-group row">
                          <label class="control-label col-md-4">
                            IP Address
                          </label>
                          <div class="col-md-8">
                              <input name="ip_address" type="text" class="form-control" data-inputmask="'alias': 'ip'" data-mask value="" id="edit-ip_address">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            DEPT
                          </label>
                          <div class="col-md-8">
                              <select name="dept_id" id="edit-dept_id" class="form-control">
                                  <option value=""></option>
                                  @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                                  @endforeach
                              </select>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            Name
                          </label>
                          <div class="col-md-8">
                              <input name="name" type="text" class="form-control" value="" id="edit-name">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            Type
                          </label>
                          <div class="col-md-8">
                              <select name="type" id="edit-type" class="form-control">
                                  <option value=""></option>
                                  <option value="mini_bullet">Mini Bullet</option>
                                  <option value="mini_dom">Mini Dome</option>
                                  <option value="big_bullet">Big Bullet</option>
                                  <option value="big_dome">Big Dome</option>
                                  <option value="speed_dome">Speed Dome</option>
                                  <option value="plat_recognition">Plat Recognition</option>
                                  <option value="thermal">Thermal</option>
                              </select>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            Merk
                          </label>
                          <div class="col-md-8">
                              <input name="merk" type="text" class="form-control" value="" id="edit-merk">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            Model
                          </label>
                          <div class="col-md-8">
                              <input name="model" type="text" class="form-control" value="" id="edit-model">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            NVR
                          </label>
                          <div class="col-md-8">
                              <select name="nvr_id" id="edit-nvr_id" class="form-control">
                                  <option value=""></option>
                                  @foreach ($nvr_only as $_nvr)
                                      <option value="{{ $_nvr->id }}">{{ $_nvr->ip_address }}</option>
                                  @endforeach
                            </select>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            Channel
                          </label>
                          <div class="col-md-8">
                              <input type="number" name="channel_number" id="edit-channel_number" class="form-control">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            Location
                          </label>
                          <div class="col-md-8">
                              <input name="location" type="text" class="form-control" value="" id="edit-location">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            ASSET
                          </label>
                          <div class="col-md-8">
                              <input name="asset_number" type="text" class="form-control" value="" id="edit-asset_number">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="control-label col-md-4">
                            Username
                          </label>
                          <div class="col-md-8">
                              <input name="username" type="text" class="form-control" value="" id="edit-username">
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="edit-password" class="control-label col-md-4">
                            Password
                          </label>
                          <div class="col-md-8">
                              <input name="password" type="password" class="form-control" value="" id="edit-password">
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="edit-skip_check" class="control-label col-md-4">
                              Skip Check
                            </label>
                            <div class="col-md-2" style="padding-right: 0 !important">
                                <input placeholder="Y/N" name="skip_check" type="text" class="form-control" value="" id="edit-skip_check">
                                {{-- <label class="radio-inline" style="margin-left: 0 !important; padding-left: 19px !important">
                                    <input type="radio" name="skip_check" value="Y"> Y
                                </label>
                                <label class="radio-inline" style="margin-left: 0 !important; padding-left: 19px !important">
                                    <input type="radio" name="skip_check" value="N"> N
                                </label> --}}
                            </div>
                            <div class="col-md-6">
                                <input placeholder="Skip Reason" name="skip_reason" type="text" class="form-control" value="" id="edit-skip_reason">
                            </div>
                          </div>
                        <div class="row" style="text-align: center;padding-top: 10px">
                              <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Save Change</button>
                          </div>
                      </form>
                      
                  </div>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        
        <div class="modal" id="offlineCameras">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="loading-outer hide">
                    <div class="loading-inner" style="margin-top: 50px">
                        <div></div>
                    </div>
                </div>
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Offline Camera</h4>
              </div>
              <div class="modal-body">
                  <div class="row">
                      <h4 class="col-md-12">Latest offline cameras</h4>
                      <div class="col-md-7">
                          <table class="table table-hover table-bordered table-striped">
                              <thead>
                                  <tr>
                                      <th>No</th>
                                      <th>IP Address</th>
                                      <th>Name</th>
                                      <th>Dept</th>
                                  </tr>
                              </thead>
                              <tbody class="offline-camera-list">
                                  
                              </tbody>
                          </table>
                      </div>
                      <div class="col-md-5">
                          <div class="box box-solid box-default">
                              <div class="box-body">
                                  <div id="offline-department-chart"></div>
                              </div>
                          </div>
                    </div>
                    <h4 class="col-md-12">Skip Check Offline Cameras</h4>
                    <div class="col-md-12">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>IP Address</th>
                                    <th>Name</th>
                                    <th>Dept</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody class="offline-camera-skip-list">
                                
                            </tbody>
                        </table>
                    </div>
                  </div>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>
        
        <div class="modal" id="importExcel">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Import From Excel</h4>
              </div>
              <div class="modal-body">
                  <form action="" method="POST" enctype="multipart/form-data" id="importExcelForm">
                      <input type="file" name="excel" id="excel">
                      <div class="space-6"></div>
                      <button type="submit" class="btn btn-success">Submit</button>
                      <a href="{{ URL::to('/files/import.xlsx') }}">Download Template</a>
                  </form>
                  <hr>
                  <h4>List NVR ID</h4>
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>NVR</th>
                              <th>ID</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($nvr_only as $nvr)
                              <tr>
                                  <td>{{ $nvr->ip_address }}</td>
                                  <td>{{ $nvr->id }}</td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
                  <h4>List DEPT ID</h4>
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th>DEPARTMENT</th>
                              <th>ID</th>
                          </tr>
                      </thead>
                      <tbody>
                          @foreach ($departments as $department)
                              <tr>
                                  <td>{{ $department->name }}</td>
                                  <td>{{ $department->id }}</td>
                              </tr>
                          @endforeach
                      </tbody>
                  </table>
              </div>
            </div>
            <!-- /.modal-content -->
          </div>
          <!-- /.modal-dialog -->
        </div>

    </div>

@endsection
