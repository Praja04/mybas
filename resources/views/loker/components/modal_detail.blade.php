<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content bas-modal">

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
            <div class="modal-body bas-modal-body">
                <div class="bas-table-wrap">
                    <table class="bas-table">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Divisi</th>
                                <th>Tgl Masuk</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detail_penghuni_list">
                            {{-- AJAX CONTENT --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer bas-modal-footer">
                <div class="d-flex align-items-center">
                    <button type="button" id="btn_rusak" class="bas-btn bas-btn-outline mr-2" data-toggle="tooltip"
                        title="Ubah status menjadi Rusak (Unit tidak akan bisa di-plotting)">
                        <i class="fas fa-tools mr-2"></i> Tandai Rusak
                    </button>
                    <button type="button" id="btn_aktif" class="bas-btn bas-btn-primary" data-toggle="tooltip"
                        title="Aktifkan kembali unit untuk bisa digunakan">
                        <i class="fas fa-check-circle mr-2"></i> Aktifkan
                    </button>
                </div>

                <button type="button" class="bas-btn bas-btn-outline" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
