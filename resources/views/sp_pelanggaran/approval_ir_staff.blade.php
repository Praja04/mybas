@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    .gradient-header { background: linear-gradient(135deg, #1e3c72, #2a5298); color: #ffffff; }
    .status-badge { font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .escalation-banner {
        border-left: 4px solid #17a2b8;
        background-color: #eef9fa;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-user-search-line me-2"></i> Review SP – IR Staff</h4>
        </div>
    </div>
</div>

{{-- Nav Pills Tab Filter (Pemisah Review SP vs Konfirmasi Cancel SP) --}}
<ul class="nav nav-pills mb-3 gap-2">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'approval' ? 'active bg-primary text-white' : 'bg-white text-dark border' }} fw-bold shadow-sm py-2 px-3"
           href="{{ route('sp_pelanggaran.approval', ['tab' => 'approval']) }}">
            <i class="ri-checkbox-circle-line me-1"></i> Persetujuan SP Baru
            <span class="badge {{ $activeTab === 'approval' ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill ms-1">
                {{ $countApproval }}
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'cancel' ? 'active bg-warning text-dark' : 'bg-white text-dark border' }} fw-bold shadow-sm py-2 px-3"
           href="{{ route('sp_pelanggaran.approval', ['tab' => 'cancel']) }}">
            <i class="ri-alert-line me-1"></i> Pengajuan Pembatalan (Cancel SP)
            <span class="badge {{ $activeTab === 'cancel' ? 'bg-dark text-white' : 'bg-warning text-dark' }} rounded-pill ms-1">
                {{ $countCancel }}
            </span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'all' ? 'active bg-secondary text-white' : 'bg-white text-dark border' }} fw-bold shadow-sm py-2 px-3"
           href="{{ route('sp_pelanggaran.approval', ['tab' => 'all']) }}">
            <i class="ri-file-list-3-line me-1"></i> Semua Pengajuan
            <span class="badge bg-secondary text-white rounded-pill ms-1">
                {{ $countApproval + $countCancel }}
            </span>
        </a>
    </li>
</ul>

<div class="card shadow-sm border-0">
    <div class="card-header {{ $activeTab === 'cancel' ? 'bg-warning text-dark' : 'gradient-header' }} py-3">
        <h5 class="card-title mb-0 {{ $activeTab === 'cancel' ? 'text-dark fw-bold' : 'text-white' }}">
            <i class="{{ $activeTab === 'cancel' ? 'ri-alert-line' : 'ri-list-check' }} me-2"></i> 
            @if($activeTab === 'cancel')
                Daftar Pengajuan Pembatalan SP (Cancel)
            @elseif($activeTab === 'approval')
                Daftar SP Menunggu Review & Penetapan Kode IR Staff
            @else
                Daftar Semua SP Menunggu Penanganan IR Staff
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th>No</th>
                        <th>Karyawan</th>
                        <th>NIK</th>
                        <th>Kode Admin</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spRecords as $index => $sp)
                    <tr>
                        <td>{{ ($spRecords->currentPage() - 1) * $spRecords->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $sp->employee->nama ?? '-' }}</strong></td>
                        <td>{{ $sp->employee->nik ?? '-' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $sp->kode_admin ?: 'Tanpa Kode Admin' }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($sp->tanggal_pelanggaran)->format('d M Y') }}</td>
                        <td>
                            @if($sp->current_status === 'CANCEL_PENDING_IR')
                                <span class="badge bg-warning text-dark status-badge"><i class="ri-alert-line me-1"></i>KONFIRMASI CANCEL SP</span>
                            @else
                                <span class="badge bg-info status-badge">PENDING IR STAFF</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info btnDetailSp me-1" data-id="{{ $sp->id }}">
                                <i class="ri-eye-line"></i> Detail
                            </button>
                            @if($sp->current_status === 'CANCEL_PENDING_IR')
                                <button class="btn btn-sm btn-warning btnApproveCancelSpIr me-1" data-id="{{ $sp->id }}">
                                    <i class="ri-check-double-line me-1"></i> Konfirmasi Cancel
                                </button>
                            @else
                                <button class="btn btn-sm btn-primary btnReviewIr me-1" 
                                        data-id="{{ $sp->id }}" 
                                        data-employee-id="{{ $sp->employee_id }}"
                                        data-nama="{{ $sp->employee->nama ?? '-' }}"
                                        data-nik="{{ $sp->employee->nik ?? '-' }}"
                                        data-kode-admin="{{ $sp->kode_admin }}"
                                        data-pasal="{{ $sp->pasal_dilanggar }}"
                                        data-alasan="{{ $sp->alasan }}"
                                        data-sumber="{{ $sp->sumber_data ?: 'PELANGGARAN' }}">
                                    <i class="ri-edit-box-line me-1"></i> Review & Tetapkan Kode IR
                                </button>
                                <button class="btn btn-sm btn-danger btnRejectIrStaff" data-id="{{ $sp->id }}">
                                    <i class="ri-close-line"></i> Reject
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            Tidak ada SP yang menunggu verifikasi IR Staff saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($spRecords->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $spRecords->links() }}
    </div>
    @endif
