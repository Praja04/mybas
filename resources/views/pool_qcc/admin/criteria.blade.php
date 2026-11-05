@extends('pool_qcc.layout')

@section('title', 'Kriteria Penilaian Booth - Pool QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800 gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                <a href="{{ route('pool-qcc.admin.dashboard') }}" class="hover:underline">Admin</a>
                <span>&bull;</span>
                <a href="{{ route('pool-qcc.admin.judges') }}" class="hover:underline text-slate-500">Dewan Juri</a>
                <span>&bull;</span>
                <span class="text-amber-500 font-extrabold">Kriteria Penilaian</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                <span>Kriteria Penilaian Booth QCC</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/30">
                    <i class="fa-solid fa-list-check mr-1"></i> Standar Evaluasi
                </span>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Atur kriteria penilaian, deskripsi pedoman juri, bobot persentase (%) dan skor maksimal per aspek evaluasi.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="openCreateModal()"
                class="px-5 py-2.5 rounded-2xl text-xs font-extrabold bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white shadow-lg shadow-indigo-600/25 transition flex items-center space-x-2 cursor-pointer">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Kriteria Baru</span>
            </button>
            <a href="{{ route('pool-qcc.jury.scoring') }}"
                class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center space-x-2">
                <i class="fa-solid fa-scale-balanced text-indigo-500"></i>
                <span>Simulasi Penilaian Juri</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="glass-panel rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-list-ol"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Kriteria</span>
                <span class="text-2xl font-extrabold text-slate-900 dark:text-white" id="statTotalCriteria">0</span>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Kriteria Aktif</span>
                <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400" id="statActiveCriteria">0</span>
            </div>
        </div>

        <div class="glass-panel rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center space-x-4">
            <div id="statWeightIconContainer" class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-black">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Bobot Aktif</span>
                    <span id="statWeightStatusBadge" class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">100% Ideal</span>
                </div>
                <div class="flex items-baseline space-x-1.5 mt-0.5">
                    <span class="text-2xl font-extrabold text-slate-900 dark:text-white" id="statTotalWeight">0</span>
                    <span class="text-sm font-bold text-slate-400">%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Bobot Reminder -->
    <div id="weightWarningAlert" class="hidden mb-6 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 flex items-start space-x-3 text-xs">
        <i class="fa-solid fa-triangle-exclamation text-amber-500 text-base mt-0.5 flex-shrink-0"></i>
        <div>
            <strong class="font-extrabold block text-sm">Perhatian: Total Bobot Kriteria Aktif Belum 100%!</strong>
            <span id="weightWarningMessage">Total akumulasi bobot saat ini tidak sama dengan 100%. Sistem akan tetap menormalisasi secara proporsional, namun sangat disarankan agar total bobot pas 100%.</span>
        </div>
    </div>

    <!-- Filter & Criteria Table Card -->
    <div class="glass-panel rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Kriteria Penilaian Booth</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kriteria di bawah ini akan otomatis muncul pada form penilaian ketika Dewan Juri menilai booth.</p>
            </div>
            <div class="flex items-center space-x-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="searchCriteriaInput" placeholder="Cari kriteria..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-indigo-500">
                </div>
                <button type="button" onclick="loadCriteriaData()" title="Segarkan Data"
                    class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-rotate"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-800/50 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4 text-center w-14">Urutan</th>
                        <th class="py-3 px-4">Nama Aspek & Deskripsi Kriteria</th>
                        <th class="py-3 px-4 text-center w-28">Bobot (%)</th>
                        <th class="py-3 px-4 text-center w-28">Skor Maks</th>
                        <th class="py-3 px-4 text-center w-28">Status</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody id="criteriaTableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-circle-notch fa-spin text-2xl text-indigo-500 mb-2 block"></i>
                            <span>Memuat data kriteria penilaian...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 1: TAMBAH KRITERIA BARU                                  -->
<!-- ============================================================== -->
<div id="modalCreateCriterion" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl max-w-lg w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-scale-up">
        <!-- Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base leading-tight">Tambah Kriteria Penilaian</h3>
                    <p class="text-[11px] text-white/80">Kriteria aspek evaluasi booth inovasi QCC</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-xs transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form id="createCriterionForm" onsubmit="saveNewCriterion(event)" class="p-6 space-y-4">
            <!-- Nama Kriteria -->
            <div>
                <label for="createName" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Nama Aspek / Kriteria <span class="text-rose-500">*</span>:
                </label>
                <input type="text" id="createName" required placeholder="Contoh: Kreativitas & Orisinalitas Inovasi"
                    class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-bold focus:outline-none focus:border-indigo-500">
            </div>

            <!-- Deskripsi / Pedoman Penilaian -->
            <div>
                <label for="createDescription" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Deskripsi & Panduan Penilaian Juri:
                </label>
                <textarea id="createDescription" rows="3" placeholder="Jelaskan indikator apa yang dinilai juri pada kriteria ini..."
                    class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Bobot (%) -->
                <div>
                    <label for="createWeight" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Bobot (%) <span class="text-rose-500">*</span>:
                    </label>
                    <div class="relative">
                        <input type="number" id="createWeight" min="0" max="100" step="0.5" value="25" required
                            class="w-full pl-3 pr-8 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-black text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                    </div>
                </div>

                <!-- Skor Maksimal -->
                <div>
                    <label for="createMaxScore" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Skor Maksimal:
                    </label>
                    <input type="number" id="createMaxScore" min="1" max="1000" step="1" value="100" required
                        class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-black text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Urutan Tampilan -->
                <div>
                    <label for="createOrder" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        No. Urut:
                    </label>
                    <input type="number" id="createOrder" min="0" max="100" step="1" value="1"
                        class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <div class="pt-2 flex items-center space-x-2">
                <input type="checkbox" id="createIsActive" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                <label for="createIsActive" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                    Aktifkan kriteria ini langsung pada form penilaian juri
                </label>
            </div>

            <!-- Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="submit" id="btnSaveCreate" class="px-5 py-2.5 rounded-xl text-xs font-extrabold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/30 transition flex items-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>Simpan Kriteria</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL 2: EDIT KRITERIA                                         -->
<!-- ============================================================== -->
<div id="modalEditCriterion" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel rounded-3xl max-w-lg w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-scale-up">
        <!-- Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base leading-tight">Edit Kriteria Penilaian</h3>
                    <p class="text-[11px] text-white/80">Perbarui parameter bobot atau pedoman kriteria</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-xs transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form id="editCriterionForm" onsubmit="saveEditCriterion(event)" class="p-6 space-y-4">
            <input type="hidden" id="editCriterionId" value="">

            <!-- Nama Kriteria -->
            <div>
                <label for="editName" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Nama Aspek / Kriteria <span class="text-rose-500">*</span>:
                </label>
                <input type="text" id="editName" required
                    class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white font-bold focus:outline-none focus:border-amber-500">
            </div>

            <!-- Deskripsi / Pedoman Penilaian -->
            <div>
                <label for="editDescription" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                    Deskripsi & Panduan Penilaian Juri:
                </label>
                <textarea id="editDescription" rows="3"
                    class="w-full px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Bobot (%) -->
                <div>
                    <label for="editWeight" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Bobot (%) <span class="text-rose-500">*</span>:
                    </label>
                    <div class="relative">
                        <input type="number" id="editWeight" min="0" max="100" step="0.5" required
                            class="w-full pl-3 pr-8 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-black text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                    </div>
                </div>

                <!-- Skor Maksimal -->
                <div>
                    <label for="editMaxScore" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Skor Maksimal:
                    </label>
                    <input type="number" id="editMaxScore" min="1" max="1000" step="1" required
                        class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-black text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                </div>

                <!-- Urutan Tampilan -->
                <div>
                    <label for="editOrder" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        No. Urut:
                    </label>
                    <input type="number" id="editOrder" min="0" max="100" step="1"
                        class="w-full px-3 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div class="pt-2 flex items-center space-x-2">
                <input type="checkbox" id="editIsActive" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400">
                <label for="editIsActive" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                    Status Aktif (Tampil di Form Penilaian Juri)
                </label>
            </div>

            <!-- Footer -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="submit" id="btnSaveEdit" class="px-5 py-2.5 rounded-xl text-xs font-extrabold bg-amber-500 hover:bg-amber-600 text-white shadow-md shadow-amber-500/30 transition flex items-center space-x-2 cursor-pointer">
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
let currentCriteria = [];

$(document).ready(function() {
    loadCriteriaData();

    $('#searchCriteriaInput').on('input', function() {
        renderTable($(this).val().toLowerCase().trim());
    });
});

function loadCriteriaData() {
    $.ajax({
        url: "{{ route('pool-qcc.admin.api.criteria') }}",
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                currentCriteria = res.data || [];
                updateStats(res.meta || {});
                renderTable($('#searchCriteriaInput').val().toLowerCase().trim());
            }
        },
        error: function(err) {
            console.error('Error loading criteria:', err);
            $('#criteriaTableBody').html(`
                <tr>
                    <td colspan="6" class="py-8 text-center text-rose-500 font-bold">
                        <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Gagal memuat data kriteria.
                    </td>
                </tr>
            `);
        }
    });
}

