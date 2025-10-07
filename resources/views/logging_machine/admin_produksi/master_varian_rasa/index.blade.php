@extends('layouts.base-display')

@section('title', 'FORM INPUT MASTER REPAIR MESIN')

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
                              <a href="/logging_machine/adm_prod" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>
                                   <form action="/logging_machine/adm_prod/post_master_varian" method="POST">
                                        @csrf
                                                  <div class="kolom_item">
                                                       <div class="row">
                                                            <div class="col-sm-12">
                                                                 <div class="form-group">
                                                                      <label for="exampleFormControlTextarea1">Varian Rasa</label>
                                                                      <textarea class="form-control" id="exampleFormControlTextarea1" name="varian[]"></textarea>
                                                                    </div>
                                                            </div>
                                                       </div>
                                                       <a href="javascript:void(0)" class="btn btn-dark btn-sm mb-3 mt-4 Additem" style="border-radius: 10px"> <i class="fas fa-plus"></i> Tambah</a>
                                                       <hr>
                                                  </div>
                                             
                                                  <div class="kolom_item_copy" style="display: none">
                                               
                                                       <div class="row">
                                                            <div class="col-sm-6">
                                                                 <div class="form-group">
                                                                      <label for="exampleFormControlTextarea1">Varian Rasa</label>
                                                                      <textarea class="form-control" id="exampleFormControlTextarea1" name="varian[]"></textarea>
                                                                    </div>
                                                            </div>
                                                       </div>
                                                       <a href="javascript:void(0)" class="btn btn-warning btn-sm mb-3 mt-4 Hapus" style="border-radius: 10px"> <i class="fas fa-trash"></i> Hapus</a>
                                                       <hr>
                                                  </div>
                                                  
                              
                                             <div class="row">
                                                  <div class="col-sm-5"></div>
                                                  <div class="col-sm-4"></div>
                                                  <div class="col-sm-3">
                                                       <button type="button" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                                  </div>
                                             </div>
                                        <hr>
                                             <div class="row">
                                                  <div class="col-sm-12">
                                                    <div class="table-responsive">
                                                       <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                            <th class="text-center">No.</th>
                                                            <th class="text-center">Varian</th>
                                                            <th class="text-center">Opsi</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                 @foreach ($varian as $list)
                                                            <tr>
                                                                 <td class="text-center">{{$loop->iteration}}</td>       
                                                                 <td class="text-center">{{$list->rasa}}</td>    
                                                                 <td class="text-center"> 
                                                                      <a href="/logging_machine/adm_prod/get_master_varian/{{$list->id}}" class="btn btn-info btn-sm" style="border-radius: 7px"><i class="fas fa-edit"></i> Edit</a>
                                                                      <a href="/logging_machine/adm_prod/delete_master_varian/{{$list->id}}" class="btn btn-primary btn-sm" style="border-radius: 7px"><i class="fas fa-trash"></i> Hapus</a>
                                                                 </td>   
                                                            </tr> 
                                                            @endforeach
                                                            </tbody>
                                                       </table>
                                                  </div>
                                             </div>
                                        </div>
                                             
                                             <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                  <div class="modal-dialog modal-dialog-centered" role="document">
                                                  <div class="modal-content"> 
                                                  <div class="modal-header">
                                                       <h5 class="modal-title" id="exampleModalLongTitle">Apakah Data Sudah Benar?</h5>
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
                         </div>
                    </div>
               </div>

          
          


@endsection

@push('scripts')
<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
  <script type="text/javascript">
        $('.table').DataTable();

     $('.Varian').select2();

    $('.Additem').click(function() {
       var fieldHTML = '<div class="form-group col-sm-12 kolom_item">' + $(".kolom_item_copy").html() + '</div>';
          $('body').find('.kolom_item:last').after(fieldHTML);
        $('.Hapus').show();
          $("body").on("click", ".Hapus", function() {
        $(this).parents(".kolom_item").remove();
      });
  });

  </script>

@endpush