</div>

<!-- Modal Review & Penetapan Kode IR Staff -->
<div class="modal fade" id="modalReviewIr" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header gradient-header text-white">
                <h5 class="modal-title text-white">
                    <i class="ri-scales-line me-2"></i> Review & Penetapan Kode IR Staff
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formReviewIr">
                @csrf
                <input type="hidden" id="modal_sp_id">
                <div class="modal-body">
                    <!-- Employee & SP Info Card -->
                    <div class="card mb-3 border-0 bg-light">
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="small text-muted">Karyawan:</div>
                                    <div class="fw-bold" id="info_karyawan">-</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted">Kode Admin Input:</div>
                                    <div class="fw-bold text-primary" id="info_kode_admin">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active SP & Escalation Banner -->
                    <div class="p-3 mb-3 rounded escalation-banner" id="bannerActiveSp">
                        <div class="d-flex align-items-start">
                            <i class="ri-information-line ri-xl me-2 text-info mt-1" id="iconEscalation"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold" id="titleActiveSp">Pemeriksaan SP Aktif...</h6>
                                <small class="text-muted" id="descActiveSp">Memuat status SP aktif karyawan dari sistem...</small>
                                <!-- Riwayat SP 6 bulan terakhir -->
                                <div id="spHistorySection" class="mt-2" style="display:none">
                                    <div class="small fw-bold text-muted mb-1"><i class="ri-history-line me-1"></i> Riwayat SP 6 Bulan Terakhir:</div>
                                    <table class="table table-sm table-bordered mb-0" style="font-size:11px">
                                        <thead class="table-light">
                                            <tr><th>Tgl</th><th>Jenis</th><th>Sumber</th><th>Status</th><th>Nomor</th></tr>
                                        </thead>
                                        <tbody id="spHistoryRows"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kode Pelanggaran Select -->
                    <div class="mb-3">
                        <label for="select_kode_ir" class="form-label fw-bold text-primary">
                            <i class="ri-shield-keyhole-line me-1"></i> Kode Pelanggaran
                        </label>
                        <select id="select_kode_ir" class="form-select select2-modal">
                            <option value="">-- Pilih / Ketik Kode Pelanggaran --</option>
                        </select>
                        <div class="form-text small">Direkomendasikan otomatis oleh sistem sesuai Kode Admin & SP Aktif. Bebas diedit oleh IR Staff.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_jenis_pelanggaran" class="form-label fw-bold">Jenis Pelanggaran / Tingkat SP <span class="text-danger">*</span></label>
                                <select id="modal_jenis_pelanggaran" class="form-select" required>
                                    <option value="">-- Pilih Tingkat SP --</option>
                                    <option value="SP 1">Surat Peringatan 1 (SP 1 / SP I)</option>
                                    <option value="SP 2">Surat Peringatan 2 (SP 2 / SP II)</option>
                                    <option value="SP 3">Surat Peringatan 3 (SP 3 / SP III)</option>
                                    <option value="SP 3+">SP III+ / SP Berat</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="modal_pasal_dilanggar" class="form-label fw-bold">Pasal / Aturan yang Dilanggar</label>
                                <input type="text" id="modal_pasal_dilanggar" class="form-control" placeholder="Pasal / dasar pertimbangan PP...">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="modal_alasan" class="form-label fw-bold">Alasan / Bentuk Pelanggaran Resmi</label>
                        <textarea id="modal_alasan" class="form-control" rows="2" placeholder="Uraian atau kronologi pelanggaran..."></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="modal_notes" class="form-label fw-bold">Catatan Review IR Staff (Opsional)</label>
                        <textarea id="modal_notes" class="form-control" rows="2" placeholder="Catatan tambahan untuk IR Head..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSubmitModalIr">
                        <i class="ri-send-plane-fill me-1"></i> Teruskan ke IR Head
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail SP -->
<div class="modal fade" id="modalDetailSp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header gradient-header text-white">
                <h5 class="modal-title text-white">
                    <i class="ri-file-text-line me-2"></i> Detail Surat Peringatan (SP)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Karyawan:</td>
                                <td><strong id="detail_nama_karyawan">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIK:</td>
                                <td><code id="detail_nik_karyawan">-</code></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Divisi / Bagian:</td>
                                <td id="detail_dept_karyawan">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Pelanggaran:</td>
                                <td id="detail_tanggal_pelanggaran">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" width="40%">Nomor SP:</td>
                                <td><strong class="text-primary" id="detail_nomor_sp">-</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kode Admin:</td>
                                <td><span class="badge bg-light text-dark border" id="detail_kode_admin">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kode IR:</td>
                                <td><span class="badge bg-secondary" id="detail_kode_ir">-</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis / Tingkat SP:</td>
                                <td><span class="badge bg-danger" id="detail_jenis_sp">-</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="p-3 bg-light rounded mb-3">
                    <h6 class="fw-bold text-primary mb-1"><i class="ri-file-shield-line me-1"></i> Pasal / Aturan yang Dilanggar:</h6>
                    <p class="mb-2 text-dark" id="detail_pasal_dilanggar">-</p>

                    <h6 class="fw-bold text-primary mb-1"><i class="ri-align-left me-1"></i> Alasan / Bentuk Pelanggaran:</h6>
                    <p class="mb-0 text-dark" id="detail_alasan">-</p>
                </div>

                <div class="mb-3" id="container_lampiran">
                    <label class="fw-bold text-muted small d-block">File Bukti / Lampiran:</label>
                    <div id="detail_lampiran_link">Tidak ada lampiran</div>
                </div>

                <div class="mb-0">
                    <h6 class="fw-bold text-primary"><i class="ri-history-line me-1"></i> Riwayat Approval:</h6>
                    <div id="detail_approval_logs" class="small text-muted">Belum ada log approval.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    let modalDetail = new bootstrap.Modal(document.getElementById('modalDetailSp'));

    // Detail Action Handler
    $(document).on('click', '.btnDetailSp', function() {
        let id = $(this).data('id');
        $.get('/sp-pelanggaran/' + id + '/detail', function(res) {
            if (res.status === 'success') {
                let data = res.data;
                let emp = data.employee || {};

                $('#detail_nama_karyawan').text(emp.nama || '-');
                $('#detail_nik_karyawan').text(emp.nik || '-');
                $('#detail_dept_karyawan').text(emp.kode_divisi || emp.kode_bagian || '-');
                $('#detail_tanggal_pelanggaran').text(data.tanggal_pelanggaran || '-');

                $('#detail_nomor_sp').text(data.nomor_sp_generated || data.no_sp || 'DRAFT');
                $('#detail_kode_admin').text(data.kode_admin || '-');
                $('#detail_kode_ir').text(data.kode_ir || '-');
                $('#detail_jenis_sp').text(data.jenis_pelanggaran || 'Belum Ditetapkan');

                $('#detail_pasal_dilanggar').text(data.pasal_dilanggar || '-');
                $('#detail_alasan').text(data.alasan || '-');

                let lampiranPelanggaran = data.lampiran
                    ? '<a href="/' + data.lampiran + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ri-file-download-line me-1"></i> Bukti Pelanggaran (Saat Buat SP)</a>'
                    : '<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Tidak ada lampiran saat buat SP</span>';

                let lampiranCancel = data.lampiran_cancel
                    ? '<a href="/' + data.lampiran_cancel + '" target="_blank" class="btn btn-sm btn-outline-warning text-dark fw-bold"><i class="ri-file-download-line me-1"></i> Bukti Pembatalan (Saat Cancel SP)</a>'
                    : '<span class="text-muted small"><i class="ri-close-circle-line me-1"></i> Tidak ada lampiran pembatalan</span>';

                let lampiranHtml = `
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light">
                                <strong class="d-block small text-primary mb-1"><i class="ri-file-text-line me-1"></i> 1. Lampiran Bukti Pelanggaran:</strong>
                                ${lampiranPelanggaran}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-2 border rounded bg-light">
                                <strong class="d-block small text-dark mb-1"><i class="ri-file-warning-line me-1 text-warning"></i> 2. Lampiran Bukti Pembatalan:</strong>
                                ${lampiranCancel}
                            </div>
                        </div>
                    </div>
                `;
                $('#detail_lampiran_link').html(lampiranHtml);

                // Render Approval Logs
                let logs = data.approval_logs || [];
                if (logs.length > 0) {
                    let html = '<ul class="list-group list-group-flush border-top mt-1">';
                    $.each(logs, function(i, log) {
                        html += '<li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">';
                        html += '<div><strong>' + log.action + '</strong> - <small>' + (log.notes || '-') + '</small></div>';
                        html += '<span class="badge bg-light text-dark">' + log.created_at + '</span>';
                        html += '</li>';
                    });
                    html += '</ul>';
                    $('#detail_approval_logs').html(html);
                } else {
                    $('#detail_approval_logs').html('<span class="text-muted">Belum ada riwayat approval.</span>');
                }

                modalDetail.show();
            }
        });
    });
    let irKodesList = [];

    $('.select2-modal').select2({
        dropdownParent: $('#modalReviewIr'),
        theme: 'bootstrap-5'
    });

    $('.btnReviewIr').on('click', function() {
        let spId = $(this).data('id');
        let empId = $(this).data('employee-id');
        let nama = $(this).data('nama');
        let nik = $(this).data('nik');
        let kodeAdmin = $(this).data('kode-admin') || '-';
        let pasal = $(this).data('pasal') || '';
        let alasan = $(this).data('alasan') || '';
        let sumberData = $(this).data('sumber') || 'PELANGGARAN';

        $('#modal_sp_id').val(spId);
        $('#info_karyawan').text(nama + ' (' + nik + ')');
        $('#info_kode_admin').text(kodeAdmin + (sumberData === 'MANGKIR' ? ' [MANGKIR]' : ' [PELANGGARAN]'));
        $('#modal_pasal_dilanggar').val(pasal);
        $('#modal_alasan').val(alasan);
        $('#modal_notes').val('');
        $('#modal_jenis_pelanggaran').val('');
        $('#spHistorySection').hide();
        $('#spHistoryRows').html('');

        // Reset & loading active SP status
        $('#bannerActiveSp').removeClass('border-danger border-success border-warning').addClass('escalation-banner');
        $('#titleActiveSp').text('Memeriksa Masa SP Aktif Karyawan...');
        $('#descActiveSp').text('Sistem sedang mengecek riwayat SP aktif...');

        // Fetch active SP check
        $.get('/sp-pelanggaran/check-active/' + empId, function(res) {
            // Pilih kode list sesuai sumber data SP yang sedang direview
            let kodeList = sumberData === 'MANGKIR' ? (res.ir_kodes_mangkir || []) : (res.ir_kodes_pelanggaran || res.ir_kodes || []);
            irKodesList = kodeList;

            // Populate select_kode_ir
            let $select = $('#select_kode_ir');
            $select.empty().append('<option value="">-- Pilih / Ketik Kode Pelanggaran --</option>');
            $.each(kodeList, function(i, item) {
                $select.append(
                    $('<option></option>')
                        .val(item.kode)
                        .text(item.kode + ' [' + (item.jenis_sp || '-') + ']')
                        .data('jenis', item.jenis_sp)
                        .data('dasar', item.dasar_pertimbangan || item.pasal_dilanggar)
                        .data('bentuk', item.bentuk_pelanggaran || item.deskripsi)
                );
            });

            // Render riwayat SP karyawan 6 bulan terakhir
            let history = res.sp_history || [];
            if (history.length > 0) {
                let hRows = '';
                history.forEach(function(h) {
                    let badge = h.sumber_data === 'MANGKIR' ? '<span class="badge bg-primary" style="font-size:10px">MANGKIR</span>' : '<span class="badge bg-warning text-dark" style="font-size:10px">PELANGGARAN</span>';
                    let spBadge = h.jenis_pelanggaran ? '<span class="badge bg-danger" style="font-size:10px">' + h.jenis_pelanggaran + '</span>' : '<span class="badge bg-secondary" style="font-size:10px">Proses</span>';
                    
                    let dateDisplay = h.tanggal || '-';
                    if (h.all_dates && h.all_dates.length > 1) {
                        let dateList = h.all_dates.map(d => {
                            let parts = d.split('-');
                            return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : d;
                        }).join(', ');
                        dateDisplay = `<strong>${h.tanggal}</strong><br><small class="text-primary fw-bold"><i class="ri-calendar-event-line me-1"></i>+${h.all_dates.length - 1} tgl: ${dateList}</small>`;
                    }

                    hRows += '<tr><td style="vertical-align:middle">' + dateDisplay + '</td><td style="vertical-align:middle">' + spBadge + '</td><td style="vertical-align:middle">' + badge + '</td><td style="vertical-align:middle"><span class="badge bg-light text-dark border" style="font-size:10px">' + h.current_status + '</span></td><td style="vertical-align:middle">' + (h.nomor || '-') + '</td></tr>';
                });
                $('#spHistoryRows').html(hRows);
                $('#spHistorySection').show();
            }

            let matchedKode = null;

            if (res.is_active) {
                $('#bannerActiveSp').css({'border-left-color': '#dc3545', 'background-color': '#fff5f5'});
                let srcLabel = res.data.sumber_data === 'MANGKIR' ? '(dari SP Mangkir)' : '(dari SP Pelanggaran)';
                $('#titleActiveSp').html('<span class="text-danger"><i class="ri-error-warning-fill me-1"></i> SP Aktif Terdeteksi (' + res.data.jenis_pelanggaran + ') ' + srcLabel + '</span>');
                $('#descActiveSp').html('Karyawan sedang dalam masa SP Aktif (No: ' + res.data.no_sp + '). <strong>Rekomendasi Eskalasi: ' + res.data.next_level + '</strong>');

                // Pre-select escalation level
                let lvl = res.data.next_level;
                if (lvl === 'SP II') $('#modal_jenis_pelanggaran').val('SP 2');
                else if (lvl === 'SP III') $('#modal_jenis_pelanggaran').val('SP 3');
                else if (lvl === 'SP III+') $('#modal_jenis_pelanggaran').val('SP 3+');

                // Auto-match kode IR: prefix (misal "SP 1 +") + kode admin (misal "Mangkir 2") -> "SP 1 + Mangkir 2"
                if (res.data.prefix && kodeAdmin !== '-') {
                    let searchKode = res.data.prefix + ' ' + kodeAdmin;
                    matchedKode = kodeList.find(k => k.kode.toLowerCase() === searchKode.toLowerCase());
                    // Fallback: partial match
                    if (!matchedKode) {
                        matchedKode = kodeList.find(k => k.kode.toLowerCase().includes(kodeAdmin.toLowerCase()));
                    }
                }
            } else {
                $('#bannerActiveSp').css({'border-left-color': '#28a745', 'background-color': '#f4fbf7'});
                $('#titleActiveSp').html('<span class="text-success"><i class="ri-checkbox-circle-fill me-1"></i> Tidak Ada SP Aktif</span>');
                $('#descActiveSp').html('Karyawan bersih / tidak memiliki SP aktif saat ini. <strong>Rekomendasi Tingkat: SP I</strong>');
                $('#modal_jenis_pelanggaran').val('SP 1');

                // Langsung cari kode yang cocok
                if (kodeAdmin !== '-') {
                    matchedKode = kodeList.find(k => k.kode.toLowerCase() === kodeAdmin.toLowerCase());
                    if (!matchedKode) {
                        matchedKode = kodeList.find(k => k.kode.toLowerCase().includes(kodeAdmin.toLowerCase()));
                    }
                }
            }

            if (matchedKode) {
                $select.val(matchedKode.kode).trigger('change');
            }
        });

        $('#modalReviewIr').modal('show');
    });

    $('#select_kode_ir').on('change', function() {
        let $opt = $(this).find(':selected');
        if ($opt.val()) {
            let jenis = $opt.data('jenis');
            let dasar = $opt.data('dasar');
            let bentuk = $opt.data('bentuk');

            if (jenis) {
                if (jenis === 'SP I' || jenis === 'SP 1') $('#modal_jenis_pelanggaran').val('SP 1');
                else if (jenis === 'SP II' || jenis === 'SP 2') $('#modal_jenis_pelanggaran').val('SP 2');
                else if (jenis === 'SP III' || jenis === 'SP 3') $('#modal_jenis_pelanggaran').val('SP 3');
                else if (jenis === 'SP III+' || jenis === 'SP 3+') $('#modal_jenis_pelanggaran').val('SP 3+');
            }
            if (dasar) $('#modal_pasal_dilanggar').val(dasar);
            if (bentuk) $('#modal_alasan').val(bentuk);
        }
    });

    $('#formReviewIr').on('submit', function(e) {
        e.preventDefault();
        let spId = $('#modal_sp_id').val();
        let kodeIr = $('#select_kode_ir').val();
        let jenis = $('#modal_jenis_pelanggaran').val();
        let pasal = $('#modal_pasal_dilanggar').val();
        let alasan = $('#modal_alasan').val();
        let notes = $('#modal_notes').val();

        if (!jenis) {
            Swal.fire('Perhatian', 'Pilih Jenis Pelanggaran / Tingkat SP terlebih dahulu.', 'warning');
            return;
        }

        $('#btnSubmitModalIr').prop('disabled', true).html('<i class="ri-loader-4-line spinner me-1"></i> Memproses...');

        $.post('/sp-pelanggaran/' + spId + '/irstaff-submit', {
            _token: '{{ csrf_token() }}',
            kode_ir: kodeIr,
            jenis_pelanggaran: jenis,
            pasal_dilanggar: pasal,
            alasan: alasan,
            notes: notes
        }, function(res) {
            $('#modalReviewIr').modal('hide');
            Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
        }).fail(function(xhr) {
            let err = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
            Swal.fire('Gagal!', err, 'error');
            $('#btnSubmitModalIr').prop('disabled', false).html('<i class="ri-send-plane-fill me-1"></i> Teruskan ke IR Head');
        });
    });

    // Handler Reject IR Staff
    $(document).on('click', '.btnRejectIrStaff', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Tolak SP (Reject IR Staff)',
            text: 'SP yang ditolak akan diarsipkan dalam sistem dengan status DITOLAK (tidak dikembalikan ke Admin).',
            input: 'textarea',
            inputLabel: 'Catatan Penolakan (Wajib):',
            inputPlaceholder: 'Masukkan alasan penolakan...',
            inputValidator: (value) => {
                if (!value.trim()) return 'Catatan penolakan wajib diisi!';
            },
            showCancelButton: true,
            confirmButtonText: '<i class="ri-close-circle-line me-1"></i> Ya, Tolak SP',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let notes = result.value;
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/' + id + '/irstaff-reject', {
                    _token: '{{ csrf_token() }}',
                    notes: notes
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                });
            }
        });
    });

    // Handler Konfirmasi Cancel IR Staff
    $(document).on('click', '.btnApproveCancelSpIr', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Konfirmasi Pembatalan (Cancel) SP',
            text: 'Apakah Anda yakin ingin mengonfirmasi pembatalan SP ini dan meneruskannya ke IR Head?',
            input: 'textarea',
            inputLabel: 'Catatan Konfirmasi Cancel (Opsional):',
            inputPlaceholder: 'Masukkan catatan konfirmasi...',
            showCancelButton: true,
            confirmButtonText: '<i class="ri-check-double-line me-1"></i> Konfirmasi Pembatalan',
            confirmButtonColor: '#ffc107',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let notes = result.value || '';
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                $.post('/sp-pelanggaran/' + id + '/irstaff-approve-cancel', {
                    _token: '{{ csrf_token() }}',
                    notes: notes
                }, function(res) {
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                }).fail(function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                });
            }
        });
    });
});
</script>
@endpush
