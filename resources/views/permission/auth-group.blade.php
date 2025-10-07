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
                            <h3 class="card-label">Auth Group</h3>
                        </div>
                        <div class="card-toolbar">
                          <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreate">BUAT GROUP</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th width="10px">NO</th>
                                    <th>NAME</th>
                                    <th width="10px">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auth_groups as $key => $group)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td><a onClick="groupPermissions('{{ $group->id }}')" href="javascript:;">{{ $group->name }}</a></td>
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

    <div class="modal" id="modalCreate">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="form-horizontal" action="" role="form" id="formCreate" method="POST">
                    @csrf
                    <input type="hidden" name="group_id" class="group_id" value="">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">BUAT GROUP PERMISSION</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row _name">
                            <label for="name" class="col-sm-2 control-label pt-3">Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="name" placeholder="Group Name">
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

    <div class="modal" id="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" action="" role="form" id="form" method="POST">
                @csrf
                <input type="hidden" name="group_id" class="group_id" value="">
                <div class="modal-header">
                    <h4 class="modal-title">AUTH PERMISSIONS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="auth-permissions">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary pull-left submit-button">OK</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
    </div>

@endsection

@push('scripts')

<script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

<script>
    $('.table').DataTable();

    function groupPermissions(id)
    {
      // alert(id);
      $(".group_id").val(id);
      $.ajax({
        url: '{{ URL::to('/permission/auth-group/get-permissions') }}',
        type: 'POST',
        data: {
          id: id
        },
        success: function ( response )
        {
          console.log( response );
          $(".auth-permissions").html("");
          $.each(response.auth_permissions, (key, data) => {
            $(".auth-permissions").append(`
              <li>
                <div class="checkbox">
                  <label>
                    <input class="mr-1" name="permissions[]" type="checkbox" checked value="`+data.id+`">`+data.codename+`
                  </label>
                </div>
              </li>
            `);
          });
          $(".auth-permissions").append(`
            <li>---------------------</li>
          `);
          $.each(response.permissions_left, (key, data) => {
            $(".auth-permissions").append(`
              <li>
                <div class="checkbox">
                  <label>
                    <input class="mr-1" name="permissions[]" type="checkbox" value="`+data.id+`">`+data.codename+`
                  </label>
                </div>
              </li>
            `);
          });
          $("#modal").modal("show");
          // console.log( response );
        },
        error: function ( error) {
          console.log( error );
        }
      })
    }

    $(document).ready( function () {
      $('#form').submit( function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
          url: '{{ URL::to('/permission/auth-group/change-permissions') }}',
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
      });

      $('#formCreate').submit( function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
          url: '{{ URL::to('/permission/auth-group/store') }}',
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
