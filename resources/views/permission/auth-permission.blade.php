@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Auth Permission</h3>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-primary mb-2" data-toggle="modal" data-target="#modal">BUAT PERMISSION</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>PERMISSION</th>
                                    <th>CODENAME</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auth_permissions as $key => $permission)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $permission->name }}</td>
                                        <td>{{ $permission->codename }}</td>
                                        <td>
                                            {{-- <button onClick="ubah('{{ $permission->id }}')" class="btn btn-xs btn-warning" title="Ubah">
                                                <i class="fa fa-pencil"></i>
                                            </button> --}}
                                            {{-- <button onClick="hapus('{{ $permission->id }}')" class="btn btn-danger btn-xs" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button> --}}
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

    <div class="modal" id="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" role="form" id="form" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">BUAT AUTH PERMISSION BARU</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row _name">
                        <label for="name" class="col-sm-2 control-label pt-3">Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" id="name" placeholder="Permission Name">
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group row _codename">
                        <label for="codename" class="col-sm-2 control-label pt-3">Codename</label>
                        <div class="col-sm-10">
                            <input type="text" name="codename" class="form-control" id="codename" placeholder="Permission Codename">
                            <span class="help-block"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-left submit-button">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
          </div>
        </div>
    </div>

@endsection

@push('scripts')

<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

<script>
    $('.table').DataTable();

    $(document).ready( function () {
      $('#form').submit( function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
          url: '{{ URL::to  ('/permission/auth-permission/store') }}',
          type: 'POST',
          data: data,
          success: function ( response ) {
            if (response.success == 1) {
              setTimeout(function() {
                location.reload();
              }, 500);
            }else{
              alert("Tidak bisa menyimpan data, silahkan periksa inputan anda");
            }
          },
          error : function ( error ) {
            if (error.status == 422) {
              // alert("Gagal");
              $('.help-block').text('');
              $.each(error.responseJSON.errors, (index, item) => {
                $('._'+index+' .help-block').text(item);
              });
              // console.log(item);
            }
          }
        })
      })
    })
</script>

@endpush
