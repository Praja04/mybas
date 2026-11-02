@extends('pool_qcc.layout')

@section('title', 'Log Jawaban Pooling - Pool QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                <a href="{{ route('pool-qcc.admin.dashboard') }}" class="hover:underline">Admin</a>
                <span>&bull;</span>
                <span class="text-slate-500">Log Votes</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Log Aktivitas Pooling</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Audit trail seluruh jawaban pertanyaan yang diberikan oleh peserta untuk setiap booth.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm">
                Total Vote: {{ $votes->total() }} Kali
            </span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel rounded-2xl p-4 sm:p-5 mb-8 shadow-sm">
        <form method="GET" action="{{ route('pool-qcc.admin.votes') }}" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Filter Booth:</label>
                <select name="booth_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:border-indigo-500 shadow-sm">
                    <option value="">-- Semua Booth --</option>
                    @foreach($booths as $b)
                        <option value="{{ $b->id }}" {{ request('booth_id') == $b->id ? 'selected' : '' }}>
                            [{{ $b->code }}] {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Status Jawaban:</label>
                <select name="status" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-xs font-medium focus:outline-none focus:border-indigo-500 shadow-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="correct" {{ request('status') == 'correct' ? 'selected' : '' }}>Benar Saja</option>
                    <option value="wrong" {{ request('status') == 'wrong' ? 'selected' : '' }}>Salah Saja</option>
                </select>
            </div>

            @if(request('booth_id') || request('status'))
                <div class="self-end pb-0.5">
                    <a href="{{ route('pool-qcc.admin.votes') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold border border-slate-200 dark:border-slate-700 inline-flex items-center space-x-1">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Reset Filter</span>
                    </a>
                </div>
            @endif
        </form>
    </div>

    <!-- Votes Table -->
    <div class="glass-panel rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Peserta</th>
                        <th class="py-3 px-4">Booth</th>
                        <th class="py-3 px-4">Pertanyaan & Jawaban</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($votes as $v)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                        <td class="py-3 px-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $v->created_at ? $v->created_at->format('H:i:s') : '-' }}<br>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $v->created_at ? $v->created_at->format('d/m/Y') : '' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $v->participant->name ?? 'N/A' }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400">NIK: {{ $v->participant->nik ?? '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-xs">
                            <span class="font-black text-indigo-700 dark:text-indigo-400 font-mono">[{{ $v->booth->code ?? 'N/A' }}]</span> 
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $v->booth->name ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs max-w-md">
                            <div class="text-slate-800 dark:text-slate-200 line-clamp-1 mb-1 font-bold">{{ $v->question->question_text ?? 'Soal telah dihapus' }}</div>
                            <div class="flex items-center space-x-2 text-[11px]">
                                <span>Pilihan: <strong class="{{ $v->is_correct ? 'text-emerald-600 dark:text-emerald-400 font-black' : 'text-rose-600 dark:text-rose-400 font-black' }}">{{ $v->participant_answer }}</strong></span>
                                <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                                <span class="text-slate-500">Kunci: <strong class="text-emerald-600 dark:text-emerald-400 font-black">{{ $v->question->correct_answer ?? '-' }}</strong></span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($v->is_correct)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                    <i class="fa-solid fa-check mr-1"></i> Benar
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                    <i class="fa-solid fa-xmark mr-1"></i> Salah
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-black {{ $v->points_awarded > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                            +{{ $v->points_awarded }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            Belum ada catatan vote yang cocok dengan filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $votes->links() }}
    </div>
</div>
@endsection
