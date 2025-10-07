@extends('layouts.base-display')

@section('title', 'FORM EDIT MASTER SAMPLING GRAMATUR')

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
                  <div class="col-md-12 mb-3">
                       <div class="card">
                            <div class="card-body">
                              <a href="/logging_machine/adm_prod/master_sampling" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                                   <form action="/logging_machine/adm_prod/update_master_sampling/{{$detail->id}}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="row">
                                             <div class="col-md-4">
                                                  <div class="form-group">
                                                       <label for="exampleFormControlInput1">Jam Ke </label>
                                                       <select class="form-control Varian" name="jam_ke">
                                                            <option value="{{$detail->jam_ke}}" selected>{{$detail->jam_ke}}</option>
                                                            <option disabled>Silahkan Pilih</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                  </select>
                                                  </div>
                                             </div>
                                             <div class="col-md-8">
                                                  <div class="form-group">
                                                       <label for="exampleFormControlInput1">Waktu Mulai</label>
                                                       <input type="time" class="form-control" name="waktu_mulai" placeholder="Masukan Waktu Mulai" value="{{$detail->waktu_mulai}}">
                                                  </div>
                                                  <div class="form-group">
                                                       <label for="exampleFormControlInput1">Waktu Selesai</label>
                                                       <input type="time" class="form-control" name="waktu_selesai" placeholder="Masukan Waktu Selesai" value="{{$detail->waktu_selesai}}">
                                                  </div>
                                             </div>
                                        </div>
                                             <div class="row">
                                                  <div class="col-sm-5"></div>
                                                  <div class="col-sm-4"></div>
                                                  <div class="col-sm-3">
                                                       <button type="submit" class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                                  </div>
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

@endpush
