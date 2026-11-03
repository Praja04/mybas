@extends('pool_qcc.layout')

@section('title', 'Admin Dashboard - Pool QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Admin -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Dashboard Monitoring QCC</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pantau statistik pooling booth, bank pertanyaan, dan partisipasi peserta secara langsung.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <button type="button" id="btnRefreshDashboard" onclick="loadDashboardData()" class="px-3.5 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-sm transition flex items-center space-x-1.5 cursor-pointer">
                <i id="refreshIcon" class="fa-solid fa-arrows-rotate text-xs text-indigo-500"></i>
                <span>Refresh</span>
            </button>
            <a href="{{ route('pool-qcc.admin.booths') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-store text-xs"></i>
                <span>Kelola Booth</span>
            </a>
            <a href="{{ route('pool-qcc.admin.questions') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-sm transition flex items-center space-x-2">
                <i class="fa-solid fa-clipboard-question text-xs text-amber-500"></i>
                <span>Bank Soal</span>
            </a>
        </div>
    </div>

    <!-- Alert Booth Incomplete (< 5 Pertanyaan) (Client-side populated) -->
    <div id="incompleteBoothsContainer" class="hidden mb-8">
        <!-- Dynamic alert if any booth has < 5 active questions -->
    </div>

    <!-- Metric Stats Grid (Client-side populated) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="glass-card rounded-2xl p-5 border border-slate-200 dark:border-indigo-500/20">
            <div class="flex items-center justify-between text-indigo-600 dark:text-indigo-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Booth</span>
                <i class="fa-solid fa-store text-lg"></i>
            </div>
            <div id="statTotalBooths" class="text-3xl font-black text-slate-900 dark:text-white">
                <span class="inline-block w-12 h-8 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Booth Inovasi QCC</div>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200 dark:border-cyan-500/20">
            <div class="flex items-center justify-between text-cyan-600 dark:text-cyan-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Soal</span>
                <i class="fa-solid fa-circle-question text-lg"></i>
            </div>
            <div id="statTotalQuestions" class="text-3xl font-black text-slate-900 dark:text-white">
                <span class="inline-block w-12 h-8 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Bank Soal Terdaftar</div>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200 dark:border-emerald-500/20">
            <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Peserta Tap</span>
                <i class="fa-solid fa-users text-lg"></i>
            </div>
            <div id="statTotalParticipants" class="text-3xl font-black text-slate-900 dark:text-white">
                <span class="inline-block w-12 h-8 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Telah Tapping RFID</div>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200 dark:border-purple-500/20">
            <div class="flex items-center justify-between text-purple-600 dark:text-purple-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Pool/Vote</span>
                <i class="fa-solid fa-check-to-slot text-lg"></i>
            </div>
            <div id="statTotalVotes" class="text-3xl font-black text-slate-900 dark:text-white">
                <span class="inline-block w-12 h-8 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kali Booth Di-pool</div>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-200 dark:border-amber-500/20 col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between text-amber-600 dark:text-amber-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tingkat Akurasi</span>
                <i class="fa-solid fa-bullseye text-lg"></i>
            </div>
            <div id="statAccuracyRate" class="text-3xl font-black text-slate-900 dark:text-white">
                <span class="inline-block w-14 h-8 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></span>
            </div>
            <div id="statCorrectVotes" class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mt-1">
                - Jawaban Benar
            </div>
        </div>
    </div>

    <!-- Leaderboard Two Columns (Client-side populated) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Booths -->
        <div class="glass-panel rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                    <i class="fa-solid fa-trophy text-amber-500 mr-2"></i> Top 5 Booth Terpopuler & Poin
                </h3>
                <a href="{{ route('pool-qcc.admin.booths') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Semua Booth &rarr;</a>
            </div>
            <div id="topBoothsContainer" class="divide-y divide-slate-100 dark:divide-slate-800">
                <!-- Dynamic client-side rows -->
            </div>
        </div>

        <!-- Top Participants -->
        <div class="glass-panel rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                    <i class="fa-solid fa-medal text-cyan-600 dark:text-cyan-400 mr-2"></i> Top 5 Peserta Teraktif
                </h3>
                <a href="{{ route('pool-qcc.admin.participants') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Semua Peserta &rarr;</a>
            </div>
            <div id="topParticipantsContainer" class="divide-y divide-slate-100 dark:divide-slate-800">
                <!-- Dynamic client-side rows -->
            </div>
        </div>
    </div>

    <!-- Recent Votes Table (Client-side populated) -->
    <div class="glass-panel rounded-2xl p-6 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                <i class="fa-solid fa-clock-rotate-left text-slate-400 mr-2"></i> 10 Aktivitas Pooling Terbaru
            </h3>
            <a href="{{ route('pool-qcc.admin.votes') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Lihat Semua Log &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Peserta</th>
                        <th class="py-3 px-4">Booth Dituju</th>
                        <th class="py-3 px-4">Jawaban</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Poin Diperoleh</th>
                    </tr>
                </thead>
                <tbody id="recentVotesTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    <!-- Dynamic client-side rows -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function escapeHtml(str) {
    if (!str) return '';
    return $('<div>').text(str).html();
}

