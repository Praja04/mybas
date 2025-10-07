@extends('hr-connect.layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
<style>
.checkwish:disabled {
    cursor: not-allowed;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        @if(!$hrd_ir)
        <ul class="nav nav-pills nav-justified mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link waves-effect waves-light active" data-bs-toggle="tab" href="#floting" role="tab">
                    Ploting Kode Group
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link waves-effect waves-light" data-bs-toggle="tab" href="#okb" role="tab">
                    Karyawan Aktif
                </a>
            </li>  
        </ul>
        @endif
        <div class="col-lg-12">
        
            <div class="tab-content text-muted">
                {{-- Floting Kode --}}
                <div class="tab-pane {{ !$hrd_ir ? 'active' : '' }}" id="floting" role="tabpanel">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <button class="btn btn-sm btn-success" onClick="uploadExcelModal()">Upload Excel</button>
                            &nbsp;&nbsp;
                            <a href="/assets/media/hr_connect/Admin - Ploting Kode Group.xlsx" class="btn btn-sm btn-info">Template</a>&nbsp;&nbsp;
                            <button class="btn btn-sm btn-primary" onClick="ketentuanUploadPlotingModal()">Ketentuan Upload</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ploting Kode Group</h5>
                        </div>
                        <div class="card-body">
                            <table id="tableAjax" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Pilih</th>
                                        <th>Tgl Masuk</th>
                                        <th>NIK</th>
                                        <th>Nama</th>
                                        <th>Proses</th>
                                        <th>Kode Admin</th>
                                        <th>Kode Group</th>
                                        <th>Dept</th>
                                        <th>Kode Bagian</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary custom-toggle active mb-3" data-bs-toggle="button" id="btnSubmit">
                        <span class="icon-on"><i class="ri-alarm-line align-bottom me-1"></i> Mohon Tunggu</span>
                        <span class="icon-off">Submit</span>
                    </button>
                </div>

                {{-- Karyawan Aktif --}}
                <div class="tab-pane {{ $hrd_ir ? 'active' : '' }}" id="okb" role="tabpanel">
                    <button type="button" class="btn btn-success mb-2 rounded-pill" id="btnCart">
                        lihat cart
                        <span class="badge bg-warning text-dark ms-2" id="cart-count">0</span>
                    </button>
                    <div class="row">
                        <div class="col-lg-12 mb-3 mt-5">
                            <button class="btn btn-sm btn-success" onClick="uploadExcelCheckoutModal()">Upload Excel</button>
                            &nbsp;&nbsp;
                            <a href="/assets/media/hr_connect/Admin - Checkout.xlsx" class="btn btn-sm btn-info">Template</a>&nbsp;&nbsp;
                            <button class="btn btn-sm btn-primary" onClick="ketentuanUploadCheckoutModal()">Ketentuan Upload</button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Data Karyawan Aktif</h5>
                        </div>
                        <div class="card-body">
                            <table id="tableAjax2" class="table table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th scope="col" width="10%">
                                            Cart
                                        </th>
                                        <th width="20%">Nik</th>
                                        <th width="30%">Nama</th>
                                        <th>Divisi</th>
                                        <th>Kode Admin</th>
                                        <th>Kode Bagian</th>
                                        <th>Kode Group</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SeeProfile !-->
<div class="modal fade" id="seeProfile" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div id="p_name"></div>
            </div>
            <div class="modal-body">
                <div class="mt-1">
                    <div id="p_container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SeeCart !-->
<div class="modal modal-xl fade" id="seeCart" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                Cart List
            </div>
            <div class="modal-body text-center">
                <div class="mt-1">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="row mb-3">
                                <div class="col-lg-3">
                                    <select id="pilihAlasanKeluar" class="form-select">
                                        <option value="">Pilih Alasan Keluar</option>
                                        <option value="Resign">Resign</option>
                                        <option value="Habis Kontrak">Habis Kontrak</option>
                                        <option value="Kabur">Kabur</option>
                                        <option value="Cut Probation">Cut Probation</option>
                                        <option value="PHK">PHK</option>
                                        <option value="Cancel Join">Cancel Join</option>
                                        <option value="Pensiun">Pensiun</option>
                                        <option value="Pensiun Dini">Pensiun Dini</option>
                                        <option value="Dikualifikasi Mengundurkan Diri">Dikualifikasi Mengundurkan Diri</option>
                                        <option value="Cut Probation Lebih Awal">Cut Probation Lebih Awal</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <input type="date" class="form-control" id="pilihTanggalKeluar">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <table id="cart-table" class="table table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Nama</th>
                                                <th style="width: 20%;">NIK</th>
                                                <th style="width: 10%;">Dept</th>
                                                <th style="width: 10%;">Alasan Keluar</th>
                                                <th style="width: 10%;">Tanggal Keluar</th>
                                                <th style="width: 10%;">Batal</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-md btn-success mt-3 text-white rounded-pill" id="btnCheckout">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="modalData" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDataLabel">Upload Karyawan Masuk</h5>
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

<div class="modal fade" id="modalData2" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalData2Label">Upload Karyawan Keluar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mt-1">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="file" class="form-control" id="fileUploadCheckout" accept=".xlsx">
                            <button class="btn btn-primary mt-2" id="uploadCheckoutExcel">Upload</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ketentuanUploadPlotingModal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ketentuanUploadPlotingModalLabel">Ketentuan Upload Karyawan Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-1">
                    <div class="row">
                        <div class="col-lg-12">
                            <h5 style="font-weight: bold">Contoh Template</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Tgl Masuk</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Dept</th>
                                    <th>Kode Bagian</th>
                                    <th>Proses</th>
                                    <th>Kode Admin</th>
                                    <th>Kode Group</th>
                                </tr>
                                <tr>
                                    <td>10/9/2024</td>	
                                    <td>123456789</td>	
                                    <td>Testing 1</td>	
                                    <td>PRO</td>	
                                    <td>PRN_02</td>	
                                    <td>IN</td>	
                                    <td>PAS_PRN_A</td>	
                                    <td>ENG_PRN_A</td>
                                </tr>
                                <tr>
                                    <td>10/9/2024</td>	
                                    <td>132674758</td>	
                                    <td>Testing 2</td>	
                                    <td>PRO</td>	
                                    <td>PRN_02</td>	
                                    <td>NO-IN</td>	
                                    <td></td>	
                                    <td></td>
                                </tr>
                            </table>
                            <h5 style="font-weight: bold">Aturan Penulisan</h5>
                            <ol>
                                <li>NIK harus sesuai.</li>
                                <li><u>Bila Proses nya NO-IN silahkan kosongkan Kode Admin dan Kode Group</u></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ketentuanUploadCheckoutModal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ketentuanUploadCheckoutModalLabel">Ketentuan Upload Karyawan Keluar</h5>
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
                                    <th>Divisi</th>
                                    <th>Kode Admin</th>
                                    <th>Kode Bagian</th>
                                    <th>Kode Group</th>
                                    <th>Alasan Keluar</th>
                                    <th>Tanggal Keluar</th>
                                </tr>
                                <tr>
                                    <td>123456789</td>	
                                    <td>Testing 1</td>	
                                    <td>PRO</td>	
                                    <td>PAS_PRN_A</td>	
                                    <td>PRN_02</td>	
                                    <td>ENG_PRN_A</td>	
                                    <td>Habis Kontrak</td>
                                    <td>10/20/2024</td>
                                </tr>
                                <tr>
                                    <td>132674758</td>	
                                    <td>Testing 2</td>	
                                    <td>PRO</td>	
                                    <td>PAS_PRN_02</td>	
                                    <td>PRN_02</td>	
                                    <td>PRN_02_BNP17</td>	
                                    <td>Habis Kontrak</td>
                                    <td>10/20/2024</td>
                                </tr>
                            </table>
                            <h5 style="font-weight: bold">Aturan Penulisan</h5>
                            <ol>
                                <li>NIK harus sesuai.</li>
                                <li>Harus isi alasan keluar.</li>
                                <li><u>Tanggal keluar silahkan isi dengan format bulan/tanggal/tahun seperti: 10/09/2024</u></li>
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
<script src="{{ asset('assets/plugins/global/select2.full.min.js') }}"></script>
{{-- <script src="{{ asset('assets/velzon/js/pages/select2.init.js') }}"></script> --}}
<script>
var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];

$(document).ready(function() {
    $('#pilihAlasanKeluar').change(function() {
        var selectedAlasanKeluar = $(this).val();
        if (selectedAlasanKeluar !== "") {
            $('.alasanKeluar').val(selectedAlasanKeluar).change();
            updateCartAlasanKeluar(selectedAlasanKeluar);
        }
    });

    updateCartTable();

    $(document).on('change', '#pilihTanggalKeluar', function() {
        var selectedTanggalKeluar = $(this).val();
        if (selectedTanggalKeluar !== "") {
            $('.tglKeluar').val(selectedTanggalKeluar).change();
            updateCartTanggalKeluar(selectedTanggalKeluar);
        }
    });

    $(document).on("click","#uploadExcel", function(){
        let excelFile = $("#fileUpload")[0].files[0];
        let formData = new FormData();
        formData.append('excel_file', excelFile);

        $.ajax({
            type: "POST",
            url: "/hr-connect/dept-adm/data-karyawan/uploadExcelKaryawanMasuk",
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

    $(document).on("click","#uploadCheckoutExcel", function(){
        let excelFile = $("#fileUploadCheckout")[0].files[0];
        let formData = new FormData();
        formData.append('excel_file', excelFile);

        $.ajax({
            type: "POST",
            url: "/hr-connect/dept-adm/data-karyawan/uploadExcelKaryawanKeluar",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $("#modalData2").modal("hide");
                
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

    updateCartTable();
});

function uploadExcelModal(){
    $("#modalData").modal("show");
}

function uploadExcelCheckoutModal(){
    $("#modalData2").modal("show");
}

function ketentuanUploadPlotingModal(){
    $("#ketentuanUploadPlotingModal").modal("show");
}

function ketentuanUploadCheckoutModal(){
    $("#ketentuanUploadCheckoutModal").modal("show");
}

// Start Floting Kode Group 
$("#btnSubmit").hide();
// $("#btnSubmit2").hide();

let table = $("#tableAjax").dataTable({
    processing: false,
    serverSide: true,
   
    ajax: {
        type: "GET",
        url: "/hr-connect/dept-adm/data-karyawan/getDataFloting"
    },
        data: null,
    columns: [
        {
            render: function(data, type, row){
                return `
                <center>
                    <input type="checkbox" class="checkwish" data-check="${row.id}" disabled />
                </center>
                `;
            }
        },
        { data: 'tanggal_masuk' },
        { data: 'nik' },
        { data: 'nama' },
        {
            render: function(data, type, row){
                return `
                    <select class="form-select statusProses">
                        // Jangan Diubah Value nya
                        <option value="IN">In</option>
                        <option value="NO-IN">No-In</option>
                    </select>
                `;
            }
        },
        {
            render: function(data, type, row){
                return `
                    <select class="js-example-basic-single kodeAdmin">
                        <option value="">Pilih</option>
                        @foreach($pkw_admin as $admin)
                        <option value="{{ $admin->kode_admin }}">{{ $admin->kode_admin }}</option>
                        @endforeach
                    </select>
                `;
            }
        },
        {
            render: function(data, type, row){
                return `
                    <select class="js-example-basic-single kodeGroup">
                        <option value="">Pilih</option>
                        @foreach($pkw_group as $group)
                        <option value="{{ $group->kode_group }}">{{ $group->kode_group }}</option>
                        @endforeach
                    </select>
                `;
            }
        },
        { data: 'kode_divisi' },
        { data: 'kode_bagian' },
    ]
});

table.on('draw.dt', function() {
    $('.js-example-basic-single').select2();
});

$(document).on("change", ".statusProses", function(){
    let row = $(this).closest("tr");
    let statusProses = $(this).val();
    let checkwish = row.find(".checkwish");
    let kodeGroup = row.find('.kodeGroup');

    if(statusProses == "NO-IN"){
        checkwish.prop("checked", true);
        kodeGroup.prop("disabled", true);
        kodeGroup.empty().append('<option value="">Pilih</option>');
        
        $("#btnSubmit").show();
    }else if(statusProses == "IN"){
        kodeGroup.prop("disabled", false);
    }
});

$(document).on('change', '.checkwish', function(){
    $("#btnSubmit").show();
});

$(document).on('change', '.kodeGroup', function(){
    let row = $(this).closest('tr');
    let checkwish = row.find('.checkwish');
    let selectedValue = $(this).val();

    if (selectedValue) {
        checkwish.prop('checked', true);
        $("#btnSubmit").show();
    } else {
        checkwish.prop('checked', false);

        if ($(".checkwish:checked").length === 0) {
            $("#btnSubmit").hide();
        }
    }
});

// DONE
$("#btnSubmit").click(function(){
    let dataToSend = [];
    
    $("#tableAjax tbody input[type=checkbox]:checked").each(function(){
        let row = $(this).closest('tr');

        let idCheckwish = row.find('.checkwish').data("check");
        let kodeGroup = row.find('.kodeGroup').val();
        let kodeAdmin = row.find('.kodeAdmin').val();
        let p_in = row.find('.statusProses').val();

        if(p_in == "IN"){
            if(kodeGroup == ""){
                alert('Kode Group harus diisi!');
            }
        }
        dataToSend.push({
            idCheckwish: idCheckwish, 
            kodeGroup: kodeGroup, 
            kodeAdmin: kodeAdmin, 
            p_in: p_in
        });
    });

    $.ajax({
        url: "{{ url('/hr-connect/dept-adm/data-karyawan/setGroupCode') }}",
        type: "POST",
        data: {data: dataToSend},
        success: function(response){
            Toastify({
                text: "Berhasil memberikan fasilitas!",
                duration: 3000,
                gravity: "top",
                position: 'right',
                backgroundColor: "linear-gradient(to right, #28a745, #218838)",
            }).showToast();

            table.api().draw();
            table2.api().draw();
            $("#btnSubmit").hide();
        },
        error: function(xhr, status, error){
            console.error(xhr.responseText);
        }
    });
});
// End Floting Kode Group

// Start Karyawan Aktif
let table2 = $("#tableAjax2").dataTable({
    processing: false,
    serverSide: true,
    // paging: false,
    // dom: "<'row'<'col-sm-12 text-right'Bf>>\
    //         <'row'<'col-sm-12'tr>>\
    //         <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
    // buttons: [
    //     {
    //         extend: 'excel',
    //         text: 'Export to Excel',
    //         filename: 'Admin - Checkout',
    //         exportOptions: {
    //             columns: [1,2,3,4,5,6]
    //         }
    //     },
    // ], 
    ajax: {
        type: "GET",
        url: "/hr-connect/dept-adm/data-karyawan/getDataOkb"
    },
    columns: [
        {
            render: function(data, type, row){

                var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];

                var found = cartContainer.find(function(cart){
                    return cart.id == row.id;
                });

                // console.log(cartContainer, found);

                if (found) {
                    return `
                    <center>
                        <button class="btn btn-icon btn-success" onClick="removeFromCart('${row.id}')">
                            <i class="mdi mdi-cart cartId" data-cart="${row.id}"></i>
                        </button>
                    </center>
                    `;
                }

                return `
                <center>
                    <button class="btn btn-icon" onClick="addToCart('${row.id}', '${row.nik}', '${row.nama}', '${row.kode_bagian}','${row.kode_divisi}','${row.kode_bagian}','${row.kode_admin}')">
                        <i class="mdi mdi-cart cartId" data-cart="${row.id}"></i>
                    </button>
                </center>
                `;
            }
        },
        { data: 'nik' },
        { data: 'nama' },
        { data: 'kode_divisi' },
        { data: 'kode_admin' },
        { data: 'kode_bagian' },
        { data: 'kode_group' },
    ],
});

// DONE
$(document).on("click", "#btnCheckout", function(){
    let cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];

    if (cartContainer.length === 0) {
        Toastify({
            text: "Tidak ada karyawan di dalam cart!",
            duration: 3000,
            gravity: "top",
            position: 'right',
            backgroundColor: "linear-gradient(to right, #ff9999, #ff6666)",
        }).showToast();

        return;
    }

    if (!validateAlasanKeluar()) {
        Toastify({
            text: "Mohon pilih alasan keluar sebelum checkout!",
            duration: 3000,
            gravity: "top",
            position: 'right',
            backgroundColor: "linear-gradient(to right, #ff9999, #ff6666)",
        }).showToast();

        return;
    }

    if (!validateTglKeluar()) {
        Toastify({
            text: "Mohon pilih tanggal keluar sebelum checkout!",
            duration: 3000,
            gravity: "top",
            position: 'right',
            backgroundColor: "linear-gradient(to right, #ff9999, #ff6666)",
        }).showToast();

        return;
    }

    $.ajax({
        type: "POST",
        url: "/hr-connect/dept-adm/data-karyawan/checkout",
        data: {
            data: cartContainer,
        },
        success: function(response) {
            localStorage.removeItem("karyawan_aktif_cartContainer");
            Toastify({
                text: "Berhasil melakukan checkout!",
                duration: 3000,
                gravity: "top",
                position: 'right',
                backgroundColor: "linear-gradient(to right, #28a745, #218838)",
            }).showToast();

            // localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify([]));
            
            $('#cart-count').text(0);

            updateCartTable();
            
            $("#seeCart").modal("hide");
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

function validateAlasanKeluar() {
    let isValid = true;
    $("#cart-table tbody select.alasanKeluar").each(function(){
        if (!$(this).val()) {
            isValid = false;
            return false; 
        }
    });
    return isValid;
}

function validateTglKeluar() {
    let isValid = true;
    $("#cart-table tbody input.tglKeluar").each(function(){
        if (!$(this).val()) {
            isValid = false;
            return false; 
        }
    });
    return isValid;
}

// DONE
$("#btnCart").click(function(){
    $("#seeCart").modal("show");
});

$('#cart-count').text(cartContainer.length);

function updateReason(cartId, reason) {
    var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
    cartContainer.forEach(function(cart) {
        if (cart.id === cartId) {
            cart.alasan_keluar = reason;
        }
    });
    localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
    updateCartTable();
}

function updateTglKeluar(cartId, tglKeluar) {
    var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
    cartContainer.forEach(function(cart) {
        if (cart.id === cartId) {
            cart.tanggal_keluar = tglKeluar;
        }
    });
    localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
    updateCartTable();
}

function updateCartAlasanKeluar(alasan_keluar) {
    var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];

    cartContainer.forEach(function(cart) {
        cart.alasan_keluar = alasan_keluar;
    });

    localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
    updateCartTable();
}

function updateCartTable(data) {
    $('#cart-table tbody').empty();
    
    var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
    var cartData = data || cartContainer;

    cartData.forEach(function(cart) {
        $('#cart-table tbody').append(`
            <tr>
                <td>${cart.nama}</td>
                <td>${cart.nik}</td>
                <td>${cart.dept}</td>
                <td>
                    <select class="form-select alasanKeluar" data-cart-id="${cart.id}" onChange="updateReason('${cart.id}', this.value)">
                        <option value="">Pilih</option>
                        <option value="Resign" ${cart.alasan_keluar === 'Resign' ? 'selected' : ''}>Resign</option>
                        <option value="Habis Kontrak" ${cart.alasan_keluar === 'Habis Kontrak' ? 'selected' : ''}>Habis Kontrak</option>
                        <option value="Kabur" ${cart.alasan_keluar === 'Kabur' ? 'selected' : ''}>Kabur</option>
                        <option value="Cut Probation" ${cart.alasan_keluar === 'Cut Probation' ? 'selected' : ''}>Cut Probation</option>
                        <option value="PHK" ${cart.alasan_keluar === 'PHK' ? 'selected' : ''}>PHK</option>
                        <option value="Cancel Join" ${cart.alasan_keluar === 'Cancel Join' ? 'selected' : ''}>Cancel Join</option>
                        <option value="Pensiun" ${cart.alasan_keluar === 'Pensiun' ? 'selected' : ''}>Pensiun</option>
                        <option value="Pensiun Dini" ${cart.alasan_keluar === 'Pensiun Dini' ? 'selected' : ''}>Pensiun Dini</option>
                        <option value="Dikualifikasi Mengundurkan Diri" ${cart.alasan_keluar === 'Dikualifikasi Mengundurkan Diri' ? 'selected' : ''}>Dikualifikasi Mengundurkan Diri</option>
                        <option value="Cut Probation Lebih Awal" ${cart.alasan_keluar === 'Cut Probation Lebih Awal' ? 'selected' : ''}>Cut Probation Lebih Awal</option>
                    </select>
                </td>
                <td>
                    <input type="date" class="form-control tglKeluar" data-cart-id="${cart.id}" onChange="updateTglKeluar('${cart.id}', this.value)" value="${cart.tanggal_keluar || ''}">
                </td>
                <td>
                    <button class="btn btn-sm btn-danger removeCartId" onClick="removeFromCart('${cart.id}')">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    // Reload table without refresh pagination
    table2.api().draw(false);
}

function addToCart(id, nik, nama, dept, kode_divisi,kode_bagian,kode_admin) {
    var cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
    var foundIndex = cartContainer.findIndex(function(cart) {
        return cart.id === id;
    });

    if (foundIndex !== -1) {
        cartContainer[foundIndex] = {
            id: id,
            nik: nik,
            nama: nama,
            dept: dept,
            kode_divisi: kode_divisi,
            kode_bagian: kode_bagian,
            kode_admin: kode_admin,
            alasan_keluar: '',
            tanggal_keluar: ''
        };
    } else {
        cartContainer.push({
            id: id,
            nik: nik,
            nama: nama,
            dept: dept,
            kode_divisi: kode_divisi,
            kode_bagian: kode_bagian,
            kode_admin: kode_admin,
            alasan_keluar: '',
            tanggal_keluar: ''
        });
    }

    localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
    $('#cart-count').text(cartContainer.length);
    updateCartTable();
}

// DONE
function removeFromCart(id) {
    let cartContainer = JSON.parse(localStorage.getItem("karyawan_aktif_cartContainer")) || [];
    let found = cartContainer.findIndex(function(cart){
        return cart.id === id;
    });
    
    if (found !== -1) {
        cartContainer.splice(found, 1);
        localStorage.setItem("karyawan_aktif_cartContainer", JSON.stringify(cartContainer));
    }

    $('#cart-count').text(cartContainer.length);

    updateCartTable()
}
// End Karyawan Aktif
</script>
@endpush