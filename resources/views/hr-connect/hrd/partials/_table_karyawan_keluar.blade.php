<div class="card shadow-sm border-0">
    <div class="card-header border-bottom p-4">
        <div class="row align-items-center gy-3">
            <div class="col-xl-4 col-lg-12">
                <h5 class="card-title mb-0" style="font-weight: 600;">
                    <i class="ri-folder-user-line text-success me-2"></i> Finalisasi Offboarding HRD
                </h5>
            </div>
            <div
                class="col-xl-8 col-lg-12 d-flex justify-content-xl-end justify-content-start align-items-center gap-2 flex-wrap">

                <a href="/assets/media/hr_connect/HRD IR - Karyawan Keluar.xlsx"
                    class="btn btn-sm btn-soft-info fw-bold shadow-sm">
                    <i class="ri-download-line align-bottom me-1"></i> Template
                </a>

                <button class="btn btn-sm btn-soft-secondary fw-bold shadow-sm" onClick="ketentuanUploadModal()">
                    <i class="ri-information-line align-bottom me-1"></i> Info
                </button>

                <button class="btn btn-sm btn-success fw-bold shadow-sm" onClick="uploadExcelModal()">
                    <i class="ri-file-excel-2-line align-bottom me-1"></i> Upload Excel
                </button>

                <div class="vr align-middle d-none d-sm-block"></div>

                <button class="btn btn-sm btn-warning font-weight-bolder shadow-sm" id="btnToggleMassal"
                    data-bs-toggle="tooltip" title="Aktifkan mode finalisasi massal">
                    <i class="ri-checkbox-multiple-line align-bottom me-1"></i> Pilih Massal
                </button>

                <button class="btn btn-sm btn-dark font-weight-bolder shadow-sm" id="btnSubmit" style="display: none;">
                    <i class="ri-check-double-line align-bottom me-1"></i> Eksekusi (<span id="countChecked">0</span>)
                </button>

            </div>
        </div>
    </div>
    <div class="card-body pb-4">
        <div class="table-responsive">
            <table id="tableAjax" class="table table-bordered table-hover align-middle table-custom-header"
                style="width:100%">
                <thead class="table-light text-muted">
                    <tr>
                        <th style="width: 20%;">Nama Lengkap</th>
                        <th style="width: 5%;">NIK</th>
                        <th style="width: 5%;">Kode Bagian</th>
                        <th style="width: 10%">Kode Group</th>
                        <th style="width: 15%">Tgl Keluar</th>
                        <th>Alasan Keluar</th>
                        <th style="width: 15%; text-align: center;" id="headerTindakan">Tindakan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
