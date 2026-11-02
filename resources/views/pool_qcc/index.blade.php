@extends('pool_qcc.layout')

@section('title', 'Portal Pool QCC - Quality Control Circle Exhibition')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-700 via-indigo-600 to-cyan-600 dark:from-indigo-950 dark:via-slate-900 dark:to-purple-950 text-white p-8 sm:p-12 mb-12 shadow-xl border border-indigo-200 dark:border-indigo-500/20">
        <div class="max-w-3xl relative z-10">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur border border-white/20 mb-4 shadow-xs">
                <i class="fa-solid fa-sparkles mr-1.5 text-amber-300"></i> Event Booth Innovation & Knowledge Pooling
            </span>
            <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight mb-4">
                Selamat Datang di <br>
                <span class="text-cyan-200">POOL QCC Challenge</span>
            </h1>
            <p class="text-indigo-100 dark:text-slate-300 text-base sm:text-lg mb-8 leading-relaxed font-medium">
                Setiap booth pameran QCC memiliki perangkat stand masing-masing. Peserta mengunjungi booth pilihan (maksimal <strong>3 booth</strong>), melakukan <strong>tapping ID card</strong> di booth tersebut, dan menjawab pertanyaan acak. Jawaban benar memberikan <strong>+1 Poin</strong> untuk Peserta dan Booth!
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('pool-qcc.booths-index') }}" class="px-6 py-3.5 rounded-xl font-bold text-indigo-900 bg-white hover:bg-indigo-50 shadow-lg flex items-center space-x-2 transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-store text-lg text-indigo-600"></i>
                    <span>Pilih Layar Stand Booth</span>
                </a>
                <a href="{{ route('pool-qcc.admin.dashboard') }}" class="px-6 py-3.5 rounded-xl font-bold text-white bg-indigo-900/60 hover:bg-indigo-900 border border-white/20 flex items-center space-x-2 transition">
                    <i class="fa-solid fa-gears text-lg text-cyan-300"></i>
                    <span>Kelola Booth & Soal (Admin)</span>
                </a>
                <a href="{{ route('pool-qcc.leaderboard') }}" class="px-6 py-3.5 rounded-xl font-bold text-amber-200 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-300/40 flex items-center space-x-2 transition">
                    <i class="fa-solid fa-trophy text-lg text-amber-300"></i>
                    <span>Live Leaderboard</span>
                </a>
            </div>
        </div>
        <!-- Decorative Icon -->
        <div class="hidden lg:block absolute -right-6 -bottom-8 opacity-20 pointer-events-none text-white">
            <i class="fa-solid fa-award text-[300px]"></i>
        </div>
    </div>

    <!-- 3 Quick Access Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <!-- 1. Stand Booth Peserta -->
        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between group">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform shadow-xs">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Layar Stand Booth</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">
                    Buka stand booth di tablet/PC booth masing-masing. Peserta datang, tap ID card pada booth tersebut, jawab kuis acak, dan setelah selesai layar otomatis kembali standby.
                </p>
            </div>
            <a href="{{ route('pool-qcc.booths-index') }}" class="w-full py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white dark:bg-indigo-600/30 dark:hover:bg-indigo-600 dark:text-indigo-200 dark:hover:text-white font-bold text-center border border-indigo-200 dark:border-indigo-500/30 transition text-sm flex items-center justify-center space-x-2">
                <span>Pilih & Buka Layar Booth</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- 2. Admin Management -->
        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between group">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-cyan-50 text-cyan-600 dark:bg-cyan-500/20 dark:text-cyan-400 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform shadow-xs">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Admin Booth & Pertanyaan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">
                    Manajemen pendaftaran booth (kode auto-generate), dropdown departemen, dan bank soal (minimal 5 pertanyaan per booth, default 1 poin per soal).
                </p>
            </div>
            <a href="{{ route('pool-qcc.admin.booths') }}" class="w-full py-2.5 rounded-xl bg-cyan-50 hover:bg-cyan-600 text-cyan-700 hover:text-white dark:bg-cyan-600/30 dark:hover:bg-cyan-600 dark:text-cyan-200 dark:hover:text-white font-bold text-center border border-cyan-200 dark:border-cyan-500/30 transition text-sm flex items-center justify-center space-x-2">
                <span>Kelola Data Booth</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- 3. Leaderboard -->
        <div class="glass-card rounded-2xl p-6 flex flex-col justify-between group">
            <div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400 flex items-center justify-center text-2xl mb-5 group-hover:scale-110 transition-transform shadow-xs">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Live Ranking & Statistik</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed mb-6">
                    Tampilan display layar penuh untuk memantau perolehan skor booth terpopuler, peserta peraih poin tertinggi, serta statistik total pooling.
                </p>
            </div>
            <a href="{{ route('pool-qcc.leaderboard') }}" class="w-full py-2.5 rounded-xl bg-amber-50 hover:bg-amber-500 text-amber-700 hover:text-slate-950 dark:bg-amber-600/30 dark:hover:bg-amber-600 dark:text-amber-200 dark:hover:text-white font-bold text-center border border-amber-200 dark:border-amber-500/30 transition text-sm flex items-center justify-center space-x-2">
                <span>Buka Live Ranking</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>

    <!-- Mekanisme & Aturan Pooling Ringkas -->
    <div class="glass-panel rounded-2xl p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center">
            <i class="fa-solid fa-circle-info text-indigo-600 dark:text-indigo-400 mr-2"></i> Alur Kerja Pooling QCC di Setiap Booth
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">Langkah 1</span>
                <div class="font-bold text-slate-900 dark:text-white mb-1">Kunjungi Booth Pilihan</div>
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">Peserta mendatangi booth yang ingin di-pool (maksimal 3 booth berbeda selama event).</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">Langkah 2</span>
                <div class="font-bold text-slate-900 dark:text-white mb-1">Tapping ID Card di Booth</div>
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">Tempelkan ID Card pada scanner booth tersebut. Sistem memvalidasi kuota dan status vote.</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">Langkah 3</span>
                <div class="font-bold text-slate-900 dark:text-white mb-1">Jawab Pertanyaan Acak</div>
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">Pertanyaan acak dari booth tersebut akan langsung muncul di layar untuk dijawab peserta.</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">Langkah 4</span>
                <div class="font-bold text-slate-900 dark:text-white mb-1">Poin Simultan & Standby</div>
                <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed">Benar = +1 Poin Peserta & Booth. Layar otomatis kembali standby di booth tersebut dalam 5 detik.</p>
            </div>
        </div>
    </div>
</div>
@endsection
