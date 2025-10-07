
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
                        <span class="card-label font-weight-bolder text-dark">Edit Master Obat</span>
                    </h3>
                    <div class="float-right">
                        <a href="javascript:history.back()" class="btn btn-lg btn-info"><i class="fas fa-arrow-circle-left"></i> kembali</a>
                    </div>
                </div>
                <form action="{{url('/PostEditObat')}}" method="POST" id="edit">
                    @csrf
                    <input type="hidden" name="id" class="form-control"value="{{$master->id}}">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleSelectl">OBAT</label>
                            <input type="text" name="nama_obat" class="form-control" value="{{$master->nama_obat}}">
                        </div>
                        <div class="form-group">
                            <label for="exampleSelectl">HARGA</label>
                            <input type="text" name="harga" class="form-control"value="{{$master->harga,2,',','.'}}">
                        </div>
                        <div class="form-group">
                            <label for="exampleSelectl">SATUAN</label>
                            <input type="text" name="satuan" class="form-control"value="{{$master->satuan}}">
                        </div>
                        <div class="float-right">
                           <button type="submit" class="btn btn-primary btn-sm BtnUpdateFile" onclick = "return confirm('Apakah Anda Yakin Akan Merubah Data Ini?');" style="border-radius: 13px;"><i class="fas fa-save"></i> Update</button>
                           <button type="button" class="btn btn-info spinner spinner-darker-info spinner-left mr-3 Proses" style="display: none;" disabled>
                           Proses Update..
                           </button>
                       </div>
                    </form>
                </div>
                <!--end::Body-->
            </div>
            <!--end::Advance Table Widget 4-->
        </div>
    </div>
    
</div>

@endsection
<!--end::Row-->
<!--end::Dashboard-->
@push('scripts')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script> 
$('#edit').on('submit', function(){
    $('.BtnUpdateFile').hide()
  $('.spinner').show()
})
</script>
@endpush
