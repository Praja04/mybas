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
        <div class="col-lg-2">
            <label>Tanggal:</label>
            <input type="date" class="form-control mb-3" id="tanggalFilter">
        </div>
        <div class="col-lg-2">
            <label>.</label><br>
            <button class="btn btn-primary" onClick="tampilkanSemua()">Show All</button>
        </div>
        <div class="col-6">
            <label>.</label><br>
            <button class="btn btn-success" onClick="uploadExcelModal()">Upload Excel</button>
            &nbsp;&nbsp;
            <a href="/assets/media/hr_connect/HRD IR - Karyawan Keluar.xlsx" class="btn btn-info">Template</a>&nbsp;&nbsp;
            <button class="btn btn-primary" onClick="ketentuanUploadModal()">Ketentuan Upload</button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Karyawan Keluar</h5>
                </div>
                <div class="card-body">
                    <table id="tableAjax" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th data-ordering="false" style="width: 5%;">
                                    <center>
                                        <input type="checkbox" id="checkAll" />
                                    </center>
                                </th>
                                <th data-ordering="false" style="width: 20%;">NIK</th>
                                <th data-ordering="false" style="width: 30%;">Nama</th>
                                <th data-ordering="false">Kode Jabatan</th>
                                <th data-ordering="false">Kode Bagian</th>
                                <th data-ordering="false">Kode Group</th>
                                <th data-ordering="false">Tgl Keluar</th>
                                <th data-ordering="false">Alasan Keluar</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <button type="button" class="btn btn-secondary custom-toggle active mb-3" data-bs-toggle="button" id="btnSubmit">Submit</button>
        </div>
    </div>
</div>

<div class="modal fade" id="modalData" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDataLabel">HRD IR Upload Karyawan Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mt-1">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="file" class="form-control" id="fileUpload" accept=".xlsx">
                            <button class="btn btn-primary mt-2" id="uploadExcel">Upload</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKetentuanUpload" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalKetentuanUploadLabel">Ketentuan Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-1">
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 style="font-weight: bold">Contoh Template</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Dept</th>
                                    <th>Alasan Keluar</th>
                                    <th>Tgl Keluar</th>
                                    <th>Status</th>
                                </tr>
                                <tr>
                                    <td>123456789</td>	
                                    <td>Testing 1</td>	
                                    <td>PRN_02</td>	
                                    <td>Habis Kontrak</td>	
                                    <td>10/20/2024</td>	
                                    <td>1</td>	
                                </tr>
                                <tr>
                                    <td>132674758</td>	
                                    <td>Testing 2</td>	
                                    <td>PRN_02</td>	
                                    <td>Habis Kontrak</td>	
                                    <td>10/20/2024</td>	
                                    <td>0</td>	
                                </tr>
                            </table>
                            <h5 style="font-weight: bold">Aturan Penulisan</h5>
                            <ol>
                                <li>NIK harus sesuai.</li>
                                <li>Bila Status tidak 1 maka data tidak diproses.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>
<script>
$(document).ready(function(){
    $("#btnSubmit").hide();

    let defaultTanggal = moment().format("YYYY-MM-DD");
    // let defaultTanggal = "semua";
    let showAll = 1;

    $("#tanggalFilter").val("");

    $(document).on("change", "#tanggalFilter", function(){
        defaultTanggal = $(this).val();
        showAll = 0;
        $("#tableAjax").DataTable().draw();
    });

    window.tampilkanSemua = function() {
        showAll = 1;
        $("#tableAjax").DataTable().draw(); 
    };

    $(document).on('change', '#checkAll', function() {
        let isChecked = $(this).is(':checked');
        $("#tableAjax tbody input[type=checkbox].checklist").prop('checked', isChecked);
        $("#btnSubmit").show();
    });
    
    function copyToClipboard(text) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("Copy");
        textArea.remove();
        
        Toastify({
            text: "NIK telah disalin: " + text,
            duration: 3000,
            gravity: "top",
            position: 'right',
            backgroundColor: "linear-gradient(to right, #28a745, #218838)",
        }).showToast();
    }

    $(document).on('change', '.checklist', function(){
        $("#btnSubmit").show();

        // Jika udah di ceklis ya copy nik setelah - 
        if (this.checked) {
            let nik = $(this).data('nik');

            if (typeof nik === 'string' || typeof nik === 'number') {
                nik = String(nik);
                var splited = nik.split('-'); 

                var nik_copied = splited.length > 1 ? splited[1] : splited[0];

                if (nik_copied) {
                    copyToClipboard(nik_copied);
                }
            } else {
                console.error("NIK bukan string atau angka:", nik);
            }
        }
    });

    let table = $("#tableAjax").dataTable({
        processing: false,
        serverSide: true,
        paging: false,
        ordering: false,
        dom: "<'row'<'col-sm-12 text-right'Bf>>\
            <'row'<'col-sm-12'tr>>\
            <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
        buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                filename: 'HRD IR - Karyawan Keluar',
                exportOptions: {
                    columns: [1,2,3,4,5,6,7]
                }
            },
        ],
        ajax: {
            type: "GET",
            url: "/hr-connect/dept-hrd/karyawan-keluar/getData",
            data: function(d){
                d.tanggal = defaultTanggal;
                d.tampilkan_semua = showAll;
            }
        },
        columns: [
            {
                render: function(data, type, row){
                    return `
                    <center>
                        <input type="checkbox" class="checklist" data-nik="${row.nik}" value="${row.id}">
                    </center>
                    `;
                }
            },
            { data: 'nik' },
            { data: 'nama' },
            { data: 'kode_jabatan' },
            { data: 'kode_bagian' },
            { data: 'kode_group' },
            { data: 'tanggal_keluar' },
            { data: 'alasan_keluar' },
        ]
    });
    
    $(document).on("change",".checklist", function(){
        $("#btnSubmit").show();
        let isChecked = $(this).prop('checked');
    });

    $(document).on("click","#btnSubmit", function(){
        let dataToSend = [];

        $("#tableAjax tbody input[type=checkbox].checklist:checked").each(function(){
            let checklistId = $(this).val(); 
            let status = 'check';

            dataToSend.push({checklistId: checklistId, status: status});
        });
        
        $.ajax({
            type: "POST",
            url: "/hr-connect/dept-hrd/karyawan-keluar/update",
            data: {
                data: dataToSend
            },
            success: function(response){
                Toastify({
                    text: "Berhasil memperbarui data karyawan keluar!",
                    duration: 3000,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: "linear-gradient(to right, #28a745, #218838)",
                }).showToast();

                table.api().draw();

                $("#checkAll").prop('checked', false);

                $("#btnSubmit").hide();
            },
            error: function(xhr, status, error){
                console.error(xhr.responseText);
            }
        })
    });

    $(document).on("click","#uploadExcel", function(){
        let excelFile = $("#fileUpload")[0].files[0];
        let formData = new FormData();
        formData.append('excel_file', excelFile);

        $.ajax({
            type: "POST",
            url: "/hr-connect/dept-hrd/karyawan-keluar/uploadExcel",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $("#modalData").modal("hide");
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                    timer: 2000, 
                    showConfirmButton: false
                }).then(() => {
                    setTimeout(function() {
                        location.reload(); 
                    }, 2000); 
                });
            },
            error: function(xhr) {
                let message = xhr.responseJSON.message || 'Terjadi kesalahan saat mengunggah file.';
                alert(message);
            }
        });
    });
});

function uploadExcelModal(){
    $("#modalData").modal("show");
}

function ketentuanUploadModal(){
    $("#modalKetentuanUpload").modal("show");
}
</script>
@endpush