function updateStats(meta) {
    $('#statTotalCriteria').text(meta.total_criteria || currentCriteria.length);
    $('#statActiveCriteria').text(meta.active_criteria || currentCriteria.filter(c => c.is_active).length);

    const totalWeight = parseFloat(meta.total_active_weight || 0);
    $('#statTotalWeight').text(totalWeight.toFixed(totalWeight % 1 === 0 ? 0 : 1));

    const $badge = $('#statWeightStatusBadge');
    const $alert = $('#weightWarningAlert');
    const $iconContainer = $('#statWeightIconContainer');

    if (Math.abs(totalWeight - 100) < 0.01) {
        $badge.removeClass('bg-amber-100 text-amber-800 bg-rose-100 text-rose-800').addClass('bg-emerald-100 text-emerald-700').text('100% Ideal');
        $iconContainer.removeClass('bg-amber-50 text-amber-600 bg-rose-50 text-rose-600').addClass('bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400');
        $alert.addClass('hidden');
    } else {
        $badge.removeClass('bg-emerald-100 text-emerald-700').addClass('bg-amber-100 text-amber-800').text(`${totalWeight}% (Bukan 100%)`);
        $iconContainer.removeClass('bg-emerald-50 text-emerald-600').addClass('bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400');
        $('#weightWarningMessage').text(`Akumulasi bobot kriteria aktif saat ini adalah ${totalWeight}%. Agar hasil penilaian proporsional sempurna 100 poin, sesuaikan total bobot menjadi 100%.`);
        $alert.removeClass('hidden');
    }
}

