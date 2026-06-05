<div class="modal-content">
    <div class="modal-header p-4">
        <h5 class="modal-title fw-bold"><i class="ri-information-fill text-info me-2"></i> Ketentuan Upload
            Plotting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body p-4">
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <strong>Aturan Penulisan:</strong><br>
            1. NIK harus persis sesuai di database.<br>
            2. <u>Bila Prosesnya NO-IN, biarkan kolom Kode Admin & Kode Group KOSONG.</u>
        </div>
        <h6 class="fw-bold mb-3">Contoh Format Excel:</h6>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle text-center" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Dept</th>
                        <th>Kode Bagian</th>
                        <th>Proses</th>
                        <th>Kode Admin</th>
                        <th>Kode Group</th>
                        <th>Tgl Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Testing 1</td>
                        <td>123456789</td>
                        <td>PRO</td>
                        <td>PRN_02</td>
                        <td><span class="badge bg-success">IN</span></td>
                        <td>PAS_PRN_A</td>
                        <td>ENG_PRN_A</td>
                        <td>10/9/2024</td>
                    </tr>
                    <tr>
                        <td>Testing 2</td>
                        <td>132674758</td>
                        <td>PRO</td>
                        <td>PRN_02</td>
                        <td><span class="badge bg-danger">NO-IN</span></td>
                        <td class="text-muted bg-light">-</td>
                        <td class="text-muted bg-light">-</td>
                        <td>10/9/2024</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
