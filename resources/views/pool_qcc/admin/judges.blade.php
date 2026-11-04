@extends('pool_qcc.layout')

@section('title', 'Manajemen Juri & Manajemen - Pool QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800 gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                <a href="{{ route('pool-qcc.admin.dashboard') }}" class="hover:underline">Admin</a>
                <span>&bull;</span>
                <span class="text-slate-500">Panel Khusus</span>
                <span>&bull;</span>
                <span class="text-amber-500 font-extrabold">Juri & Manajemen</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <span>Manajemen Juri & Manajemen VIP</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                    <i class="fa-solid fa-crown mr-1"></i> Peran Khusus
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola Dewan Juri (penilai booth skala 0-100) serta Pimpinan / Manajemen (Factory Manager, Direktur) dengan bobot poin vote dinamis.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('pool-qcc.jury.scoring') }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-scale-balanced"></i>
                <span>Layar Penilaian Juri</span>
            </a>
            <a href="{{ route('pool-qcc.jury.recap') }}" class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-amber-500 text-white hover:bg-amber-600 shadow-md shadow-amber-500/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-trophy"></i>
                <span>Rekap Pemenang</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="glass-panel rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Terdaftar</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white" id="statTotalJudges">0</span>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Status Aktif</span>
                <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400" id="statActiveJudges">0</span>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Aturan Penilaian</span>
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mt-0.5">Hanya 'Juri' yang menilai booth (0-100)</span>
            </div>
        </div>
    </div>

    <!-- Scanner / Register Input Card -->
    <div class="glass-panel rounded-2xl p-6 sm:p-7 mb-8 border border-slate-200 dark:border-slate-800 shadow-md bg-gradient-to-br from-white via-white to-indigo-50/30 dark:from-slate-900 dark:via-slate-900 dark:to-indigo-950/20">
        <div class="max-w-3xl">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-indigo-600/30">
                    <i class="fa-solid fa-id-card-clip"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">Pendaftaran Juri & Manajemen VIP Baru</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Tap kartu RFID atau masukkan NIK karyawan untuk mendaftarkan Juri, Factory Manager, atau Direktur.
                    </p>
                </div>
            </div>

            <form id="judgeLookupForm" onsubmit="handleLookup(event)" class="mt-4">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-barcode absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
                        <input type="text" id="judgeScanInput" name="input" autofocus autocomplete="off"
                            placeholder="Tempelkan kartu RFID atau ketik NIK (contoh: 120000086)..."
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-white dark:bg-slate-900 border-2 border-indigo-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm font-bold placeholder:text-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 transition shadow-inner">
                    </div>
                    <button type="submit" id="btnLookup"
                        class="px-6 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-extrabold transition shadow-lg shadow-indigo-600/25 flex items-center justify-center space-x-2 cursor-pointer flex-shrink-0">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Cek Identitas</span>
                    </button>
                </div>
            </form>
            <div class="mt-2 text-[11px] text-slate-400 flex items-center space-x-2">
                <i class="fa-solid fa-circle-info text-indigo-500"></i>
                <span>Data profil (nama, foto, departemen) akan otomatis disinkronkan dari database karyawan pusat.</span>
            </div>
        </div>
    </div>

    <!-- Filter & Registered Table -->
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Juri & Manajemen Terdaftar</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar juri dan jajaran manajemen dengan hak bobot vote kuis dan penilaian booth.</p>
            </div>
            <div class="w-full sm:w-72 relative">
                <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchJudgeTable" placeholder="Cari nama, NIK, gelar..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Profil</th>
                        <th class="py-3.5 px-4">NIK / RFID</th>
                        <th class="py-3.5 px-4">Departemen</th>
                        <th class="py-3.5 px-4 text-center">Gelar / Peranan</th>
                        <th class="py-3.5 px-4 text-center">Bobot Vote</th>
                        <th class="py-3.5 px-4 text-center">Penilaian Booth</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="judgesTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    <tr>
                        <td colspan="9" class="text-center py-10 text-slate-400 text-xs">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat data juri & manajemen...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 1: KONFIRMASI PENDAFTARAN JURI / MANAJEMEN BARU          -->
