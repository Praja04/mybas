<div class="modal fade" id="modalDetail" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content bas-modal">

            {{-- Hidden ID untuk pegangan AJAX --}}
            <input type="hidden" id="detail_kode_rak">
            <input type="hidden" id="detail_no_loker">

            {{-- HEADER --}}
            <div class="modal-header bas-modal-header">
                <div>
                    <h5 class="bas-modal-title">
                        <i class="fas fa-th-large mr-2"></i>
                        Detail Loker <span id="detail_no_label"></span>
                    </h5>
                    <div class="bas-modal-sub">Informasi penghuni unit secara real-time</div>
                </div>

                <button type="button" class="bas-modal-close" data-dismiss="modal" data-toggle="tooltip"
                    title="Tutup Jendela">
                    <i class="ki ki-close"></i>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body bas-modal-body" style="overflow: hidden;">

                {{-- Gunakan wrapper bawaan bootstrap untuk table responsive --}}
                <div class="table-responsive">
                    <table class="bas-table" id="table_detail"
                        style="width: 100%; min-width: 800px; table-layout: auto;">
                        <thead>
                            <tr>
                                <th style="width: 100px;">NIK</th>
                                <th style="min-width: 200px;">Nama</th>
                                <th style="width: 150px;">Kategori</th>
                                <th style="width: 150px;">Divisi</th>
                                <th style="width: 150px;">Tgl Masuk</th>
                                <th class="text-right kolom-aksi" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detail_penghuni_list">
                            {{-- Data dari AJAX --}}
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer bas-modal-footer">
                @if (in_array('loker_operator', $permissions))
                    <div class="d-flex align-items-center">
                        <button type="button" id="btn_rusak" class="bas-btn bas-btn-outline-danger mr-2"
                            data-toggle="tooltip" title="Tandai unit sedang rusak">
                            <i class="fas fa-tools mr-2"></i> Tandai Rusak
                        </button>
                        <button type="button" id="btn_aktif" class="bas-btn bas-btn-primary mr-2"
                            style="display: none;" data-toggle="tooltip" title="Aktifkan kembali unit">
                            <i class="fas fa-check-circle mr-2"></i> Aktifkan
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
        /* Paksa table-responsive agar bisa scroll */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            /* Ini kunci supaya bisa di-slide samping */
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        /* Pastikan tabel tidak menciut lebih kecil dari 800px di layar HP */
        .bas-table {
            min-width: 800px !important;
            width: 100%;
            border-collapse: collapse;
        }

        /* Paksa tombol aksi tetap satu baris tanpa kepotong */
        .text-nowrap {
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        /* Hilangkan batasan dari container pembungkus jika ada */
        .bas-table-wrap {
            overflow: visible !important;
        }
    </style>
@endpush
