@extends('layouts.base-display')
@section('title', 'Form Close Request Maintenance')

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


            <div class="row gutters-sm">
                <div class="col-sm-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('/logging_machine/close_request/' . Crypt::encrypt($data->id) ) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                 <input type="text" class="form-control" value="{{ $data->jam_mulai_maintenance }}" name="jam_mulai_maintenance">


                                <a href="/logging_machine/maintenance/{{ $data->approval_maintenance_nik }}" class="btn btn-danger btn-sm mb-3"
                                    style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Nama Pengaju</label>
                                            <input type="text" class="form-control" value="{{ $data->nama }}" name="nama"
                                                disabled>
                                            <input type="text" class="form-control" value="{{ $data->jenis_maintenance }}"
                                                name="kategori" hidden>

                                            <input type="text" class="form-control"
                                                value="{{ $data->approval_maintenance_nik }}" name="nik_maintenance"
                                                hidden>

                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Tanggal Permintaan</label>
                                            <input type="text" class="form-control" value="{{ $data->tgl_pengisian }}"
                                                name="tgl_permintaan" disabled>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">Varian</label>
                                            <select class="form-control" name="varian" disabled>
                                                <option disabled selected>{{ $data->varian }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">No. Mesin</label>
                                            <select class="form-control" name="no_mesin" disabled>
                                                <option disabled selected>{{ $data->no_mesin }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="exampleFormControlInput1">Shift/Group</label>
                                                <input type="text" class="form-control" placeholder="Contoh: 1/C"
                                                name="shift_group" value="{{ $data->shift_group }}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Detail Kerusakan</label>
                                            <textarea class="form-control" name="kerusakan" required
                                                placeholder="{{ $data->kerusakan }}" disabled></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label>Repair Kerusakan</label>
                                                <textarea class="form-control" name="approval_maintenance_remarks" required
                                                placeholder="Masukan Upaya Perbaikan Mesin" autofocus></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                         <div class="form-group">
                                        <label class="col-sm-5 col-form-label">Pilih Team : </label>
                                            <div class="form-group">
                                                <select class="form-control selectpicker" multiple="multiple" name="team[]">
                                                    <option value="{{$nama_maintenance->username}}" selected><b>{{$nama_maintenance->name}} </b></option>
                                                    @foreach ($eng as $item)
                                                    <option value="{{$item->name}}"><b>{{$item->name}}</b> | NIK: {{ $item->username}}</option>
                                                    @endforeach
                                            </select>
                                            <span class="text-danger mt-4"> Pilih Banyak Jika Lebih Dari 1.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="row">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-3">
                                        <button type="button" data-toggle="modal" data-target="#exampleModalCenter"
                                            class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i
                                                class="fas fa-save"></i> Close Request</button>
                                    </div>
                                </div>
                                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">Apakah Anda Yakin?</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                                    Ya, Simpan</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>




            @endsection

            @push('scripts')


            @endpush