<!-- ============================================================== -->
<div id="modalConfirmJudge" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl max-w-lg w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-scale-up">
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h3 class="font-extrabold text-base">Konfirmasi Juri / Manajemen</h3>
            </div>
            <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-xs transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div id="modalJudgeAvatar" class="w-20 h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border-2 border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-2xl font-black overflow-hidden flex-shrink-0 shadow-md">
                    <!-- Photo or Initials -->
                </div>
                <div class="flex-1 min-w-0">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30 uppercase tracking-wider mb-1">
                        Karyawan Teridentifikasi
                    </span>
                    <h4 id="modalJudgeName" class="text-lg font-black text-slate-900 dark:text-white truncate">-</h4>
                    <p id="modalJudgeDept" class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">-</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-5">
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nomor NIK</span>
                    <span id="modalJudgeNik" class="text-sm font-extrabold text-slate-900 dark:text-white font-mono">-</span>
                </div>
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ID Card RFID</span>
                    <span id="modalJudgeRfid" class="text-sm font-extrabold text-slate-900 dark:text-white font-mono">-</span>
                </div>
            </div>

            <!-- Gelar / Peran Dropdown -->
            <div class="mb-4">
                <label for="modalJudgeTitle" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Gelar / Peranan:
                </label>
                <select id="modalJudgeTitle" class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-bold focus:outline-none focus:border-indigo-500">
                    <option value="Juri" selected>Juri (Penilai Booth & Vote Kuis)</option>
                    <option value="Factory Manager">Factory Manager (Khusus Vote Kuis)</option>
                    <option value="Direktur">Direktur (Khusus Vote Kuis)</option>
                </select>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    Hanya gelar <strong>Juri</strong> yang berhak memberikan penilaian evaluasi booth (0-100).
                </span>
            </div>

            <!-- Bobot Nilai Vote (Dinamis / Custom) -->
            <div class="mb-5">
                <label for="modalJudgeVoteWeight" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Bobot Poin Vote Kuis:
                </label>
                <div class="flex items-center space-x-3">
                    <input type="number" id="modalJudgeVoteWeight" min="1" max="100" value="10"
                        class="w-28 px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-black text-center text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Poin per vote kuis booth benar (default: 10).</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
            <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                Batal
            </button>
            <button type="button" id="btnConfirmSave" onclick="saveConfirmedJudge()" class="px-5 py-2.5 rounded-xl text-xs font-extrabold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/30 transition flex items-center space-x-2">
                <i class="fa-solid fa-check"></i>
                <span>Konfirmasi & Daftarkan</span>
            </button>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 2: EDIT JURI / MANAJEMEN                                 -->
<!-- ============================================================== -->
<div id="modalEditJudge" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl max-w-lg w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-scale-up">
        <!-- Modal Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <h3 class="font-extrabold text-base">Edit Data Juri / Manajemen</h3>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-xs transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editJudgeForm" onsubmit="saveEditJudge(event)" class="p-6 space-y-4">
            <input type="hidden" id="editJudgeId" value="">

            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div id="editJudgeAvatar" class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950 border border-indigo-200 dark:border-indigo-800 flex items-center justify-center text-lg font-black text-indigo-600 overflow-hidden flex-shrink-0">
                    J
                </div>
                <div>
                    <h4 id="editJudgeName" class="text-base font-extrabold text-slate-900 dark:text-white leading-tight">-</h4>
                    <p id="editJudgeNik" class="text-xs text-slate-400 font-mono mt-0.5">-</p>
                </div>
            </div>

            <!-- Gelar / Peran Dropdown -->
            <div>
                <label for="editJudgeTitle" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Gelar / Peranan:
                </label>
                <select id="editJudgeTitle" required class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-bold focus:outline-none focus:border-amber-500">
                    <option value="Juri">Juri (Penilai Booth & Vote Kuis)</option>
                    <option value="Factory Manager">Factory Manager (Khusus Vote Kuis)</option>
                    <option value="Direktur">Direktur (Khusus Vote Kuis)</option>
                </select>
                <span class="text-[11px] text-slate-400 mt-1 block">
                    Peran <strong>Juri</strong> dapat menilai booth 0-100. Peran lain hanya melakukan vote kuis.
                </span>
            </div>

            <!-- Bobot Poin Vote -->
            <div>
                <label for="editJudgeVoteWeight" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Bobot Poin Vote Kuis:
                </label>
                <div class="flex items-center space-x-3">
                    <input type="number" id="editJudgeVoteWeight" min="1" max="100" required
                        class="w-28 px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-sm font-black text-center text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Poin per vote kuis booth (misal: 10, 15, 20).</span>
                </div>
            </div>

            <!-- Departemen -->
            <div>
                <label for="editJudgeDept" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Departemen:
                </label>
                <input type="text" id="editJudgeDept" class="w-full px-4 py-2 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-medium focus:outline-none focus:border-amber-500">
            </div>

            <!-- Status Aktif -->
            <div class="pt-2 flex items-center space-x-2">
                <input type="checkbox" id="editJudgeIsActive" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400">
                <label for="editJudgeIsActive" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                    Status Aktif (Dapat Melakukan Vote & Penilaian)
                </label>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="submit" id="btnSaveEdit" class="px-5 py-2.5 rounded-xl text-xs font-extrabold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/30 transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentJudges = [];
