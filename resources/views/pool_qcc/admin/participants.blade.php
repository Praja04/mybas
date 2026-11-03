@extends('pool_qcc.layout')

@section('title', 'Data Peserta - Pool QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                <a href="{{ route('pool-qcc.admin.dashboard') }}" class="hover:underline">Admin</a>
                <span>&bull;</span>
                <span class="text-slate-500">Peserta</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Data Peserta & Riwayat Tap</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar seluruh peserta yang telah melakukan tapping ID card RFID dan progres pooling 10 booth mereka.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span id="totalParticipantsBadge" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm">
                Total: Memuat...
            </span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="glass-panel rounded-2xl p-4 sm:p-5 mb-8 shadow-sm">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <div class="flex-1 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="search_participant" placeholder="Cari nama karyawan, NIK, kode RFID, atau departemen..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:border-indigo-500 shadow-sm">
            </div>

            <div class="flex items-center space-x-2">
                <select id="filter_participant_status" class="px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:border-indigo-500 shadow-sm">
                    <option value="">-- Semua Status Kuota --</option>
                    <option value="completed">Selesai (10/10 Booth)</option>
                    <option value="in_progress">Belum Selesai (&lt; 10 Booth)</option>
                </select>

                <button type="button" id="btnResetParticipantFilter" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-slate-700 transition cursor-pointer flex items-center space-x-1" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    <span class="hidden sm:inline">Reset</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Participants Table (Client-side rendered via jQuery) -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3.5 px-4">Peserta / NIK</th>
                        <th class="py-3.5 px-4">RFID Card</th>
                        <th class="py-3.5 px-4">Departemen</th>
                        <th class="py-3.5 px-4 text-center">Status Kuota</th>
                        <th class="py-3.5 px-4">Booth Terpilih</th>
                        <th class="py-3.5 px-4 text-right">Total Poin</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="participantsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    <!-- Dynamic client-side rows -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Custom Tailwind Pagination Container -->
    <div id="participantsPagination" class="mt-6"></div>
</div>
@endsection

@section('scripts')
<script>
let currentPage = 1;
let currentSearch = '';
let currentStatus = '';
let searchTimeout;

function escapeHtml(str) {
    if (!str) return '';
    return $('<div>').text(str).html();
}

function renderSkeletonParticipants() {
    let skeletons = '';
    for (let i = 0; i < 6; i++) {
        skeletons += `
            <tr class="animate-pulse">
                <td class="py-3.5 px-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                        <div><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-28 mb-1"></div><div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-16"></div></div>
                    </div>
                </td>
                <td class="py-3.5 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-20"></div></td>
                <td class="py-3.5 px-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-24"></div></td>
                <td class="py-3.5 px-4 text-center"><div class="h-6 bg-slate-200 dark:bg-slate-800 rounded-full w-24 mx-auto"></div></td>
                <td class="py-3.5 px-4"><div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-32"></div></td>
                <td class="py-3.5 px-4 text-right"><div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-14 ml-auto"></div></td>
                <td class="py-3.5 px-4 text-center"><div class="h-7 bg-slate-200 dark:bg-slate-800 rounded-xl w-16 mx-auto"></div></td>
            </tr>
        `;
    }
    $('#participantsTableBody').html(skeletons);
    $('#participantsPagination').empty();
}

function renderParticipantsPagination(pagination) {
    if (!pagination || pagination.total === 0) {
        $('#participantsPagination').empty();
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
            <button type="button" onclick="goToParticipantPage(${current - 1})"
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
        pagesHtml += `<button type="button" onclick="goToParticipantPage(1)" class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">1</button>`;
        if (startPage > 2) {
            pagesHtml += `<span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">...</span>`;
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
                <button type="button" onclick="goToParticipantPage(${p})"
                    class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    ${p}
                </button>
            `;
        }
    }

    if (endPage < last) {
        if (endPage < last - 1) {
            pagesHtml += `<span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">...</span>`;
        }
        pagesHtml += `<button type="button" onclick="goToParticipantPage(${last})" class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">${last}</button>`;
    }

    // Tombol Berikutnya
    if (current < last) {
        pagesHtml += `
            <button type="button" onclick="goToParticipantPage(${current + 1})"
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
                Menampilkan <span class="font-bold text-slate-900 dark:text-white">${from}</span> sampai <span class="font-bold text-slate-900 dark:text-white">${to}</span> dari <span class="font-bold text-slate-900 dark:text-white">${total}</span> peserta
            </div>
            <div class="flex items-center space-x-1.5">
                ${pagesHtml}
            </div>
        </div>
    `;

    $('#participantsPagination').html(html);
}

function goToParticipantPage(page) {
    currentPage = page;
    loadParticipantsData(page, currentSearch, currentStatus);
    $('html, body').animate({ scrollTop: $('#participantsTableBody').offset().top - 120 }, 200);
}

function loadParticipantsData(page = 1, search = '', status = '') {
    renderSkeletonParticipants();

    $.ajax({
        url: "{{ route('pool-qcc.admin.api.participants') }}",
        type: 'GET',
        data: {
            page: page,
            search: search,
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

            $('#totalParticipantsBadge').text('Total: ' + (pagination.total || 0) + ' Peserta');

            if (items.length === 0) {
                $('#participantsTableBody').html(`
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-users text-4xl mb-2 block text-slate-400"></i>
                            Belum ada peserta yang cocok dengan kriteria pencarian.
                        </td>
                    </tr>
                `);
                $('#participantsPagination').empty();
                return;
            }

            let rowsHtml = '';
            items.forEach(function(p) {
                const avatarHtml = p.photo
                    ? `<img src="${p.photo}" alt="${escapeHtml(p.name)}" class="w-9 h-9 rounded-full object-cover border border-slate-300 dark:border-slate-700">`
                    : `<div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-200 dark:border-indigo-500/30">${p.initials}</div>`;

                const quotaBadge = p.booths_voted_count >= 3
                    ? `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                           <i class="fa-solid fa-circle-check mr-1"></i> Selesai (3/3)
                       </span>`
                    : `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                           ${p.booths_voted_count} / 10 Booth
                       </span>`;

                let votesHtml = '';
                if (p.votes && p.votes.length > 0) {
                    p.votes.forEach(function(v) {
                        const vBadgeClass = v.is_correct
                            ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30'
                            : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700';
                        const scoreText = v.is_correct ? '+' + v.points_awarded : '0';
                        votesHtml += `<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold ${vBadgeClass}">${escapeHtml(v.booth_code)} (${scoreText})</span> `;
                    });
                } else {
                    votesHtml = '<span class="text-xs text-slate-400 italic">Belum pool booth</span>';
                }

                const totalScore = Number(p.total_score || 0).toLocaleString();

                rowsHtml += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-3">
                                ${avatarHtml}
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">${escapeHtml(p.name)}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">NIK: ${escapeHtml(p.nik || '-')}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs text-slate-600 dark:text-slate-400 font-bold">${escapeHtml(p.rfid || '-')}</td>
                        <td class="py-3.5 px-4 text-xs font-medium">${escapeHtml(p.department || '-')}</td>
                        <td class="py-3.5 px-4 text-center">
                            ${quotaBadge}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                ${votesHtml}
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <span class="text-base font-black text-emerald-600 dark:text-emerald-400">${totalScore} Pts</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <button type="button" onclick="confirmResetParticipant(${p.id}, '${escapeHtml(p.name)}')" class="px-2.5 py-1 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 transition shadow-xs cursor-pointer" title="Reset Kuota & Vote Peserta">
                                <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                            </button>
                        </td>
                    </tr>
                `;
            });

            $('#participantsTableBody').html(rowsHtml);
            renderParticipantsPagination(pagination);
        },
        error: function(xhr) {
            $('#participantsTableBody').html(`
                <tr>
                    <td colspan="7" class="py-12 text-center text-rose-500">
                        <i class="fa-solid fa-triangle-exclamation text-3xl mb-2 block"></i>
                        Gagal memuat data peserta.
                        <button onclick="loadParticipantsData(currentPage, currentSearch, currentStatus)" class="mt-2 block mx-auto px-3 py-1 bg-indigo-600 text-white rounded-lg text-xs">Coba Lagi</button>
                    </td>
                </tr>
            `);
            $('#participantsPagination').empty();
        }
    });
}

function confirmResetParticipant(id, name) {
    Swal.fire({
        title: 'Reset Peserta?',
        html: `Apakah Anda yakin ingin mereset kuota vote & skor peserta <b>"${name}"</b>?<br><span class="text-rose-500 text-xs font-semibold mt-1.5 block">Peserta dapat melakukan tapping dan voting kuis ulang dari awal.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-rotate-left mr-1"></i> Ya, Reset!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/pool-qcc/admin/participants/${id}/reset`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).catch(xhr => {
                Swal.showValidationMessage(xhr.responseJSON?.message || 'Gagal mereset peserta.');
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
        customClass: {
            popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800',
            confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
            cancelButton: 'rounded-xl font-bold px-4 py-2 text-xs'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Client-side reload via jQuery
            loadParticipantsData(currentPage, currentSearch, currentStatus);

            Swal.fire({
                icon: 'success',
                title: 'Berhasil di-reset!',
                text: result.value?.message || 'Riwayat vote peserta berhasil di-reset.',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800'
                }
            });
        }
    });
}

$(document).ready(function() {
    loadParticipantsData(1);

    $('#search_participant').on('input', function() {
        clearTimeout(searchTimeout);
        const val = $(this).val();
        searchTimeout = setTimeout(function() {
            currentSearch = val;
            currentPage = 1;
            loadParticipantsData(1, currentSearch, currentStatus);
        }, 300);
    });

    $('#filter_participant_status').on('change', function() {
        currentStatus = $(this).val();
        currentPage = 1;
        loadParticipantsData(1, currentSearch, currentStatus);
    });

    $('#btnResetParticipantFilter').on('click', function() {
        $('#search_participant').val('');
        $('#filter_participant_status').val('');
        currentSearch = '';
        currentStatus = '';
        currentPage = 1;
        loadParticipantsData(1);
    });
});
</script>
@endsection
