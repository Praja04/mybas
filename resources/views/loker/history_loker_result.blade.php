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
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-custom">
                                <div class="card-header">
                                    <div class="card-toolbar"></div>
                                      <div class="card-toolbar">
                                        <a href="{{ url('loker/export_loker_spesifik/'.$result[0]->kode_area.'/'.$tgl_mulai.'/'.$tgl_selesai) }}" class="btn btn-sm"  style="background-color: rgb(143, 238, 0)"><i class="fas fa-file-excel text-dark"></i> Export To Excel </a>
                                    </div>
                                </div>
                                <form action="{{ url('loker/pencarian_history_loker')}}" method="post">
                                    @csrf
                                <div class="card-body">
                                    <div class="form-group mb-8">
                                        <div class="alert alert-custom alert-default" role="alert">
                                            <div class="alert-icon"><i class="fas fa-history text-primary"></i></div>
                                            <div class="alert-text">
                                            History Loker Area  
                                            @if($kategori == 'ps1')
                                            Pria Sepatu 1
                                            @elseif($kategori == 'ps2')
                                            Pria Sepatu 2
                                            @elseif($kategori == 'pb1')
                                            Pria Baju 1
                                            @elseif($kategori == 'pb2')
                                            Pria Baju 2
                                            @elseif($kategori == 'ws1')
                                            Wanita Sepatu 1
                                            @elseif($kategori == 'ws2')
                                            Wanita Sepatu 2
                                            @elseif($kategori == 'wb1')
                                            Wanita Baju 1
                                            @else
                                            Wanita Baju 2
                                            @endif
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" value="{{$kategori}}" name="kategori">

                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="exampleSelect1">Pilih Loker</label>
                                                <select class="form-control select2 PilihLoker" id="exampleSelect1" style="width: 100%;" name="loker">
                                                    <option disabled selected>Silahkan Pilih</option>
                                                    @foreach ($master_loker as $val)
                                                    <option value="{{$val->no_loker}}">{{$val->no_loker}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-8 mt-2">
                                            <br>
                                            <div class="form-group row">
                                            <label for="example-date-input" class="col-2 col-form-label">Tanggal</label>
                                            <div class="col-4">
                                            <input class="form-control" type="date" id="example-date-input" name="tgl_mulai"/>
                                            </div> 
                                            <p class="mt-2">Sampai</p>
                                            <div class="col-4">
                                            <input class="form-control" type="date" id="example-date-input" name="tgl_selesai"/>
                                            <button type="submit" class="btn btn-sm btn-primary mt-4 btn-block"><i class="fas fa-search"></i> Pencarian</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr  class="text-center">
                                                    <td colspan="3">Loker : <b>{{$loker}}</b></td>
                                                    <td colspan="4">Range Pencarian : <b>{{\carbon\carbon::parse($tgl_mulai)->format('d-M-Y')}} Sampai {{\carbon\carbon::parse($tgl_selesai)->format('d-M-Y')}}</b> </td>
                                                </tr>
                                                <tr class="text-center">
                                                    <th>No.</th>
                                                    <th>Status</th>
                                                    {{-- <th>Area Loker</th> --}}
                                                    {{-- <th>Kode Blok</th> --}}
                                                    <th>Keterangan</th>
                                                    <th>Nama Karyawan Terakhir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($result as $value)
                                                <tr class="text-center">
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>
                                                        @if($value->status == 'IN')
                                                        <span class="badge badge-pill badge-info">IN</span>
                                                        @else
                                                        <span class="badge badge-pill badge-primary">OUT</span>
                                                        @endif
                                                    </td>
                                                    {{-- <td>
                                                         @if($value->kode_area == 'wb1')
                                                        Wanita Baju 1
                                                        @elseif($value->kode_area == 'wb2')
                                                        Wanita Baju 2
                                                        @elseif($value->kode_area == 'ws1')
                                                        Wanita Sepatu 1
                                                        @elseif($value->kode_area == 'ws2')
                                                        Wanita Sepatu 2
                                                        @elseif($value->kode_area == 'pb1')
                                                        Pria Baju 1
                                                        @elseif($value->kode_area == 'pb2')
                                                        Pria Baju 2
                                                        @elseif($value->kode_area == 'ps1')
                                                        Pria Sepatu 1
                                                        @else
                                                        Pria Sepatu 2
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$value->kode_blok}}
                                                    </td> --}}
                                                    <td>
                                                        {{$value->keterangan}}
                                                    </td>
                                                    <td>
                                                        {{$value->penghuni_sebelumnya}}
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
</div>
</div>
</div>
        

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">
    $('.PilihLoker').select2();
    $('.table').DataTable();


    </script>
@endpush