let pendingJudgeData = null;

$(document).ready(function() {
    loadJudgesData();

    // Search filter in table
    $('#searchJudgeTable').on('input', function() {
        renderTable($(this).val().toLowerCase());
    });

    // Auto-focus scanner input
    $('#judgeScanInput').focus();
});

// Load daftar juri dari backend
function loadJudgesData() {
    $.ajax({
        url: "{{ route('pool-qcc.admin.api.judges') }}",
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                currentJudges = res.data || [];
                $('#statTotalJudges').text(res.total || 0);
                $('#statActiveJudges').text(res.active_count || 0);
                renderTable();
            }
        },
        error: function(err) {
            $('#judgesTableBody').html(`
                <tr>
                    <td colspan="9" class="text-center py-8 text-rose-500 text-xs">
                        Gagal memuat data juri & manajemen.
                    </td>
                </tr>
            `);
        }
    });
}

// Render data ke tabel
function renderTable(keyword = '') {
    const $tbody = $('#judgesTableBody');
    $tbody.empty();

    let filtered = currentJudges;
    if (keyword) {
        filtered = currentJudges.filter(j => {
            return (j.name && j.name.toLowerCase().includes(keyword)) ||
                   (j.nik && j.nik.toLowerCase().includes(keyword)) ||
                   (j.title && j.title.toLowerCase().includes(keyword)) ||
                   (j.department && j.department.toLowerCase().includes(keyword)) ||
                   (j.rfid && j.rfid.toLowerCase().includes(keyword));
        });
    }

    if (filtered.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="9" class="text-center py-10 text-slate-400 text-xs">
                    <i class="fa-solid fa-inbox text-2xl mb-2 block text-slate-300 dark:text-slate-600"></i>
                    Belum ada data juri & manajemen yang cocok.
                </td>
            </tr>
        `);
        return;
    }

    filtered.forEach((j, index) => {
        // Initial avatar
        const firstInitial = (j.name || 'J').trim().charAt(0).toUpperCase();
        const avatarHtml = j.photo
            ? `<img src="${j.photo}" alt="${escapeHtml(j.name)}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shadow-sm">`
            : `<div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-sm">${firstInitial}</div>`;

        const statusBadge = j.is_active
            ? `<button onclick="toggleJudge(${j.id})" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20 transition cursor-pointer" title="Klik untuk nonaktifkan"><i class="fa-solid fa-check-circle mr-1"></i> Aktif</button>`
            : `<button onclick="toggleJudge(${j.id})" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-500/10 text-slate-500 border border-slate-500/30 hover:bg-slate-500/20 transition cursor-pointer" title="Klik untuk aktifkan"><i class="fa-solid fa-ban mr-1"></i> Nonaktif</button>`;

        // Role Badge Styling
        const titleText = j.title || 'Juri';
        let roleBadgeClass = 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800';
        let roleIcon = 'fa-scale-balanced';

        if (titleText.toLowerCase().includes('direktur')) {
            roleBadgeClass = 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800';
            roleIcon = 'fa-crown';
        } else if (titleText.toLowerCase().includes('manager')) {
            roleBadgeClass = 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800';
            roleIcon = 'fa-user-tie';
        }

        const isScorer = titleText.toLowerCase().includes('juri');
        const evalBadge = isScorer
            ? `<span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800" title="Progress evaluasi booth">${j.evaluations_count} Booth</span>`
            : `<span class="text-[11px] text-slate-400 italic">Khusus Vote Kuis</span>`;

        const tr = `
            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                <td class="py-3.5 px-4 text-center font-bold text-xs text-slate-400">${index + 1}</td>
                <td class="py-3.5 px-4">
                    <div class="flex items-center space-x-3">
                        ${avatarHtml}
                        <div>
                            <div class="font-extrabold text-slate-900 dark:text-white flex items-center space-x-1.5">
                                <span>${escapeHtml(j.name)}</span>
                            </div>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">NIK: ${escapeHtml(j.nik || '-')}</span>
                        </div>
                    </div>
                </td>
                <td class="py-3.5 px-4">
                    <div class="font-mono text-xs font-extrabold text-slate-800 dark:text-slate-200">${escapeHtml(j.nik || '-')}</div>
                    <div class="text-[10px] font-mono text-slate-400">RFID: ${escapeHtml(j.rfid || '-')}</div>
                </td>
                <td class="py-3.5 px-4">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        ${escapeHtml(j.department || 'Umum')}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-extrabold ${roleBadgeClass}">
                        <i class="fa-solid ${roleIcon} mr-1.5 text-[10px]"></i> ${escapeHtml(titleText)}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                        +${j.vote_weight || 10} Poin
                    </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                    ${evalBadge}
                </td>
                <td class="py-3.5 px-4 text-center">
                    ${statusBadge}
                </td>
                <td class="py-3.5 px-4 text-center">
                    <div class="flex items-center justify-center space-x-1.5">
                        <button onclick="openEditJudgeModal(${j.id})" class="p-2 rounded-xl text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/50 transition cursor-pointer" title="Edit Juri / Manajemen">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button onclick="deleteJudge(${j.id}, '${escapeHtml(j.name)}')" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition cursor-pointer" title="Hapus Data">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        $tbody.append(tr);
    });
}

// Handler Scanner RFID / NIK Manual
function handleLookup(e) {
    e.preventDefault();
    const inputVal = $('#judgeScanInput').val().trim();
    if (!inputVal) {
        Swal.fire({
            icon: 'warning',
            title: 'Input Kosong',
            text: 'Silakan tap kartu RFID atau ketikkan NIK terlebih dahulu.',
            confirmButtonColor: '#4f46e5',
        });
        return;
    }

    const $btn = $('#btnLookup');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Mencari...');

    $.ajax({
        url: "{{ route('pool-qcc.admin.judges.lookup') }}",
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            input: inputVal
        },
        dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html(origHtml);
            if (res.status === 'already_registered') {
                Swal.fire({
                    icon: 'info',
                    title: 'Sudah Terdaftar',
                    text: res.message,
                    confirmButtonColor: '#4f46e5',
                });
                $('#judgeScanInput').val('').focus();
                return;
            }

            if (res.status === 'success' && res.data) {
                pendingJudgeData = res.data;
                openConfirmModal(res.data);
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html(origHtml);
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Karyawan tidak ditemukan di database.';
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Ditemukan',
                text: msg,
                confirmButtonColor: '#4f46e5',
            });
            $('#judgeScanInput').select().focus();
        }
    });
}

// Buka Modal Konfirmasi
function openConfirmModal(data) {
    $('#modalJudgeName').text(data.name || '-');
    $('#modalJudgeDept').text(data.department || '-');
    $('#modalJudgeNik').text(data.nik || '-');
    $('#modalJudgeRfid').text(data.rfid || '-');
    $('#modalJudgeTitle').val('Juri');
    $('#modalJudgeVoteWeight').val(10);

    const firstInitial = (data.name || 'J').trim().charAt(0).toUpperCase();
    if (data.photo) {
        $('#modalJudgeAvatar').html(`<img src="${data.photo}" class="w-full h-full object-cover">`);
    } else {
        $('#modalJudgeAvatar').html(`<span>${firstInitial}</span>`);
    }

    $('#modalConfirmJudge').removeClass('hidden');
}

// Tutup Modal Konfirmasi
function closeModal() {
    $('#modalConfirmJudge').addClass('hidden');
    pendingJudgeData = null;
    $('#judgeScanInput').val('').focus();
}

// Simpan Juri yang Dikonfirmasi
function saveConfirmedJudge() {
    if (!pendingJudgeData) return;

    const $btn = $('#btnConfirmSave');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...');

    const payload = {
        _token: "{{ csrf_token() }}",
        nik: pendingJudgeData.nik,
        name: pendingJudgeData.name,
        department: pendingJudgeData.department,
        rfid: pendingJudgeData.rfid,
        photo: pendingJudgeData.photo,
        title: $('#modalJudgeTitle').val() || 'Juri',
        vote_weight: $('#modalJudgeVoteWeight').val() || 10,
    };

    $.ajax({
        url: "{{ route('pool-qcc.admin.judges.store') }}",
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html(origHtml);
            closeModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message || 'Juri / Manajemen berhasil didaftarkan!',
                confirmButtonColor: '#4f46e5',
                timer: 2000,
            });
            loadJudgesData();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html(origHtml);
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menyimpan data.';
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg,
                confirmButtonColor: '#4f46e5',
            });
        }
    });
}

// ==============================================================
// MODAL EDIT JURI / MANAJEMEN
// ==============================================================
function openEditJudgeModal(id) {
    const judge = currentJudges.find(j => j.id == id);
    if (!judge) return;

    $('#editJudgeId').val(judge.id);
    $('#editJudgeName').text(judge.name);
    $('#editJudgeNik').text('NIK: ' + (judge.nik || '-') + ' | RFID: ' + (judge.rfid || '-'));
    $('#editJudgeDept').val(judge.department || '');
    $('#editJudgeVoteWeight').val(judge.vote_weight || 10);
    $('#editJudgeIsActive').prop('checked', judge.is_active);

    // Set dropdown gelar
    let titleVal = judge.title || 'Juri';
    if (!['Juri', 'Factory Manager', 'Direktur'].includes(titleVal)) {
        if (titleVal.toLowerCase().includes('direktur')) titleVal = 'Direktur';
        else if (titleVal.toLowerCase().includes('manager')) titleVal = 'Factory Manager';
        else titleVal = 'Juri';
    }
    $('#editJudgeTitle').val(titleVal);

    if (judge.photo) {
        $('#editJudgeAvatar').html(`<img src="${judge.photo}" class="w-full h-full object-cover">`);
    } else {
        const firstInitial = (judge.name || 'J').trim().charAt(0).toUpperCase();
        $('#editJudgeAvatar').html(`<span>${firstInitial}</span>`);
    }

    $('#modalEditJudge').removeClass('hidden');
}

function closeEditModal() {
    $('#modalEditJudge').addClass('hidden');
}

function saveEditJudge(e) {
    e.preventDefault();
    const id = $('#editJudgeId').val();
    if (!id) return;

    const $btn = $('#btnSaveEdit');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...');

    const payload = {
        _token: "{{ csrf_token() }}",
        _method: 'PUT',
        title: $('#editJudgeTitle').val(),
        vote_weight: $('#editJudgeVoteWeight').val(),
        department: $('#editJudgeDept').val(),
        is_active: $('#editJudgeIsActive').is(':checked') ? 1 : 0
    };

    $.ajax({
        url: "{{ url('pool-qcc/admin/judges') }}/" + id,
        type: 'POST',
        data: payload,
        dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html(origHtml);
            closeEditModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Diperbarui!',
                text: res.message || 'Data berhasil diperbarui.',
                confirmButtonColor: '#4f46e5',
                timer: 2000,
            });
            loadJudgesData();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html(origHtml);
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memperbarui data.';
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg,
                confirmButtonColor: '#4f46e5',
            });
        }
    });
}

// Toggle status aktif
function toggleJudge(id) {
    $.ajax({
        url: "{{ url('pool-qcc/admin/judges') }}/" + id + "/toggle",
        type: 'POST',
        data: { _token: "{{ csrf_token() }}" },
        dataType: 'json',
        success: function(res) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: res.message,
                showConfirmButton: false,
                timer: 2000
            });
            loadJudgesData();
        }
    });
}

// Hapus Juri
function deleteJudge(id, name) {
    Swal.fire({
        title: 'Hapus Data?',
        html: `Apakah Anda yakin ingin menghapus <b>${name}</b>?<br><span class="text-xs text-rose-500">Semua riwayat evaluasi terkait juga akan terhapus.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('pool-qcc/admin/judges') }}/" + id,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus',
                        text: res.message,
                        confirmButtonColor: '#4f46e5',
                        timer: 1500
                    });
                    loadJudgesData();
                }
            });
        }
    });
}

// Helper escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
@endsection
