@extends('pool_qcc.layout')

@section('title', 'Pilih Booth - Pool QCC')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div
                    class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                    <span>Pameran Inovasi QCC</span>
                    <span>&bull;</span>
                    <span class="text-slate-500">Pilih Booth</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Pilih Layar Booth</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pilih booth yang ingin diaktifkan layarnya atau
                    ingin Anda kunjungi untuk melakukan tapping ID card dan pooling.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <span
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 shadow-sm">
                    Total: {{ $booths->count() }} Booth Aktif
                </span>
            </div>
        </div>

        <!-- Alert Mekanisme Baru -->
        <div
            class="mb-8 p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2.5">
                <i class="fa-solid fa-circle-info text-base text-indigo-600 dark:text-indigo-400"></i>
                <span><strong>Alur Tapping Booth:</strong> Buka layar booth di bawah, tempelkan ID Card Anda pada scanner
                    booth tersebut, lalu jawab pertanyaan acak yang muncul. Selesai menjawab, layar akan otomatis kembali
                    standby di booth tersebut.</span>
            </div>
        </div>

        <!-- Booths Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($booths as $b)
                <div
                    class="glass-card rounded-2xl overflow-hidden flex flex-col justify-between border {{ $b->active_questions_count >= 5 ? 'border-slate-200 dark:border-slate-800' : 'border-amber-400 dark:border-amber-500/50' }}">
                    <!-- Card Header / Image -->
                    <div class="relative h-44 bg-slate-100 dark:bg-slate-900 overflow-hidden">
                        @if ($b->image)
                            <img src="{{ asset($b->image) }}" alt="{{ $b->name }}" class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-tr from-slate-100 to-indigo-50 dark:from-slate-900 dark:to-indigo-950/60 text-slate-400">
                                <i class="fa-solid fa-store text-5xl mb-2 text-indigo-400/50"></i>
                                <span class="text-xs text-slate-500 font-medium">Stand Booth QCC</span>
                            </div>
                        @endif
                        <!-- Code Badge -->
                        <div class="absolute top-3 left-3">
                            <span
                                class="px-3 py-1 rounded-xl text-xs font-black bg-slate-900/90 text-white backdrop-blur shadow-sm tracking-wider">
                                {{ $b->code }}
                            </span>
                        </div>
                        <!-- Status Badge -->
                        <div class="absolute top-3 right-3 flex items-center space-x-1">
                            @if ($b->active_questions_count >= 5)
                                <span
                                    class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-md flex items-center space-x-1">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                    <span>{{ $b->active_questions_count }} Soal</span>
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 shadow-md flex items-center space-x-1">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                    <span>{{ $b->active_questions_count }}/5 Soal</span>
                                </span>
                            @endif
                        </div>
                        <!-- Dept Badge -->
                        <div class="absolute bottom-3 left-3">
                            <span
                                class="text-xs px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white font-bold shadow-sm backdrop-blur">
                                {{ $b->category ?? 'General' }}
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 leading-snug">
                                {{ $b->name }}</h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 mb-4 leading-relaxed">
                                {{ $b->description ?? 'Booth inovasi dan continuous improvement QCC.' }}
                            </p>

                            <div
                                class="text-xs text-slate-700 dark:text-slate-300 py-2 border-y border-slate-100 dark:border-slate-800 mb-4 flex items-center justify-between">
                                <span class="text-slate-500 text-xs">PIC Booth:</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $b->pic_name ?? '-' }}</span>
                            </div>

                            <!-- Scores & Voters -->
                            <div
                                class="flex items-center justify-between text-xs mb-4 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                                <div>
                                    <span class="text-slate-500 block text-[11px] font-medium">Total Skor Booth:</span>
                                    <span
                                        class="text-base font-black text-emerald-600 dark:text-emerald-400">{{ number_format($b->total_points) }}
                                        Pts</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-500 block text-[11px] font-medium">Telah Di-pool:</span>
                                    <span
                                        class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ $b->total_voters }}
                                        Peserta</span>
                                </div>
                            </div>
                        </div>

                        <!-- Call to action button: Open Booth Screen -->
                        <div class="pt-2">
                            @if ($b->active_questions_count >= 5)
                                <a href="{{ route('pool-qcc.booth-station', $b->id) }}"
                                    class="w-full py-3 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/30 flex items-center justify-center space-x-2 transition transform hover:-translate-y-0.5">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                    <span>Buka Layar Stand Booth Ini</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            @else
                                <button disabled
                                    class="w-full py-3 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 cursor-not-allowed flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-lock"></i>
                                    <span>Soal Belum Lengkap (< 5)</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center glass-panel rounded-2xl">
                    <i class="fa-solid fa-store-slash text-5xl text-slate-400 mb-3 block"></i>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">Belum Ada Booth Terdaftar</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Silakan hubungi administrator untuk mendaftarkan
                        booth pameran QCC.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
