<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-hidden="true">
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
                            {{-- State Awal: Loading --}}
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <div class="spinner spinner-primary spinner-lg mr-15"></div>
                                    <span class="text-muted ml-5">Memuat data penghuni...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer bas-modal-footer">
                <div class="d-flex align-items-center">
                    {{-- Tombol aksi yang tampil bergantian sesuai status unit --}}
                    <button type="button" id="btn_rusak" class="bas-btn bas-btn-outline-danger mr-2"
                        onclick="updateStatusUnit('rusak')" data-toggle="tooltip" title="Tandai unit sedang rusak">
                        <i class="fas fa-tools mr-2"></i> Tandai Rusak
                    </button>
                    <button type="button" id="btn_aktif" class="bas-btn bas-btn-primary mr-2"
                        onclick="updateStatusUnit('aktif')" style="display: none;" data-toggle="tooltip"
                        title="Aktifkan kembali unit">
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
