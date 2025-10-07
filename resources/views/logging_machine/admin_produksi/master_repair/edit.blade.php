@extends('layouts.base-display')

@section('title', 'FORM EDIT MASTER REPAIR MESIN')

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
                            <form action="/logging_machine/adm_prod/update_master_mesin_repair/{{$detail->id}}/{{$detail->no_mesin}}" method="POST">
                                 @csrf
                                 @method('PATCH')
                            <div class="card-body">
                              <a href="/logging_machine/adm_prod/master_mesin" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                               
                                             <div class="row">
                                                  <div class="col-md-4">
                                                       <div class="form-group">
                                                            <label for="exampleFormControlInput1"> Jenis Mesin </label>
                                                            <select class="form-control Varian" name="jenis_mesin">
                                                            <option value="{{$detail->jenis_mesin}}">{{$detail->jenis_mesin}}</option>
                                                            <option disabled>Silahkan Pilih</option>
                                                            <option value="packing">Packing</option>
                                                            <option value="proses">Proses</option>
                                                       </select>
                                                       </div>
                                                  </div>
                                                  <div class="col-md-4">
                                                       <div class="form-group">
                                                            <label for="exampleFormControlInput1"> No.Mesin </label>
                                                            <select class="form-control Varian" name="no_mesin">
                                                            <option value="{{$detail->no_mesin}}">{{$detail->no_mesin}}</option>
                                                            <option disabled>Silahkan Pilih</option>
                                                       </select>
                                                       </div>
                                                  </div>
                                                  <div class="col-sm-4">
                                                       <label for="exampleFormControlInput1">Kategori</label>
                                                       <select class="form-control Varian" name="kategori">
                                                            <option disabled>Silahkan Pilih</option>
                                                            <option value="Operator">Operator</option>
                                                            <option value="Engineering">Engineering</option>
                                                       </select>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-md-12">
                                                       <div class="form-group">
                                                            <label for="exampleFormControlInput1">Reason</label>
                                                            <select class="form-control Varian" name="reason">
                                                                 <option value="{{$detail->reason}}" selected>{{$detail->reason}}</option>
                                                                 <option disabled>Silahkan Pilih</option>
                                                                 @foreach ($reason as $item)
                                                                 <option value="{{$item->reason}}">{{$item->reason}}</option>
                                                                 @endforeach
                                                            </select>
                                                          </div>
                                                  </div>
                                             </div>
                                             <div class="row">
                                                  <div class="col-md-12">
                                                       <div class="form-group">
                                                            <label for="exampleFormControlTextarea1">Repair</label>
                                                            <textarea class="form-control" id="exampleFormControlTextarea1" name="repair" >{{$detail->repair}}</textarea>
                                                          </div>
                                                  </div>
                                             </div>
                                           
                                             <hr>
                                             <div class="row">
                                                  <div class="col-sm-5"></div>
                                                  <div class="col-sm-4"></div>
                                                  <div class="col-sm-3">
                                                       <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                                  </div>
                                             </div>
                                        <hr>
                                             <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                  <div class="modal-dialog modal-dialog-centered" role="document">
                                                  <div class="modal-content"> 
                                                  <div class="modal-header">
                                                       <h5 class="modal-title" id="exampleModalLongTitle">Apakah detail Sudah Benar?</h5>
                                                       <button type="button" class="close" detail-dismiss="modal" aria-label="Close">
                                                       <span aria-hidden="true">&times;</span>
                                                       </button>
                                                  </div>
                                                  <div class="modal-footer">
                                                       <button type="button" class="btn btn-secondary" detail-dismiss="modal">Close</button>
                                                       <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ya, Simpan</button>
                                                  </div>
                                                  </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </form>
                                   </div>
                              </div>
                         </div>
                    </div>

          
          


@endsection

@push('scripts')


@endpush