function renderTable(keyword = '') {
    const $tbody = $('#criteriaTableBody');
    $tbody.empty();

    let filtered = currentCriteria;
    if (keyword) {
        filtered = filtered.filter(c => {
            return (c.name || '').toLowerCase().includes(keyword) ||
                   (c.description || '').toLowerCase().includes(keyword);
        });
    }

    if (filtered.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="6" class="py-12 text-center text-slate-400">
                    <i class="fa-solid fa-clipboard-list text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                    <span class="text-sm font-semibold">Tidak ada data kriteria yang sesuai.</span>
                </td>
            </tr>
        `);
        return;
    }

    filtered.forEach((c) => {
        const statusBadge = c.is_active
            ? `<button onclick="toggleCriterion(${c.id})" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20 transition cursor-pointer" title="Klik untuk nonaktifkan"><i class="fa-solid fa-check-circle mr-1"></i> Aktif</button>`
            : `<button onclick="toggleCriterion(${c.id})" class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-500/10 text-slate-500 border border-slate-500/30 hover:bg-slate-500/20 transition cursor-pointer" title="Klik untuk aktifkan"><i class="fa-solid fa-ban mr-1"></i> Nonaktif</button>`;

        const tr = `
            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                <td class="py-3.5 px-4 text-center font-extrabold text-xs text-slate-500">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 inline-flex items-center justify-center">
                        ${c.order || '-'}
                    </span>
                </td>
                <td class="py-3.5 px-4">
                    <div class="font-extrabold text-slate-900 dark:text-white text-sm">
                        ${escapeHtml(c.name)}
                    </div>
                    ${c.description ? `<p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">${escapeHtml(c.description)}</p>` : ''}
                </td>
                <td class="py-3.5 px-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                        ${c.weight}%
                    </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                    <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">
                        ${c.max_score}
                    </span>
                </td>
                <td class="py-3.5 px-4 text-center">
                    ${statusBadge}
                </td>
                <td class="py-3.5 px-4 text-center">
                    <div class="flex items-center justify-center space-x-1.5">
                        <button onclick="openEditModal(${c.id})" class="p-2 rounded-xl text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/50 transition cursor-pointer" title="Edit Kriteria">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button onclick="deleteCriterion(${c.id}, '${escapeHtml(c.name)}')" class="p-2 rounded-xl text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition cursor-pointer" title="Hapus Kriteria">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        $tbody.append(tr);
    });
}

