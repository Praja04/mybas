@extends('hr-connect.layouts.base')

@push('styles')
<style>
input[type="checkbox"]:disabled {
    opacity: 1; 
    border-width: 2px; 
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pemulihan Data</h5>
                </div>
                <div class="card-body">
                    <table id="tableAjax" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false" style="width: 20%;">NIK</th>
                                <th data-ordering="false" style="width: 30%;">Nama</th>
                                <th data-ordering="false">Dept</th>
                                <th data-ordering="false">Alasan Keluar</th>
                                <th data-ordering="false" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>
<script>
function table(){
    $("#tableAjax").dataTable({
        processing: false,
        ordering: false,
        bDestroy: true,
        ajax: {
            type: "GET",
            url: "/hr-connect/dept-hrd/pemulihan-data/getData"
        },
        columns: [
            { data: 'nik' },
            { data: 'nama' },
            { data: 'kode_bagian' },
            { data: 'alasan_keluar' },
            { 
                data: null,
                render: function(data, type, row){
                    return `
                    <center>
                        <button class="btn btn-info btn-sm" onClick="restore('${row.nik}')"><i class="mdi mdi-restore"></i> restore</button>
                    </center>`
                }
             },
        ]
    });
} table();

function restore(nik){
    Swal.fire({
        title: 'Konfirmasi Pemulihan',
        text: "Apakah Anda yakin ingin memulihkan data ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, pulihkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/hr-connect/dept-hrd/pemulihan-data`,
                type: 'PUT', 
                data: {
                    nik
                },
                success: function(res) {
                    Swal.fire(
                        'Berhasil!',
                        res.message,
                        'success'
                    );

                    table();
                },
                error: function(xhr) {
                    Swal.fire(
                        'Gagal!',
                        'Terjadi kesalahan saat memulihkan data.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endpush