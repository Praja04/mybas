@extends('layouts.base-display')

@section('title', 'FORM CHECKSHEET KEBERSIHAN/KETERATURAN')

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
                         <form action="{{url('/logging_machine/post_kebersihan')}}" method="POST">
                              @csrf

                              <input type="text" class="form-control" value="{{$user->id}}" name="id_logging_machine" hidden>
                              <input type="text" class="form-control" value="{{$user->nik}}" name="nik" hidden>

                              <a href="/logging_machine/index/{{$user->nik}}" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>

                              @if ($cek)
                              <div class="row">
                                 <div class="col-md-8">
                                   <div class="card mb-3">
                                     <div class="card-body">
                                       <div class="alert alert-primary" role="alert">
                                         
                                         Opps.. Anda Sudah Mengisi Checksheet Kebersihan/Keteraturan. Silahkan Lihat Di History.
                                           <br>
                                         <a href="/logging_machine/history_kebersihan/{{$user->no_mesin}}/{{Crypt::encrypt($user->nik)}}" class="btn btn-warning btn-md mt-3"> Lihat history <i class="fas fa-arrow-right"></i></a>
                                       </div>
                                     </div>
                                   </div>
                               </div>
                              </div>
                         @else
                              <div class="row">
                                <div class="col-md-6 mt-4">
                                	<div class="form-group row">
                                    <label class="col-3 col-form-label">Lantai</label>
                                    <div class="col-9 col-form-label">
                                      <div class="checkbox-list">
                                        <label class="checkbox">
                                        <input type="checkbox" class="Lantai" name="lantai" value="Cukup Bersih" />
                                        <span></span>Cukup Bersih</label>
                                        <label class="checkbox">
                                        <input type="checkbox" class="Lantai"  name="lantai" value="Bersih" />
                                        <span></span>Bersih</label>
                                        <label class="checkbox">
                                        <input type="checkbox" class="Lantai" checked="checked"  name="lantai" value="Kurang Bersih" />
                                        <span></span>Kurang Bersih</label>
                                      </div>
                                    </div>
                                </div>
                            </div>
                              <div class="col-md-6">
                                <div class="form-group row">
                                  <label class="col-3 col-form-label">Bak</label>
                                  <div class="col-9 col-form-label">
                                    <div class="checkbox-list">
                                      <label class="checkbox">
                                      <input type="checkbox" class="Bak" name="bak" value="Cukup Bersih" />
                                      <span></span>Cukup Bersih</label>
                                      <label class="checkbox">
                                      <input type="checkbox" class="Bak"  name="bak" value="Bersih" />
                                      <span></span>Bersih</label>
                                      <label class="checkbox">
                                      <input type="checkbox" class="Bak" checked="checked"   name="bak" value="Kurang Bersih" />
                                      <span></span>Kurang Bersih</label>
                                    </div>
                                  </div>
                                </div>
                               </div>
                            </div>
                         <hr>
                         <div class="row">
                          <div class="col-sm-6">
                            <div class="form-group row">
                              <label class="col-3 col-form-label">Body Mesin</label>
                              <div class="col-9 col-form-label">
                                <div class="checkbox-list">
                                  <label class="checkbox">
                                  <input type="checkbox" class="Body_mesin"  name="body_mesin" value="Cukup Bersih" />
                                  <span></span>Cukup Bersih</label>
                                  <label class="checkbox">
                                  <input type="checkbox" class="Body_mesin"  name="body_mesin" value="Bersih" />
                                  <span></span>Bersih</label>
                                  <label class="checkbox">
                                  <input type="checkbox" class="Body_mesin" checked="checked"  name="body_mesin" value="Kurang Bersih" />
                                  <span></span>Kurang Bersih</label>
                                </div>
                              </div>
                            </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group row">
                                <label class="col-3 col-form-label">Sealer</label>
                                <div class="col-9 col-form-label">
                                  <div class="checkbox-list">
                                    <label class="checkbox">
                                    <input type="checkbox" class="Sealer" name="sealer" value="Cukup Bersih" />
                                    <span></span>Cukup Bersih</label>
                                    <label class="checkbox">
                                    <input type="checkbox" class="Sealer"  name="sealer" value="Bersih" />
                                    <span></span>Bersih</label>
                                    <label class="checkbox">
                                    <input type="checkbox" class="Sealer" checked="checked"  name="sealer" value="Kurang Bersih" />
                                    <span></span>Kurang Bersih</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group row">
                                <label class="col-3 col-form-label">Gayung</label>
                                <div class="col-9 col-form-label">
                                  <div class="checkbox-list">
                                    <label class="checkbox">
                                    <input type="checkbox" class="Gayung" name="gayung" value="Cukup Bersih" />
                                    <span></span>Cukup Bersih</label>
                                    <label class="checkbox">
                                    <input type="checkbox" class="Gayung"  name="gayung" value="Bersih" />
                                    <span></span>Bersih</label>
                                    <label class="checkbox">
                                    <input type="checkbox" class="Gayung" checked="checked"   name="gayung" value="Kurang Bersih" />
                                    <span></span>Kurang Bersih</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <div class="col-sm-6">
                            <div class="form-group row">
                              <label class="col-3 col-form-label">Sodokan</label>
                              <div class="col-9 col-form-label">
                                <div class="checkbox-list">
                                  <label class="checkbox">
                                  <input type="checkbox" class="Sodokan"  name="sodokan" value="Cukup Bersih" />
                                  <span></span>Cukup Bersih</label>
                                  <label class="checkbox">
                                  <input type="checkbox" class="Sodokan"  name="sodokan" value="Bersih" />
                                  <span></span>Bersih</label>
                                  <label class="checkbox">
                                  <input type="checkbox" class="Sodokan" checked="checked"  name="sodokan" value="Kurang Bersih" />
                                  <span></span>Kurang Bersih</label>
                                </div>
                              </div>
                            </div>
                         </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                          <div class="form-group row">
                            <label class="col-3 col-form-label">Tutup Hopper</label>
                            <div class="col-9 col-form-label">
                              <div class="checkbox-list">
                                <label class="checkbox">
                                <input type="checkbox" class="Tutup_hopper"  name="tutup_hopper" value="Cukup Bersih" />
                                <span></span>Cukup Bersih</label>
                                <label class="checkbox">
                                <input type="checkbox" class="Tutup_hopper"  name="tutup_hopper" value="Bersih" />
                                <span></span>Bersih</label>
                                <label class="checkbox">
                                <input type="checkbox" class="Tutup_hopper" checked="checked"  name="tutup_hopper" value="Kurang Bersih" />
                                <span></span>Kurang Bersih</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group row">
                            <label class="col-3 col-form-label">Serbet</label>
                            <div class="col-9 col-form-label">
                              <div class="checkbox-list">
                                <label class="checkbox">
                                <input type="checkbox" class="Serbet"  name="serbet" value="Cukup Bersih" />
                                <span></span>Cukup Bersih</label>
                                <label class="checkbox">
                                <input type="checkbox" class="Serbet"  name="serbet" value="Bersih" />
                                <span></span>Bersih</label>
                                <label class="checkbox">
                                <input type="checkbox" class="Serbet" checked="checked" name="serbet" value="Kurang Bersih" />
                                <span></span>Kurang Bersih</label>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
               <div class="row">
                    <div class="col-sm-5"></div>
                    <div class="col-sm-4"></div>
                    <div class="col-sm-3">
                         <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                    </div>
               </div>
               @endif
               <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content"> 
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">Apakah Anda Yakin?</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Ya, Simpan</button>
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

    <script type="text/javascript">
   
    $('.Lantai').click(function() {
      $('.Lantai').not(this).prop('checked', false);
    });
   
    $('.Body_mesin').click(function() {
      $('.Body_mesin').not(this).prop('checked', false);
    });
    
    $('.Gayung').click(function() {
      $('.Gayung').not(this).prop('checked', false);
    });
    
    $('.Tutup_hopper').click(function() {
      $('.Tutup_hopper').not(this).prop('checked', false);
    });

    $('.Bak').click(function() {
      $('.Bak').not(this).prop('checked', false);
    });
   
    $('.Sealer').click(function() {
      $('.Sealer').not(this).prop('checked', false);
    });

    $('.Sodokan').click(function() {
      $('.Sodokan').not(this).prop('checked', false);
    });

    $('.Serbet').click(function() {
      $('.Serbet').not(this).prop('checked', false);
    });

    </script>
  

@endpush
