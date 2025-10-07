@extends('layouts.base')

@section('content')

<div class="container-fluid">

    <!--begin::Row-->
    <div class="row">

        <div class="col-lg-12">
            <!--begin::Advance Table Widget 4-->
            <div class="card card-custom card-stretch gutter-b">
                <!--begin::Header-->
                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">Update Jumlah Pesanan</span>
                    </h3>
                </div>
                <form action="{{url('/PostUpdatePesananCatering')}}" method="POST">
                    @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleSelectl">Tanggal Update Pesanan</label>
                        <input type="date" name="tanggal_pesan" class="form-control" value="{{ $tanggal }}">
                    </div>
                    <div class="form-group">
                        <label for="exampleSelectl">SHIFT</label>
                        <select class="form-control form-control-lg" id="exampleSelectl" name="shift">
                            <option value="1" @if($shift == 1) selected @endif>1</option>
                            <option value="2" @if($shift == 2) selected @endif>2</option>
                            <option value="3" @if($shift == 3) selected @endif>3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exampleSelectl">Jumlah Porsi</label>
                        <input type="number" name="jumlah_porsi" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="exampleSelectl">Alasan Update</label>
                        <input type="text" name="alasan_update" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="exampleSelectl">Jenis Update</label>
                        <select class="form-control form-control-lg" id="exampleSelectl" name="jenis">
                            <option value="penambahan">Penambahan</option>
                            <option value="pengurangan">Pengurangan</option>                 
                        </select>
                    </div>
                </form>
                    <button type="submit" class="btn btn-info btn-lg">simpan</button>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Advance Table Widget 4-->
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->
</div>

@endsection
