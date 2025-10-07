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
                <div class="col-sm-6">
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
                                    <form action="/bas_logger/spv/import_sampel_varian" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Pilih File</label>
                                                <input accept=".xlsx" type="file" name="file" class="form-control">
                                            </div>
                                            <div class="col-sm-12">
                                                <a href="/master_import/BAS_MASTER_SAMPEL_VARIAN.xlsx"
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
                                <form action="/bas_logger/spv/post_sampel_varian" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="exampleFormControlSelect1">No. Standar</label>
                                                   <input type="text" name="no_standar" class="form-control"
                                                    placeholder="Masukan No. Standar" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Jenis Sampel</label>
                                                <select class="form-control Sampel" name="jenis_sampel" required>
                                                    <option disabled selected>Silahkan Pilih</option>
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
                                                    <option disabled selected>Silahkan Pilih</option>
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
                                                    placeholder="Masukan Jenis Sampel">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" style="display: none" id="other_varian">
                                                <label for="exampleFormControlInput1">Jenis Varian</label>
                                                    <input type="text" name="jenis_varian_other" class="form-control"
                                                    placeholder="Masukan Jenis Varian">
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

                  
                 <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <form action="/bas_logger/spv/delete_sampel_varian" method="post">
                                            @csrf
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No.</th>
                                                    <th class="text-center">Aksi</th>
                                                    <th class="text-center">No Standar</th>
                                                    <th class="text-center">Jenis Sampel</th>
                                                    <th class="text-center">varian</th>
                                                    <th class="text-center">Tools</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data as $val)
                                                <tr>
                                                    <td class="text-center">
                                                        {{$loop->iteration}}
                                                    </td>
                                                     <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input Cbox" name="id[]"
                                                                value="{{ $val->id }}">
                                                            <label class="form-check-label"
                                                                for="exampleCheck1">Pilih</label>
                                                        </div>
                                                      </form>
                                                    </td>
                                                    <td class="text-center">
                                                        {{$val->no_standar}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$val->jenis_sampel}}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$val->jenis_varian}}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="/bas_logger/spv/edit_sampel_varian/{{$val->id}}" class="btn btn-sm btn-info"><i class="fas fa-edit fa-sm"></i></a>
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





@endsection


@push('scripts')

<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script type="text/javascript">
        $('.table').DataTable();

        $('.Varian').select2();
        $('.Sampel').select2();
        
        $('#import').hide();
        $('#manual').hide();

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

            $(".Cbox").click(function() {
                $("#btn_hapus").attr("disabled", !this.checked);
            });

    </script>

@endpush
