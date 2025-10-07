@extends('layouts.base-display')

@section('title', 'FORM INPUT MASTER SAMPLING GRAMATUR')

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
               <a href="/logging_machine/adm_prod" class="btn btn-danger btn-sm mb-3" style="border-radius: 10px"> <i class="fas fa-arrow-left"></i> Kembali</a>


               <div class="row">
                    <div class="col-md-12">
                         <div class="form-group">
                              <label for="exampleFormControlInput1">Jenis Import Data</label>
                              <select class="form-control jenis_import" >
                              <option disabled selected>Silahkan Pilih</option>
                              <option value="1">Import Excel</option>
                              <option value="2">Manual</option>
                         </select>
                         </div>
                    </div>
               </div>

               
               <div id="import">
                    <div class="row">
                         <form action="{{url('/adm_prod/import_master_gramatur')}}" method="POST" enctype="multipart/form-data">
                              @csrf
                    <div class="col-md-12">
                         <div class="form-group">
                              <label for="exampleFormControlInput1">Pilih File</label>
                              <input accept=".xlsx" type="file" name="file" class="form-control" >
                         </div>
                         <div class="col-sm-12">
                              <a href="/master_import/MASTER_JAM_SAMPLING.xlsx" class="btn mb-2 btn-block text-white" style="border-radius: 8px; background-color: green"><i class="fas fa-file-excel text-white"></i> Download Master Excel</a>
                              <button type="submit" class="btn btn-primary mb-2 btn-block" style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                         </div>
                    </div>
                 </form>
               </div>
          </div>


          <div id="manual">
                    <form action="/logging_machine/adm_prod/post_master_sampling" method="POST">
                         @csrf
                                        <div class="row">
                                             <div class="col-md-4">
                                                  <div class="form-group">
                                                       <label for="exampleFormControlInput1">Shift</label>
                                                       <select class="form-control Varian" name="shift">
                                                       <option disabled selected>Silahkan Pilih</option>
                                                       <option value="1">1</option>
                                                       <option value="2">2</option>
                                                       <option value="3">3</option>
                                                  </select>
                                                  </div>
                                                  <div class="form-group">
                                                       <label for="exampleFormControlInput1">Jam Ke </label>
                                                       <select class="form-control Varian" name="jam_ke">
                                                       <option disabled selected>Silahkan Pilih</option>
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
                                                       <input type="time" class="form-control" name="waktu_mulai" min="0" max="23" placeholder="Masukan Waktu Mulai">
                                                  </div>
                                                  <div class="form-group">
                                                       <label for="exampleFormControlInput1">Waktu Selesai</label>
                                                       <input type="time" class="form-control" name="waktu_selesai" min="00:00" max="24:00" placeholder="Masukan Waktu Selesai">
                                                  </div>
                                             </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mb-2" style="border-radius: 8px"><i class="fas fa-save"></i> Simpan</button>
                                   </div>
                         <hr>
                              <div class="row">
                                   <div class="col-sm-12">
                                        <div class="table-responsive">
                                        <table class="table table-hover">
                                             <thead>
                                             <tr>
                                             <th class="text-center">Shift</th>
                                             <th class="text-center">Jam Ke</th>
                                             <th class="text-center">Waktu Mulai</th>
                                             <th class="text-center">Waktu Selesai</th>
                                             <th class="text-center">Opsi</th>
                                             </tr>
                                             </thead>
                                             <tbody>
                                                  @foreach ($sampling as $list)
                                             <tr>
                                                  <td class="text-center">{{$list->shift}}</td>       
                                                  <td class="text-center">{{$list->jam_ke}}</td>       
                                                  <td class="text-center">{{$list->waktu_mulai}}</td>    
                                                  <td class="text-center">{{$list->waktu_selesai}}</td>    
                                                  <td class="text-center"> 
                                                       <a href="/logging_machine/adm_prod/get_master_sampling/{{$list->id}}" class="btn btn-info btn-sm" style="border-radius: 7px"><i class="fas fa-edit"></i> Edit</a>
                                                       <a href="/logging_machine/adm_prod/delete_master_sampling/{{$list->id}}" class="btn btn-primary btn-sm" style="border-radius: 7px"><i class="fas fa-trash"></i> Hapus</a>
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





@endsection

@push('scripts')
<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

<script type="text/javascript">
    $('.table').DataTable();

$('.Additem').click(function() {
var fieldHTML = '<div class="form-group col-sm-12 kolom_item">' + $(".kolom_item_copy").html() + '</div>';
$('body').find('.kolom_item:last').after(fieldHTML);
$('.Hapus').show();
$("body").on("click", ".Hapus", function() {
$(this).parents(".kolom_item").remove();
});
});


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



</script>

@endpush
