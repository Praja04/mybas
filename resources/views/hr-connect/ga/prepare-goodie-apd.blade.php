@extends('hr-connect.layouts.base')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Persiapan Goodie Bag & APD</h5>
                </div>
                <div class="card-body">
                    <table id="tableAjax" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tgl Masuk</th>
                                <th>Jumlah Orang</th>
                                <th>Confirmed</th>
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
<script>
// tableGoodie();

// function tableGoodie(){
//     $.ajax({
//         type: "GET",
//         url: "/hr-connect/dept-ga/perlengkapan-goodie-apd/getData",
//         success: function(res){
//             $("#tableAjax tbody").empty()
            
//             if (res.hasOwnProperty('goodies')) {
//                 res.goodies.forEach(item => {
//                     let row = `<tr>
//                                     <td>${item.unique_nik.match(/.{1,2}/g).join('-')}</td>
//                                     <td>${item.count}</td>
//                                     <td>
//                                         <button class="btn btn-primary btnConfirm" data-jumlah="${item.count}" data-id="${item.id}" data-tgl="${item.unique_nik}">Konfirmasi</button>
//                                     </td>
//                                </tr>`;
//                     $("#tableAjax tbody").append(row);
//                 });
//             }
//         },
//         error: function(xhr){
//             alert(xhr.responseText);
//         }
//     });
// }

$(document).ready(function() {
    $('#tableAjax').DataTable({
        ajax: {
            type: "GET",
            url: "/hr-connect/dept-ga/perlengkapan-goodie-apd/getData",
        },
        columns: [
            { 
                data: "tanggal_masuk",
            },
            {
                data: "count",
                orderable: false
            },
            {
                data: "count",
                width: "15%",
                orderable: false,
                render: function(data, type, row){
                    return `<center>
                        <button class="btn btn-sm btn-primary btnConfirm" data-id="${row.id}" data-jumlah="${data}" data-tgl="${row.tanggal_masuk}">Konfirmasi</button>
                        </center>`;
                }
            }
        ],
        order: [[0, 'desc']]
    });
});

$(document).on("click", ".btnConfirm", function(){
    let jumlah = $(this).data("jumlah");
    let tgl_masuk = $(this).data("tgl");

    Swal.fire({
        title: "Notifikasi Konfirmasi",
        icon: "info",
        text: "Apakah sudah mempersiapkan Goodie Bag dan APD sebanyak " + jumlah + " unit ?",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonText: "Konfirmasi",
        cancelButtonText: "Batalkan"
    }).then((result) => {
        if(result.isConfirmed){
            $.ajax({
                type: "POST",
                url: "/hr-connect/dept-ga/perlengkapan-goodie-apd/updateData",
                data: {
                    confirm: 'Y',
                    tgl_masuk: tgl_masuk,
                    jumlah: jumlah
                },
                success: function(res){
                    // localStorage.removeItem('GoodieApd');
                    $("#tableAjax").DataTable().ajax.reload();
                    
                    Toastify({
                        text: res.msg,
                        duration: 3000,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: "linear-gradient(to right, #28a745, #218838)",
                    }).showToast();
                }
            })
        }
    });
});
</script>
@endpush