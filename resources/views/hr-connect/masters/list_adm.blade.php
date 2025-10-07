@extends('hr-connect.layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <button id="btnStore" class="btn btn-sm btn-primary rounded-pill mb-3">tambah data</button>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Masters Admin</h5>
                </div>
                <div class="card-body">
                    <table id="tableAjax" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Kode Bagian</th>
                                <th style="width: 10%;">Kode Admin</th>
                                <th style="width: 20%;">NIK</th>
                                <th style="width: 30%;">Nama Admin</th> <!-- Users !-->
                                <th style="width: 10%;">Action</th> 
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Store Data !-->
<div class="modal fade" id="modalData" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDataLabel">Tambah Data Masters Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mt-1">
                    <form id="storeForm">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="">Kode Bagian</label>
                                <select id="kode_bagian" class="js-example-basic-single">
                                    <option value="">Pilih Kode Bagian</option>
                                    @foreach($kode_bagian as $bag)
                                    <option value="{{ $bag->kode_bagian }}">{{ $bag->kode_bagian }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="">Kode Admin</label>
                                <select id="kode_admin" class="js-example-basic-single">
                                    <option value="">Pilih Kode Bagian</option>
                                    @foreach($kode_admin as $adm)
                                    <option value="{{ $adm->kode_admin }}">{{ $adm->kode_admin }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <label for="">Nama Admin</label>
                                <select id="nama_admin" class="js-example-basic-single">
                                    <option value="">Pilih Nama Admin</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->name }}">{{ $user->username.' - '.$user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-12">
                                <input type="hidden" id="editId">
                                <button class="btn btn-primary rounded-pill mt-3">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/global/select2.full.min.js') }}"></script>
{{-- <script src="/assets/velzon/js/pages/select2.init.js"></script> --}}
<script>
$(document).ready(function(){
    let table = $("#tableAjax").dataTable({
        processing: false,
        serverSide: true,
        paging: false,
        ajax: {
            type: "GET",
            url: "/hr-connect/masters/admin/getData"
        },
        columns: [
            { data: "kode_bagian" },
            { data: "kode_admin" },
            { data: "nik_admin" },
            { data: "nama_admin" },
            {
                data: 'kode_admin',
                render: function(data, type, row){
                    return `
                        <button class="btn btn-sm btn-success btnEdit" data-id="${row.id}"><i class="mdi mdi-account-edit"></i></button>
                        <button class="btn btn-sm btn-danger btnDelete" data-id="${row.id}"><i class="mdi mdi-trash-can"></i></button>
                    `;
                }
            }
        ]
    });

    $('.js-example-basic-single').select2({
        dropdownParent: $('#modalData')
    });

    $("#btnStore").click(function(){
        $("#modalDataLabel").text('Tambah Data Masters Admin');
        $("#kode_admin").val('');
        $("#nama_admin").val('');
        $("#editId").val('');
        $("#modalData").modal("show");
    });

    $(document).on("click", ".btnEdit", function(){
        let id = $(this).data("id");
        $("#modalDataLabel").text('Edit Data Masters Admin');
        $("#editId").val(id);

        $.ajax({
            type: "GET",
            url: "/hr-connect/masters/admin/show/" + id,
            success: function(response){
                $("#kode_admin").val(response.kode_admin).trigger('change');;
                $("#nama_admin").val(response.nama_admin).trigger('change');;
                $("#modalData").modal("show");
            },
            error: function(xhr){
                alert(xhr.responseText);
            }
        });
    });

    $("#storeForm").submit(function(e){
        e.preventDefault();
        let id = $("#editId").val();
        let url = id ? "/hr-connect/masters/admin/" + id : "/hr-connect/masters/admin/store";
        let formData = {
            "kode_bagian": $("#kode_bagian").val(),
            "kode_admin": $("#kode_admin").val(),
            "nama_admin": $("#nama_admin").val(),
        };

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            success: function(res){
                table.api().draw();

                if(res.status == 'success'){
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: res.message
                    })
                }

                $("#modalData").modal("hide");
            },
            error: function(xhr){
                let errors = xhr.responseJSON.errors;
                let message = '';
                $.each(errors, function(key, value) {
                    message += value[0] + '<br>';
                });

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: message,
                });
            }
        });
    });

    $(document).on("click", ".btnDelete", function(){
        Swal.fire({
            title: "Confirm",
            icon: "info",
            text: "Yakin ingin menghapus data?",
            showDenyButton: true,
            confirmButtonText: "Lanjutkan!",
            denyButtonText: "Batal",
        }).then((result) => {
            if(result.isConfirmed){
                let id = $(this).data("id");

                $.ajax({
                    type: "DELETE",
                    url: "/hr-connect/masters/admin/" + id,
                    success: function(){
                        Swal.fire("Success","Berhasil menghapus data masters admin!","success");
                        table.api().draw();
                    },
                    error: function(xhr){
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
});
</script>
@endpush