function renderSkeletonDashboard() {
    $('#statTotalBooths, #statTotalQuestions, #statTotalParticipants, #statTotalVotes, #statAccuracyRate').html(
        '<span class="inline-block w-12 h-8 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></span>'
    );

    let listSkeletons = '';
    for (let i = 0; i < 5; i++) {
        listSkeletons += `
            <div class="py-3.5 flex items-center justify-between animate-pulse">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-800"></div>
                    <div>
                        <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-28 mb-1"></div>
                        <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-20"></div>
                    </div>
                </div>
                <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-16"></div>
            </div>
        `;
    }
    $('#topBoothsContainer, #topParticipantsContainer').html(listSkeletons);

    let tableSkeletons = '';
    for (let i = 0; i < 5; i++) {
        tableSkeletons += `
            <tr class="animate-pulse">
                <td class="py-3 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-24"></div></td>
                <td class="py-3 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-28"></div></td>
                <td class="py-3 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-28"></div></td>
                <td class="py-3 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-16"></div></td>
                <td class="py-3 px-4 text-center"><div class="h-5 bg-slate-200 dark:bg-slate-800 rounded-lg w-16 mx-auto"></div></td>
                <td class="py-3 px-4 text-right"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8 ml-auto"></div></td>
            </tr>
        `;
    }
    $('#recentVotesTableBody').html(tableSkeletons);
}

