@extends('layouts.base')

@push('styles')
    <link rel="stylesheet" href="{{ url('/assets/plugins/custom/datatables/datatables.bundle.css') }}">
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-header flex-wrap border-0 pt-6 pb-0">
                        <div class="card-title">
                            <h3 class="card-label">Master
                                <span class="d-block text-muted pt-2 font-size-sm">Master Manage Approval</span></h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="javascript:" class="btn btn-primary font-weight-bolder" onClick="showModalCreateNew()"><i class="fa fa-plus-circle"></i> Create New</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bagian</th>
                                    <th>Approval 1</th>
                                    <th>Approval 2</th>
                                    <th>Approval 3</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($approval as $key => $a)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $a->bagian->nama_bagian  }}</td>
                                    <td>{{ $a->approval_1->name }}</td>
                                    <td>{{ $a->approval_2->name }}</td>
                                    <td>{{ $a->approval_3->name }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formApproval">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel"><span id="modal-title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-divisi">Divisi</label>
                        <div class="col-9">
                            <select name="divisi" id="pilih-divisi" class="form-control">
                                <option value="">Pilih Divisi</option>
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-bagian">Bagian</label>
                        <div class="col-9">
                            <select disabled name="bagian" id="pilih-bagian" class="form-control">
                                <option value="">Pilih Bagian</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label  class="col-3 col-form-label" for="pilih-approval-1">Approval 1</label>
                        <div class="col-9">
                            <select required name="approval1" id="pilih-approval-1" class="form-control">
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-3 col-form-label" for="pilih-approval-2">Approval 2</label>
                        <div class="col-9">
                            <select required name="approval2" id="pilih-approval-2" class="form-control">
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-3 col-form-label" for="pilih-approval-3">Approval 3</label>
                        <div class="col-9">
                            <select required name="approval3" id="pilih-approval-3" class="form-control">
                                <option value="">Pilih User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary font-weight-bold" id="submitButton">Save</button>
                    <button type="button" class="btn btn-default font-weight-bold" data-dismiss="modal">Close</button>
                </div>
            </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script type="text/javascript">
        $('.table').DataTable();

        function showModalCreateNew()
        {
            $('#modal-title').text('Create New');
            $('#modal').modal('show');
        }

        $('#pilih-divisi').on('change', function () {
            var divisiId = $(this).val();
            $('#pilih-bagian').attr('disabled', true);
            $.ajax({
                url: "{{ url('/') }}/bagian/get-by-divisi/"+divisiId,
                type: "GET",
                dataType: "JSON",
                success: function ( response ) {
                    $('#pilih-bagian').html('<option value="">Pilih Bagian</option>');
                    $('#pilih-bagian').removeAttr('disabled');
                    $.each(response.bagians, function (key,val) {
                        $('#pilih-bagian').append('<option value="' + val.id + '">' + val.nama_bagian + '</option>');
                    });
                },
                error: function ( error ) {
                    console.log( error );
                }
            });
        });

        $('#formApproval').on('submit', function(e) {
            e.preventDefault();
            $('#submitButton').attr('disabled', true);
            $('#submitButton').text('Processing..');
            var data = $(this).serialize();
            $.ajax({
                url: "{{ url('/') }}/master/approval/store",
                data: data,
                type: "POST",
                dataType: "JSON",
                success: function ( response ) {
                    $('#modal').modal('hide');
                    Swal.fire("Oke!", "Data berhasil disimpan!", "success")
                        .then((value) => {
                            $('#submitButton').removeAttr('disabled');
                            $('#submitButton').text('Submit');
                            location.reload();
                        });
                },
                error: function ( e ) {
                    // console.log( e );
                    Swal.fire("Gagal!", "Mohon coba lagi!", "error");
                    $('#submitButton').removeAttr('disabled');
                    $('#submitButton').text('Submit');
                }
            });
        });
    </script>

@endpush
