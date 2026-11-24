@extends('layouts.base')

@section('content')
<style>
    .ts-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); padding: 1.25rem; margin-bottom: 1.25rem; }
    .ts-card h5 { color: #a80000; font-weight: 700; }
    .ts-table th { background: #fdecea; color: #a80000; font-size: .8rem; vertical-align: middle; white-space: nowrap; }
    .ts-table td { font-size: .82rem; vertical-align: middle; }
    .ts-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: .72rem; font-weight: 600; }
    .ts-num { font-weight: 700; font-variant-numeric: tabular-nums; }
    .ts-final { font-size: .95rem; font-weight: 800; color: #a80000; }
    .ts-pa { display: inline-block; min-width: 30px; padding: 3px 10px; border-radius: 8px; font-size: .9rem; font-weight: 800; }
    .ts-pa-A { background: #e8f5e9; color: #1b5e20; }
    .ts-pa-B { background: #e3f2fd; color: #0d47a1; }
    .ts-pa-C { background: #fff3e0; color: #e65100; }
    .ts-pa-D { background: #fdecea; color: #a80000; }
    .ts-pa-E { background: #eceff1; color: #455a64; }
    .ts-pa-outline { box-shadow: inset 0 0 0 2px rgba(0,0,0,.10); }
    .ts-mute { color: #9e9e9e; }
    .ts-sum-label { font-weight: 700; }
    .ts-sum-row-converted td { background: #d9e1f2; }
    .ts-sum-row-rank td { background: #e7f0fb; }
    .ts-sum-row-final td { background: #fff3cd; }
    .ts-sum-row-kurva td { background: #fff2cc; }
    .ts-sum-row-persen td { background: #fce4d6; }
    .ts-table th.ts-grp-empty { background: #000; border: none; }
    .ts-table th.ts-grp-result { background: #000; color: #fff; text-align: center; font-weight: 700; font-size: .8rem; letter-spacing: .02em; }
    .ts-table th.ts-grp-rating { background: #a80000; color: #fff; text-align: center; font-weight: 700; font-size: .8rem; letter-spacing: .02em; }
    .ts-table th.ts-sub-left { background: #000; color: #fff; text-align: center; }
    .ts-table th.ts-sub-result { background: #FFC000; color: #000; text-align: center; }
    .ts-table th.ts-sub-rating { background: #f8cecc; color: #000; text-align: center; }
    .ts-table th.ts-sortable { cursor: pointer; user-select: none; }
    .ts-table th.ts-sortable:hover { background: #f3b8b6; }
    .ts-table th.ts-sortable .ts-sort-arrow { display: inline-block; margin-left: .35rem; font-size: .72rem; color: #6c757d; opacity: .55; }
    .ts-table th.ts-sortable.is-sorted { background: #f2b3b1; }
    .ts-table th.ts-sortable.is-sorted .ts-sort-arrow { opacity: 1; color: #212529; }
    .ts-table tbody tr.ts-row > td { transition: background-color .12s ease; }
    .ts-table tbody tr.ts-row:hover > td { background-color: #f2f2f2; }
    .ts-guide-step { display: inline-flex; width: 22px; height: 22px; border-radius: 50%; background: #a80000; color: #fff; align-items: center; justify-content: center; font-size: .75rem; font-weight: 700; }
    .ts-guide-note { font-size: .78rem; color: #757575; }
    #tsSummaryNote { padding: .5rem .75rem; border-radius: 8px; border-left: 4px solid transparent; font-weight: 600; transition: background-color .15s ease; }
    #tsSummaryNote.ts-note-applied { background: #e8f5e9; border-left-color: #2e7d32; color: #1b5e20; }
    #tsSummaryNote.ts-note-skipped { background: #fdecea; border-left-color: #c62828; color: #a80000; }
    .ts-formula { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: .75rem 1rem; font-size: .82rem; line-height: 1.9; }
    .ts-formula .ts-fx-label { display: inline-block; min-width: 120px; color: #6c757d; font-weight: 600; }
    .ts-formula .ts-fx-sub { padding-left: 120px; }
    .ts-formula b { font-variant-numeric: tabular-nums; }
    .ts-fx-status { padding: .5rem .75rem; border-radius: 8px; border-left: 4px solid transparent; font-weight: 600; }
    .ts-fx-status-skipped { background: #fdecea; border-left-color: #c62828; color: #a80000; }
    .ts-fx-status-applied { background: #e8f5e9; border-left-color: #2e7d32; color: #1b5e20; }
    .ns-preview { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: .5rem .75rem; font-size: .78rem; line-height: 1.9; }
    .ns-preview b { font-variant-numeric: tabular-nums; }
    .ns-merged { border: 1px solid #e6e8ec; border-radius: 10px; background: #fff; overflow-x: auto; }
    .ns-merged table { width: 100%; min-width: 960px; border-collapse: separate; border-spacing: 0; font-size: .78rem; margin: 0; }
    .ns-merged th, .ns-merged td { padding: .55rem .6rem; text-align: center; vertical-align: middle; border-bottom: 1px solid #eef0f3; }
    .ns-merged tbody tr:last-child td { border-bottom: 0; }
    .ns-merged thead .grp th { font-size: .8rem; font-weight: 700; color: #222; padding-top: .8rem; border-bottom: 2px solid #a80000; }
    .ns-merged thead .grp th small { display: block; font-weight: 500; font-size: .68rem; color: #6b7280; }
    .ns-merged thead .sub th { font-size: .7rem; font-weight: 600; color: #6b7280; background: #fafbfc; }
    .ns-merged .sep { border-left: 1px solid #e6e8ec; }
    .ns-merged .ns-pct { min-width: 56px; color: #4b5563; }
    .ns-merged .ns-bar { min-width: 110px; padding-left: .3rem !important; padding-right: .7rem !important; }
    .ns-merged .ns-bar .track { height: 8px; background: #eef0f3; border-radius: 4px; overflow: hidden; }
    .ns-merged .ns-bar .fill { display: block; height: 100%; border-radius: 4px; min-width: 0; }
    .ns-merged .ns-n { font-weight: 700; color: #111; font-variant-numeric: tabular-nums; }
    .ns-merged .ns-n100 { color: #6b7280; font-variant-numeric: tabular-nums; }
    .ns-merged .ns-peak { background: var(--tint, #f3f4f6); }
    .ns-merged .ns-all { background: #fafbfc; }
    .ns-merged .ns-all .ns-n { font-size: .85rem; }
    .ns-merged .ns-all .pct { color: #a80000; font-weight: 700; }
    .ns-merged thead .grp th.th-all { background: #fff5f5; color: #a80000; }
    .ns-merged tfoot td { font-weight: 700; color: #111; border-top: 2px solid #d9dce1; border-bottom: 0; background: #fafbfc; }
    .ns-merged tfoot .lbl { font-size: .72rem; }
    .ns-table td { vertical-align: middle; padding: .3rem .4rem; }
    .ns-table input[type=range] { padding: 0; }
    .ts-grade-link { background: none; border: 0; padding: 0; color: #a80000; font-weight: 600; text-decoration: underline; cursor: pointer; }
    .ts-grade-link:hover { color: #6d0000; }
    .ts-grade-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
    .ts-grade-table th, .ts-grade-table td { border: 1px solid #e0c4c4; padding: .5rem .55rem; vertical-align: top; }
    .ts-grade-table thead th { background: #a80000; color: #fff; text-align: center; font-weight: 700; }
    .ts-grade-table td.ts-grade-band { background: #a80000; color: #fff; text-align: center; font-size: 1.05rem; font-weight: 800; }
    .ts-grade-table td.ts-grade-range { background: #fdecea; color: #a80000; text-align: center; font-weight: 700; }
    .ts-grade-table td.ts-grade-desc { background: #fff; color: #212529; }
    .ts-grade-desc b, .ts-grade-desc em { color: #a80000; }
    .ts-grade-final { background: #a80000; color: #fff; text-align: center; font-weight: 700; padding: .6rem .75rem; border-radius: 4px; margin-top: .75rem; }
</style>

<div class="container-fluid">
    <div class="ts-card">
        <h4 style="color:#a80000; font-weight:700; margin:0;">PMS Total Scoring</h4>
        @include('hr.partials.ipp-pk-subnav')
    </div>

    <div class="ts-card">
        <h5>Panduan Perhitungan Skor</h5>
        <div class="row">
            <div class="col-lg-7 mb-3 mb-lg-0">
                <table class="table table-bordered ts-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:46px;">#</th>
                            <th style="width:150px;">Komponen</th>
                            <th>Rumus / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center"><span class="ts-guide-step">1</span></td>
                            <td><strong>IPP Score</strong></td>
                            <td>Rata-rata skor IPP aktual tahap <strong>End Year</strong>.</td>
                        </tr>
                        <tr>
                            <td class="text-center"><span class="ts-guide-step">2</span></td>
                            <td><strong>PK Score</strong></td>
                            <td><strong>Level 2&ndash;3:</strong> rata-rata nilai per komponen &mdash; Core Value, Behavior, dan Leadership Competency (diambil dari nilai <strong>Leader</strong>). Leadership Competency ikut dihitung hanya bila karyawan memiliki bawahan; bila tidak, PK Score = rata-rata Core Value + Behavior.<br><strong>Level lainnya:</strong> <strong>70% Core Value</strong> + <strong>30% Leadership Competency</strong>. Leadership Competency dinilai hanya bila karyawan memiliki bawahan; bila tidak, PK Score = Core Value saja (100%).</td>
                        </tr>
                        <tr>
                            <td class="text-center"><span class="ts-guide-step">3</span></td>
                            <td><strong>Hasil Formulasi</strong></td>
                            <td><strong>70% &times; IPP Score</strong> + <strong>30% &times; PK Score</strong> (dibulatkan 2 desimal).</td>
                        </tr>
                        <tr>
                            <td class="text-center"><span class="ts-guide-step">4</span></td>
                            <td><strong>PA Scale Original</strong></td>
                            <td><button type="button" class="ts-grade-link" data-toggle="modal" data-target="#gradeScaleModal">Konversi Hasil Formulasi</button> &mdash; lihat tabel di samping.</td>
                        </tr>
                        <tr>
                            <td class="text-center"><span class="ts-guide-step">5</span></td>
                            <td><strong>PA Scale Normalize</strong></td>
                            <td>PA Scale Normalize adalah sistem pemeringkatan otomatis yang dihitung terpisah untuk <strong>Level 2&ndash;3</strong> dan <strong>Level 4&ndash;7</strong> (Level 4&ndash;5 dan 6&ndash;7 digabung), berdasarkan skor tertinggi ke terendah. Masing-masing group dikelompokkan ke predikat A (5%), B (15%), C (65%), D (10%), dan E (5%) menggunakan metode sisa kuota terbesar. Aturan pembagian kuota berlaku jika peserta group minimal 12 orang (jika kurang, nilai menggunakan hasil konversi), karyawan dengan skor kembar di batas nilai masuk ke predikat yang lebih tinggi, dan persentase kuota serta minimum peserta dapat diatur per group per periode melalui tombol Atur Kuota Final. Lihat contoh cara perhitungan di bagian bawah.</td>
                        </tr>
                        <tr>
                            <td class="text-center"><span class="ts-guide-step">6</span></td>
                            <td><strong>PA Scale Calibrated</strong></td>
                            <td>Nilai akhir untuk pelaporan secara otomatis mengikuti Normalize, namun Anda bisa mengubahnya secara manual per karyawan melalui dropdown PA SCALE CALIBRATED atau menggunakan tombol ⤑ Salin dari Normalize untuk langsung mengisi semua kolom calibrated yang masih kosong sesuai filter tanpa menimpa nilai yang sudah ada.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-5">
                <table class="table table-bordered ts-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:110px;">PA Scale Original</th>
                            <th class="text-center">Rentang Hasil Formulasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="text-center"><span class="ts-pa ts-pa-A">A</span></td><td class="text-center">&ge; 4.20</td></tr>
                        <tr><td class="text-center"><span class="ts-pa ts-pa-B">B</span></td><td class="text-center">3.80 &ndash; 4.19</td></tr>
                        <tr><td class="text-center"><span class="ts-pa ts-pa-C">C</span></td><td class="text-center">3.00 &ndash; 3.79</td></tr>
                        <tr><td class="text-center"><span class="ts-pa ts-pa-D">D</span></td><td class="text-center">2.00 &ndash; 2.99</td></tr>
                        <tr><td class="text-center"><span class="ts-pa ts-pa-E">E</span></td><td class="text-center">1.00 &ndash; 1.99</td></tr>
                    </tbody>
                </table>
                <p class="ts-guide-note mt-2 mb-0">* Hasil Formulasi &lt; 1.00 tidak diberikan PA Scale.</p>
            </div>
        </div>
    </div>

    @if(empty($isManagerView))
    <div class="ts-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 class="mb-0">LOCKING PA SCALE CALIBRATED</h5>
            <span class="ts-badge" id="tsLockBadge" style="background:#e8f5e9;color:#1b5e20;">Terbuka</span>
        </div>
        <div class="d-flex align-items-end flex-wrap" style="gap:.75rem;">
            <div>
                <label style="font-size:.78rem; font-weight:600;">Periode</label>
                <select id="tsLockPeriode" class="form-control form-control-sm" style="min-width:120px;"></select>
            </div>
            <button type="button" class="btn btn-sm btn-danger" id="btnLockToggle" disabled>
                <i class="fas fa-lock"></i> Kunci PA Scale CALIBRATED
            </button>
            <small class="text-muted" id="tsLockMeta" style="font-size:.75rem;"></small>
        </div>
        <p class="ts-guide-note mt-2 mb-0">Saat periode terkunci, dropdown PA SCALE CALIBRATED dan tombol Salin dari Normalize tidak dapat diubah oleh manager; HRD (admin) tetap dapat mengubahnya sampai kunci dibuka.</p>
    </div>

    <div class="ts-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 class="mb-0">LOCKING IPP AKTUAL</h5>
            <span class="ts-badge" id="tsLockIppBadge" style="background:#e8f5e9;color:#1b5e20;">Terbuka</span>
        </div>
        <div class="d-flex align-items-end flex-wrap mb-2" style="gap:.75rem;">
            <div>
                <label style="font-size:.78rem; font-weight:600;">Tahapan</label>
                <select id="tsLockIppTahapan" class="form-control form-control-sm" style="min-width:170px;"></select>
            </div>
            <small class="text-muted" id="tsLockIppMeta" style="font-size:.75rem;"></small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" style="font-size:.85rem;">
                <thead>
                    <tr>
                        <th style="min-width:180px;">Kolom Aktual IPP</th>
                        <th class="text-center" style="min-width:110px;">Status</th>
                        <th class="text-center" style="min-width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tsLockIppBody">
                    <tr><td colspan="3" class="text-center text-muted">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
        <p class="ts-guide-note mt-2 mb-0">Saat kolom terkunci, input Aktual (Departemen/Karyawan/Atasan) pada tahapan ini tidak dapat diubah di halaman IPP. Saat <strong>Batalkan Approval</strong> terkunci, seluruh aksi approval (Approve/Reject/Batalkan Approval) pada halaman IPP Atasan tidak dapat dilakukan dan hanya bisa dibuka oleh Admin HR. Admin HR (permission Template IPP) tetap dapat mengubah Aktual; user lain harus menunggu kunci dibuka.</p>
    </div>

    <div class="ts-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 class="mb-0">LOCKING PK</h5>
            <span class="ts-badge" id="tsLockPkBadge" style="background:#e8f5e9;color:#1b5e20;">Terbuka</span>
        </div>
        <div class="d-flex align-items-end flex-wrap mb-2" style="gap:.75rem;">
            <div>
                <label style="font-size:.78rem; font-weight:600;">Periode</label>
                <select id="tsLockPkPeriode" class="form-control form-control-sm" style="min-width:120px;"></select>
            </div>
            <small class="text-muted" id="tsLockPkMeta" style="font-size:.75rem;"></small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" style="font-size:.85rem;">
                <thead>
                    <tr>
                        <th style="min-width:260px;">Kolom PK</th>
                        <th class="text-center" style="min-width:110px;">Status</th>
                        <th class="text-center" style="min-width:150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tsLockPkBody">
                    <tr><td colspan="3" class="text-center text-muted">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
        <p class="ts-guide-note mt-2 mb-0">Saat kolom terkunci, aksi berikut tidak dapat dilakukan pada periode ini di halaman <strong>/hr/pk-assessment</strong> (Self Assessment &amp; Leader Assessment): input <strong>Nilai Karyawan</strong>, input <strong>Nilai Atasan</strong>, <strong>Approval Associate Manager</strong>, <strong>Approval Manager</strong>, dan <strong>Unapproval</strong>. Kunci berlaku untuk semua user; hanya Admin HR yang dapat membuka kunci.</p>
    </div>

    @if (!empty($canManageNotifications))
    <div class="ts-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 class="mb-0">NOTIFIKASI EMAIL IPP &amp; PK</h5>
            <span class="ts-badge" id="tsNotifEmailBadge" style="background:#e8f5e9;color:#1b5e20;">Aktif</span>
        </div>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="tsNotifEmail" {{ !empty($notificationControl['is_active']) ? 'checked' : '' }}>
            <label class="custom-control-label" for="tsNotifEmail" style="font-weight:600;">Kirim email notifikasi PMS (IPP &amp; PK)</label>
        </div>
        <small class="text-muted d-block mt-1" id="tsNotifEmailMeta" style="font-size:.75rem;"></small>
        <p class="ts-guide-note mt-2 mb-0">Saat dinonaktifkan, seluruh email notifikasi IPP (IPP baru, pengajuan approval, keputusan approval, diskusi baru, jawaban diskusi) dan PK (PK baru, self assessment selesai, keputusan approval, diskusi baru, jawaban diskusi) tidak dibuat/dikirim. Email yang sudah masuk antrean sebelum dinonaktifkan tetap dikirim. Berguna saat testing agar tidak ada email keluar ke karyawan. Kartu notifikasi IPP &amp; PK di beranda juga tidak ditampilkan selama nonaktif.</p>
    </div>
    @endif
    @endif

    <div class="ts-card">
        <h5>Filter</h5>
        <form id="tsFormFilter" class="row" style="gap:0;">
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Periode (End Year)</label>
                <select id="tsFltPeriode" class="form-control form-control-sm">
                    <option value="">— Semua Periode —</option>
                    @forelse ($periodes as $p)
                        <option value="{{ $p }}" {{ $p == $defaultPeriode ? 'selected' : '' }}>{{ $p }}</option>
                    @empty
                        <option value="">-</option>
                    @endforelse
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Group</label>
                <select id="tsFltGroup" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    <option value="2-3">Level 2–3</option>
                    <option value="4-7">Level 4–7 (4–5 &amp; 6–7)</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Departemen / Bagian</label>
                <select id="tsFltDept" class="form-control form-control-sm">
                    <option value="" data-type="dept">— Semua —</option>
                    @foreach ($deptOptions as $o)
                        <option value="{{ $o['v'] }}" data-type="{{ $o['t'] }}">{{ $o['v'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">PA Scale</label>
                <select id="tsFltPaScale" class="form-control form-control-sm">
                    <option value="original">Original</option>
                    <option value="normalize">Normalize</option>
                    <option value="calibrated">Calibrated</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Grade</label>
                <select id="tsFltGrade" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label style="font-size:.78rem; font-weight:600;">Cari</label>
                <input type="text" id="tsFltSearch" class="form-control form-control-sm" placeholder="NIK / Nama / Jabatan / Bagian...">
            </div>
            <div class="col-md-2 offset-md-10 mb-2 d-flex align-items-end" style="gap:.4rem;">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">Cari</button>
                @if(empty($isManagerView))
                <button type="button" class="btn btn-success btn-sm flex-fill" id="btnExportScoring" title="Export Excel">
                    <i class="fas fa-file-excel"></i> Export
                </button>
                @endif
            </div>
        </form>
    </div>

    <div class="ts-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 class="mb-0">Note &mdash; SUGGESTED PA BERLAKU <span class="text-muted" id="tsSummaryPeriode" style="font-size:.85rem; font-weight:400;"></span></h5>
            <div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">
                <span class="ts-badge" id="tsNormalScaleInfo" style="background:#fce4d6;color:#a80000;display:none;"></span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 mb-3 mb-lg-0">
                <div class="mb-1" style="font-size:.85rem;">
                    <span class="text-muted">Jumlah orang:</span> <b id="tsSummaryPeople">-</b>
                    <span class="text-muted" id="tsSummaryPeopleNote" style="font-size:.78rem;"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered ts-table mb-0">
                        <thead>
                            <tr>
                                <th style="min-width:230px;">Keterangan</th>
                                <th class="text-center" style="width:70px;">E</th>
                                <th class="text-center" style="width:70px;">D</th>
                                <th class="text-center" style="width:70px;">C</th>
                                <th class="text-center" style="width:70px;">B</th>
                                <th class="text-center" style="width:70px;">A</th>
                            </tr>
                        </thead>
                        <tbody id="tsSummaryBody">
                            <tr><td colspan="6" class="text-center text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-7">
                <div id="tsNormalChart" style="min-height:320px;"></div>
                <div id="tsNormalChartEmpty" class="text-center text-muted d-none" style="padding:4rem 0;">Grafik tidak tersedia.</div>
            </div>
        </div>
        <p class="ts-guide-note mt-2 mb-0" id="tsSummaryNote"></p>
        <div class="mt-2 d-flex flex-wrap" style="gap:.5rem;">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#tsFormulaBox" aria-expanded="false" aria-controls="tsFormulaBox">
                Contoh Perhitungan
            </button>
            @if(empty($isManagerView))
            <button class="btn btn-sm btn-outline-primary" type="button" id="btnNormalScale">
                <i class="fas fa-sliders-h"></i> Atur Kuota Final
            </button>
            @endif
        </div>
        <div class="collapse mt-2" id="tsFormulaBox">
            <div class="ts-formula" id="tsFormulaBody"></div>
        </div>
    </div>

    <div class="ts-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2" style="gap:.5rem;">
            <h5 class="mb-0">Hasil Total Scoring @if(!empty($isManagerView))<span class="badge badge-warning" style="font-size:.7rem;">Tim Saya</span>@endif <span class="text-muted" id="tsPeriodeLabel" style="font-size:.85rem; font-weight:400;"></span></h5>
        </div>
        <p class="ts-guide-note mb-2">Klik judul kolom <strong>HASIL FORMULASI</strong> / <strong>PA SCALE</strong> untuk mengurutkan: klik 1&times; menaik, 2&times; menurun, 3&times; kembali ke urutan awal.</p>
        <div class="table-responsive">
            <table class="table table-bordered ts-table mb-0">
                <thead>
                    <tr>
                        <th colspan="{{ empty($isManagerView) ? 10 : 9 }}" class="ts-grp-empty"></th>
                        <th colspan="2" class="ts-grp-result">END YEAR RESULT</th>
                        <th colspan="4" class="ts-grp-rating">END YEAR FINAL RATING</th>
                    </tr>
                    <tr>
                        <th class="ts-sub-left">NO</th>
                        <th class="ts-sub-left">PERIODE</th>
                        <th class="ts-sub-left">NIK</th>
                        <th class="ts-sub-left">NAMA</th>
                        <th class="ts-sub-left">DEPT</th>
                        <th class="text-center ts-sub-left">LEVEL</th>
                        <th class="ts-sub-left">BAGIAN</th>
                        <th class="ts-sub-left">NAMA POSISI</th>
                        <th class="text-center ts-sub-left">PUNYA BAWAHAN</th>
                        @if(empty($isManagerView))
                        <th class="text-center ts-sub-left">FORM</th>
                        @endif
                        <th class="text-center ts-sub-result">IPP SCORE</th>
                        <th class="text-center ts-sub-result">PK SCORE</th>
                        <th class="text-center ts-sub-rating ts-sortable" data-sort="final_score" aria-sort="none" tabindex="0" title="Klik untuk mengurutkan">HASIL FORMULASI<i class="fas fa-sort ts-sort-arrow" aria-hidden="true"></i></th>
                        <th class="text-center ts-sub-rating ts-sortable" data-sort="pa_scale" aria-sort="none" tabindex="0" title="Klik untuk mengurutkan">PA SCALE<br>ORIGINAL<i class="fas fa-sort ts-sort-arrow" aria-hidden="true"></i></th>
                        <th class="text-center ts-sub-rating ts-sortable" data-sort="pa_scale_rank" aria-sort="none" tabindex="0" title="Klik untuk mengurutkan">PA SCALE<br>NORMALIZE<i class="fas fa-sort ts-sort-arrow" aria-hidden="true"></i></th>
                        <th class="text-center ts-sub-rating ts-sortable" data-sort="pa_scale_final" aria-sort="none" tabindex="0" title="Klik untuk mengurutkan">PA SCALE<br>CALIBRATED<i class="fas fa-sort ts-sort-arrow" aria-hidden="true"></i><br>
                            <button type="button" class="btn btn-xs btn-outline-info mt-1" id="btnSalinRankFinal" style="font-size:.62rem; padding:.1rem .3rem; line-height:1.15; white-space:normal; max-width:130px;" disabled>⧉ Salin dari Normalize</button>
                        </th>
                    </tr>
                </thead>
                <tbody id="tsTbody"><tr><td colspan="{{ empty($isManagerView) ? 16 : 15 }}" class="text-center text-muted">Memuat data...</td></tr></tbody>
            </table>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:.5rem;">
            <span class="text-muted" style="font-size:.85rem;" id="tsPageLabel"></span>
            <div id="tsPagination"></div>
        </div>
    </div>
</div>

{{-- MODAL SETTING KURVA NORMAL --}}
<div class="modal fade" id="normalScaleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#a80000; font-weight:700;">Atur Kuota Final &mdash; PA Scale</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @php
                    $nsGroupLabels = ['2-3' => 'Setting persentase grade level 2-3 (Staff)', '4-7' => 'Setting persentase grade level 4-7 (Supervisor dan Associate Manager)'];
                    $nsGradeDefaults = ['A' => 5, 'B' => 15, 'C' => 65, 'D' => 10];
                @endphp
                <div class="form-row align-items-end mb-3">
                    <div class="form-group col-md-3 mb-0">
                        <label style="font-size:.78rem; font-weight:600;">Periode</label>
                        <select id="nsPeriode" class="form-control form-control-sm"></select>
                    </div>
                </div>
                @foreach ($nsGroupLabels as $nsGroup => $nsGroupLabel)
                <div class="ns-group-block border rounded p-3 mb-3" data-group="{{ $nsGroup }}">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-2" style="gap:.5rem;">
                        <h6 class="mb-0" style="font-weight:700;">{{ $nsGroupLabel }}</h6>
                        <small class="text-muted ns-meta" data-group="{{ $nsGroup }}" style="font-size:.72rem;"></small>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <table class="table table-sm ns-table mb-1">
                                <tbody>
                                    @foreach ($nsGradeDefaults as $nsGrade => $nsDefault)
                                    <tr>
                                        <td style="width:34px;"><span class="ts-pa ts-pa-{{ $nsGrade }}">{{ $nsGrade }}</span></td>
                                        <td><input type="range" class="form-control-range ns-range" data-group="{{ $nsGroup }}" data-grade="{{ $nsGrade }}" min="0" max="100" step="1" value="{{ $nsDefault }}"></td>
                                        <td style="width:96px;">
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control ns-num" data-group="{{ $nsGroup }}" data-grade="{{ $nsGrade }}" min="0" max="100" step="1" value="{{ $nsDefault }}">
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td><span class="ts-pa ts-pa-E">E</span></td>
                                        <td colspan="2">
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control ns-num-e" data-group="{{ $nsGroup }}" value="5" readonly>
                                                <div class="input-group-append"><span class="input-group-text">% (otomatis)</span></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex align-items-center flex-wrap mb-2" style="gap:.5rem;">
                                <span class="ts-badge ns-sum-badge" data-group="{{ $nsGroup }}" style="background:#e8f5e9;color:#1b5e20;">Total 100%</span>
                            </div>
                            <div class="mb-2" style="font-size:.75rem; color:#000;">Ditetapkan oleh direksi dan factory manager</div>
                            <div class="form-group mb-2">
                                <label style="font-size:.78rem; font-weight:600;">Minimum peserta (Normalize diterapkan bila jumlah peserta &ge;)</label>
                                <div class="input-group input-group-sm" style="max-width:220px;">
                                    <input type="number" class="form-control ns-min" data-group="{{ $nsGroup }}" min="1" max="1000" step="1" value="3">
                                    <div class="input-group-append"><span class="input-group-text">orang</span></div>
                                </div>
                            </div>
                            <div class="ns-preview ns-cutoff-info" data-group="{{ $nsGroup }}"></div>
                        </div>
                        <div class="col-lg-7">
                            <div class="d-flex flex-wrap mb-1 ns-chart-legend" data-group="{{ $nsGroup }}" style="gap:.4rem .8rem; font-size:.72rem;"></div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap mt-3 mb-1" style="gap:.5rem;">
                                <span class="text-muted" style="font-size:.78rem; font-weight:600;">Kuota bila N = 100 orang</span>
                                <span class="text-muted ns-count-hint" data-group="{{ $nsGroup }}" style="font-size:.72rem;"></span>
                            </div>
                            <div class="ns-count-chart" data-group="{{ $nsGroup }}" style="min-height:240px;"></div>
                            <div class="text-muted mt-1" style="font-size:.72rem;">
                                Simulasi grafik = Jumlah populasi adalah 100. Grafik akan normal apabila presentase nilai tengah (grade c) lebih tinggi.
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="mb-3">
                    <div class="text-muted mb-1" style="font-size:.78rem; font-weight:600;">Kuota Final per grade (metode rangking) &mdash; Ringkasan Gabungan</div>
                    <div id="nsMergedQuota" class="ns-merged"></div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm mr-auto" id="btnNsReset">Kembalikan Default</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="btnNsSave">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PERINGKAT KINERJA (GRADE) --}}
<div class="modal fade" id="gradeScaleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color:#a80000; font-weight:700;">Peringkat Kinerja (Grade)</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="ts-grade-table">
                        <thead>
                            <tr>
                                <th>Far Below the Target</th>
                                <th>Below the Target</th>
                                <th>Acceptable Target</th>
                                <th>Above the Target</th>
                                <th>Far Above the Target</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ts-grade-band">E</td>
                                <td class="ts-grade-band">D</td>
                                <td class="ts-grade-band">C</td>
                                <td class="ts-grade-band">B</td>
                                <td class="ts-grade-band">A</td>
                            </tr>
                            <tr>
                                <td class="ts-grade-range">(1 &ndash; 1,99)</td>
                                <td class="ts-grade-range">(2 &ndash; 2,99)</td>
                                <td class="ts-grade-range">(3,00 &ndash; 3,79)</td>
                                <td class="ts-grade-range">(3,80 &ndash; 4,19)</td>
                                <td class="ts-grade-range">(4,20 &ndash; 5)</td>
                            </tr>
                            <tr>
                                <td class="ts-grade-desc">Pencapaian hasil kerja (kuantitas, kualitas dan waktu) <b>jauh dibawah target</b> yang disepakati dan membutuhkan <b><em>perbaikan secara significant</em></b>.</td>
                                <td class="ts-grade-desc">Pencapaian hasil kerja (kuantitas, kualitas dan waktu) <b>dibawah target</b> yang disepakati</td>
                                <td class="ts-grade-desc">Pencapaian hasil kerja (kuantitas, kualitas dan waktu) <b>sesuai dengan target</b> atau <b>sesekali melebihi target</b> yang disepakati.</td>
                                <td class="ts-grade-desc">Pencapaian hasil kerja (kuantitas, kualitas dan waktu) secara <b>konsisten melebihi target</b> yang disepakati &amp; memberikan <b>nilai tambah di dalam fungsi/organisasi</b>.</td>
                                <td class="ts-grade-desc">Pencapaian hasil kerja (kuantitas, kualitas dan waktu) <b>konsisten jauh melebihi target</b> &amp; memberikan <b>dampak significant</b> bagi fungsi lain/ Perusahaan serta menjadi <b><em>role model</em></b>.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="ts-grade-final">*Final Rating : Total IPP Result + Penilaian Karya<br>70%</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/velzon/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/velzon/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
$(function () {
    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function num(v, dec) {
        if (v === null || v === undefined || v === '') return '<span class="ts-mute">-</span>';
        return '<span class="ts-num">' + Number(v).toFixed(dec === undefined ? 2 : dec) + '</span>';
    }

    function paBadge(g, outline) {
        if (!g) return '<span class="ts-mute">-</span>';
        return '<span class="ts-pa ts-pa-' + escapeHtml(g) + (outline ? ' ts-pa-outline' : '') + '">' + escapeHtml(g) + '</span>';
    }

    function formButtons(x) {
        let s = '';
        if (x.form_ipp_url) {
            s += '<a class="btn btn-sm btn-outline-primary mr-1" target="_blank" rel="noopener" href="' + x.form_ipp_url + '">IPP</a>';
        } else {
            s += '<button class="btn btn-sm btn-outline-secondary mr-1" disabled title="Data IPP belum tersedia">IPP</button>';
        }
        if (x.form_pk_url) {
            s += '<a class="btn btn-sm btn-outline-dark" target="_blank" rel="noopener" href="' + x.form_pk_url + '">PK</a>';
        } else {
            s += '<button class="btn btn-sm btn-outline-secondary" disabled title="Data PK belum tersedia">PK</button>';
        }
        return s;
    }

    function paFinalByText(by, at) {
        return by
            ? ('Diubah oleh ' + by + (at ? ' · ' + at : ''))
            : 'Belum diisi HRD';
    }

    function paFinalSelect(x) {
        let cur = x.pa_scale_final || '';
        let opts = '<option value="">-</option>';
        ['A', 'B', 'C', 'D', 'E'].forEach(function (g) {
            opts += '<option value="' + g + '"' + (g === cur ? ' selected' : '') + '>' + g + '</option>';
        });
        let byText = paFinalByText(x.pa_final_by, x.pa_final_at);
        let title = escapeHtml(byText);
        let note = escapeHtml(byText);
        let disabled = '';
        if (x.pa_final_locked) {
            disabled = ' disabled';
            title = 'PA Scale Calibrated periode ini terkunci (LOCKING PA SCALE CALIBRATED).';
            note = 'Terkunci';
        }
        return '<select class="form-control form-control-sm ts-pa-final" data-periode="' + escapeHtml(x.periode) + '" data-nik="' + escapeHtml(x.nik) + '" data-prev="' + escapeHtml(cur) + '" title="' + title + '" style="min-width:64px;"' + disabled + '>' + opts + '</select>' +
            '<div class="text-muted ts-pa-final-by" style="font-size:.68rem; line-height:1.2; margin-top:2px; white-space:normal; max-width:130px; margin-left:auto; margin-right:auto;">' + note + '</div>';
    }

    const tsShowForm = @json(empty($isManagerView));
    const tsColCount = tsShowForm ? 16 : 15;
    let page = 1, perPage = 20;
    let tsSort = { key: '', dir: '' };
    let tsLockMap = {};

    function renderSortIndicators() {
        $('.ts-sortable').each(function () {
            let $th = $(this);
            let active = $th.data('sort') === tsSort.key;
            let icon = 'fa-sort';
            let title = 'Klik untuk mengurutkan';
            if (active) {
                if (tsSort.dir === 'desc') {
                    icon = 'fa-sort-down';
                    title = 'Klik untuk reset ke urutan awal';
                } else {
                    icon = 'fa-sort-up';
                    title = 'Klik untuk urutkan menurun';
                }
            }
            $th.toggleClass('is-sorted', active)
                .attr('aria-sort', active ? (tsSort.dir === 'desc' ? 'descending' : 'ascending') : 'none')
                .attr('title', title);
            $th.find('.ts-sort-arrow').removeClass('fa-sort fa-sort-up fa-sort-down').addClass(icon);
        });
    }

    function toggleSort(key) {
        key = key || '';
        if (!key) {
            return;
        }
        if (tsSort.key !== key) {
            tsSort = { key: key, dir: 'asc' };
        } else if (tsSort.dir === 'asc') {
            tsSort.dir = 'desc';
        } else {
            tsSort = { key: '', dir: '' };
        }
        renderSortIndicators();
        page = 1;
        loadList();
    }

    function renderDeptOptions(options) {
        if (!options || !options.length) {
            return;
        }
        let sel = $('#tsFltDept');
        let current = sel.val();
        let exists = options.some(function (o) { return o.v === current; });
        let html = '<option value="" data-type="dept">— Semua —</option>';
        options.forEach(function (o) {
            html += '<option value="' + escapeHtml(o.v) + '" data-type="' + escapeHtml(o.t) + '">' + escapeHtml(o.v) + '</option>';
        });
        sel.html(html);
        sel.val(exists ? current : '');
    }

    function loadList(onDone) {
        $('#tsTbody').html('<tr><td colspan="' + tsColCount + '" class="text-center text-muted">Memuat data...</td></tr>');
        $.get('/hr/pms-total-scoring/list', {
            page: page,
            per_page: perPage,
            periode: $('#tsFltPeriode').val(),
            group: $('#tsFltGroup').val(),
            departemen: $('#tsFltDept').val(),
            dept_type: $('#tsFltDept option:selected').data('type') || 'dept',
            pa_scale_type: $('#tsFltPaScale').val() || '',
            pa_scale_grade: $('#tsFltGrade').val() || '',
            search: $('#tsFltSearch').val(),
            sort: tsSort.key,
            dir: tsSort.dir
        }, function (res) {
            $('#tsPeriodeLabel').text(res.periode ? ('— Periode ' + res.periode) : '— Semua Periode');
            renderDeptOptions(res.dept_options);
            renderSummary(res.summary);
            let items = res.data || [];
            let html = '';
            if (!items.length) {
                html = '<tr><td colspan="' + tsColCount + '" class="text-center text-muted">Tidak ada data untuk periode ini.</td></tr>';
            }
            items.forEach(function (x, i) {
                let bagian = x.bagian || '-';
                html += '<tr class="ts-row">' +
                    '<td class="text-center">' + ((res.meta.page - 1) * res.meta.per_page + i + 1) + '</td>' +
                    '<td>' + escapeHtml(x.periode || '-') + '</td>' +
                    '<td>' + escapeHtml(x.nik) + '</td>' +
                    '<td><strong>' + escapeHtml(x.nama) + '</strong></td>' +
                    '<td>' + escapeHtml(x.dept_label || x.departemen || '-') + '</td>' +
                    '<td class="text-center">' + escapeHtml(x.level) + '</td>' +
                    '<td>' + escapeHtml(bagian) + '</td>' +
                    '<td>' + escapeHtml(x.jabatan || '-') + '</td>' +
                    '<td class="text-center">' + (x.punya_bawahan === null || x.punya_bawahan === undefined ? '<span class="ts-mute">-</span>' : escapeHtml(x.punya_bawahan)) + '</td>' +
                    (tsShowForm ? '<td class="text-center" style="white-space:nowrap;">' + formButtons(x) + '</td>' : '') +
                    '<td class="text-center">' + num(x.ipp_score) + '</td>' +
                    '<td class="text-center">' + num(x.pk_score) + '</td>' +
                    '<td class="text-center">' + (x.final_score === null || x.final_score === undefined ? '<span class="ts-mute">-</span>' : '<span class="ts-final">' + Number(x.final_score).toFixed(2) + '</span>') + '</td>' +
                    '<td class="text-center">' + paBadge(x.pa_scale) + '</td>' +
                    '<td class="text-center">' + paBadge(x.pa_scale_rank, true) + '</td>' +
                    '<td class="text-center">' + paFinalSelect(x) + '</td>' +
                    '</tr>';
            });
            $('#tsTbody').html(html);
            renderPager(res.meta || {});
            tsLockMap = res.locking || {};
            let fltLocked = !!tsLockMap[$('#tsFltPeriode').val()] && !res.can_edit_locked;
            $('#btnSalinRankFinal')
                .prop('disabled', fltLocked)
                .attr('title', fltLocked ? 'PA Scale Calibrated periode ini terkunci.' : '');
            if (typeof onDone === 'function') onDone(true);
        }).fail(function (xhr) {
            $('#tsTbody').html('<tr><td colspan="' + tsColCount + '" class="text-center text-danger">Gagal memuat (HTTP ' + xhr.status + ').</td></tr>');
            renderSummary(null);
            $('#btnSalinRankFinal').prop('disabled', true);
            if (typeof onDone === 'function') onDone(false);
        });
    }

    $(document).on('change', '.ts-pa-final', function () {
        let $sel = $(this);
        savePaFinal($sel, $sel.val() || '', null);
    });

    function savePaFinal($sel, val, onDone) {
        let periode = String($sel.data('periode') || '');
        let nik = String($sel.data('nik') || '');
        let prev = String($sel.data('prev') || '');
        val = String(val == null ? '' : val);
        if (val === prev) {
            if (typeof onDone === 'function') onDone(true, null);
            return;
        }

        $sel.prop('disabled', true);
        $.post('/hr/pms-total-scoring/pa-final', {
            periode: periode,
            nik: nik,
            pa_scale_final: val
        }, function (res) {
            $sel.data('prev', val).prop('disabled', false);
            let byText = paFinalByText(res && res.updated_by, res && res.updated_at);
            $sel.attr('title', byText);
            $sel.siblings('.ts-pa-final-by').text(byText);
            if (tsLastSummary && String(tsLastSummary.periode) === periode && tsLastSummary.final) {
                if (prev !== '' && tsLastSummary.final[prev] > 0) {
                    tsLastSummary.final[prev]--;
                }
                if (val !== '') {
                    tsLastSummary.final[val] = (tsLastSummary.final[val] || 0) + 1;
                }
                renderSummary(tsLastSummary);
            }
            if (typeof onDone === 'function') {
                onDone(true, res);
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan',
                    text: (res && res.message) || '',
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        }).fail(function (xhr) {
            $sel.val(prev).prop('disabled', false);
            let msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : ('Gagal menyimpan (HTTP ' + xhr.status + ').');
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.keys(xhr.responseJSON.errors).map(function (k) {
                    return xhr.responseJSON.errors[k].join(' ');
                }).join(' ');
            }
            if (typeof onDone === 'function') {
                onDone(false, { message: msg });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
            }
        });
    }

    $(document).on('click', '#btnSalinRankFinal', function () {
        let $btn = $(this);
        let label = 'Rank';

        let periode = String($('#tsFltPeriode').val() || '');
        if (periode === '') {
            Swal.fire('Info', 'Pilih periode terlebih dahulu untuk menyalin PA Scale Calibrated dari ' + label + '.', 'info');
            return;
        }

        let origText = $btn.html();
        $btn.prop('disabled', true).html('Menyimpan…');

        $.post('/hr/pms-total-scoring/pa-final-bulk', {
            periode: periode,
            group: $('#tsFltGroup').val(),
            departemen: $('#tsFltDept').val(),
            dept_type: $('#tsFltDept option:selected').data('type') || 'dept',
            pa_scale_type: $('#tsFltPaScale').val() || '',
            pa_scale_grade: $('#tsFltGrade').val() || '',
            search: $('#tsFltSearch').val(),
            source: 'rank'
        }, function (res) {
            $btn.prop('disabled', false).html(origText);
            let filled = Number(res && res.filled ? res.filled : 0);
            let skipped = Number(res && res.skipped ? res.skipped : 0);
            let showResult = function () {
                if (filled > 0) {
                    let text = filled + ' kolom PA Scale Calibrated diisi dari ' + label + '.';
                    if (skipped > 0) {
                        text += ' ' + skipped + ' baris dilewati (sudah terisi / ' + label + ' kosong / di luar tim).';
                    }
                    Swal.fire('Berhasil', text, 'success');
                } else {
                    Swal.fire('Info', (res && res.message) || ('Tidak ada kolom PA Scale Calibrated kosong yang bisa diisi dari ' + label + '.'), 'info');
                }
            };
            if (filled > 0) {
                loadList(function () { showResult(); });
            } else {
                showResult();
            }
        }).fail(function (xhr) {
            $btn.prop('disabled', false).html(origText);
            let msg = (xhr.responseJSON && xhr.responseJSON.message)
                ? xhr.responseJSON.message
                : ('Gagal menyimpan (HTTP ' + xhr.status + ').');
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.keys(xhr.responseJSON.errors).map(function (k) {
                    return xhr.responseJSON.errors[k].join(' ');
                }).join(' ');
            }
            Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
        });
    });

    let tsChart = null;
    let tsLastSummary = null;
    let tsDefaultPeriode = @json($defaultPeriode);

    function renderSummary(s) {
        if (!s) {
            tsLastSummary = null;
            $('#tsSummaryBody').html('<tr><td colspan="6" class="text-center text-muted">Ringkasan tidak tersedia.</td></tr>');
            $('#tsSummaryPeople').text('-');
            $('#tsSummaryPeopleNote').text('');
            $('#tsSummaryPeriode').text('');
            $('#tsSummaryNote').removeClass('ts-note-applied ts-note-skipped').text('');
            $('#tsNormalScaleInfo').hide().text('');
            renderNormalChart(null);
            renderFormula(null);
            return;
        }

        tsLastSummary = s;

        let grades = ['E', 'D', 'C', 'B', 'A'];
        function cells(values, fmt, dec) {
            return grades.map(function (g) {
                let v = values ? values[g] : null;
                if (v === null || v === undefined || v === '') return '<td class="text-center ts-mute">-</td>';
                if (fmt === 'percent') return '<td class="text-center ts-num">' + Number(v).toFixed(0) + '%</td>';
                if (fmt === 'decimal') return '<td class="text-center ts-num">' + Number(v).toFixed(dec === undefined ? 1 : dec) + '</td>';
                return '<td class="text-center ts-num">' + escapeHtml(v) + '</td>';
            }).join('');
        }
        function row(label, cellsHtml, cls) {
            return '<tr class="' + cls + '"><td class="ts-sum-label">' + label + '</td>' + cellsHtml + '</tr>';
        }

        let html = '';
        html += row('PA SCALE ORIGINAL', cells(s.converted), 'ts-sum-row-converted');
        html += row('PA SCALE NORMALIZE', cells(s.rank), 'ts-sum-row-rank');
        html += row('PA SCALE CALIBRATED', cells(s.final), 'ts-sum-row-final');
        html += row('KUOTA FINAL', cells(s.rank_quota), 'ts-sum-row-kurva');
        html += row('PERSEN KUOTA', cells(s.percent, 'percent'), 'ts-sum-row-persen');
        $('#tsSummaryBody').html(html);
        $('#tsSummaryPeople').text(Number(s.num_people || 0));
        $('#tsSummaryPeopleNote').text(s.filtered ? '(hasil filter)' : '');

        $('#tsSummaryPeriode').text('— Periode ' + escapeHtml(s.periode));

        let pct = s.percent || { E: 5, D: 10, C: 65, B: 15, A: 5 };
        let $note = $('#tsSummaryNote');
        let groupNote = [];
        if (s.groups) {
            ['2-3', '4-7'].forEach(function (g) {
                let gs = s.groups[g];
                if (!gs) return;
                groupNote.push(nsGroupLabel(g) + ': N ' + Number(gs.n || 0) + (gs.applied ? ' diterapkan' : ' tidak diterapkan'));
            });
        }
        $note.removeClass('ts-note-applied ts-note-skipped');
        if (s.rank_applied) {
            $note.addClass('ts-note-applied').text('✔ Kuota Final diterapkan untuk seluruh group periode ini.' + (groupNote.length ? ' (' + groupNote.join(' · ') + ')' : ''));
        } else if (s.rank_applied_partial) {
            $note.addClass('ts-note-skipped').text('ℹ  Kuota Final diterapkan sebagian.' + (groupNote.length ? ' ' + groupNote.join(' · ') : ''));
        } else {
            $note.addClass('ts-note-skipped').text('ℹ  Kuota Final tidak diterapkan untuk periode ini.' + (groupNote.length ? ' ' + groupNote.join(' · ') : ''));
        }
        let quotaTag = 'Kuota ' + (s.rank_is_custom ? 'Custom' : 'Default') + ' (gabungan 2-3 & 4-7): A ' + pct.A + ' / B ' + pct.B + ' / C ' + pct.C + ' / D ' + pct.D + ' / E ' + pct.E + ' (%)';
        $('#tsNormalScaleInfo').text(quotaTag).show();

        renderNormalChart(s);
        renderFormula(s);
    }

    function renderFormula(s) {
        let $body = $('#tsFormulaBody');
        if (!$body.length) return;

        if (!s) {
            $body.html('<div class="ts-fx-status ts-fx-status-skipped">ℹ Ringkasan belum dimuat.</div>');
            return;
        }

        function line(label, text) {
            return '<div><span class="ts-fx-label">' + label + '</span>' + text + '</div>';
        }

        let html = '';
        html += line('Populasi', '= Dihitung terpisah per group: Level 2&ndash;3 dan Level 4&ndash;7 (Level 4&ndash;5 dan 6&ndash;7 digabung). Ranking memakai populasi penuh masing-masing group (tidak terpengaruh filter).');
        html += line('PA Score Normalize ', '= Diurutkan dari score paling atas ke bawah; peringkat 1 = skor tertinggi.');
        html += line('Kuota Final ', '= Populasi (N) Dikalikan dengan persentase tiap grade, lalu dibulatkan sesuai dengan angka utama.');
        html += line('Terkait Sisa Kuota ', '= Jika totalnya belum pas dengan populasi, berikan sisa kuota ke grade yang memiliki desimal terbesar. Jika angka desimalnya sama, Sisa kuota akan diberikan ke grade yang lebih tinggi.');

        let groups = s.groups || {};
        let keys = ['2-3', '4-7'].filter(function (g) { return !!groups[g]; });
        if (!keys.length) {
            keys = Object.keys(groups);
        }
        if (!keys.length) {
            groups = {
                '4-7': {
                    label: 'Level 4-7 (4-5 & 6-7)',
                    n: Number(s.rank_n || 0),
                    percent: s.percent,
                    quota: s.rank_quota,
                    minimum_n: s.rank_minimum_n,
                    applied: s.rank_applied
                }
            };
            keys = ['4-7'];
        }

        let gradesOrder = ['A', 'B', 'C', 'D', 'E'];
        let gradePriority = { A: 4, B: 3, C: 2, D: 1, E: 0 };

        keys.forEach(function (key) {
            let gs = groups[key] || {};
            let n = Number(gs.n || 0);
            let pct = gs.percent || { E: 5, D: 10, C: 65, B: 15, A: 5 };
            let quota = gs.quota || {};
            let label = gs.label || nsGroupLabel(key);

            html += '<br><div class="ts-fx-sub"><b>Skema perhitungan ' + escapeHtml(label) + '</b></div>';
            html += '<div class="ts-fx-sub"><b>' + escapeHtml(label) + ' (N = ' + n + ')</b></div>';
            if (n <= 0) {
                html += '<div class="ts-fx-sub">Tidak ada peserta pada group ini.</div>';
                return;
            }
            if (gs.applied === false) {
                html += '<div class="ts-fx-sub">Normalize tidak diterapkan karena jumlah peserta &lt; ' + Number(gs.minimum_n || 3) + ' orang; PA Scale Normalize mengikuti PA Scale Original.</div>';
                return;
            }

            let exactQ = {}, floorQ = {}, remQ = {};
            gradesOrder.forEach(function (g) {
                let p = Number(pct[g] || 0);
                exactQ[g] = n * p / 100;
                floorQ[g] = Math.floor(exactQ[g]);
                remQ[g] = exactQ[g] - floorQ[g];
            });
            let sumFloor = gradesOrder.reduce(function (a, g) { return a + floorQ[g]; }, 0);
            let leftQuota = n - sumFloor;
            let orderQ = gradesOrder.slice().sort(function (a, b) {
                if (remQ[b] !== remQ[a]) return remQ[b] - remQ[a];
                return gradePriority[b] - gradePriority[a];
            });
            let winners = orderQ.slice(0, Math.max(0, leftQuota));

            html += '<div class="ts-fx-sub"><b>Langkah 1: Hitung Kuota Awal</b></div>';
            gradesOrder.forEach(function (g) {
                html += '<div class="ts-fx-sub">Grade ' + g + ' (' + Number(pct[g] || 0) + '%) : ' + n + ' &times; ' + Number(pct[g] || 0) + '% = ' + exactQ[g].toFixed(2) + ' &rarr; ' + floorQ[g] + ' orang</div>';
            });
            html += '<div class="ts-fx-sub">(Baru terpakai ' + sumFloor + ' orang, masih ada ' + leftQuota + ' sisa kuota)</div>';
            if (leftQuota > 0) {
                html += '<div class="ts-fx-sub"><b>Langkah 2: Bagi ' + leftQuota + ' Sisa Kuota (lihat angka di belakang koma)</b></div>';
                winners.forEach(function (g) {
                    let tieLosers = gradesOrder.filter(function (x) {
                        return x !== g && winners.indexOf(x) === -1 && gradePriority[x] < gradePriority[g] && remQ[x].toFixed(2) === remQ[g].toFixed(2);
                    });
                    let tieNote = tieLosers.length ? ' (menang dari ' + tieLosers.join(', ') + ' karena grade lebih tinggi)' : '';
                    html += '<div class="ts-fx-sub">Grade ' + g + ' (koma ' + remQ[g].toFixed(2) + ') dapat +1' + tieNote + ' &rarr; ' + Number(quota[g] || 0) + ' orang</div>';
                });
            } else {
                html += '<div class="ts-fx-sub">(Tidak ada sisa kuota; total kuota awal sudah pas dengan N.)</div>';
            }
            html += '<div class="ts-fx-sub"><b>Hasil Akhir Kuota</b></div>';
            html += '<div class="ts-fx-sub">Grade A: ' + Number(quota.A || 0) + ' &middot; Grade B: ' + Number(quota.B || 0) + ' &middot; Grade C: ' + Number(quota.C || 0) + ' &middot; Grade D: ' + Number(quota.D || 0) + ' &middot; Grade E: ' + Number(quota.E || 0) + ' &mdash; <b>Total: ' + n + ' orang</b></div>';
            if (gs.minimum_n) {
                html += '<div class="ts-fx-sub">*Minimum peserta group: ' + Number(gs.minimum_n) + ' orang.</div>';
            }
        });

        $body.html(html);
    }

    function renderNormalChart(s) {
        let el = document.querySelector('#tsNormalChart');

        if (!s || typeof ApexCharts === 'undefined' || !el) {
            if (tsChart) { tsChart.destroy(); tsChart = null; }
            $('#tsNormalChart').addClass('d-none');
            $('#tsNormalChartEmpty').removeClass('d-none');
            return;
        }

        $('#tsNormalChart').removeClass('d-none');
        $('#tsNormalChartEmpty').addClass('d-none');

        let grades = ['E', 'D', 'C', 'B', 'A'];
        let pick = function (obj, g) { return Number((obj || {})[g] || 0); };
        let series = [
            { name: 'PA SCALE ORIGINAL', data: grades.map(function (g) { return pick(s.converted, g); }) },
            { name: 'PA SCALE NORMALIZE', data: grades.map(function (g) { return pick(s.rank, g); }) },
            { name: 'PA SCALE CALIBRATED', data: grades.map(function (g) { return pick(s.final, g); }) },
            { name: 'KUOTA FINAL', data: grades.map(function (g) { return pick(s.rank_quota, g); }) }
        ];
        let colors = ['#4472C4', '#7030A0', '#BF9000', '#C00000'];

        let options = {
            chart: { type: 'line', height: 320, toolbar: { show: false }, animations: { speed: 300 } },
            series: series,
            colors: colors,
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 5, strokeWidth: 2, hover: { size: 7 } },
            xaxis: { categories: grades, title: { text: 'PA Scale', style: { fontSize: '12px' } } },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                title: { text: 'Jumlah Orang', style: { fontSize: '12px' } },
                labels: { formatter: function (v) { return Number.isInteger(v) ? v : Number(v).toFixed(1); } }
            },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px' },
            dataLabels: { enabled: false },
            grid: { borderColor: '#e0e0e0' },
            tooltip: { shared: true, intersect: false }
        };

        if (tsChart) {
            tsChart.updateOptions({ series: series, colors: colors, xaxis: { categories: grades } });
        } else {
            tsChart = new ApexCharts(el, options);
            tsChart.render();
        }
    }

    function renderPager(meta) {
        let p = parseInt(meta.page || 1), last = parseInt(meta.last_page || 1), total = parseInt(meta.total || 0);
        if (total === 0) { $('#tsPageLabel').text(''); $('#tsPagination').html(''); return; }
        let prev = p > 1 ? '<button class="btn btn-sm btn-outline-primary mr-1 ts-page-btn" data-page="' + (p - 1) + '">Prev</button>' : '<button class="btn btn-sm btn-outline-secondary mr-1" disabled>Prev</button>';
        let next = p < last ? '<button class="btn btn-sm btn-outline-primary ts-page-btn" data-page="' + (p + 1) + '">Next</button>' : '<button class="btn btn-sm btn-outline-secondary" disabled>Next</button>';
        let opts = '';
        [10, 20, 50, 100].forEach(function (n) { opts += '<option value="' + n + '"' + (n === perPage ? ' selected' : '') + '>' + n + '</option>'; });
        $('#tsPageLabel').text('Halaman ' + p + ' dari ' + last + ' (' + total + ' baris)');
        $('#tsPagination').html('<div class="d-flex align-items-center" style="gap:.5rem;"><span class="text-muted" style="font-size:.82rem;">Tampilkan</span><select id="tsPerPageSel" class="form-control form-control-sm" style="width:auto;">' + opts + '</select><div>' + prev + next + '</div></div>');
    }

    // ===== Setting kuota Final (modal) =====
    let nsCountCharts = {};
    let nsData = null;
    let nsGroups = {};
    let nsColors = { A: '#1b5e20', B: '#0d47a1', C: '#e65100', D: '#a80000', E: '#455a64' };
    let nsTints = { A: '#e8f5e9', B: '#e3f0ff', C: '#fff3e0', D: '#fdecea', E: '#eceff1' };
    let nsGroupMeta = { '2-3': { label: 'Level 2–3', note: '' }, '4-7': { label: 'Level 4–7', note: '4–5 & 6–7' } };

    function nsGroupLabel(group) {
        return group === '2-3' ? 'Level 2-3' : 'Level 4-7 (4-5 & 6-7)';
    }

    function nsSummaryGroupStat(group) {
        if (!tsLastSummary || !tsLastSummary.groups) return null;
        if (String(tsLastSummary.periode) !== String($('#nsPeriode').val())) return null;
        return tsLastSummary.groups[group] || null;
    }

    function nsField(group, selector) {
        return $('.ns-group-block[data-group="' + group + '"] ' + selector);
    }

    function nsLargestRemainder(n, pct) {
        let grades = ['A', 'B', 'C', 'D', 'E'];
        let priority = { A: 4, B: 3, C: 2, D: 1, E: 0 };
        let quota = {}, rem = {};
        grades.forEach(function (g) {
            let exact = Math.max(0, n) * (Number(pct[g] || 0) / 100);
            quota[g] = Math.floor(exact);
            rem[g] = exact - quota[g];
        });
        let left = Math.max(0, n) - grades.reduce(function (a, g) { return a + quota[g]; }, 0);
        let order = grades.slice().sort(function (a, b) {
            if (rem[b] !== rem[a]) return rem[b] - rem[a];
            return priority[b] - priority[a];
        });
        let i = 0;
        while (left > 0) {
            quota[order[i % order.length]]++;
            left--;
            i++;
        }
        return quota;
    }

    function nsCollect(group) {
        let pct = {};
        ['A', 'B', 'C', 'D'].forEach(function (g) {
            let v = parseInt(nsField(group, '.ns-num[data-grade="' + g + '"]').val(), 10);
            if (isNaN(v)) v = 0;
            pct[g] = Math.max(0, Math.min(100, v));
        });
        pct.E = Math.max(0, 100 - pct.A - pct.B - pct.C - pct.D);
        return pct;
    }

    function nsClamp(group, changed) {
        let vals = {};
        ['A', 'B', 'C', 'D'].forEach(function (g) {
            let v = parseInt(nsField(group, '.ns-num[data-grade="' + g + '"]').val(), 10);
            vals[g] = isNaN(v) ? 0 : Math.max(0, Math.min(100, v));
        });
        let others = 0;
        ['A', 'B', 'C', 'D'].forEach(function (g) { if (g !== changed) others += vals[g]; });
        if (vals[changed] + others > 100) vals[changed] = Math.max(0, 100 - others);
        nsField(group, '.ns-num[data-grade="' + changed + '"]').val(vals[changed]);
        nsField(group, '.ns-range[data-grade="' + changed + '"]').val(vals[changed]);
    }

    function nsApplyGroup(group, data) {
        data = data || {};
        let pct = data.percent || { E: 5, D: 10, C: 65, B: 15, A: 5 };

        ['A', 'B', 'C', 'D'].forEach(function (g) {
            nsField(group, '.ns-num[data-grade="' + g + '"]').val(pct[g]);
            nsField(group, '.ns-range[data-grade="' + g + '"]').val(pct[g]);
        });
        nsField(group, '.ns-num-e').val(pct.E);
        nsField(group, '.ns-min').val(Number(data.minimum_peserta || 3));
        nsField(group, '.ns-meta').text((data.is_custom ? 'Custom' : 'Default') +
            (data.updated_by ? (' · oleh ' + data.updated_by + (data.updated_at ? ' · ' + data.updated_at : '')) : ''));
        nsUpdatePreview(group);
    }

    function nsAllValid() {
        return ['2-3', '4-7'].every(function (group) {
            let pct = nsCollect(group);
            return (pct.A + pct.B + pct.C + pct.D) <= 100;
        });
    }

    function nsUpdatePreview(group) {
        let pct = nsCollect(group);
        let total = pct.A + pct.B + pct.C + pct.D;
        let ok = total <= 100;
        nsField(group, '.ns-num-e').val(pct.E);
        nsField(group, '.ns-sum-badge')
            .text('A+B+C+D = ' + total + '% · E = ' + pct.E + '%')
            .css({ background: ok ? '#e8f5e9' : '#fdecea', color: ok ? '#1b5e20' : '#a80000' });
        $('#btnNsSave').prop('disabled', !nsAllValid());

        let minP = Math.max(1, parseInt(nsField(group, '.ns-min').val(), 10) || 3);

        let chips = '';
        ['A', 'B', 'C', 'D', 'E'].forEach(function (g) {
            chips += '<span style="display:inline-flex;align-items:center;gap:.3rem;">' +
                '<i style="width:10px;height:10px;border-radius:2px;background:' + nsColors[g] + ';display:inline-block;"></i>' +
                '<b style="color:' + nsColors[g] + '">' + g + '</b> ' + pct[g] + '%</span>';
        });
        nsField(group, '.ns-chart-legend').html(chips);

        let html = '<div><span class="text-muted">Persen kuota:</span> ' +
            '<b style="color:' + nsColors.A + '">A</b> ' + pct.A + '% &middot; ' +
            '<b style="color:' + nsColors.B + '">B</b> ' + pct.B + '% &middot; ' +
            '<b style="color:' + nsColors.C + '">C</b> ' + pct.C + '% &middot; ' +
            '<b style="color:' + nsColors.D + '">D</b> ' + pct.D + '% &middot; ' +
            '<b style="color:' + nsColors.E + '">E</b> ' + pct.E + '%</div>';

        let q100 = nsLargestRemainder(100, pct);
        html += '<div><span class="text-muted">Kuota bila N = 100 orang:</span> A ' + q100.A + ' &middot; B ' + q100.B + ' &middot; C ' + q100.C + ' &middot; D ' + q100.D + ' &middot; E ' + q100.E + '</div>';

        let groupStat = nsSummaryGroupStat(group);
        let groupN = groupStat ? Number(groupStat.n || 0) : 0;
        if (groupN > 0) {
            let qn = nsLargestRemainder(groupN, pct);
            html += '<div><span class="text-muted">Kuota untuk N = ' + groupN + ' orang (' + escapeHtml(nsGroupLabel(group)) + '):</span> A ' + qn.A + ' &middot; B ' + qn.B + ' &middot; C ' + qn.C + ' &middot; D ' + qn.D + ' &middot; E ' + qn.E + '</div>';
        }

        html += '<div><span class="text-muted">Normalize diterapkan bila jumlah peserta &ge;</span> <b>' + minP + ' orang</b>; jika tidak, PA Scale Normalize mengikuti PA Scale Original.</div>';
        if (groupN >= minP) {
            html += '<div class="text-muted" style="font-style:italic;">*Catatan: fitur minimum peserta tidak berlaku karena populasi group (N = ' + groupN + ' orang) selalu berada di atas ambang tersebut.</div>';
        }
        nsField(group, '.ns-cutoff-info').html(html);

        nsRenderCountChart(group, pct);
        renderMergedQuota();
    }

    function renderMergedQuota() {
        let el = document.getElementById('nsMergedQuota');
        if (!el) return;

        let groupKeys = ['2-3', '4-7'];
        let data = {};
        groupKeys.forEach(function (key) {
            let pct = nsCollect(key);
            let stat = nsSummaryGroupStat(key);
            let n = stat ? Number(stat.n || 0) : 0;
            data[key] = {
                N: n,
                pct: pct,
                n100: nsLargestRemainder(100, pct),
                nN: nsLargestRemainder(n, pct)
            };
        });

        let grades = ['A', 'B', 'C', 'D', 'E'];
        function sum(obj) {
            return grades.reduce(function (t, g) { return t + Number(obj[g] || 0); }, 0);
        }
        function fmt1(v) { return Number(v).toFixed(1).replace('.', ','); }

        let allN = groupKeys.reduce(function (t, key) { return t + data[key].N; }, 0);
        let allQ = {};
        let allPct = {};
        grades.forEach(function (gr) {
            allQ[gr] = groupKeys.reduce(function (t, key) { return t + data[key].nN[gr]; }, 0);
            allPct[gr] = allN ? (allQ[gr] / allN) * 100 : 0;
        });

        let peak = {};
        groupKeys.forEach(function (key) {
            let d = data[key];
            peak[key] = grades.reduce(function (a, b) { return d.nN[b] > d.nN[a] ? b : a; }, grades[0]);
        });
        let peakAll = grades.reduce(function (a, b) { return allQ[b] > allQ[a] ? b : a; }, grades[0]);

        let h = '<table><thead>' +
            '<tr class="grp"><th rowspan="2" style="border-bottom:1px solid #eef0f3;background:#fafbfc;width:64px">Grade</th>';
        groupKeys.forEach(function (key) {
            let meta = nsGroupMeta[key] || { label: nsGroupLabel(key), note: '' };
            h += '<th colspan="4" class="sep">' + escapeHtml(meta.label) +
                '<small>' + (meta.note ? escapeHtml(meta.note) + ' · ' : '') + data[key].N + ' orang</small></th>';
        });
        h += '<th colspan="3" class="sep th-all">Gabungan seluruh level<small>Level 2–7 · ' + allN + ' orang</small></th></tr>' +
            '<tr class="sub">';
        groupKeys.forEach(function (key) {
            h += '<th class="sep">Persen</th><th>Sebaran</th><th>N=100</th><th>N=' + data[key].N + '</th>';
        });
        h += '<th class="sep">Kuota</th><th>Persen</th><th>Sebaran</th></tr></thead><tbody>';

        grades.forEach(function (gr) {
            h += '<tr><td><span class="ts-pa ts-pa-' + gr + '">' + gr + '</span></td>';
            groupKeys.forEach(function (key) {
                let d = data[key];
                let isPeak = peak[key] === gr && d.nN[gr] > 0;
                let tint = isPeak ? ' ns-peak" style="--tint:' + nsTints[gr] : '';
                h += '<td class="ns-pct sep">' + Number(d.pct[gr] || 0) + '%</td>' +
                    '<td class="ns-bar"><div class="track"><span class="fill" style="width:' + Math.min(Number(d.pct[gr] || 0), 100) + '%;background:' + nsColors[gr] + '"></span></div></td>' +
                    '<td class="ns-n100">' + d.n100[gr] + '</td>' +
                    '<td class="ns-n' + tint + '">' + d.nN[gr] + '</td>';
            });
            let tintAll = (gr === peakAll && allQ[gr] > 0) ? ' ns-peak" style="--tint:' + nsTints[gr] : '';
            h += '<td class="ns-all ns-n sep' + tintAll + '">' + allQ[gr] + '</td>' +
                '<td class="ns-all pct' + ((gr === peakAll && allQ[gr] > 0) ? ' ns-peak" style="--tint:' + nsTints[gr] : '') + '">' + fmt1(allPct[gr]) + '%</td>' +
                '<td class="ns-all ns-bar"><div class="track"><span class="fill" style="width:' + Math.min(allPct[gr], 100) + '%;background:' + nsColors[gr] + '"></span></div></td></tr>';
        });

        h += '</tbody><tfoot><tr><td class="lbl">Total</td>';
        groupKeys.forEach(function (key) {
            let d = data[key];
            h += '<td class="sep">' + sum(d.pct) + '%</td><td></td><td>' + sum(d.n100) + '</td><td>' + sum(d.nN) + '</td>';
        });
        h += '<td class="sep">' + sum(allQ) + '</td><td>' + fmt1(sum(allPct)) + '%</td><td></td></tr></tfoot></table>';

        el.innerHTML = h;
    }

    function nsRenderCountChart(group, pct) {
        let el = document.querySelector('.ns-group-block[data-group="' + group + '"] .ns-count-chart');
        if (!el || typeof ApexCharts === 'undefined') return;

        let grades = ['E', 'D', 'C', 'B', 'A'];
        let quota = nsLargestRemainder(100, pct);
        let data = grades.map(function (g) { return quota[g]; });
        let discrete = grades.map(function (g, i) {
            return { seriesIndex: 0, dataPointIndex: i, fillColor: nsColors[g], strokeColor: '#fff', size: 7 };
        });

        let allSame = grades.every(function (g) { return quota[g] === quota[grades[0]]; });
        let hint = nsField(group, '.ns-count-hint');
        if (allSame) {
            hint.text('Total 100 orang · rata di semua grade = ' + quota[grades[0]] + ' orang');
        } else {
            let topGrade = grades[0];
            grades.forEach(function (g) { if (quota[g] > quota[topGrade]) topGrade = g; });
            hint.text('Total 100 orang · puncak di ' + topGrade + ' = ' + quota[topGrade] + ' orang');
        }

        let options = {
            chart: { type: 'line', height: 240, toolbar: { show: false }, animations: { speed: 200 } },
            series: [{ name: 'Jumlah orang (N = 100)', data: data }],
            colors: ['#a80000'],
            stroke: { curve: 'straight', width: 2.5 },
            markers: { size: 7, strokeWidth: 2, strokeColors: '#fff', hover: { size: 9 }, discrete: discrete },
            dataLabels: {
                enabled: true,
                offsetY: -10,
                formatter: function (v) { return Number(v) + ' org'; },
                style: { fontSize: '11px', fontWeight: 700, colors: ['#424242'] }
            },
            xaxis: {
                categories: grades,
                title: { text: 'PA Scale (grade)', style: { fontSize: '11px' } }
            },
            yaxis: {
                min: 0, max: 100, tickAmount: 5,
                title: { text: 'Jumlah orang (N = 100)', style: { fontSize: '11px' } },
                labels: { formatter: function (v) { return Math.round(v); } }
            },
            grid: { borderColor: '#e0e0e0' },
            legend: { show: false },
            tooltip: {
                y: { formatter: function (v) { return Number(v) + ' orang (' + Number(v) + '%)'; } }
            }
        };

        if (nsCountCharts[group]) {
            nsCountCharts[group].updateOptions(options, true, true);
        } else {
            nsCountCharts[group] = new ApexCharts(el, options);
            nsCountCharts[group].render();
        }
    }

    function nsLoad(periode) {
        $('.ns-meta').text('Memuat...');
        $.get('/hr/pms-total-scoring/normal-scale', { periode: periode }, function (res) {
            nsData = res;
            nsGroups = res.groups || {};
            let sel = $('#nsPeriode');
            sel.empty();
            let opts = res.periodes || [];
            if (!opts.length) opts = [res.periode];
            opts.forEach(function (p) {
                sel.append($('<option>', { value: p, text: p, selected: p === res.periode }));
            });
            ['2-3', '4-7'].forEach(function (group) {
                nsApplyGroup(group, nsGroups[group] || {});
            });
        }).fail(function () {
            $('.ns-meta').text('Gagal memuat pengaturan.');
        });
    }

    $(document).on('click', '#btnNormalScale', function () {
        $('#normalScaleModal').modal('show');
    });

    $(document).on('shown.bs.modal', '#normalScaleModal', function () {
        nsLoad($('#tsFltPeriode').val() || tsDefaultPeriode);
    });

    $(document).on('change', '#nsPeriode', function () { nsLoad($(this).val()); });

    $(document).on('input change', '.ns-range', function () {
        let group = $(this).data('group');
        let g = $(this).data('grade');
        nsField(group, '.ns-num[data-grade="' + g + '"]').val($(this).val());
        nsClamp(group, g);
        nsUpdatePreview(group);
    });

    $(document).on('input change', '.ns-num', function () {
        let group = $(this).data('group');
        let g = $(this).data('grade');
        let v = parseInt($(this).val(), 10);
        if (isNaN(v)) v = 0;
        v = Math.max(0, Math.min(100, v));
        nsField(group, '.ns-range[data-grade="' + g + '"]').val(v);
        nsClamp(group, g);
        nsUpdatePreview(group);
    });

    $(document).on('input change', '.ns-min', function () {
        nsUpdatePreview($(this).data('group'));
    });

    $(document).on('click', '#btnNsSave', function () {
        let groups = {};
        let invalid = null;

        ['2-3', '4-7'].forEach(function (group) {
            let pct = nsCollect(group);
            if ((pct.A + pct.B + pct.C + pct.D) > 100 && invalid === null) {
                invalid = nsGroupLabel(group);
            }
            groups[group] = {
                grade_a: pct.A,
                grade_b: pct.B,
                grade_c: pct.C,
                grade_d: pct.D,
                minimum_peserta: Math.max(1, parseInt(nsField(group, '.ns-min').val(), 10) || 3)
            };
        });

        if (invalid !== null) {
            Swal.fire('Perhatian', 'Total A + B + C + D ' + invalid + ' tidak boleh lebih dari 100%.', 'warning');
            return;
        }

        let $btn = $(this).prop('disabled', true).text('Menyimpan...');
        $.post('/hr/pms-total-scoring/normal-scale', {
            periode: $('#nsPeriode').val(),
            groups: groups
        }, function (res) {
            nsData = res;
            if (res.groups) {
                nsGroups = res.groups;
                ['2-3', '4-7'].forEach(function (group) {
                    nsApplyGroup(group, nsGroups[group] || {});
                });
            }
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
            loadList();
        }).fail(function (xhr) {
            Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan kuota Final.', 'error');
        }).always(function () {
            $btn.prop('disabled', false).text('Simpan');
        });
    });

    $(document).on('click', '#btnNsReset', function () {
        let periode = $('#nsPeriode').val();
        Swal.fire({
            icon: 'question',
            title: 'Kembalikan default?',
            text: 'Kuota Final Level 2-3 dan Level 4-7 (4-5 & 6-7) periode ' + periode + ' akan dikembalikan ke default A 5%, B 15%, C 65%, D 10%, E 5%.',
            showCancelButton: true,
            confirmButtonText: 'Ya, kembalikan',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed && !r.value) return;
            $.post('/hr/pms-total-scoring/normal-scale/reset', { periode: periode, all_groups: 1 }, function (res) {
                nsData = res;
                if (res.groups) {
                    nsGroups = res.groups;
                    ['2-3', '4-7'].forEach(function (group) {
                        nsApplyGroup(group, nsGroups[group] || {});
                    });
                }
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
                loadList();
            }).fail(function (xhr) {
                Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal mengembalikan default.', 'error');
            });
        });
    });

    let tsLockState = null;

    function tsLockRender(res) {
        tsLockState = res;
        let sel = $('#tsLockPeriode');
        if (!sel.find('option').length) {
            let opts = res.periodes || [];
            if (!opts.length && res.periode) opts = [res.periode];
            opts.forEach(function (p) {
                sel.append($('<option>', { value: p, text: p }));
            });
        }
        sel.val(res.periode);
        let locked = !!res.is_locked;
        $('#tsLockBadge')
            .text(locked ? 'TERKUNCI' : 'Terbuka')
            .css({ background: locked ? '#fdecea' : '#e8f5e9', color: locked ? '#a80000' : '#1b5e20' });
        $('#btnLockToggle')
            .prop('disabled', false)
            .toggleClass('btn-danger', !locked)
            .toggleClass('btn-success', locked)
            .html(locked
                ? '<i class="fas fa-lock-open"></i> Buka Kunci'
                : '<i class="fas fa-lock"></i> Kunci PA Scale CALIBRATED');
        let meta = '';
        if (locked) {
            meta = 'Dikunci oleh ' + (res.locked_by || '-') + (res.locked_at ? ' · ' + res.locked_at : '');
        } else if (res.updated_by) {
            meta = 'Terakhir diubah oleh ' + res.updated_by + (res.updated_at ? ' · ' + res.updated_at : '');
        }
        $('#tsLockMeta').text(meta);
    }

    function tsLockLoad(periode) {
        $('#tsLockMeta').text('Memuat...');
        $.get('/hr/pms-total-scoring/locking-pascale', { periode: periode || '' }, function (res) {
            tsLockRender(res);
        }).fail(function () {
            $('#tsLockMeta').text('Gagal memuat status locking.');
        });
    }

    $(document).on('change', '#tsLockPeriode', function () {
        tsLockLoad($(this).val());
    });

    $(document).on('click', '#btnLockToggle', function () {
        if (!tsLockState) return;
        let locked = !!tsLockState.is_locked;
        let periode = String($('#tsLockPeriode').val() || '');
        if (periode === '') return;
        Swal.fire({
            icon: 'question',
            title: locked ? 'Buka kunci PA Scale Calibrated?' : 'Kunci PA Scale Calibrated?',
            text: locked
                ? ('Input PA Scale Calibrated periode ' + periode + ' akan dapat diubah kembali.')
                : ('Input PA Scale Calibrated periode ' + periode + ' akan dikunci untuk semua user.'),
            showCancelButton: true,
            confirmButtonText: locked ? 'Ya, buka kunci' : 'Ya, kunci',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed && !r.value) return;
            $('#btnLockToggle').prop('disabled', true);
            $.post('/hr/pms-total-scoring/locking-pascale', {
                periode: periode,
                is_locked: locked ? 0 : 1
            }, function (res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
                tsLockRender(res);
                loadList();
            }).fail(function (xhr) {
                $('#btnLockToggle').prop('disabled', false);
                Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan status locking.', 'error');
            });
        });
    });

    let tsLockIppState = null;

    function tsLockIppBadge(locked) {
        return '<span class="ts-badge" style="background:' + (locked ? '#fdecea' : '#e8f5e9') + ';color:' + (locked ? '#a80000' : '#1b5e20') + ';">' +
            (locked ? 'TERKUNCI' : 'Terbuka') + '</span>';
    }

    function tsLockIppRender(res) {
        tsLockIppState = res;
        let sel = $('#tsLockIppTahapan');
        sel.empty();
        let opts = res.tahapan_options || [];
        let labels = res.tahapan_labels || {};
        if (!opts.length && res.tahapan) opts = [res.tahapan];
        opts.forEach(function (t) {
            sel.append($('<option>', { value: t, text: labels[t] || t }));
        });
        if (res.tahapan) sel.val(res.tahapan);

        let anyLocked = !!res.any_locked;
        $('#tsLockIppBadge')
            .text(anyLocked ? 'TERKUNCI' : 'Terbuka')
            .css({ background: anyLocked ? '#fdecea' : '#e8f5e9', color: anyLocked ? '#a80000' : '#1b5e20' });

        let canManage = !!res.can_manage;
        let cols = res.columns || {};
        let tbody = '';
        Object.keys(cols).forEach(function (key) {
            let c = cols[key] || {};
            let locked = !!c.is_locked;
            let action = canManage
                ? '<button type="button" class="btn btn-sm ' + (locked ? 'btn-success' : 'btn-danger') + ' btn-ts-lock-ipp" ' +
                  'data-column="' + key + '" data-locked="' + (locked ? 1 : 0) + '">' +
                  (locked ? '<i class="fas fa-lock-open"></i> Buka Kunci' : '<i class="fas fa-lock"></i> Kunci') + '</button>'
                : '<span class="text-muted">-</span>';
            tbody += '<tr>' +
                '<td>' + escapeHtml(c.label || key) + '</td>' +
                '<td class="text-center">' + tsLockIppBadge(locked) + '</td>' +
                '<td class="text-center">' + action + '</td>' +
                '</tr>';
        });
        $('#tsLockIppBody').html(tbody);

        let meta = '';
        if (res.locked_by && res.locked_at) {
            meta = 'Terakhir dikunci oleh ' + res.locked_by + ' · ' + res.locked_at;
        } else if (res.updated_by) {
            meta = 'Terakhir diubah oleh ' + res.updated_by + (res.updated_at ? ' · ' + res.updated_at : '');
        }
        $('#tsLockIppMeta').text(meta);
    }

    function tsLockIppLoad(tahapan) {
        $('#tsLockIppMeta').text('Memuat...');
        $.get('/hr/pms-total-scoring/locking-ipp', { tahapan: tahapan || '' }, function (res) {
            tsLockIppRender(res);
        }).fail(function () {
            $('#tsLockIppMeta').text('Gagal memuat status locking IPP.');
            $('#tsLockIppBody').html('<tr><td colspan="3" class="text-center text-muted">Gagal memuat data.</td></tr>');
        });
    }

    $(document).on('change', '#tsLockIppTahapan', function () {
        tsLockIppLoad($(this).val());
    });

    $(document).on('click', '.btn-ts-lock-ipp', function () {
        if (!tsLockIppState) return;
        let $btn = $(this);
        let column = String($btn.data('column') || '');
        let locked = String($btn.data('locked')) === '1';
        let tahapan = String($('#tsLockIppTahapan').val() || '');
        if (tahapan === '' || column === '') return;
        let cols = tsLockIppState.columns || {};
        let label = (cols[column] && cols[column].label) || column;
        let tahapanText = ((tsLockIppState.tahapan_labels || {})[tahapan]) || tahapan;
        Swal.fire({
            icon: 'question',
            title: (locked ? 'Buka kunci ' : 'Kunci ') + label + '?',
            text: locked
                ? ('Kolom ' + label + ' tahapan ' + tahapanText + ' akan dapat diubah kembali.')
                : ('Kolom ' + label + ' tahapan ' + tahapanText + ' akan dikunci (Admin HR dikecualikan).'),
            showCancelButton: true,
            confirmButtonText: locked ? 'Ya, buka kunci' : 'Ya, kunci',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed && !r.value) return;
            $btn.prop('disabled', true);
            $.post('/hr/pms-total-scoring/locking-ipp', {
                tahapan: tahapan,
                column: column,
                is_locked: locked ? 0 : 1
            }, function (res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
                tsLockIppRender(res);
            }).fail(function (xhr) {
                $btn.prop('disabled', false);
                Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan status locking IPP.', 'error');
            });
        });
    });

    let tsLockPkState = null;

    function tsLockPkBadge(locked) {
        return '<span class="ts-badge" style="background:' + (locked ? '#fdecea' : '#e8f5e9') + ';color:' + (locked ? '#a80000' : '#1b5e20') + ';">' +
            (locked ? 'TERKUNCI' : 'Terbuka') + '</span>';
    }

    function tsLockPkRender(res) {
        tsLockPkState = res;
        let sel = $('#tsLockPkPeriode');
        sel.empty();
        let opts = res.periodes || [];
        if (!opts.length && res.periode) opts = [res.periode];
        opts.forEach(function (p) {
            sel.append($('<option>', { value: p, text: 'Periode ' + p }));
        });
        if (res.periode) sel.val(res.periode);

        let anyLocked = !!res.any_locked;
        $('#tsLockPkBadge')
            .text(anyLocked ? 'TERKUNCI' : 'Terbuka')
            .css({ background: anyLocked ? '#fdecea' : '#e8f5e9', color: anyLocked ? '#a80000' : '#1b5e20' });

        let canManage = !!res.can_manage;
        let cols = res.columns || {};
        let tbody = '';
        Object.keys(cols).forEach(function (key) {
            let c = cols[key] || {};
            let locked = !!c.is_locked;
            let action = canManage
                ? '<button type="button" class="btn btn-sm ' + (locked ? 'btn-success' : 'btn-danger') + ' btn-ts-lock-pk" ' +
                  'data-column="' + key + '" data-locked="' + (locked ? 1 : 0) + '">' +
                  (locked ? '<i class="fas fa-lock-open"></i> Buka Kunci' : '<i class="fas fa-lock"></i> Kunci') + '</button>'
                : '<span class="text-muted">-</span>';
            tbody += '<tr>' +
                '<td>' + escapeHtml(c.label || key) + '</td>' +
                '<td class="text-center">' + tsLockPkBadge(locked) + '</td>' +
                '<td class="text-center">' + action + '</td>' +
                '</tr>';
        });
        $('#tsLockPkBody').html(tbody);

        let meta = '';
        if (res.locked_by && res.locked_at) {
            meta = 'Terakhir dikunci oleh ' + res.locked_by + ' · ' + res.locked_at;
        } else if (res.updated_by) {
            meta = 'Terakhir diubah oleh ' + res.updated_by + (res.updated_at ? ' · ' + res.updated_at : '');
        }
        $('#tsLockPkMeta').text(meta);
    }

    function tsLockPkLoad(periode) {
        $('#tsLockPkMeta').text('Memuat...');
        $.get('/hr/pms-total-scoring/locking-pk', { periode: periode || '' }, function (res) {
            tsLockPkRender(res);
        }).fail(function () {
            $('#tsLockPkMeta').text('Gagal memuat status locking PK.');
            $('#tsLockPkBody').html('<tr><td colspan="3" class="text-center text-muted">Gagal memuat data.</td></tr>');
        });
    }

    $(document).on('change', '#tsLockPkPeriode', function () {
        tsLockPkLoad($(this).val());
    });

    $(document).on('click', '.btn-ts-lock-pk', function () {
        if (!tsLockPkState) return;
        let $btn = $(this);
        let column = String($btn.data('column') || '');
        let locked = String($btn.data('locked')) === '1';
        let periode = String($('#tsLockPkPeriode').val() || '');
        if (periode === '' || column === '') return;
        let cols = tsLockPkState.columns || {};
        let label = (cols[column] && cols[column].label) || column;
        Swal.fire({
            icon: 'question',
            title: (locked ? 'Buka kunci ' : 'Kunci ') + label + '?',
            text: locked
                ? ('Kolom ' + label + ' periode ' + periode + ' akan dapat diubah kembali.')
                : ('Kolom ' + label + ' periode ' + periode + ' akan dikunci untuk semua user.'),
            showCancelButton: true,
            confirmButtonText: locked ? 'Ya, buka kunci' : 'Ya, kunci',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed && !r.value) return;
            $btn.prop('disabled', true);
            $.post('/hr/pms-total-scoring/locking-pk', {
                periode: periode,
                column: column,
                is_locked: locked ? 0 : 1
            }, function (res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
                tsLockPkRender(res);
            }).fail(function (xhr) {
                $btn.prop('disabled', false);
                Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan status LOCKING PK.', 'error');
            });
        });
    });

    $(document).on('click', '.ts-page-btn', function () { page = parseInt($(this).data('page')); loadList(); });
    $(document).on('change', '#tsPerPageSel', function () { perPage = parseInt($(this).val()) || 20; page = 1; loadList(); });
    $('#tsFormFilter').on('submit', function (e) { e.preventDefault(); page = 1; loadList(); });
    $('#tsFltPeriode, #tsFltGroup, #tsFltDept, #tsFltPaScale, #tsFltGrade').on('change', function () { page = 1; loadList(); });

    $(document).on('click', 'th.ts-sortable', function (e) {
        if ($(e.target).closest('button, a, select, input').length) {
            return;
        }
        toggleSort($(this).data('sort'));
    });

    $(document).on('keydown', 'th.ts-sortable', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ' && e.key !== 'Spacebar') {
            return;
        }
        e.preventDefault();
        toggleSort($(this).data('sort'));
    });

    $('#btnExportScoring').on('click', function () {
        let params = $.param({
            periode: $('#tsFltPeriode').val() || '',
            group: $('#tsFltGroup').val() || '',
            departemen: $('#tsFltDept').val() || '',
            dept_type: $('#tsFltDept option:selected').data('type') || 'dept',
            pa_scale_type: $('#tsFltPaScale').val() || '',
            pa_scale_grade: $('#tsFltGrade').val() || '',
            search: $('#tsFltSearch').val() || '',
            sort: tsSort.key || '',
            dir: tsSort.dir || ''
        });
        var exportUrl = '/hr/pms-total-scoring/export?' + params;
        var link = document.createElement('a');
        link.href = exportUrl;
        link.target = '_blank';
        link.rel = 'noopener';
        document.body.appendChild(link);
        link.click();
        link.remove();
    });

    function tsNotifRender(state) {
        if (!state) return;
        let active = !!state.is_active;
        $('#tsNotifEmail').prop('checked', active);
        $('#tsNotifEmailBadge')
            .text(active ? 'Aktif' : 'Nonaktif')
            .css({
                background: active ? '#e8f5e9' : '#fdecea',
                color: active ? '#1b5e20' : '#a80000'
            });
        let meta = active
            ? 'Email notifikasi IPP & PK akan dibuat dan dikirim.'
            : 'Email notifikasi IPP & PK tidak dibuat/dikirim (email yang sudah antre tetap dikirim).';
        if (state.updated_by) {
            meta += ' Terakhir diubah oleh ' + state.updated_by + (state.updated_at ? ' · ' + state.updated_at : '') + '.';
        }
        $('#tsNotifEmailMeta').text(meta);
    }

    $(document).on('change', '#tsNotifEmail', function () {
        let $cb = $(this);
        let active = $cb.is(':checked');
        Swal.fire({
            icon: active ? 'question' : 'warning',
            title: active ? 'Aktifkan notifikasi email PMS?' : 'Nonaktifkan notifikasi email PMS?',
            text: active
                ? 'Seluruh email notifikasi IPP & PK akan dikirim kembali untuk aktivitas berikutnya.'
                : 'Seluruh email notifikasi IPP & PK tidak akan dibuat/dikirim. Email yang sudah antre tetap dikirim.',
            showCancelButton: true,
            confirmButtonText: active ? 'Ya, aktifkan' : 'Ya, nonaktifkan',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (!r.isConfirmed && !r.value) {
                $cb.prop('checked', !active);
                return;
            }
            $cb.prop('disabled', true);
            $.post('/hr/pms-total-scoring/notifikasi', {
                is_active: active ? 1 : 0
            }, function (res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
                tsNotifRender(res.state || {});
            }).fail(function (xhr) {
                $cb.prop('checked', !active);
                Swal.fire('Gagal', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal menyimpan kontrol notifikasi email.', 'error');
            }).always(function () {
                $cb.prop('disabled', false);
            });
        });
    });

    if ($('#tsNotifEmail').length) {
        tsNotifRender(@json($notificationControl ?? null));
    }
    if ($('#tsLockPeriode').length) {
        tsLockLoad(tsDefaultPeriode);
    }
    if ($('#tsLockIppTahapan').length) {
        tsLockIppLoad('');
    }
    if ($('#tsLockPkPeriode').length) {
        tsLockPkLoad('');
    }
    renderSortIndicators();
    loadList();
});
</script>
@endpush