function loadDashboardData() {
    $('#refreshIcon').addClass('fa-spin');
    renderSkeletonDashboard();

    $.ajax({
        url: "{{ route('pool-qcc.admin.api.dashboard') }}",
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            $('#refreshIcon').removeClass('fa-spin');
            const data = response.data || {};

            // 1. Metric Stats
            $('#statTotalBooths').text(data.totalBooths || 0);
            $('#statTotalQuestions').text(data.totalQuestions || 0);
            $('#statTotalParticipants').text(data.totalParticipants || 0);
            $('#statTotalVotes').text(data.totalVotes || 0);
            $('#statAccuracyRate').text((data.accuracyRate || 0) + '%');
            $('#statCorrectVotes').text((data.correctVotes || 0) + ' Jawaban Benar');

            // 2. Incomplete Booths Alert
            const incomplete = data.incompleteBooths || [];
            if (incomplete.length > 0) {
                let badgesHtml = '';
                incomplete.forEach(function(ib) {
                    const addUrl = `{{ url('/pool-qcc/admin/questions') }}?booth_id=${ib.id}`;
                    badgesHtml += `
                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-white border border-amber-300 text-amber-900 dark:bg-amber-500/20 dark:text-amber-300 dark:border-amber-500/30 shadow-xs">
                            <strong>${escapeHtml(ib.code)}</strong> - ${escapeHtml(ib.name)} (${ib.active_questions_count || 0} / 5 Soal)
                            <a href="${addUrl}" class="ml-2 font-bold text-indigo-600 dark:text-indigo-300 hover:underline"><i class="fa-solid fa-plus-circle"></i> Tambah</a>
                        </span>
                    `;
                });

                $('#incompleteBoothsContainer').html(`
                    <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 dark:bg-amber-500/10 dark:border-amber-500/30 dark:text-amber-300 shadow-sm">
                        <div class="flex items-start space-x-3">
                            <i class="fa-solid fa-triangle-exclamation text-2xl mt-0.5 text-amber-600 dark:text-amber-400"></i>
                            <div class="flex-1">
                                <h4 class="font-bold text-base text-slate-900 dark:text-white">Peringatan: Terdapat Booth yang Belum Memenuhi Minimal 5 Soal!</h4>
                                <p class="text-xs text-amber-800 dark:text-amber-200/80 mt-1">Setiap booth wajib memiliki minimal 5 pertanyaan aktif agar kuis random peserta dapat berjalan optimal.</p>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    ${badgesHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                `).removeClass('hidden');
            } else {
                $('#incompleteBoothsContainer').addClass('hidden').empty();
            }

            // 3. Top Booths
            const topBooths = data.topBooths || [];
            if (topBooths.length === 0) {
                $('#topBoothsContainer').html('<div class="py-6 text-center text-sm text-slate-500">Belum ada data booth.</div>');
            } else {
                let tbHtml = '';
                topBooths.forEach(function(tb, idx) {
                    let rankBg = 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
                    if (idx === 0) rankBg = 'bg-amber-400 text-slate-950 font-black';
                    else if (idx === 1) rankBg = 'bg-slate-200 text-slate-800 font-bold';
                    else if (idx === 2) rankBg = 'bg-amber-700 text-white';

                    tbHtml += `
                        <div class="py-3.5 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs ${rankBg}">
                                    #${idx + 1}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">${escapeHtml(tb.name)}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">${escapeHtml(tb.category || 'Circle QCC')} &bull; Kode: <span class="font-mono font-bold">${escapeHtml(tb.code)}</span></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-black text-emerald-600 dark:text-emerald-400">${Number(tb.total_points || 0).toLocaleString()} Pts</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">${Number(tb.total_voters || 0).toLocaleString()} kali di-pool</div>
                            </div>
                        </div>
                    `;
                });
                $('#topBoothsContainer').html(tbHtml);
            }

            // 4. Top Participants
            const topParticipants = data.topParticipants || [];
            if (topParticipants.length === 0) {
                $('#topParticipantsContainer').html('<div class="py-6 text-center text-sm text-slate-500">Belum ada peserta tapping ID Card.</div>');
            } else {
                let tpHtml = '';
                topParticipants.forEach(function(tp, idx) {
                    let rankBg = 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
                    if (idx === 0) rankBg = 'bg-cyan-500 text-white';

                    tpHtml += `
                        <div class="py-3.5 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs ${rankBg}">
                                    #${idx + 1}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white text-sm">${escapeHtml(tp.name)}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">NIK: ${escapeHtml(tp.nik || '-')} &bull; ${escapeHtml(tp.department || 'Umum')}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-black text-indigo-600 dark:text-indigo-400">${Number(tp.total_score || 0).toLocaleString()} Pts</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">${tp.booths_voted_count || 0} / 10 Booth Selesai</div>
                            </div>
                        </div>
                    `;
                });
                $('#topParticipantsContainer').html(tpHtml);
            }

            // 5. Recent Votes
            const recentVotes = data.recentVotes || [];
            if (recentVotes.length === 0) {
                $('#recentVotesTableBody').html('<tr><td colspan="6" class="py-6 text-center text-slate-500 text-sm">Belum ada riwayat pooling yang tercatat.</td></tr>');
            } else {
                let rvHtml = '';
                recentVotes.forEach(function(rv) {
                    const statusBadge = rv.is_correct
                        ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                               <i class="fa-solid fa-check mr-1"></i> Benar
                           </span>`
                        : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                               <i class="fa-solid fa-xmark mr-1"></i> Salah
                           </span>`;

                    const pointClass = rv.points_awarded > 0
                        ? 'text-emerald-600 dark:text-emerald-400'
                        : 'text-slate-400';

                    rvHtml += `
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                            <td class="py-3 px-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">${escapeHtml(rv.created_at_formatted)}</td>
                            <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">${escapeHtml(rv.participant_name)}</td>
                            <td class="py-3 px-4 font-medium">${escapeHtml(rv.booth_name)} ${rv.booth_code ? '(' + escapeHtml(rv.booth_code) + ')' : ''}</td>
                            <td class="py-3 px-4 text-xs">Pilihan: <strong class="text-slate-900 dark:text-white">${escapeHtml(rv.participant_answer)}</strong></td>
                            <td class="py-3 px-4 text-center">
                                ${statusBadge}
                            </td>
                            <td class="py-3 px-4 text-right font-black ${pointClass}">
                                +${rv.points_awarded}
                            </td>
                        </tr>
                    `;
                });
                $('#recentVotesTableBody').html(rvHtml);
            }
        },
        error: function(xhr) {
            $('#refreshIcon').removeClass('fa-spin');
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Dashboard',
                text: 'Terjadi kesalahan saat memuat data statistik monitoring.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });
}

$(document).ready(function() {
    loadDashboardData();
});
</script>
@endsection