// ==============================================================
// MODAL CREATE & EDIT
// ==============================================================
function openCreateModal() {
    $('#createCriterionForm')[0].reset();
    $('#createWeight').val(25);
    $('#createMaxScore').val(100);
    $('#createOrder').val(currentCriteria.length + 1);
    $('#createIsActive').prop('checked', true);
    $('#modalCreateCriterion').removeClass('hidden');
    setTimeout(() => $('#createName').focus(), 100);
}

function closeCreateModal() {
    $('#modalCreateCriterion').addClass('hidden');
}

function saveNewCriterion(e) {
    e.preventDefault();
    const name = $('#createName').val().trim();
    const weight = $('#createWeight').val();
    if (!name || weight === '') return;

    const $btn = $('#btnSaveCreate');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...');

    $.ajax({
        url: "{{ route('pool-qcc.admin.criteria.store') }}",
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            name: name,
            description: $('#createDescription').val().trim(),
            weight: weight,
            max_score: $('#createMaxScore').val() || 100,
            order: $('#createOrder').val() || 0,
            is_active: $('#createIsActive').is(':checked') ? 1 : 0
        },
        dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html(origHtml);
            closeCreateModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message,
                confirmButtonColor: '#4f46e5',
                timer: 1800
            });
            loadCriteriaData();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html(origHtml);
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menyimpan kriteria.';
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg,
                confirmButtonColor: '#4f46e5'
            });
        }
    });
}

function openEditModal(id) {
    const criterion = currentCriteria.find(c => c.id == id);
    if (!criterion) return;

    $('#editCriterionId').val(criterion.id);
    $('#editName').val(criterion.name);
    $('#editDescription').val(criterion.description || '');
    $('#editWeight').val(criterion.weight);
    $('#editMaxScore').val(criterion.max_score);
    $('#editOrder').val(criterion.order);
    $('#editIsActive').prop('checked', criterion.is_active);

    $('#modalEditCriterion').removeClass('hidden');
    setTimeout(() => $('#editName').focus(), 100);
}

function closeEditModal() {
    $('#modalEditCriterion').addClass('hidden');
}

function saveEditCriterion(e) {
    e.preventDefault();
    const id = $('#editCriterionId').val();
    const name = $('#editName').val().trim();
    const weight = $('#editWeight').val();
    if (!id || !name || weight === '') return;

    const $btn = $('#btnSaveEdit');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menyimpan...');

    $.ajax({
        url: "{{ url('pool-qcc/admin/criteria') }}/" + id,
        type: 'PUT',
        data: {
            _token: "{{ csrf_token() }}",
            name: name,
            description: $('#editDescription').val().trim(),
            weight: weight,
            max_score: $('#editMaxScore').val() || 100,
            order: $('#editOrder').val() || 0,
            is_active: $('#editIsActive').is(':checked') ? 1 : 0
        },
        dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html(origHtml);
            closeEditModal();
            Swal.fire({
                icon: 'success',
                title: 'Diperbarui!',
                text: res.message,
                confirmButtonColor: '#4f46e5',
                timer: 1800
            });
            loadCriteriaData();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html(origHtml);
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memperbarui kriteria.';
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: msg,
                confirmButtonColor: '#4f46e5'
            });
        }
    });
}

function toggleCriterion(id) {
    $.ajax({
        url: "{{ url('pool-qcc/admin/criteria') }}/" + id + "/toggle",
        type: 'POST',
        data: { _token: "{{ csrf_token() }}" },
        dataType: 'json',
        success: function(res) {
            Swal.fire({
                icon: 'success',
                title: 'Status Diperbarui',
                text: res.message,
                confirmButtonColor: '#4f46e5',
                timer: 1500
            });
            loadCriteriaData();
        }
    });
}

function deleteCriterion(id, name) {
    Swal.fire({
        title: 'Hapus Kriteria?',
        html: `Apakah Anda yakin ingin menghapus kriteria <b>${name}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ url('pool-qcc/admin/criteria') }}/" + id,
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
                    loadCriteriaData();
                }
            });
        }
    });
}

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

