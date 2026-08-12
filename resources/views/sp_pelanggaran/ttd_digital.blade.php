@extends('sp_pelanggaran.layouts.base')

@push('styles')
<style>
    :root {
        --primary: #1e3c72;
        --primary-light: #2a5298;
        --accent: #10b981;
        --danger: #ef4444;
        --bg-card: #ffffff;
    }

    .gradient-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: #fff;
    }

    /* ── Upload Zone ─────────────────────────────────────────── */
    .upload-zone {
        border: 2.5px dashed #94a3b8;
        border-radius: 14px;
        background: #f8fafc;
        text-align: center;
        padding: 40px 20px;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
    }
    .upload-zone:hover, .upload-zone.drag-over {
        border-color: var(--accent);
        background: #f0fdf4;
        box-shadow: 0 0 0 4px rgba(16,185,129,0.12);
    }
    .upload-zone input[type="file"] {
        position: absolute; inset: 0; opacity: 0; cursor: pointer;
    }
    .upload-zone-icon { font-size: 3rem; color: #94a3b8; }
    .upload-zone.drag-over .upload-zone-icon { color: var(--accent); }

    /* ── Signature Preview Box ────────────────────────────────── */
    .ttd-preview-box {
        background: repeating-conic-gradient(#f1f5f9 0% 25%, #ffffff 0% 50%) 0 0 / 20px 20px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        min-height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px;
        overflow: hidden;
    }
    .ttd-preview-box img {
        max-height: 120px;
        max-width: 100%;
        object-fit: contain;
        filter: drop-shadow(0 1px 3px rgba(0,0,0,0.12));
    }

    /* ── Status Badges ────────────────────────────────────────── */
    .badge-role { font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .badge-ir-head  { background: #fef3c7; color: #92400e; }
    .badge-ir-staff { background: #dbeafe; color: #1e40af; }
    .badge-depthead { background: #d1fae5; color: #065f46; }
    .badge-active   { background: #dcfce7; color: #166534; }
    .badge-inactive { background: #fee2e2; color: #991b1b; }

    /* ── Tips Card ────────────────────────────────────────────── */
    .tips-card {
        background: linear-gradient(135deg, #fffbeb, #fef9c3);
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 14px 18px;
    }

    /* ── Existing TTD Card ────────────────────────────────────── */
    .ttd-card-current {
        border-left: 4px solid var(--accent);
        background: linear-gradient(135deg, #f0fdf4, #ffffff);
    }

    /* ── Live Preview Overlay ─────────────────────────────────── */
    #livePreviewImg { transition: opacity 0.3s ease; }
</style>
@endpush

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 text-primary"><i class="ri-quill-pen-fill me-2"></i>TTD Digital SP</h4>
            <small class="text-muted">Tanda tangan disimpan sekali, digunakan otomatis di semua Surat Peringatan</small>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION A: TTD Milik Saya (User yang Login)
═══════════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-4">
    {{-- Card Kiri: TTD saat ini --}}
    <div class="col-md-5">
        <div class="card shadow-sm border-0 h-100 ttd-card-current">
            <div class="card-header gradient-header py-3 rounded-top">
                <h5 class="mb-0 text-white"><i class="ri-shield-check-line me-2"></i>TTD Saya Saat Ini</h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                @if($mySignature && $mySignature->signature_path)
                    <div class="ttd-preview-box w-100 mb-3" style="min-height:160px">
                        <img src="{{ $mySignature->signature_url }}" alt="TTD Saya" id="currentTtdImg">
                    </div>
                    <div class="fw-bold mb-1">{{ Auth::user()->name ?? '-' }}</div>
                    <div class="text-muted small mb-2">{{ $mySignature->nama_jabatan }}</div>
                    <span class="badge-role badge-active mb-3">
                        <i class="ri-checkbox-circle-line me-1"></i>TTD Aktif
                    </span>
                    <small class="text-muted">Upload terakhir: {{ $mySignature->uploaded_at ? $mySignature->uploaded_at->translatedFormat('d M Y H:i') : '-' }}</small>
                    <button class="btn btn-sm btn-outline-danger mt-3" id="btnHapusTtd" data-id="{{ $mySignature->id }}">
                        <i class="ri-delete-bin-line me-1"></i>Hapus TTD
                    </button>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="ri-quill-pen-line" style="font-size:3rem; opacity:.3"></i>
                        <div class="mt-2 fw-bold">Belum Ada TTD</div>
                        <small>Upload tanda tangan Anda di form sebelah →</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card Kanan: Form Upload --}}
    <div class="col-md-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header gradient-header py-3 rounded-top">
                <h5 class="mb-0 text-white">
                    <i class="ri-upload-cloud-2-line me-2"></i>
                    {{ $mySignature ? 'Perbarui TTD Digital' : 'Upload TTD Digital' }}
                </h5>
            </div>
            <div class="card-body">
                {{-- Tips --}}
                <div class="tips-card mb-4">
                    <div class="fw-bold mb-1"><i class="ri-lightbulb-flash-line me-1 text-warning"></i>Tips untuk Hasil Terbaik</div>
                    <ul class="mb-0 small text-muted ps-3">
                        <li>Gunakan format <strong>PNG dengan background transparan</strong> agar TTD terlihat natural di atas kertas SP</li>
                        <li>Ukuran file maks <strong>2 MB</strong> (PNG / JPG)</li>
                        <li>Tanda tangan sebaiknya <strong>bersih, tidak blur</strong></li>
                        <li>TTD lama akan otomatis terganti saat upload baru</li>
                    </ul>
                </div>

                <form id="formUploadTtd" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="signature_base64" id="signatureBase64">

                    {{-- Nama Jabatan --}}
                    <div class="mb-3">
                        <label for="nama_jabatan" class="form-label fw-bold">
                            <i class="ri-id-card-line me-1"></i>Nama Jabatan di PDF
                        </label>
                        <input type="text" id="nama_jabatan" name="nama_jabatan" class="form-control"
                               placeholder="Contoh: IR &amp; ER Dept. Head / Manager Produksi"
                               value="{{ $mySignature ? $mySignature->nama_jabatan : '' }}">
                        <div class="form-text small">Teks ini tampil di bawah TTD pada dokumen SP</div>
                    </div>

                    {{-- Mode Selection: Canvas vs Upload --}}
                    <ul class="nav nav-tabs mb-3" id="ttdModeTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold" id="canvas-tab" data-bs-toggle="tab" data-bs-target="#canvasTabContent" type="button" role="tab">
                                <i class="ri-edit-2-line me-1"></i> TTD Langsung (Canvas Pad)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="upload-tab" data-bs-toggle="tab" data-bs-target="#uploadTabContent" type="button" role="tab">
                                <i class="ri-upload-cloud-2-line me-1"></i> Upload File Gambar (PNG/JPG)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mb-3" id="ttdTabContent">
                        {{-- TAB 1: CANVAS PAD --}}
                        <div class="tab-pane fade show active" id="canvasTabContent" role="tabpanel">
                            <div class="p-2 border rounded bg-white text-center mb-2" style="background: repeating-conic-gradient(#f8fafc 0% 25%, #ffffff 0% 50%) 0 0 / 20px 20px;">
                                <canvas id="signatureCanvas" width="460" height="180" style="border: 2px dashed #94a3b8; border-radius: 8px; cursor: crosshair; background: transparent; touch-action: none; max-width: 100%;"></canvas>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><i class="ri-information-line me-1"></i>Gunakan Mouse / Layar Sentuh HP untuk TTD</small>
                                <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2" id="btnClearCanvas">
                                    <i class="ri-eraser-line me-1"></i> Bersihkan Canvas
                                </button>
                            </div>
                        </div>

                        {{-- TAB 2: UPLOAD FILE --}}
                        <div class="tab-pane fade" id="uploadTabContent" role="tabpanel">
                            <div class="upload-zone" id="uploadZone">
                                <input type="file" name="signature_image" id="signatureInput" accept=".png,.jpg,.jpeg">
                                <div id="uploadPlaceholder">
                                    <div class="upload-zone-icon"><i class="ri-drag-drop-line"></i></div>
                                    <div class="fw-bold mt-2">Drag & Drop atau Klik untuk Pilih File</div>
                                    <small class="text-muted">PNG / JPG — Maks 2MB</small>
                                </div>
                                {{-- Live Preview --}}
                                <div id="livePreviewWrap" style="display:none">
                                    <div class="ttd-preview-box mx-auto" style="max-width:320px;min-height:120px">
                                        <img id="livePreviewImg" src="#" alt="Preview TTD" style="max-height:100px;max-width:100%;">
                                    </div>
                                    <div class="small text-success mt-2" id="livePreviewName"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success px-4" id="btnSimpanTtd">
                            <i class="ri-save-3-line me-1"></i>Simpan TTD
                        </button>
                        <button type="button" class="btn btn-light" id="btnResetForm">
                            <i class="ri-refresh-line me-1"></i>Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     SECTION B: Semua TTD (IR Head / Admin)
═══════════════════════════════════════════════════════════ --}}
@if($isIrHead || $isAdmin)
<div class="card shadow-sm border-0 mt-2">
    <div class="card-header gradient-header py-3">
        <h5 class="mb-0 text-white"><i class="ri-team-line me-2"></i>Semua TTD Terdaftar di Sistem</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Role SP</th>
                        <th>Preview TTD</th>
                        <th>Status</th>
                        <th>Upload Terakhir</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allSignatures as $sig)
                    <tr>
                        <td><strong>{{ $sig->user->name ?? '-' }}</strong></td>
                        <td>{{ $sig->nama_jabatan ?: '-' }}</td>
                        <td>
                            @if($sig->role === 'ir_head')
                                <span class="badge-role badge-ir-head">IR Head</span>
                            @elseif($sig->role === 'ir_staff')
                                <span class="badge-role badge-ir-staff">IR Staff</span>
                            @else
                                <span class="badge-role badge-depthead">Dept Head</span>
                            @endif
                        </td>
                        <td>
                            @if($sig->signature_url)
                                <div class="ttd-preview-box" style="max-width:140px; min-height:60px; padding:6px">
                                    <img src="{{ $sig->signature_url }}" style="max-height:50px; max-width:130px;">
                                </div>
                            @else
                                <span class="text-muted small">Tidak ada gambar</span>
                            @endif
                        </td>
                        <td>
                            @if($sig->is_active)
                                <span class="badge-role badge-active"><i class="ri-checkbox-circle-line me-1"></i>Aktif</span>
                            @else
                                <span class="badge-role badge-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $sig->uploaded_at ? $sig->uploaded_at->translatedFormat('d M Y H:i') : '-' }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-danger btnHapusTtd" data-id="{{ $sig->id }}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada TTD yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Toast Notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="toastTtd" class="toast align-items-center border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toastTtdMsg">—</div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // ── HTML5 Canvas Signature Pad Handler ──────────────────────
    const canvas  = document.getElementById('signatureCanvas');
    const ctx     = canvas ? canvas.getContext('2d') : null;
    let isDrawing = false;
    let hasDrawn  = false;

    if (canvas && ctx) {
        ctx.strokeStyle = '#0f172a'; // Deep slate / black ink
        ctx.lineWidth   = 3;
        ctx.lineCap     = 'round';
        ctx.lineJoin    = 'round';

        function getCanvasPos(e) {
            const rect    = canvas.getBoundingClientRect();
            const clientX = e.clientX || (e.touches && e.touches[0].clientX);
            const clientY = e.clientY || (e.touches && e.touches[0].clientY);
            return {
                x: (clientX - rect.left) * (canvas.width / rect.width),
                y: (clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function startDraw(e) {
            isDrawing = true;
            hasDrawn  = true;
            const pos = getCanvasPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
            if (e.type === 'touchstart') e.preventDefault();
        }

        function doDraw(e) {
            if (!isDrawing) return;
            const pos = getCanvasPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
            if (e.type === 'touchmove') e.preventDefault();
        }

        function stopDraw() {
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', doDraw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);

        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', doDraw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        $('#btnClearCanvas').on('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
            $('#signatureBase64').val('');
        });
    }

    // ── Drag & Drop Handler ─────────────────────────────────────
    const zone      = document.getElementById('uploadZone');
    const input     = document.getElementById('signatureInput');
    const preview   = document.getElementById('livePreviewWrap');
    const prevImg   = document.getElementById('livePreviewImg');
    const prevName  = document.getElementById('livePreviewName');
    const placeholder = document.getElementById('uploadPlaceholder');

    ['dragenter', 'dragover'].forEach(ev => {
        if (zone) zone.addEventListener(ev, e => { e.preventDefault(); zone.classList.add('drag-over'); });
    });
    ['dragleave', 'drop'].forEach(ev => {
        if (zone) zone.addEventListener(ev, e => { e.preventDefault(); zone.classList.remove('drag-over'); });
    });
    if (zone) {
        zone.addEventListener('drop', e => {
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                showPreview(e.dataTransfer.files[0]);
            }
        });
    }

    if (input) {
        input.addEventListener('change', () => {
            if (input.files.length) showPreview(input.files[0]);
        });
    }

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = ev => {
            prevImg.src = ev.target.result;
            prevName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            placeholder.style.display = 'none';
            preview.style.display     = 'block';
        };
        reader.readAsDataURL(file);
    }

    // ── Reset Form ──────────────────────────────────────────────
    $('#btnResetForm').on('click', function() {
        $('#formUploadTtd')[0].reset();
        if (preview) preview.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
        if (prevImg) prevImg.src = '#';
        if (ctx) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
        }
        $('#signatureBase64').val('');
    });

    // ── Submit Upload / Canvas ──────────────────────────────────
    $('#formUploadTtd').on('submit', function(e) {
        e.preventDefault();

        const activeTab = $('#ttdModeTab button.active').attr('id');
        if (activeTab === 'canvas-tab') {
            if (!hasDrawn) {
                showToast('Silakan gambar tanda tangan Anda pada canvas terlebih dahulu.', 'danger');
                return false;
            }
            $('#signatureBase64').val(canvas.toDataURL('image/png'));
            $('#signatureInput').val(''); // clear file
        } else {
            $('#signatureBase64').val(''); // clear base64
            if (!input.files || !input.files.length) {
                showToast('Silakan pilih file gambar TTD Anda (PNG/JPG).', 'danger');
                return false;
            }
        }

        const fd  = new FormData(this);
        const btn = $('#btnSimpanTtd');
        btn.prop('disabled', true).html('<i class="ri-loader-4-line me-1 spinner"></i>Menyimpan...');

        $.ajax({
            url: '/ttd-digital',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function(res) {
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'Gagal menyimpan TTD.';
                showToast(msg, 'danger');
                btn.prop('disabled', false).html('<i class="ri-save-3-line me-1"></i>Simpan TTD');
            }
        });
    });

    // ── Hapus TTD ───────────────────────────────────────────────
    $(document).on('click', '.btnHapusTtd, #btnHapusTtd', function() {
        const id = $(this).data('id');
        if (!confirm('Yakin ingin menghapus TTD ini? Tanda tangan tidak akan muncul di PDF SP sampai di-upload ulang.')) return;

        $.ajax({
            url: '/ttd-digital/' + id,
            method: 'DELETE',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                showToast(res.message, 'success');
                setTimeout(() => location.reload(), 1200);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Gagal menghapus TTD.', 'danger');
            }
        });
    });

    // ── Toast Helper ────────────────────────────────────────────
    function showToast(msg, type) {
        const toast = document.getElementById('toastTtd');
        const msgEl = document.getElementById('toastTtdMsg');
        toast.className = 'toast align-items-center border-0 shadow text-bg-' + type;
        msgEl.textContent = msg;
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
    }
});
</script>
@endpush
