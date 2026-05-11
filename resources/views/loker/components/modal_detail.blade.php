<div class="modal fade" id="modalDetail" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content bas-modal">

            <input type="hidden" id="detail_kode_rak">
            <input type="hidden" id="detail_no_loker">

            <div class="modal-header bas-modal-header">
                <div>
                    <h5 class="bas-modal-title">
                        <i class="fas fa-th-large mr-2"></i>
                        Detail Loker <span id="detail_no_label"></span>
                    </h5>
                    <div class="bas-modal-sub">Data terkini pemegang hak guna fasilitas loker</div>
                </div>

                <button type="button" class="bas-modal-close" data-dismiss="modal" data-toggle="tooltip"
                    title="Tutup Jendela">
                    <i class="ki ki-close"></i>
                </button>
            </div>

            <div class="modal-body bas-modal-body" style="overflow: hidden;">

                <div class="table-responsive">
                    <table class="bas-table" id="table_detail"
                        style="width: 100%; min-width: 800px; table-layout: auto;">
                        <thead>
                            <tr>
                                <th style="width: 100px;">NIK</th>
                                <th style="min-width: 200px;">Nama</th>
                                <th style="width: 150px;">Kategori</th>
                                <th style="width: 150px;">Divisi</th>
                                <th style="width: 150px;">Tgl. Penempatan</th>
                                <th class="text-right kolom-aksi" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detail_penghuni_list">
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer bas-modal-footer">
                @if (in_array('loker_operator', $permissions))
                <div class="d-flex align-items-center">
                    <button type="button" id="btn_rusak" class="bas-btn bas-btn-outline-danger mr-2"
                        data-toggle="tooltip" title="Tandai loker ini dalam masa pemeliharaan">
                        <i class="fas fa-wrench mr-2"></i> Laporkan Pemeliharaan
                    </button>
                    <button type="button" id="btn_aktif" class="bas-btn bas-btn-primary mr-2" style="display: none;"
                        data-toggle="tooltip" title="Aktifkan kembali loker untuk dialokasikan">
                        <i class="fas fa-check-circle mr-2"></i> Selesai Pemeliharaan
                    </button>
                </div>
                @endif

                <button type="button" class="bas-btn bas-btn-outline" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<style>
    .table-responsive {
        display: block;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 1rem;
    }

    .bas-table {
        min-width: 800px !important;
        width: 100%;
        border-collapse: collapse;
    }

    .text-nowrap {
        white-space: nowrap !important;
        vertical-align: middle !important;
    }

    .bas-table-wrap {
        overflow: visible !important;
    }
</style>
@endpush