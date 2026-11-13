@extends('pool_qcc.layout')

@section('title', 'Rekap Pemenang Penilaian Dewan Juri - Pool QCC')

@php
    $canReset = in_array('qcc_pool_admin', $permissions ?? []);
@endphp

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="juryRecapWrapper">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row items-start md:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800 gap-4">
            <div>
                <div
                    class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                    <span>Pool QCC</span>
                    <span>&bull;</span>
                    <span class="text-slate-500">Hasil Penilaian Juri</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                    <span>Pemenang Penilaian Dewan Juri</span>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Hasil evaluasi independen dewan juri (skor 1 - 100), terpisah dari voting publik/leaderboard.
                </p>
            </div>

            <div class="flex items-center space-x-2">
                @if ($canReset)
                    <!-- Global Reset Button for qcc_pool_admin -->
                    <button type="button" onclick="confirmResetAllJuryEvaluations()" id="btnResetAll"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition flex items-center space-x-1.5 shadow-sm cursor-pointer"
                        title="Reset Seluruh Penilaian Juri">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="hidden sm:inline">Reset Semua Nilai</span>
                    </button>
                @endif

                <!-- Download to Excel Button -->
                <a href="{{ route('pool-qcc.jury.recap.export') }}" id="btnExportExcel"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition flex items-center space-x-1.5 cursor-pointer"
                    title="Download Rekapitulasi Penilaian Dewan Juri ke File Excel">
                    <i class="fa-solid fa-file-excel"></i>
                    <span class="hidden sm:inline">Download Excel</span>
                </a>

                <!-- Scoring Input Link -->
                <a href="{{ route('pool-qcc.jury.scoring') }}"
                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>Layar Penilaian</span>
                </a>

                <!-- Refresh Button -->
                <button type="button" onclick="loadRecapData()" id="btnReload"
                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition flex items-center justify-center cursor-pointer"
                    title="Segarkan Data">
                    <i class="fa-solid fa-rotate text-sm" id="reloadIcon"></i>
                </button>
            </div>
        </div>

        <!-- Live Status Banner -->
        <div
            class="flex items-center justify-between px-5 py-3 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 mb-8 text-xs font-semibold text-slate-600 dark:text-slate-400">
            <div class="flex items-center space-x-3">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-500 text-white animate-pulse">
                    LIVE
                </span>
                <span>Total Dewan Juri Terdaftar: <strong class="text-slate-900 dark:text-white"
                        id="statJuryCount">Memuat...</strong></span>
            </div>
            <div class="text-[11px] text-slate-400">
                Terakhir Diperbarui: <span id="lastUpdatedText">-</span>
            </div>
        </div>

        <!-- Podium Highlights: The Best Booth (Juara 1) & Runner Up (Juara 2) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8 mb-10">
            <!-- 🥇 THE BEST BOOTH (JUARA 1) -->
            <div
                class="relative overflow-hidden rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-amber-500/15 via-amber-500/5 to-transparent border-2 border-amber-400/60 dark:border-amber-500/40 shadow-2xl backdrop-blur-md">
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-amber-400/20 rounded-full blur-3xl pointer-events-none">
                </div>

                <div class="flex items-center justify-between mb-5">
                    <span
                        class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-500/30">
                        <i class="fa-solid fa-crown text-amber-200"></i>
                        <span>THE BEST BOOTH &bull; JUARA 1</span>
                    </span>
                    <span
                        class="w-12 h-12 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-xl font-black shadow-lg shadow-amber-500/30">
                        <i class="fa-solid fa-trophy"></i>
                    </span>
                </div>

                <div id="bestBoothContainer">
                    <div class="text-center py-10 text-slate-400">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-amber-500 mb-2 block"></i>
                        <p class="text-xs">Memuat data The Best Booth...</p>
                    </div>
                </div>
            </div>

            <!-- 🥈 RUNNER UP (JUARA 2) -->
            <div
                class="relative overflow-hidden rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-slate-400/10 via-slate-400/5 to-transparent border-2 border-slate-300 dark:border-slate-700 shadow-xl backdrop-blur-md">
                <div
                    class="absolute -top-12 -right-12 w-48 h-48 bg-slate-300/20 dark:bg-slate-600/10 rounded-full blur-3xl pointer-events-none">
                </div>

                <div class="flex items-center justify-between mb-5">
                    <span
                        class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider bg-slate-700 dark:bg-slate-300 text-white dark:text-slate-900 shadow-lg shadow-slate-700/20">
                        <i class="fa-solid fa-medal text-slate-300 dark:text-slate-700"></i>
                        <span>RUNNER UP &bull; JUARA 2</span>
                    </span>
                    <span
                        class="w-12 h-12 rounded-2xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-xl font-black shadow-md border border-slate-300 dark:border-slate-700">
                        <i class="fa-solid fa-award"></i>
                    </span>
                </div>

                <div id="runnerUpContainer">
                    <div class="text-center py-10 text-slate-400">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-slate-500 mb-2 block"></i>
                        <p class="text-xs">Memuat data Runner Up...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Standings Table -->
        <div class="glass-panel rounded-3xl border border-slate-200 dark:border-slate-800 shadow-lg overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Klasemen Lengkap Penilaian Juri</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Urutan peringkat booth berdasarkan
                        rata-rata skor evaluasi 1-100 dari seluruh dewan juri.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-400 hidden sm:inline">Auto-refresh aktif</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-extrabold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="py-4 px-5 text-center w-16">Rank</th>
                            <th class="py-4 px-5">Booth & Departemen</th>
                            <th class="py-4 px-5">Rata-Rata Skor</th>
                            <th class="py-4 px-5 text-center">Partisipasi Juri</th>
                            <th class="py-4 px-5 text-center">Rentang Nilai</th>
                            <th class="py-4 px-5 text-center">Aksi / Nilai</th>
                        </tr>
                    </thead>
                    <tbody id="recapTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Memuat klasemen penilaian dewan juri...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Rincian Nilai Juri per Booth -->
    <div id="modalDetailBooth"
        class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div
            class="glass-panel rounded-3xl max-w-xl w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden animate-scale-up">
            <div
                class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-200 block">Rincian Penilaian
                        Juri</span>
                    <h3 class="font-extrabold text-base" id="modalDetailBoothTitle">Booth Detail</h3>
                </div>
                <button type="button" onclick="closeDetailModal()"
                    class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6">
                <div id="modalDetailList" class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    <!-- Evaluation rows injected via JS -->
                </div>
            </div>

            <div
                class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div id="modalResetBoothContainer">
                    <!-- Populated dynamically via JS if canReset -->
                </div>
                <button type="button" onclick="closeDetailModal()"
                    class="px-5 py-2 rounded-xl text-xs font-extrabold bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-white transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const canReset = {{ $canReset ? 'true' : 'false' }};
        let autoRefreshTimer = null;
        let cachedBoothsData = [];
        let currentModalBoothId = null;
        let currentModalBoothName = '';

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        $(document).ready(function() {
            // Initial load via AJAX
            loadRecapData();

            // Auto-refresh interval 10 seconds
            autoRefreshTimer = setInterval(loadRecapData, 10000);
        });

        // Load data rekap dewan juri secara client-side
        function loadRecapData(callback) {
            const $icon = $('#reloadIcon');
            $icon.addClass('fa-spin');

            $.ajax({
                url: "{{ route('pool-qcc.jury.api.recap') }}",
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $icon.removeClass('fa-spin');
                    if (res.status === 'success' && res.data) {
                        renderRecapData(res.data);
                        if (typeof callback === 'function') {
                            callback(res.data);
                        }
                    }
                },
                error: function() {
                    $icon.removeClass('fa-spin');
                }
            });
        }

        // Render data ke DOM via jQuery
        function renderRecapData(data) {
            cachedBoothsData = data.booths || [];
            const totalJudges = data.total_active_judges || 0;

            $('#statJuryCount').text(totalJudges + ' Juri');
            $('#lastUpdatedText').text(data.last_updated || '-');

            // 1. Render The Best Booth (Juara 1)
            if (data.the_best_booth) {
                const b = data.the_best_booth;
                $('#bestBoothContainer').html(`
            <div class="flex items-start space-x-4 mb-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-500 to-amber-600 text-white font-black text-xl flex items-center justify-center shadow-md flex-shrink-0">
                    ${escapeHtml(b.code)}
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/30 uppercase tracking-wider">
                        ${escapeHtml(b.category || 'Kaizen Circle')}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 leading-tight">
                        ${escapeHtml(b.name)}
                    </h2>
                    ${b.location ? `<span class="text-xs text-slate-400 block mt-0.5"><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> ${escapeHtml(b.location)}</span>` : ''}
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-amber-200 dark:border-amber-800/40 shadow-inner flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Rata-Rata Skor Juri</span>
                    <div class="flex items-baseline space-x-1.5 mt-0.5">
                        <span class="text-3xl sm:text-4xl font-black text-amber-600 dark:text-amber-400 font-heading">
                            ${Number(b.avg_score).toFixed(2)}
                        </span>
                        <span class="text-xs font-bold text-slate-400">/ 100</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30 inline-block mb-1">
                        ${b.judges_evaluated_count} Juri Menilai
                    </span>
                    <div class="text-[11px] text-slate-400">
                        Tertinggi: <strong class="text-slate-700 dark:text-slate-200">${b.max_score}</strong>
                    </div>
                </div>
            </div>
        `);
            } else {
                $('#bestBoothContainer').html(`
            <div class="text-center py-10 text-slate-400">
                <i class="fa-solid fa-hourglass-start text-3xl mb-2 text-amber-400/60 block"></i>
                <p class="text-xs">Menunggu penilaian juri selesai...</p>
            </div>
        `);
            }

            // 2. Render Runner Up (Juara 2)
            if (data.runner_up) {
                const r = data.runner_up;
                $('#runnerUpContainer').html(`
            <div class="flex items-start space-x-4 mb-5">
                <div class="w-16 h-16 rounded-2xl bg-slate-600 dark:bg-slate-700 text-white font-black text-xl flex items-center justify-center shadow-md flex-shrink-0">
                    ${escapeHtml(r.code)}
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 uppercase tracking-wider">
                        ${escapeHtml(r.category || 'Kaizen Circle')}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1 leading-tight">
                        ${escapeHtml(r.name)}
                    </h2>
                    ${r.location ? `<span class="text-xs text-slate-400 block mt-0.5"><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> ${escapeHtml(r.location)}</span>` : ''}
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-white/80 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-700 shadow-inner flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Rata-Rata Skor Juri</span>
                    <div class="flex items-baseline space-x-1.5 mt-0.5">
                        <span class="text-3xl sm:text-4xl font-black text-slate-800 dark:text-slate-200 font-heading">
                            ${Number(r.avg_score).toFixed(2)}
                        </span>
                        <span class="text-xs font-bold text-slate-400">/ 100</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="px-3 py-1 rounded-xl text-xs font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 inline-block mb-1">
                        ${r.judges_evaluated_count} Juri Menilai
                    </span>
                    <div class="text-[11px] text-slate-400">
                        Tertinggi: <strong class="text-slate-700 dark:text-slate-200">${r.max_score}</strong>
                    </div>
                </div>
            </div>
        `);
            } else {
                $('#runnerUpContainer').html(`
            <div class="text-center py-10 text-slate-400">
                <i class="fa-solid fa-hourglass-start text-3xl mb-2 text-slate-400/60 block"></i>
                <p class="text-xs">Menunggu penilaian juri selesai...</p>
            </div>
        `);
            }

            // 3. Render Table Rows
            const $tbody = $('#recapTableBody');
            $tbody.empty();

            if (!data.booths || data.booths.length === 0) {
                $tbody.html(`
            <tr>
                <td colspan="6" class="text-center py-12 text-slate-400 text-xs">
                    Belum ada data booth.
                </td>
            </tr>
        `);
                return;
            }

            data.booths.forEach((b, idx) => {
                const rank = idx + 1;
                const isBest = (rank === 1 && b.avg_score > 0);
                const isRunner = (rank === 2 && b.avg_score > 0);

                let rankBadge = `<span class="font-extrabold text-xs text-slate-400">${rank}</span>`;
                if (isBest) {
                    rankBadge =
                        `<span class="w-8 h-8 rounded-full bg-amber-500 text-white font-black text-xs inline-flex items-center justify-center shadow-md shadow-amber-500/30" title="The Best Booth"><i class="fa-solid fa-crown"></i></span>`;
                } else if (isRunner) {
                    rankBadge =
                        `<span class="w-8 h-8 rounded-full bg-slate-600 text-white font-black text-xs inline-flex items-center justify-center shadow-md" title="Runner Up">2</span>`;
                } else if (rank === 3) {
                    rankBadge =
                        `<span class="w-8 h-8 rounded-full bg-amber-700 text-white font-black text-xs inline-flex items-center justify-center shadow-sm">3</span>`;
                }

                const trClass = isBest ? 'bg-amber-50/30 dark:bg-amber-950/20' : (isRunner ?
                    'bg-slate-50/50 dark:bg-slate-800/30' : '');
                const barColor = isBest ? 'bg-amber-500' : (isRunner ? 'bg-slate-500' : 'bg-indigo-500');

                const tr = `
            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition ${trClass}">
                <td class="py-4 px-5 text-center">${rankBadge}</td>
                <td class="py-4 px-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white font-black text-xs flex items-center justify-center border border-slate-200 dark:border-slate-700 flex-shrink-0">
                            ${escapeHtml(b.code)}
                        </div>
                        <div>
                            <div class="font-extrabold text-slate-900 dark:text-white flex items-center space-x-2">
                                <span>${escapeHtml(b.name)}</span>
                                ${isBest ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white">THE BEST BOOTH</span>' : ''}
                                ${isRunner ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-600 text-white">RUNNER UP</span>' : ''}
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(b.category || 'Kaizen Circle')}</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-5">
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-black font-heading text-slate-900 dark:text-white min-w-[55px]">
                            ${Number(b.avg_score).toFixed(2)}
                        </span>
                        <div class="flex-1 max-w-[120px] h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full ${barColor} rounded-full" style="width: ${b.avg_score}%;"></div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-5 text-center">
                    <span class="px-3 py-1 rounded-full text-xs font-bold ${b.judges_evaluated_count >= totalJudges && totalJudges > 0 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'}">
                        ${b.judges_evaluated_count} / ${totalJudges} Juri
                    </span>
                </td>
                <td class="py-4 px-5 text-center text-xs">
                    ${b.judges_evaluated_count > 0 ? `<span class="text-slate-400 font-mono">Min: <strong class="text-slate-700 dark:text-slate-300">${b.min_score}</strong> &bull; Max: <strong class="text-slate-700 dark:text-slate-300">${b.max_score}</strong></span>` : '<span class="text-slate-400">-</span>'}
                </td>
                <td class="py-4 px-5 text-center">
                    <div class="flex items-center justify-center space-x-1.5">
                        <button type="button" onclick="showJudgeDetailModal(${b.id})"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition cursor-pointer">
                            <i class="fa-solid fa-list-check mr-1"></i> Rincian Nilai
                        </button>
                        ${canReset && b.judges_evaluated_count > 0 ? `
                                <button type="button" onclick="confirmResetBooth(${b.id}, '${escapeHtml(b.code)} - ${escapeHtml(b.name)}')"
                                    class="p-1.5 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-500/20 border border-rose-200 dark:border-rose-500/30 transition cursor-pointer"
                                    title="Reset seluruh nilai booth ini">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                            ` : ''}
                    </div>
                </td>
            </tr>
        `;
                $tbody.append(tr);
            });
        }

        function showJudgeDetailModal(boothId) {
            const booth = cachedBoothsData.find(item => item.id == boothId);
            if (!booth) return;

            currentModalBoothId = booth.id;
            currentModalBoothName = `${booth.code} - ${booth.name}`;

            $('#modalDetailBoothTitle').text(`${booth.code} - ${booth.name}`);
            const $list = $('#modalDetailList');
            $list.empty();

            // Populate Reset Booth Button in Modal Footer if Admin
            if (canReset && booth.evaluations && booth.evaluations.length > 0) {
                $('#modalResetBoothContainer').html(`
                    <button type="button" onclick="confirmResetBooth(${booth.id}, '${escapeHtml(booth.code)} - ${escapeHtml(booth.name)}')"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/15 dark:hover:bg-rose-500/25 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 transition flex items-center space-x-1.5 cursor-pointer">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Reset Nilai Booth Ini</span>
                    </button>
                `);
            } else {
                $('#modalResetBoothContainer').empty();
            }

            if (!booth.evaluations || booth.evaluations.length === 0) {
                $list.html(`
            <div class="text-center py-8 text-slate-400 text-xs">
                <i class="fa-solid fa-clipboard text-2xl mb-2 block text-slate-300"></i>
                Belum ada juri yang memberikan nilai untuk booth ini.
            </div>
        `);
            } else {
                booth.evaluations.forEach(e => {
                    let criteriaHtml = '';
                    if (e.criteria_scores && Array.isArray(e.criteria_scores) && e.criteria_scores.length > 0) {
                        criteriaHtml =
                            '<div class="mt-2.5 pt-2 border-t border-slate-200/60 dark:border-slate-700/60 grid grid-cols-1 sm:grid-cols-2 gap-1.5">';
                        e.criteria_scores.forEach(cs => {
                            const titleText = cs.description ? `${cs.name} - ${cs.description}` : cs.name;
                            const displayText = cs.description ? `${cs.description}` : cs.name;
                            criteriaHtml += `
                                <div class="px-2 py-1.5 rounded-lg bg-white/80 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800 text-[10px] flex items-center justify-between gap-1" title="${escapeHtml(titleText)}">
                                    <div class="truncate">
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block truncate">${escapeHtml(displayText)}</span>
                                        ${cs.description ? `<span class="text-[9px] text-slate-400 truncate block">${escapeHtml(cs.name)} (${cs.weight}%)</span>` : `<span class="text-[9px] text-slate-400">Bobot: ${cs.weight}%</span>`}
                                    </div>
                                    <span class="font-black text-indigo-600 dark:text-indigo-400 font-mono text-xs flex-shrink-0">${cs.score}</span>
                                </div>
                            `;
                        });
                        criteriaHtml += '</div>';
                    }

                    $list.append(`
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center">
                                ${escapeHtml(e.judge_name.charAt(0).toUpperCase())}
                            </span>
                            <div>
                                <h5 class="text-xs font-extrabold text-slate-900 dark:text-white">${escapeHtml(e.judge_name)}</h5>
                                <span class="text-[10px] text-slate-400">${escapeHtml(e.judge_dept)} &bull; ${escapeHtml(e.evaluated_at)}</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-500 text-white shadow-xs">
                                ${Number(e.score).toFixed(2)}
                            </span>
                            ${canReset ? `
                                    <button type="button" onclick="confirmResetEvaluation(${e.id}, '${escapeHtml(e.judge_name)}', '${escapeHtml(booth.name)}')"
                                        class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-500/20 transition cursor-pointer"
                                        title="Reset nilai dari juri ini">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                ` : ''}
                        </div>
                    </div>
                    ${criteriaHtml}
                    ${e.notes ? `<div class="mt-2 text-xs italic text-slate-600 dark:text-slate-400 p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800">"${escapeHtml(e.notes)}"</div>` : ''}
                </div>
            `);
                });
            }

            $('#modalDetailBooth').removeClass('hidden');
        }

        function closeDetailModal() {
            $('#modalDetailBooth').addClass('hidden');
            currentModalBoothId = null;
            currentModalBoothName = '';
        }

        // 4. RESET ACTIONS (Permission: qcc_pool_admin)
        function confirmResetEvaluation(evaluationId, judgeName, boothName) {
            Swal.fire({
                title: 'Reset Nilai Juri?',
                html: `Apakah Anda yakin ingin mereset penilaian dari <b>${escapeHtml(judgeName)}</b> untuk booth <b>${escapeHtml(boothName)}</b>?<br><span class="text-rose-500 text-xs font-semibold mt-1.5 block">Juri tersebut dapat melakukan penilaian ulang kembali.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-rotate-left mr-1"></i> Ya, Reset',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: `/pool-qcc/admin/jury/evaluations/${evaluationId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json'
                    }).catch(error => {
                        Swal.showValidationMessage(
                            error.responseJSON?.message || 'Gagal mereset penilaian juri.'
                        );
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil di-reset!',
                        text: result.value.message || 'Penilaian juri berhasil direset.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadRecapData(function() {
                        if (currentModalBoothId) {
                            showJudgeDetailModal(currentModalBoothId);
                        }
                    });
                }
            });
        }

        function confirmResetBooth(boothId, boothName) {
            Swal.fire({
                title: 'Reset Semua Nilai Booth?',
                html: `Apakah Anda yakin ingin mereset <b>seluruh penilaian dewan juri</b> untuk booth <b>"${escapeHtml(boothName)}"</b>?<br><span class="text-rose-500 text-xs font-semibold mt-1.5 block">Semua juri yang pernah menilai booth ini harus menilai ulang dari awal.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-rotate-left mr-1"></i> Ya, Reset Nilai Booth',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: `/pool-qcc/admin/jury/booths/${boothId}/reset`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json'
                    }).catch(error => {
                        Swal.showValidationMessage(
                            error.responseJSON?.message || 'Gagal mereset nilai booth.'
                        );
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil di-reset!',
                        text: result.value.message || 'Penilaian booth berhasil direset.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    closeDetailModal();
                    loadRecapData();
                }
            });
        }

        function confirmResetAllJuryEvaluations() {
            Swal.fire({
                title: 'RESET SEMUA NILAI JURI?',
                html: `Perhatian: Aksi ini akan <b>menghapus seluruh hasil evaluasi & nilai</b> yang telah diberikan oleh seluruh Dewan Juri untuk semua booth!<br><span class="text-rose-500 text-xs font-black mt-2 block">Tindakan ini tidak dapat dibatalkan!</span>`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-triangle-exclamation mr-1"></i> Ya, Reset SEMUA Nilai',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: "{{ route('pool-qcc.admin.jury.reset-all') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json'
                    }).catch(error => {
                        Swal.showValidationMessage(
                            error.responseJSON?.message || 'Gagal mereset seluruh penilaian juri.'
                        );
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Semua Nilai Juri Direset!',
                        text: result.value.message || 'Seluruh data penilaian juri berhasil direset.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    closeDetailModal();
                    loadRecapData();
                }
            });
        }

        function toggleFullscreen() {
            const elem = document.documentElement;
            if (!document.fullscreenElement) {
                elem.requestFullscreen().catch(err => {});
            } else {
                document.exitFullscreen();
            }
        }
    </script>
@endsection
