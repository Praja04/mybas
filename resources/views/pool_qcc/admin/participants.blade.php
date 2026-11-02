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
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftar seluruh peserta yang telah melakukan tapping ID card RFID dan progres pooling 3 booth mereka.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm">
                Total: {{ $participants->total() }} Peserta
            </span>
        </div>
    </div>

    <!-- Participants Table -->
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
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($participants as $p)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center space-x-3">
                                @if($p->photo)
                                    <img src="{{ $p->photo }}" alt="{{ $p->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-300 dark:border-slate-700">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center font-bold text-xs border border-indigo-200 dark:border-indigo-500/30">
                                        {{ strtoupper(substr($p->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $p->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">NIK: {{ $p->nik ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-xs text-slate-600 dark:text-slate-400 font-bold">{{ $p->rfid }}</td>
                        <td class="py-3.5 px-4 text-xs font-medium">{{ $p->department ?? '-' }}</td>
                        <td class="py-3.5 px-4 text-center">
                            @if($p->booths_voted_count >= 3)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Selesai (3/3)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                    {{ $p->booths_voted_count }} / 3 Booth
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1 max-w-xs">
                                @forelse($p->votes as $v)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $v->is_correct ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }}">
                                        {{ $v->booth->code ?? 'B' }} ({{ $v->is_correct ? '+'.$v->points_awarded : '0' }})
                                    </span>
                                @empty
                                    <span class="text-xs text-slate-400 italic">Belum pool booth</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <span class="text-base font-black text-emerald-600 dark:text-emerald-400">{{ number_format($p->total_score) }} Pts</span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <form id="reset-participant-{{ $p->id }}" action="{{ route('pool-qcc.admin.participants.reset', $p->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="confirmResetParticipant('{{ $p->id }}', '{{ addslashes($p->name) }}')" class="px-2.5 py-1 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 transition shadow-xs cursor-pointer" title="Reset Kuota & Vote Peserta">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <i class="fa-solid fa-users text-4xl mb-2 block text-slate-400"></i>
                            Belum ada peserta yang melakukan tapping ID card.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $participants->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
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
        customClass: {
            popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800',
            confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
            cancelButton: 'rounded-xl font-bold px-4 py-2 text-xs'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`reset-participant-${id}`).submit();
        }
    });
}
</script>
@endsection
