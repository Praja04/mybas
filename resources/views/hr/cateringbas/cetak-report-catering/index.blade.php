        @extends('layouts.base')

        @push('styles')
            <style>
                @media print {

                    label {
                        display: none;
                    }

                    #export-pdf-button {
                        visibility: hidden !important;
                    }

                    #table-wrapper,
                    #table-wrapper * {
                        display: block !important;
                    }

                    #table-wrapper {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                    }

                    .bg-orange-500,
                    .bg-orange-500 th,
                    .bg-orange-500 td,
                    .bg-orange-500 tfoot th {
                        background-color: #F8CBAC !important;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }

                    .table,
                    .table th,
                    .table td {
                        visibility: visible;
                    }
                }

                .html2pdf__page-break {
                page-break-before: always;
            }
            </style>
        @endpush

        @section('content')
            <div class="container-fluid">
                <!-- begin::Row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-custom">
                            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                                <div class="card-title">
                                    <h3 class="card-label">Data <span style="color: red">Report Pesanan</span>
                                        <span class="d-block text-muted pt-2 font-size-sm">export data Pesanan catering</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="/cateringbas/export-excel-report-mingguan" method="POST">
                                    @csrf
                                    <div class="form-group col-lg-6">
                                        <label for="tanggal_awal">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal">
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label for="tanggal_akhir">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir">
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <button type="button" id="submit-button" class="btn btn-primary">Cari Data</button>
                                        {{-- <button type="submit"  class="btn btn-primary">Export Data Excel</button> --}}
                                    </div>
                                </form>
                                <div id="table-container" class="d-none">
                                    {{-- append data tanggal sesuai dengan pencarian --}}
                                    <div id="table-wrapper" class="collapse">
                                        <div class="row">
                                            <div class="col-lg-6 d-flex justify-content-end">
                                                <button type="button" id="export-pdf-button" class="btn btn-success" style="display:none;">
                                                    <i class="fas fa-file-pdf"></i> Export Data PDF
                                                </button>
                                            </div>
                                            <div class="col-lg-6 d-flex justify-content-start">
                                                <button style="display: none;" type="button" id="export-excel-button" class="btn btn-primary">
                                                    <i class="fas fa-file-excel"></i> Export Excel
                                                </button>
                                            </div>
                                        </div>                                        
                                        <label>Table Report - Kategori Staff</label>
                                        <table class="table table-bordered">
                                            <thead class="bg-orange-500">
                                                <tr>
                                                    <th rowspan="2" class="text-center"
                                                        style="background-color: #F8CBAC; vertical-align: middle; text-align: center;">
                                                        Tanggal</th>
                                                    <th rowspan="2" class="text-center"
                                                        style="background-color: #F8CBAC; vertical-align: middle; text-align: center;">
                                                        Kategori</th>
                                                    <th colspan="3" class="text-center"
                                                        style="background-color: #F8CBAC;">SHIFT</th>
                                                    <th rowspan="2" class="text-center"
                                                        style="background-color: #F8CBAC; vertical-align: middle; text-align: center;">
                                                        Jumlah</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center" style="background-color: #F8CBAC;">1</th>
                                                    <th class="text-center" style="background-color: #F8CBAC;">2</th>
                                                    <th class="text-center" style="background-color: #F8CBAC;">3</th>
                                                </tr>
                                            </thead>
                                            <tbody id="staff-table">
                                                <!-- Data untuk kategori "staff" akan ditambahkan di sini -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-center"
                                                        style="background-color: #F8CBAC;">Total</th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="shift1-total"></th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="shift2-total"></th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="shift3-total"></th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="grand-total"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <!-- Tabel untuk kategori "non-staff" -->
                                    <div id="table-wrapper-nonstaff" class="collapse">
                                        <label>Table Report - Kategori Non-Staff</label>
                                        <table class="table table-bordered">
                                            <thead class="bg-orange-500">
                                                <tr>
                                                    <th rowspan="2" class="text-center"
                                                        style="background-color: #F8CBAC; vertical-align: middle; text-align: center;">
                                                        Tanggal</th>
                                                    <th rowspan="2" class="text-center"
                                                        style="background-color: #F8CBAC; vertical-align: middle; text-align: center;">
                                                        Kategori</th>
                                                    <th colspan="3" class="text-center"
                                                        style="background-color: #F8CBAC;">SHIFT</th>
                                                    <th rowspan="2" class="text-center"
                                                        style="background-color: #F8CBAC; vertical-align: middle; text-align: center;">
                                                        Jumlah</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center" style="background-color: #F8CBAC;">1</th>
                                                    <th class="text-center" style="background-color: #F8CBAC;">2</th>
                                                    <th class="text-center" style="background-color: #F8CBAC;">3</th>
                                                </tr>
                                            </thead>
                                            <tbody id="non-staff-table">
                                                <!-- Data untuk kategori "non-staff" akan ditambahkan di sini -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-center"
                                                        style="background-color: #F8CBAC;">Total</th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="shift1-total-nonstaff"></th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="shift2-total-nonstaff"></th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="shift3-total-nonstaff"></th>
                                                    <th class="text-center" style="background-color: #F8CBAC;"
                                                        id="grand-total-nonstaff"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @push('scripts')
            <script src="{{ url('/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
            <script src="{{ url('/assets/plugins/global/date-eu.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

            <script type="text/javascript">
                document.addEventListener('DOMContentLoaded', function() {
                    const tanggalAwalInput = document.getElementById('tanggal_awal');
                    const tanggalAkhirInput = document.getElementById('tanggal_akhir');
                    const submitButton = document.getElementById('submit-button');
                    const exportPdfButton = document.getElementById('export-pdf-button');
                    const exportExcelButton = document.getElementById('export-excel-button');
                    const tableContainer = document.getElementById('table-container');
                    const staffTable = document.getElementById('staff-table');
                    const nonStaffTable = document.getElementById('non-staff-table');

                    submitButton.addEventListener('click', function() {
                        const tanggalAwal = tanggalAwalInput.value;
                        const tanggalAkhir = tanggalAkhirInput.value;

                        if (tanggalAwal && tanggalAkhir) {
                            const startDate = new Date(tanggalAwal);
                            const endDate = new Date(tanggalAkhir);
                            const timeDifference = Math.abs(endDate - startDate);
                            // dimatikan dulu tidak usah pakai validasi rentang waktu
                            // const differenceInDays = Math.ceil(timeDifference / (1000 * 3600 * 24));

                            // if (differenceInDays > 7) {
                            //     alert('Lebih dari 1 minggu.');
                            //     return;
                            // }

                            fetchDataAndPopulateTables(tanggalAwal, tanggalAkhir);
                        } else {
                            alert('Silakan pilih rentang tanggal');
                        }
                    });

                    function fetchDataAndPopulateTables(tanggalAwal, tanggalAkhir) {
                        $.ajax({
                            url: '/cateringbas/data-ecafesedaap-mingguan',
                            type: 'GET',
                            data: {
                                tanggal_awal: tanggalAwal,
                                tanggal_akhir: tanggalAkhir
                            },
                            success: function(response) {
                                console.log(response);
                                renderTables(response);
                                exportPdfButton.style.display = 'block';
                                exportExcelButton.style.display = 'block';
                            },
                            error: function() {
                                alert('Error fetching data');
                            }
                        });
                    }

                    function renderTables(data) {
                        let staffTableData = '';
                        let nonStaffTableData = '';
                        let shift1TotalStaff = 0;
                        let shift2TotalStaff = 0;
                        let shift3TotalStaff = 0;
                        let grandTotalStaff = 0;
                        let shift1TotalNonStaff = 0;
                        let shift2TotalNonStaff = 0;
                        let shift3TotalNonStaff = 0;
                        let grandTotalNonStaff = 0;

                        data.forEach(row => {
                            const totalSum = parseInt(row.shift1_sum, 10) + parseInt(row.shift2_sum, 10) + parseInt(
                                row.shift3_sum, 10);
                            const tableRow = `
                                <tr>
                                    <td class="border text-center">${row.tanggal}</td>
                                    <td class="border text-center">${row.kategori}</td>
                                    <td class="border text-center">${row.shift1_sum}</td>
                                    <td class="border text-center">${row.shift2_sum}</td>
                                    <td class="border text-center">${row.shift3_sum}</td>
                                    <td class="border text-center">${totalSum}</td>
                                </tr>`;

                            if (row.kategori === 'staff') {
                                staffTableData += tableRow;
                                shift1TotalStaff += parseInt(row.shift1_sum, 10);
                                shift2TotalStaff += parseInt(row.shift2_sum, 10);
                                shift3TotalStaff += parseInt(row.shift3_sum, 10);
                                grandTotalStaff += totalSum;
                            } else if (row.kategori === 'non-staff') {
                                nonStaffTableData += tableRow;
                                shift1TotalNonStaff += parseInt(row.shift1_sum, 10);
                                shift2TotalNonStaff += parseInt(row.shift2_sum, 10);
                                shift3TotalNonStaff += parseInt(row.shift3_sum, 10);
                                grandTotalNonStaff += totalSum;
                            }
                        });

                        staffTable.innerHTML = staffTableData;
                        nonStaffTable.innerHTML = nonStaffTableData;

                        if (tableContainer.classList.contains('d-none')) {
                            tableContainer.classList.remove('d-none');
                        }

                        if (nonStaffTableData) {
                            document.getElementById('table-wrapper-nonstaff').classList.remove('collapse');
                        }

                        document.getElementById('shift1-total').textContent = shift1TotalStaff;
                        document.getElementById('shift2-total').textContent = shift2TotalStaff;
                        document.getElementById('shift3-total').textContent = shift3TotalStaff;
                        document.getElementById('grand-total').textContent = grandTotalStaff;

                        document.getElementById('shift1-total-nonstaff').textContent = shift1TotalNonStaff;
                        document.getElementById('shift2-total-nonstaff').textContent = shift2TotalNonStaff;
                        document.getElementById('shift3-total-nonstaff').textContent = shift3TotalNonStaff;
                        document.getElementById('grand-total-nonstaff').textContent = grandTotalNonStaff;

                        $('#table-wrapper').collapse('show');
                    }

                    exportPdfButton.addEventListener('click', function() {
                        printTable();
                    });

                    function printTable() {
                    const staffTable = document.getElementById('table-wrapper');
                    const nonStaffTable = document.getElementById('table-wrapper-nonstaff');

                    if (staffTable.innerHTML.trim() === '' && nonStaffTable.innerHTML.trim() === '') {
                        alert('Silahkan cari tanggal dulu.');
                        exportPdfButton.style.display = 'block';
                        return;
                    }

                    exportPdfButton.style.display = 'none';

                    const elementToExport = document.createElement('div');
                    elementToExport.appendChild(staffTable.cloneNode(true));
                    
                    const pageBreak = document.createElement('div');
                    pageBreak.className = 'html2pdf__page-break';
                    elementToExport.appendChild(pageBreak);
                    
                    elementToExport.appendChild(nonStaffTable.cloneNode(true));
                    
                    const opt = {
                        margin: [10, 10, 10, 10],
                        filename: 'report-mingguan.pdf',
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2,
                            useCORS: true
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        },
                        pagebreak: {
                            mode: 'css',
                            before: '.html2pdf__page-break' 
                        }
                    };

                    // Gunakan html2pdf untuk mengekspor tabel
                    html2pdf().from(elementToExport).set(opt).toPdf().get('pdf').then(function(pdf) {
                        exportPdfButton.style.display = 'block'; 
                    }).save();
                }
                });

                const exportExcelButton = document.getElementById('export-excel-button');
                exportExcelButton.addEventListener('click', function() {
                const staffTable = document.getElementById('table-wrapper');
                const nonStaffTable = document.getElementById('table-wrapper-nonstaff');

                if (staffTable.innerHTML.trim() === '' && nonStaffTable.innerHTML.trim() === '') {
                    alert('Silahkan cari tanggal dulu.');
                    return;
                }

                function tableToSheet(table) {
                    const ws = XLSX.utils.table_to_sheet(table);

                    const range = XLSX.utils.decode_range(ws['!ref']);
                    for (let C = range.s.c; C <= range.e.c; ++C) {
                        const cellAddress = { r: 0, c: C };
                        const cellRef = XLSX.utils.encode_cell(cellAddress);

                        if (!ws[cellRef]) continue;                        
                        ws[cellRef].s = {
                            alignment: {
                                horizontal: 'center',
                            },
                            fill: {
                                bgColor: { indexed: 64, rgb: 'F8CBAC' }, 
                            }
                        };
                    }

                    return ws;
                }

                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, tableToSheet(staffTable), 'Staff');
                XLSX.utils.book_append_sheet(wb, tableToSheet(nonStaffTable), 'Non-Staff');
                XLSX.writeFile(wb, 'pesanan-.xlsx');
            });
            </script>
        @endpush
