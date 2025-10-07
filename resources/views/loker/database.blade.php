@extends('layouts.base')

@section('content')
<div class="container-fluid">

       <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" style="zoom: 130%;">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-danger"
                        href="/loker">
                        <i class="fas fa-home"></i>
                    </a>
                </li>
            </ul>

    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom">
                <form action="{{ url('loker/post_master_loker')}}" method="post">
                @csrf
                <input type="hidden" class="form-control" name="kode_area" id="" value="{{$kategori}}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">Kode Area Loker</label>
                               @if($kategori == 'ps1')
                               <input type="text" disabled class="form-control" name="" id="" value="Loker Sepatu Pria -1">
                               @elseif($kategori == 'ps2')
                               <input type="text" class="form-control" disabled name="" id="" value="Loker Sepatu Pria -2">
                               @elseif($kategori == 'pb1')
                               <input type="text" disabled class="form-control" name="" id="" value="Loker Baju pria -1">
                               @elseif($kategori == 'pb2')
                               <input type="text" class="form-control" disabled name="" id="" value="Loker Baju Pria -2">
                               @elseif($kategori == 'wb1')
                               <input type="text" class="form-control" disabled name="" id="" value="Loker Baju Wanita -1">
                               @elseif($kategori == 'wb2')
                               <input type="text" class="form-control" disabled name="" id="" value="Loker Baju Wanita -2">
                               @elseif($kategori == 'ws1')
                               <input type="text" class="form-control" disabled name="" id="" value="Loker Sepatu Wanita -1">
                               @else
                               <input type="text" class="form-control" disabled name="" id="" value="Loker Sepatu Wanita -2">
                                @endif
                              <input type="hidden" class="form-control" name="kode_area" value="{{$kategori}}">
                            </div>
                            <div class="form-group">
                              <label for="">Pilih Jenis Tambah Data</label>
                              <select class="form-control jenis_add_data" name="jenis_add_data" id="">
                                <option selected disabled> Silahkan Pilih</option>
                                <option value="1">Tambah Nomor Loker</option>
                                <option value="2" class="Add">Tambah Blok Loker</option>
                              </select>
                            </div>
                            <div class="Value1" style="display: none;">
                                <div class="form-group">
                                    <label for="">Pilih Blok Loker</label>
                                    <select class="form-control select2 KodeBlok" style="width: 100%;" name="kode_blok" id="">
                                        <option selected disabled> Silahkan Pilih</option>
                                        @foreach ($master_select as $val)
                                        <option value="{{$val->kode_blok}}">{{$val->kode_blok}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                  <div class="form-group">
                                <label for="">Nomer Terakhir Loker</label>
                                <select class="form-control mulai_dari_otomatis" name="mulai_dari" id="">
                                </select>
                                </div>
                                <div class="form-group">
                                <label for="">Tambah Nomor Sampai</label>
                                <select class="form-control sampai_otomatis" name="sampai" id="">
                                </select>
                                </div>
                            </div>
                            <div class="Value2"  style="display: none;">
                                <div class="form-group">
                                <label for="">Kode Blok Loker</label>
                                <input type="text" autocomplete="off" class="form-control" name="kode_blok_add" placeholder="Masukan Kode Blok" oninput="this.value = this.value.toUpperCase()">
                                <span class="text-danger mt-2">* Contoh: A</span>
                                </div>
                                <div class="form-group">
                                <label for="">Mulai Dari Nomor.</label>
                                <select class="form-control" name="mulai_dari_add" id="">
                                    @for($i = 1; $i <= 800; $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                                </div>
                                <div class="form-group">
                                <label for="">Sampai Nomor.</label>
                                <select class="form-control" name="sampai_add" id="">
                                    @for ($i = 1; $i <= 800; $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="float-right">
                                        <button type="submit" class="btn btn-primary" name="post" value="post"><i class="fas fa-save"></i> Simpan</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <a href="#hapus_blok" data-toggle="modal" class="btn btn-sm mb-3 ml-4 text-white bg-primary DeleteBlok" disabled style="border-radius: 10px;" id="btn_approve" style="border-radius: 10px"> <i class="fas fa-times text-white"></i>
                             Hapus Blok Loker
                         </a>
                    </div>

                        <div class="col-sm-9">
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th>No.</th>
                                        <th>Pilih</th>
                                        <th>Kode Area</th>
                                        <th>Kode Blok</th>
                                        <th>No. Loker</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($master_blok as $list)
                                    <tr class="text-center">
                                        <td>{{$loop->iteration}}</td>
                                        <td style="width: 15%;">
                                        <label class="checkbox checkbox-success">
                                            <input type="checkbox" name="id[]" class="Cbox" id="toggle" style="width: 280%;" value="{{$list->id}}"/>
                                            <span class="ml-2"></span>
                                            <label class="ml-2 mt-2"><b>Pilih</b></label>
                                        </label>
                                        </td>
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
                                        <td>{{$list->no_loker}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                   <button type="submit" class="btn btn-sm mb-3 text-white bg-primary" style="border-radius: 10px;" id="btn_approve" style="border-radius: 10px" name="hapus" value="hapus"> <i class="fas fa-trash-alt text-white"></i> Hapus Loker</button>
                                </form>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="hapus_blok" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-custom">
                                <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Blok</th>
                                                    <th>Tools</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($blok as $item)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$item->kode_blok}}</td>
                                                    <td><a href="{{url('loker/hapus_master_blok/' . $item->id)}}" class="btn btn-primary btn-sm"><i class="fas fa-times"></i> Hapus</a></td>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</div>


@endsection

@push('scripts')

    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
    $('.KodeBlok').select2();
    $('.table').DataTable();

      $('.jenis_add_data').on('change', function(){
        var nilai = this.value;
        if(nilai == '2')
        {
            $('.Value2').show();
        }
        else
        {
            $('.Value2').hide();
        }
        if(nilai == '1')
        {
            $('.Value1').show();
        }
        else
        {
            $('.Value1').hide();
        }
    });
      $('.KodeBlok').on('change', function(){
        var kode_blok = this.value;
        var kode_area = sessionStorage.getItem('kode_area');
          jQuery.ajax({
                url: 'loker/last_number_loker/' + '{{$kategori}}' + '/' + kode_blok,
                type: "GET",
                data: {
                    kode_area: kode_area,
                    kode_blok: kode_blok,
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 1) {
                        console.log(response.data);
                        $('.mulai_dari_otomatis').html("");
                        $('.mulai_dari_otomatis').append('<option value="'+ parseInt(response.data)+'" readonly selected>'+response.data+'</option>')
                        $('.sampai_otomatis').html("");
                        $('.sampai_otomatis').append('<option value="" readonly selected>Silahkan Pilih</option>')
                        var mulai = parseInt(response.data) + parseInt(1);
                        for(var i =mulai; i < 800; i++)
                        {
                        $('.sampai_otomatis').append('<option value="'+i+'">'+i+'</option>')
                        }
                    }
                }
            });
    });
        // $(".Cbox").on('click', function() {
        //         $(".DeleteBlok").hide();
        //     });

    </script>
@endpush