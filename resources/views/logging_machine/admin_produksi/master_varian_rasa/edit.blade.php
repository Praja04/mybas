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
                            <div class="card-body">
                              <a href="/logging_machine/adm_prod/master_varian" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                                   <form action="/logging_machine/adm_prod/update_master_varian/{{$detail->id}}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                             <div class="row">
                                                  <div class="col-md-6">
                                                       <div class="form-group">
                                                            <label for="exampleFormControlTextarea1">Varian Rasa</label>
                                                            <textarea class="form-control" id="exampleFormControlTextarea1" name="varian" >{{$detail->rasa}}</textarea>
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
