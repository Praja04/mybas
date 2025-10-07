@extends('hr-connect.layouts.base')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
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
            <button class="btn btn-primary" onclick="tampilkanSemua()">Show All</button>
        </div>
        <div class="col-lg-6">
            <label>.</label><br>
            <button class="btn btn-success" onClick="uploadExcelModal()">Upload Excel</button>
            &nbsp;&nbsp;
            <a href="/assets/media/hr_connect/GA Shift In.xlsx" class="btn btn-info">Template</a>&nbsp;&nbsp;
            <button class="btn btn-primary" onClick="ketentuanUploadModal()">Ketentuan Upload</button>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Karyawan Masuk</h5>
                </div>
                <div class="card-body">
                    <table id="tableAjax" class="table table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Nama</th>
                                <th style="width: 20%;">NIK</th>
                                <th style="width: 10%;">Loker</th>
                                <th style="width: 10%;">ID Card</th>
                                <th style="width: 10%;">Dept</th>
                                <th style="width: 10%;">Tgl Join</th>
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
                                    <th>Kode Blok</th>
                                    <th>No Loker</th>
                                    <th>ID Card</th>
                                </tr>
                                <tr>
                                    <td>123456789</td>	
                                    <td>Testing 1</td>	
                                    <td>SL</td>	
                                    <td>12</td>	
                                    <td>1</td>	
                                </tr>
                            </table>
                            <h5 style="font-weight: bold">Aturan Penulisan</h5>
                            <ol>
                                <li>NIK harus sesuai.</li>
                                <li>Kode Blok harus sesuai.</li>
                                <li>No Loker harus sesuai.</li>
                                <li>Bila ID Card tidak 1 maka data tidak diproses (dinyatakan belum lengkap)</li>
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
{{-- <script src="/assets/velzon/js/pages/select2.init.js"></script> --}}
<script src="{{ asset('assets/velzon/libs/moment/moment.js') }}"></script>
<script>
$(document).ready(function(){
    let containerPria = {!!json_encode($lokers_pria->toArray()) !!};
    let lokers_pria = containerPria;

    let defaultTanggal = moment().format("YYYY-MM-DD");
    let showAll = 1;

    $("#tanggalFilter").val(defaultTanggal);

    $(document).on("change", "#tanggalFilter", function(){
        defaultTanggal = $(this).val();
        showAll = 0;
        $("#tableAjax").DataTable().draw();
    });
    
    window.tampilkanSemua = function() {
        showAll = 1;
        $("#tableAjax").DataTable().draw(); 
    };

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

    let table = $("#tableAjax").dataTable({
        processing: true,
        serverSide: true,
        paging: true,
        ordering: false,
        dom: "<'row'<'col-sm-12 text-right'Bf>>\
                <'row'<'col-sm-12'tr>>\
                <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>",
        buttons: [
            {
                extend: 'excel',
                text: 'Export to Excel',
                filename: 'karyawan masuk - GA',
                exportOptions: {
                    columns: [0, 1, 4, 5]
                }
            },
        ],
        ajax: {
            url: "{{ url('/hr-connect/dept-ga/karyawan-masuk/getData') }}",
            type: "GET",
            data: function(d){
                d.tanggal = defaultTanggal;
                d.tampilkan_semua = showAll;
            }
        },
        columns: [
            {data: 'nama'},
            {data: 'nik'},
            {
                render: function(data, type, row){
                    let lokers_wanita = {!! json_encode($lokers_wanita->toArray()) !!};

                    let selectBox = '<select class="js-example-basic-single lokerNo" data-loker="' + row.id + '">';
                    
                    let [selectedLokerPria] = lokers_pria;

                    lokers_wanita = lokers_wanita.sort(() => Math.random() - 0.5); 

                    if (row.jenis_kelamin === 'L') {
                        $.each(lokers_pria, function(index, loker) {
                            // console.log(loker);
                            // selectBox += '<option value="' + loker.id + '">' + loker.kode_blok + ' - ' + loker.no_loker + '</option>';                       
                            selectBox += `<option value="${loker.id}" data-kode-area="${loker.kode_area}" data-nik="${row.nik}" data-nama="${row.nama}" data-jk="L" data-divisi="${row.kode_divisi}" data-bagian="${row.kode_bagian}" data-group="${row.kode_group}" data-kodekontrak="${row.kode_kontrak}">${loker.kode_blok} -  ${loker.no_loker}</option>`;
                        });
                    } else {
                        $.each(lokers_wanita, function(index, loker) {
                            // selectBox += '<option value="' + loker.id + '">' + loker.kode_blok + ' - ' + loker.no_loker + '</option>';                       
                            selectBox += `<option value="${loker.id}" data-kode-area="${loker.kode_area}" data-nik="${row.nik}" data-nama="${row.nama}" data-jk="P" data-divisi="${row.kode_divisi}" data-bagian="${row.kode_bagian}" data-group="${row.kode_group}" data-kodekontrak="${row.kode_kontrak}">${loker.kode_blok} - ${loker.no_loker}</option>`;
                        });
                    }

                    lokers_pria = lokers_pria.filter(function (__item) {
                        return __item.id != selectedLokerPria.id;
                    });

                    lokers_wanita = lokers_wanita.filter(function (__item) {
                        return __item.id != lokers_wanita.id;
                    });

                    selectBox += '</select>';
                    
                    return selectBox;
                }
            },
            {
                render: function(data, type, row){
                return `
                <center>
                    <input type="checkbox" class="check_id_card" value="${row.id}" data-nik="${row.nik}">
                </center>`;
                }
            },
            {data: 'kode_bagian'},
            {data: 'tanggal_masuk'},
        ],
    });

    table.on('draw.dt', function() {
        $('.js-example-basic-single').select2();
    });
    
    $("#btnSubmit").hide();
    
    $(document).on('change', '.check_id_card', function(){
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
    
    $("#btnSubmit").click(function(){
        let dataToSend = [];

        $("#tableAjax tbody input[type=checkbox]:checked").each(function(){
            let row = $(this).closest('tr');
            let idCard = $(this).val(); 
            let lokerId = row.find('.lokerNo').val();
            let lokerText = row.find('.lokerNo option:selected').text(); 
            let kodeArea = row.find('.lokerNo option:selected').data('kode-area');
            let nik = row.find('.lokerNo option:selected').data('nik');
            let nama = row.find('.lokerNo option:selected').data('nama');
            let jk = row.find('.lokerNo option:selected').data('jk');
            let divisi = row.find('.lokerNo option:selected').data('divisi');
            let bagian = row.find('.lokerNo option:selected').data('bagian');
            let group = row.find('.lokerNo option:selected').data('group');
            let kodekontrak = row.find('.lokerNo option:selected').data('kodekontrak');
            let [namaLoker, nomorLoker] = lokerText.split(" - ");

            dataToSend.push({
                lokerId: lokerId, 
                idCard: idCard, 
                namaLoker: namaLoker, 
                nomorLoker: nomorLoker, 
                kodeArea: kodeArea,
                nik: nik,
                nama: nama,
                jk: jk,
                divisi: divisi,
                bagian: bagian,
                group: group,
                kodekontrak: kodekontrak,
            });
        });

        let lokerIds = [];
        let duplicateFound = false;

        $.each(dataToSend, function(index, data) {
            if (lokerIds.includes(data.lokerId)) {
                duplicateFound = true;
                return false; 
            }
            lokerIds.push(data.lokerId);
        });

        if (duplicateFound) {
            Swal.fire({
                title: "Error",
                icon: "error",
                text: "Anda memilih loker yang sama."
            });

            $("#btnSubmit").hide();
        } else {
            $.ajax({
                url: "{{ url('/hr-connect/dept-ga/karyawan-masuk/updateStatus') }}",
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

                    lokers_pria = containerPria
                    table.api().draw();

                    $("#btnSubmit").hide();
                },
                error: function(xhr, status, error){
                    console.error(xhr.responseText);
                }
            });
        }
    });

    $(document).on("click","#uploadExcel", function(){
        let excelFile = $("#fileUpload")[0].files[0];
        let formData = new FormData();
        formData.append('excel_file', excelFile);

        $.ajax({
            type: "POST",
            url: "/hr-connect/dept-ga/karyawan-masuk/uploadExcel",
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