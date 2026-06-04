<div class="modal-content">
    <div class="modal-header p-4">
        <h5 class="modal-title fw-bold"><i class="ri-shopping-cart-2-fill text-dark me-2"></i> Daftar Karyawan
            Keluar (Checkout)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body p-4 text-center bg-light">
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card shadow-none border mb-4">
                    <div class="card-body bg-white rounded">
                        <p class="text-muted text-start mb-2 fw-bold"
                            style="font-size: 0.8rem; text-transform: uppercase;">Setel Massal (Terapkan ke
                            semua di keranjang):</p>
                        <div class="row align-items-center">
                            <div class="col-lg-4">
                                <select id="pilihAlasanKeluar" class="form-select shadow-sm">
                                    <option value="" selected disabled>-- Pilih Alasan Keluar Massal --

                                        @foreach ($alasanKeluar as $alasan)
                                    <option value="{{ $alasan->nama_reason }}">
                                        {{ $alasan->nama_reason }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <input type="date" class="form-control shadow-sm" id="pilihTanggalKeluar">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive bg-white border rounded shadow-none">
                    <table id="cart-table" class="table table-hover align-middle mb-0" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Nama</th>
                                <th style="width: 15%;">NIK</th>
                                <th style="width: 10%;">Dept</th>
                                <th style="width: 25%;">Alasan Keluar</th>
                                <th style="width: 15%;">Tanggal Keluar</th>
                                <th style="width: 10%;" class="text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer p-3 bg-white d-flex justify-content-between">
        <span class="text-muted"><i class="ri-information-line me-1"></i> Pastikan semua alasan dan tanggal
            telah terisi.</span>
        <button class="btn btn-success fw-bold px-4 shadow-sm" id="btnCheckout">
            <i class="ri-check-double-line align-bottom me-1"></i> Proses Checkout
        </button>
    </div>
</div>
