@extends('pool_qcc.layout')

@section('title', 'Log Jawaban Pooling - Pool QCC')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Page -->
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div
                    class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                    <a href="{{ route('pool-qcc.admin.dashboard') }}" class="hover:underline">Admin</a>
                    <span>&bull;</span>
                    <span class="text-slate-500">Log Votes</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Log Aktivitas Pooling</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Audit trail seluruh jawaban pertanyaan yang
                    diberikan oleh peserta untuk setiap booth.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-2">
                <a href="{{ route('pool-qcc.admin.votes.export') }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold bg-teal-600 hover:bg-teal-500 text-white shadow-sm transition flex items-center space-x-1.5 cursor-pointer"
                    title="Download Seluruh Log Riwayat Voting ke File Excel">
                    <i class="fa-solid fa-file-arrow-down text-[11px]"></i>
                    <span>Download Excel</span>
                </a>
                <span id="totalVotesBadge"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm">
                    Total Vote: Memuat...
                </span>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="glass-panel rounded-2xl p-4 sm:p-5 mb-8 shadow-sm">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Filter Booth:</label>
                    <select id="filter_booth"
                        class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:border-indigo-500 shadow-sm">
                        <option value="">-- Semua Booth --</option>
                        @foreach ($booths as $b)
                            <option value="{{ $b->id }}">
                                [{{ $b->code }}] {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Status Jawaban:</label>
                    <select id="filter_status"
                        class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:border-indigo-500 shadow-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="correct">Benar Saja</option>
                        <option value="wrong">Salah Saja</option>
                    </select>
                </div>

                <div class="self-end pb-0.5">
                    <button type="button" id="btnResetFilter"
                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-slate-700 inline-flex items-center space-x-1 cursor-pointer transition">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Reset Filter</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Votes Table (Client-side rendered via jQuery) -->
        <div class="glass-panel rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="py-3 px-4">Waktu</th>
                            <th class="py-3 px-4">Peserta</th>
                            <th class="py-3 px-4">Booth</th>
                            <th class="py-3 px-4">Pertanyaan & Jawaban</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Poin</th>
                        </tr>
                    </thead>
                    <tbody id="votesTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <!-- Dynamic client-side rows -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Custom Tailwind Pagination Container -->
        <div id="votesPagination" class="mt-6"></div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentPage = 1;
        let currentBoothId = '';
        let currentStatus = '';

        function escapeHtml(str) {
            if (!str) return '';
            return $('<div>').text(str).html();
        }

        function renderSkeletonVotes() {
            let skeletons = '';
            for (let i = 0; i < 6; i++) {
                skeletons += `
            <tr class="animate-pulse">
                <td class="py-3.5 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-16"></div></td>
                <td class="py-3.5 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-28 mb-1"></div><div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-20"></div></td>
                <td class="py-3.5 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-24"></div></td>
                <td class="py-3.5 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-48 mb-1"></div><div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-32"></div></td>
                <td class="py-3.5 px-4 text-center"><div class="h-5 bg-slate-200 dark:bg-slate-800 rounded-lg w-16 mx-auto"></div></td>
                <td class="py-3.5 px-4 text-right"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8 ml-auto"></div></td>
            </tr>
        `;
            }
            $('#votesTableBody').html(skeletons);
            $('#votesPagination').empty();
        }

        function renderVotesPagination(pagination) {
            if (!pagination || pagination.total === 0) {
                $('#votesPagination').empty();
                return;
            }

            const current = pagination.current_page;
            const last = pagination.last_page;
            const from = pagination.from || 0;
            const to = pagination.to || 0;
            const total = pagination.total || 0;

            let pagesHtml = '';

            // Tombol Sebelumnya
            if (current > 1) {
                pagesHtml += `
            <button type="button" onclick="goToVotePage(${current - 1})"
                class="px-3 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-sm transition flex items-center gap-1 cursor-pointer">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                <span class="hidden sm:inline">Sebelumnya</span>
            </button>
        `;
            } else {
                pagesHtml += `
            <span class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-800 cursor-not-allowed flex items-center gap-1">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                <span class="hidden sm:inline">Sebelumnya</span>
            </span>
        `;
            }

            // Numbered buttons
            let startPage = Math.max(1, current - 2);
            let endPage = Math.min(last, current + 2);

            if (startPage > 1) {
                pagesHtml +=
                    `<button type="button" onclick="goToVotePage(1)" class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">1</button>`;
                if (startPage > 2) {
                    pagesHtml +=
                        `<span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">...</span>`;
                }
            }

            for (let p = startPage; p <= endPage; p++) {
                if (p === current) {
                    pagesHtml += `
                <span class="w-8 h-8 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-md shadow-indigo-600/30 flex items-center justify-center">
                    ${p}
                </span>
            `;
                } else {
                    pagesHtml += `
                <button type="button" onclick="goToVotePage(${p})"
                    class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    ${p}
                </button>
            `;
                }
            }

            if (endPage < last) {
                if (endPage < last - 1) {
                    pagesHtml +=
                        `<span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">...</span>`;
                }
                pagesHtml +=
                    `<button type="button" onclick="goToVotePage(${last})" class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">${last}</button>`;
            }

            // Tombol Berikutnya
            if (current < last) {
                pagesHtml += `
            <button type="button" onclick="goToVotePage(${current + 1})"
                class="px-3 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-sm transition flex items-center gap-1 cursor-pointer">
                <span class="hidden sm:inline">Berikutnya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
        `;
            } else {
                pagesHtml += `
            <span class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-800 cursor-not-allowed flex items-center gap-1">
                <span class="hidden sm:inline">Berikutnya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </span>
        `;
            }

            const html = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl glass-panel">
            <div class="text-xs text-slate-600 dark:text-slate-400">
                Menampilkan <span class="font-bold text-slate-900 dark:text-white">${from}</span> sampai <span class="font-bold text-slate-900 dark:text-white">${to}</span> dari <span class="font-bold text-slate-900 dark:text-white">${total}</span> vote
            </div>
            <div class="flex items-center space-x-1.5">
                ${pagesHtml}
            </div>
        </div>
    `;

            $('#votesPagination').html(html);
        }

        function goToVotePage(page) {
            currentPage = page;
            loadVotesData(page, currentBoothId, currentStatus);
            $('html, body').animate({
                scrollTop: $('#votesTableBody').offset().top - 120
            }, 200);
        }

        function loadVotesData(page = 1, boothId = '', status = '') {
            renderSkeletonVotes();

            $.ajax({
                url: "{{ route('pool-qcc.admin.api.votes') }}",
                type: 'GET',
                data: {
                    page: page,
                    booth_id: boothId,
                    status: status
                },
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    const items = response.data || [];
                    const pagination = response.pagination || {};
                    currentPage = pagination.current_page || page;

                    $('#totalVotesBadge').text('Total Vote: ' + (pagination.total || 0) + ' Kali');

                    if (items.length === 0) {
                        $('#votesTableBody').html(`
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-receipt text-4xl mb-2 block text-slate-400"></i>
                            Belum ada catatan vote yang cocok dengan filter.
                        </td>
                    </tr>
                `);
                        $('#votesPagination').empty();
                        return;
                    }

                    let rowsHtml = '';
                    items.forEach(function(v) {
                        const statusBadge = v.is_correct ?
                            `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                           <i class="fa-solid fa-check mr-1"></i> Benar
                       </span>` :
                            `<span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                           <i class="fa-solid fa-xmark mr-1"></i> Salah
                       </span>`;

                        const ansClass = v.is_correct ?
                            'text-emerald-600 dark:text-emerald-400 font-black' :
                            'text-rose-600 dark:text-rose-400 font-black';

                        const pointClass = v.points_awarded > 0 ?
                            'text-emerald-600 dark:text-emerald-400' :
                            'text-slate-400';

                        rowsHtml += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            ${escapeHtml(v.created_at_time)}<br>
                            <span class="text-[10px] text-slate-400 font-mono">${escapeHtml(v.created_at_date)}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white text-xs">${escapeHtml(v.participant_name)}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">NIK: ${escapeHtml(v.participant_nik)}</div>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <span class="font-black text-indigo-700 dark:text-indigo-400 font-mono">[${escapeHtml(v.booth_code)}]</span> 
                            <span class="font-bold text-slate-800 dark:text-slate-200">${escapeHtml(v.booth_name)}</span>
                        </td>
                        <td class="py-3 px-4 text-xs max-w-md">
                            <div class="text-slate-800 dark:text-slate-200 line-clamp-1 mb-1 font-bold">${escapeHtml(v.question_text)}</div>
                            <div class="flex items-center space-x-2 text-[11px]">
                                <span>Pilihan: <strong class="${ansClass}">${escapeHtml(v.participant_answer)}</strong></span>
                                <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                                <span class="text-slate-500">Kunci: <strong class="text-emerald-600 dark:text-emerald-400 font-black">${escapeHtml(v.correct_answer)}</strong></span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            ${statusBadge}
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-black ${pointClass}">
                            +${v.points_awarded}
                        </td>
                    </tr>
                `;
                    });

                    $('#votesTableBody').html(rowsHtml);
                    renderVotesPagination(pagination);
                },
                error: function(xhr) {
                    $('#votesTableBody').html(`
                <tr>
                    <td colspan="6" class="py-12 text-center text-rose-500">
                        <i class="fa-solid fa-triangle-exclamation text-3xl mb-2 block"></i>
                        Gagal memuat log aktivitas vote.
                        <button onclick="loadVotesData(currentPage, currentBoothId, currentStatus)" class="mt-2 block mx-auto px-3 py-1 bg-indigo-600 text-white rounded-lg text-xs">Coba Lagi</button>
                    </td>
                </tr>
            `);
                    $('#votesPagination').empty();
                }
            });
        }

        $(document).ready(function() {
            loadVotesData(1);

            $('#filter_booth, #filter_status').on('change', function() {
                currentBoothId = $('#filter_booth').val();
                currentStatus = $('#filter_status').val();
                currentPage = 1;
                loadVotesData(1, currentBoothId, currentStatus);
            });

            $('#btnResetFilter').on('click', function() {
                $('#filter_booth').val('');
                $('#filter_status').val('');
                currentBoothId = '';
                currentStatus = '';
                currentPage = 1;
                loadVotesData(1);
            });
        });
    </script>
@endsection
