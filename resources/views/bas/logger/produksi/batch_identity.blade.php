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
                    <form action="/bas_logger/operator/post_batch_identity" method="post">
                      @csrf
                    <div class="card">
                        <div class="card-header text-center">
                         <b>FORM BATCH IDENTITY </b>
                        </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input class="form-control" type="text" value="{{Auth::user()->name}}" disabled />
                                    </div>
                                </div>
                                  <div class="col-sm-6">
                                      <div class="form-group">
                                            <label>NIK</label>
                                            <input class="form-control" type="text" value="{{Auth::user()->username}}" disabled />
                                       </div>
                                  </div>
                                </div>
                                
                                <hr>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Jenis Sampel</label> <span class="text-danger">*</span>
                                            <select class="form-control Sampel" name="jenis_sampel" required>
                                                <option disabled selected>Silahkan Pilih</option>
                                                @foreach ($data as $list)
                                                <option value="{{$list->jenis_sampel}}">{{$list->jenis_sampel}}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                          <div class="form-group">
                                            <label>Tanggal Pasteurisasi</label> <span class="text-danger">*</span>
                                                <input class="form-control" type="date" name="tgl_pasteurisasi" required />
                                        </div>                         
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Jenis Varian</label> <span class="text-danger">*</span>
                                            <select class="form-control Varian" name="jenis_varian" required>
                                                <option disabled selected>Silahkan Pilih</option>
                                                @foreach ($data as $val)
                                                <option value="{{$val->jenis_varian}}">{{$val->jenis_varian}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                          <div class="form-group">
                                            <label for="exampleFormControlInput1">Group</label> <span class="text-danger">*</span>
                                            <select class="form-control" name="group" required>
                                                <option disabled selected>Silahkan Pilih</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                            </select>
                                        </div> 
                                    </div>
                                </div>
                                    
                                    <hr>

                                    <div class="kolom_item">

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="exampleFormControlInput1">Main Blending</label> <span class="text-danger">*</span>
                                                    <select class="form-control Sampel" name="main_blending" required>
                                                        <option disabled selected>Silahkan Pilih</option>
                                                        <option value="TESTING 1">TESTING 1</option>
                                                        <option value="TESTING 2">TESTING 2</option>
                                                        <option value="TESTING 3">TESTING 3</option>
                                                    </select>
                                                </div> 
                                                <div class="form-group">
                                                    <label>Tanggal Produksi</label> <span class="text-danger">*</span>
                                                        <input class="form-control" type="date" name="tgl_produksi" required />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="exampleFormControlInput1">Main Batch</label> <span class="text-danger">*</span>
                                                        <select class="form-control Sampel" name="main_batch" required>
                                                            <option disabled selected>Silahkan Pilih</option>
                                                            <option value="TESTING 1">TESTING 1</option>
                                                            <option value="TESTING 2">TESTING 2</option>
                                                            <option value="TESTING 3">TESTING 3</option>
                                                        </select>
                                                    </div> 
                                                    <div class="form-group">
                                                        <label>Production Order</label>
                                                           <input class="form-control" type="text" name="production_order" placeholder="Optional" />
                                                    </div>
                                                </div>
                                            </div>

                                        <div class="row mt-4">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="exampleFormControlInput1">Storage</label> <span class="text-danger">*</span>
                                                    <select class="form-control" name="storage" required>
                                                        <option disabled selected>Silahkan Pilih</option>
                                                        <option value="STORAGE 1">STORAGE 1</option>
                                                        <option value="STORAGE 2">STORAGE 2</option>
                                                        <option value="STORAGE 3">STORAGE 3</option>
                                                    </select>
                                                </div>
                                                 <div class="form-group">
                                                    <label for="exampleFormControlTextarea1">Catatan</label>
                                                    <textarea class="form-control" name="notes" placeholder="Tulis catatan, misal notes penyebab rekomposisi"></textarea>
                                                </div> 
                                            </div>
                                        </div>

                                    </div>

                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-sm-5">
                                                </div>
                                                <div class="col-sm-5">
                                                </div>
                                                <div class="col-sm-2">
                                                <button class="btn btn-primary mr-2 btn-block"><i class="fas fa-save"></i> Simpan</button>
                                                </div>
                                            </div>
                                       </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

@endsection

@push('scripts')

    <script type="text/javascript">
        $('.Sampel').select2();
        $('.Varian').select2();

    </script>

@endpush
