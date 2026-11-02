<!-- ===================================================== -->
<!-- MAIN TWO-COLUMN 3D ARENA (BOOTH PODIUM vs PARTICIPANT PODIUM) -->
<!-- ===================================================== -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 relative z-10 flex-1 items-stretch mb-2">

    <!-- ===================================================== -->
    <!-- KOLOM 1: THE KING OF BOOTHS (STAND TERFAVORIT) 🎪      -->
    <!-- ===================================================== -->
    <div class="rounded-3xl p-4 sm:p-5 border-4 border-amber-400/80 bg-white/95 dark:bg-slate-900/95 flex flex-col justify-between shadow-2xl relative transition-all game-card-3d">
        
        <!-- Column 3D Game Title Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b-2 border-amber-300/40 dark:border-amber-500/20">
            <div class="flex items-center space-x-2.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-400 to-yellow-300 border-2 border-amber-500 shadow-md flex items-center justify-center text-xl text-amber-950 anim-game-float">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-1.5 uppercase tracking-wide">
                        <span>THE KING OF BOOTHS</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 font-black border border-amber-600 shadow-xs">Stand Favorit</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold">Peringkat akumulasi poin & pooling antar booth inovasi</p>
                </div>
            </div>
            <span class="text-xs font-black text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/60 px-2.5 py-1 rounded-xl border border-amber-300 dark:border-amber-700/50 flex items-center gap-1">
                <i class="fa-solid fa-trophy text-amber-500"></i> Top Stand
            </span>
        </div>

        @php
            $topBooths = $booths->take(3);
            $b1 = $topBooths->get(0);
            $b2 = $topBooths->get(1);
            $b3 = $topBooths->get(2);
        @endphp

        <!-- 3D CARTOON STONE/MARBLE PODIUM PLATFORM -->
        @if ($b1)
            <div class="bg-gradient-to-b from-amber-100/70 via-amber-50/50 to-orange-100/70 dark:from-slate-800/80 dark:via-slate-900/90 dark:to-slate-950 p-3 sm:p-4 rounded-3xl border-3 border-amber-300 dark:border-amber-600/40 shadow-inner mb-4">
                
                <div class="grid grid-cols-3 gap-2 sm:gap-3 items-end pt-4 pb-2">
                    
                    <!-- ================= JUARA 2 BOOTH (SILVER) ================= -->
                    @if ($b2)
                        <div class="flex flex-col items-center text-center">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-2">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-slate-200 via-white to-slate-300 border-3 border-slate-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if($b2->image)
                                        <img src="{{ Str::startsWith($b2->image, ['http://', 'https://']) ? $b2->image : (Str::startsWith($b2->image, ['uploads/', 'storage/']) ? asset($b2->image) : asset('storage/' . $b2->image)) }}" alt="{{ $b2->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-rocket text-2xl text-slate-600\'></i>';">
                                    @else
                                        <i class="fa-solid fa-rocket text-2xl text-slate-600"></i>
                                    @endif
                                </div>
                                <!-- Roman Pillar Capital Cap -->
                                <div class="absolute -bottom-2 -left-2 -right-2 h-2.5 bg-gradient-to-r from-slate-400 via-slate-200 to-slate-400 rounded-full border border-slate-500 shadow-xs"></div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div class="w-full px-1.5 py-1 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-1.5 max-w-[130px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[11px] sm:text-xs truncate" title="{{ $b2->name }}">{{ $b2->name }}</h4>
                                <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 block truncate">{{ $b2->category }}</span>
                            </div>

                            <!-- 3D Shield Badge #2 -->
                            <div class="shield-3d-2 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-slate-900 text-xs sm:text-sm -mb-3 z-10 gap-0.5 shadow-sm">
                                <i class="fa-solid fa-medal text-slate-900"></i> 2
                            </div>

                            <!-- 3D Pedestal Body (Silver) -->
                            <div class="pedestal-3d-silver w-full pt-4 pb-2.5 px-2 rounded-2xl text-slate-900 dark:text-white h-24 sm:h-28 flex flex-col justify-center">
                                <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-slate-900 leading-tight flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-coins text-amber-500"></i>
                                    <span>{{ number_format($b2->total_points) }}</span>
                                </span>
                                <span class="text-[9px] font-extrabold text-slate-600 uppercase">Pts</span>
                                <span class="text-[9px] font-bold text-slate-700 bg-white/70 rounded-full px-1.5 py-0.2 mt-1 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-check-to-slot text-slate-500"></i> {{ $b2->total_voters }} Pool
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                    <!-- ================= JUARA 1 BOOTH (GOLD SULTAN) ================= -->
                    <div class="flex flex-col items-center text-center transform -translate-y-2">
                        <!-- Floating Golden Crown -->
                        <div class="anim-crown text-3xl sm:text-4xl text-amber-500 filter drop-shadow-md -mb-1">
                            <i class="fa-solid fa-crown"></i>
                        </div>

                        <!-- 3D Avatar Frame (Gold Ornament) -->
                        <div class="relative mb-2">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-amber-200 via-yellow-100 to-amber-300 border-4 border-amber-400 shadow-lg flex items-center justify-center overflow-hidden">
                                @if($b1->image)
                                    <img src="{{ Str::startsWith($b1->image, ['http://', 'https://']) ? $b1->image : (Str::startsWith($b1->image, ['uploads/', 'storage/']) ? asset($b1->image) : asset('storage/' . $b1->image)) }}" alt="{{ $b1->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-store text-3xl text-amber-600\'></i>';">
                                @else
                                    <i class="fa-solid fa-store text-3xl text-amber-600"></i>
                                @endif
                            </div>
                            <!-- Roman Pillar Capital Cap (Golden curls) -->
                            <div class="absolute -bottom-2 -left-2 -right-2 h-3 bg-gradient-to-r from-amber-500 via-yellow-200 to-amber-500 rounded-full border-2 border-amber-600 shadow-sm"></div>
                        </div>

                        <!-- 3D Name Tag Pill (Gold) -->
                        <div class="w-full px-2 py-1.5 rounded-xl bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-300 border-2 border-amber-500 shadow-md mb-1.5 max-w-[145px]">
                            <h4 class="font-black text-slate-950 text-xs sm:text-sm truncate" title="{{ $b1->name }}">{{ $b1->name }}</h4>
                            <span class="text-[10px] font-extrabold text-amber-900 block truncate">{{ $b1->category }}</span>
                        </div>

                        <!-- 3D Shield Badge #1 (With Laurel) -->
                        <div class="shield-3d-1 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-amber-950 text-sm sm:text-base -mb-3.5 z-10 anim-coin gap-0.5 shadow-sm">
                            <i class="fa-solid fa-trophy text-slate-950 text-xs sm:text-sm drop-shadow-xs"></i> <span class="text-slate-950 font-black">1</span>
                        </div>

                        <!-- 3D Pedestal Body (Gold, Taller) -->
                        <div class="pedestal-3d-gold w-full pt-5 pb-3 px-2 rounded-2xl text-slate-950 h-32 sm:h-36 flex flex-col justify-center">
                            <span class="text-sm sm:text-base font-black text-amber-950 leading-tight flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-coins text-amber-600"></i>
                                <span>{{ number_format($b1->total_points) }}</span>
                            </span>
                            <span class="text-[10px] font-black text-amber-900 uppercase">Points</span>
                            <span class="text-[10px] font-black text-amber-950 bg-amber-200/90 rounded-full px-2 py-0.5 mt-1 border border-amber-400 flex items-center justify-center gap-1">
                                <i class="fa-solid fa-check-to-slot text-amber-700"></i> {{ $b1->total_voters }} Pemilih Terbanyak!
                            </span>
                        </div>
                    </div>

                    <!-- ================= JUARA 3 BOOTH (BRONZE) ================= -->
                    @if ($b3)
                        <div class="flex flex-col items-center text-center">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-2">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-orange-200 via-amber-100 to-orange-300 border-3 border-orange-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if($b3->image)
                                        <img src="{{ Str::startsWith($b3->image, ['http://', 'https://']) ? $b3->image : (Str::startsWith($b3->image, ['uploads/', 'storage/']) ? asset($b3->image) : asset('storage/' . $b3->image)) }}" alt="{{ $b3->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-lightbulb text-2xl text-amber-500\'></i>';">
                                    @else
                                        <i class="fa-solid fa-lightbulb text-2xl text-amber-500"></i>
                                    @endif
                                </div>
                                <!-- Roman Pillar Capital Cap -->
                                <div class="absolute -bottom-2 -left-2 -right-2 h-2.5 bg-gradient-to-r from-orange-500 via-amber-200 to-orange-500 rounded-full border border-orange-600 shadow-xs"></div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div class="w-full px-1.5 py-1 rounded-xl bg-white dark:bg-slate-800 border-2 border-orange-300 dark:border-slate-600 shadow-sm mb-1.5 max-w-[130px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[11px] sm:text-xs truncate" title="{{ $b3->name }}">{{ $b3->name }}</h4>
                                <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 block truncate">{{ $b3->category }}</span>
                            </div>

                            <!-- 3D Shield Badge #3 -->
                            <div class="shield-3d-3 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-white text-xs sm:text-sm -mb-3 z-10 gap-0.5 shadow-sm">
                                <i class="fa-solid fa-medal text-white"></i> 3
                            </div>

                            <!-- 3D Pedestal Body (Bronze) -->
                            <div class="pedestal-3d-bronze w-full pt-4 pb-2.5 px-2 rounded-2xl text-slate-900 h-24 sm:h-28 flex flex-col justify-center">
                                <span class="text-xs sm:text-sm font-black text-orange-950 leading-tight flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-coins text-amber-600"></i>
                                    <span>{{ number_format($b3->total_points) }}</span>
                                </span>
                                <span class="text-[9px] font-extrabold text-orange-900 uppercase">Pts</span>
                                <span class="text-[9px] font-bold text-orange-950 bg-white/70 rounded-full px-1.5 py-0.2 mt-1 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-check-to-slot text-orange-800"></i> {{ $b3->total_voters }} Pool
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                </div>

                <!-- 3D Reward Bar below Podium -->
                <div class="mt-2 py-1.5 px-3 rounded-2xl bg-amber-400 text-slate-950 font-black text-xs text-center border-2 border-amber-600 shadow-sm flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-gift text-amber-900"></i>
                    <span>STAND INOVASI FAVORIT QCC 2026</span>
                    <i class="fa-solid fa-gift text-amber-900"></i>
                </div>
            </div>
        @else
            <div class="py-12 text-center text-slate-400 text-xs italic">
                Belum ada data booth yang terdaftar.
            </div>
        @endif

        <!-- ===================================================== -->
        <!-- 3D GAME RANKING PILL CARDS (RANK 4 KE BAWAH)          -->
        <!-- ===================================================== -->
        <div class="mt-auto">
            <div class="text-[11px] font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                <span>Peringkat Booth Lainnya</span>
                <span class="text-slate-500 dark:text-slate-400 font-bold">{{ max(0, $booths->count() - 3) }} Booth Bertanding</span>
            </div>
            
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                @forelse($booths->slice(3) as $otherBooth)
                    <!-- Casual Game 3D Pill Card -->
                    <div class="p-2 sm:p-2.5 rounded-2xl bg-white hover:bg-amber-50/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border-2 border-slate-200 dark:border-slate-700/80 shadow-[0_4px_0_#cbd5e1] dark:shadow-[0_4px_0_#1e293b] flex items-center justify-between text-xs transition-all active:translate-y-1 active:shadow-none">
                        
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <!-- 3D Rank Embossed Circle -->
                            <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 font-black text-slate-700 dark:text-slate-200 text-xs flex items-center justify-center flex-shrink-0 shadow-inner">
                                #{{ $loop->iteration + 3 }}
                            </div>

                            <!-- Circular Avatar with 3D Ring Frame -->
                            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-slate-700 border-2 border-indigo-400 flex items-center justify-center text-sm overflow-hidden flex-shrink-0 shadow-sm text-indigo-600">
                                @if($otherBooth->image)
                                    <img src="{{ Str::startsWith($otherBooth->image, ['http://', 'https://']) ? $otherBooth->image : (Str::startsWith($otherBooth->image, ['uploads/', 'storage/']) ? asset($otherBooth->image) : asset('storage/' . $otherBooth->image)) }}" alt="{{ $otherBooth->name }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-store text-xs\'></i>';">
                                @else
                                    <i class="fa-solid fa-store text-xs"></i>
                                @endif
                            </div>

                            <!-- Name & Category -->
                            <div class="truncate">
                                <span class="font-black text-slate-900 dark:text-white truncate block text-xs">{{ $otherBooth->name }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold truncate block">
                                    {{ $otherBooth->code }} &bull; {{ $otherBooth->category }}
                                </span>
                            </div>
                        </div>

                        <!-- Right: Game Reward Chest & Points Badge -->
                        <div class="flex items-center space-x-2 flex-shrink-0 pl-2">
                            <span class="text-sm text-amber-500" title="Hadiah Koin">
                                <i class="fa-solid fa-box-open"></i>
                            </span>
                            <div class="px-2.5 py-1 rounded-xl bg-gradient-to-r from-amber-100 to-amber-200 dark:from-slate-700 dark:to-slate-800 border-2 border-amber-300 dark:border-slate-600 text-right">
                                <span class="font-black text-amber-700 dark:text-amber-300 leading-tight text-xs flex items-center justify-end gap-1">
                                    <i class="fa-solid fa-coins text-amber-500 text-[10px]"></i>
                                    <span>{{ number_format($otherBooth->total_points) }}</span>
                                </span>
                                <span class="text-[9px] text-slate-500 dark:text-slate-400 font-bold block leading-none">
                                    {{ $otherBooth->total_voters }} Pool
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    @if ($booths->count() <= 3)
                        <div class="text-center py-3 text-xs text-slate-400 italic font-bold">
                            Semua booth sudah masuk ke podium di atas! 🚀
                        </div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>

    <!-- ===================================================== -->
    <!-- KOLOM 2: THE BRAIN MASTERS (PESERTA TERPINTAR) 🧠      -->
    <!-- ===================================================== -->
    <div class="rounded-3xl p-4 sm:p-5 border-4 border-cyan-400/80 bg-white/95 dark:bg-slate-900/95 flex flex-col justify-between shadow-2xl relative transition-all game-card-3d">
        
        <!-- Column 3D Game Title Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b-2 border-cyan-300/40 dark:border-cyan-500/20">
            <div class="flex items-center space-x-2.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-400 to-teal-300 border-2 border-cyan-500 shadow-md flex items-center justify-center text-xl text-cyan-950 anim-game-float">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-1.5 uppercase tracking-wide">
                        <span>THE BRAIN MASTERS</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-cyan-400 text-slate-950 font-black border border-cyan-600 shadow-xs">Top Kuis</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold">Peringkat akumulasi jawaban kuis benar tertinggi peserta</p>
                </div>
            </div>
            <span class="text-xs font-black text-cyan-600 dark:text-cyan-400 bg-cyan-100 dark:bg-cyan-950/60 px-2.5 py-1 rounded-xl border border-cyan-300 dark:border-cyan-700/50 flex items-center gap-1">
                <i class="fa-solid fa-award text-cyan-500"></i> Top Solver
            </span>
        </div>

        @php
            $topPart = $participants->take(3);
            $p1 = $topPart->get(0);
            $p2 = $topPart->get(1);
            $p3 = $topPart->get(2);
        @endphp

        <!-- 3D CARTOON STONE/MARBLE PODIUM PLATFORM -->
        @if ($p1)
            <div class="bg-gradient-to-b from-cyan-100/70 via-cyan-50/50 to-indigo-100/70 dark:from-slate-800/80 dark:via-slate-900/90 dark:to-slate-950 p-3 sm:p-4 rounded-3xl border-3 border-cyan-300 dark:border-cyan-600/40 shadow-inner mb-4">
                
                <div class="grid grid-cols-3 gap-2 sm:gap-3 items-end pt-4 pb-2">
                    
                    <!-- ================= JUARA 2 PESERTA (SILVER) ================= -->
                    @if ($p2)
                        <div class="flex flex-col items-center text-center">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-2">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-slate-200 via-white to-slate-300 border-3 border-slate-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if($p2->photo)
                                        <img src="{{ $p2->photo }}" alt="{{ $p2->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user-astronaut text-2xl text-slate-600"></i>
                                    @endif
                                </div>
                                <!-- Roman Pillar Capital Cap -->
                                <div class="absolute -bottom-2 -left-2 -right-2 h-2.5 bg-gradient-to-r from-slate-400 via-slate-200 to-slate-400 rounded-full border border-slate-500 shadow-xs"></div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div class="w-full px-1.5 py-1 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-1.5 max-w-[130px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[11px] sm:text-xs truncate" title="{{ $p2->name }}">{{ $p2->name }}</h4>
                                <span class="text-[9px] font-bold text-cyan-600 dark:text-cyan-400 block truncate">{{ $p2->department ?? 'Umum' }}</span>
                            </div>

                            <!-- 3D Shield Badge #2 -->
                            <div class="shield-3d-2 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-slate-900 text-xs sm:text-sm -mb-3 z-10 gap-0.5 shadow-sm">
                                <i class="fa-solid fa-medal text-slate-900"></i> 2
                            </div>

                            <!-- 3D Pedestal Body (Silver) -->
                            <div class="pedestal-3d-silver w-full pt-4 pb-2.5 px-2 rounded-2xl text-slate-900 dark:text-white h-24 sm:h-28 flex flex-col justify-center">
                                <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-slate-900 leading-tight flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-coins text-amber-500"></i>
                                    <span>{{ number_format($p2->total_score) }}</span>
                                </span>
                                <span class="text-[9px] font-extrabold text-slate-600 uppercase">Pts</span>
                                <span class="text-[9px] font-bold text-slate-700 bg-white/70 rounded-full px-1.5 py-0.2 mt-1 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-bullseye text-emerald-600"></i> {{ $p2->booths_voted_count }}/3 Booth
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                    <!-- ================= JUARA 1 PESERTA (SUHU KAIZEN) ================= -->
                    <div class="flex flex-col items-center text-center transform -translate-y-2">
                        <!-- Floating Magic Hat / Hero Emblem -->
                        <div class="anim-crown text-3xl sm:text-4xl text-cyan-400 filter drop-shadow -mb-1">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>

                        <!-- 3D Avatar Frame (Gold Ornament) -->
                        <div class="relative mb-2">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-cyan-200 via-teal-100 to-indigo-300 border-4 border-cyan-400 shadow-lg flex items-center justify-center overflow-hidden">
                                @if($p1->photo)
                                    <img src="{{ $p1->photo }}" alt="{{ $p1->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-user-ninja text-3xl text-cyan-600"></i>
                                @endif
                            </div>
                            <!-- Roman Pillar Capital Cap (Golden curls) -->
                            <div class="absolute -bottom-2 -left-2 -right-2 h-3 bg-gradient-to-r from-cyan-500 via-teal-200 to-cyan-500 rounded-full border-2 border-cyan-600 shadow-sm"></div>
                        </div>

                        <!-- 3D Name Tag Pill (Cyan/Gold) -->
                        <div class="w-full px-2 py-1.5 rounded-xl bg-gradient-to-r from-cyan-300 via-teal-200 to-cyan-300 border-2 border-cyan-500 shadow-md mb-1.5 max-w-[145px]">
                            <h4 class="font-black text-slate-950 text-xs sm:text-sm truncate" title="{{ $p1->name }}">{{ $p1->name }}</h4>
                            <span class="text-[10px] font-extrabold text-teal-900 block truncate">{{ $p1->department ?? 'Umum' }}</span>
                        </div>

                        <!-- 3D Shield Badge #1 -->
                        <div class="shield-3d-1 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-amber-950 text-sm sm:text-base -mb-3.5 z-10 anim-coin gap-0.5 shadow-sm">
                            <i class="fa-solid fa-trophy text-slate-950 text-xs sm:text-sm drop-shadow-xs"></i> <span class="text-slate-950 font-black">1</span>
                        </div>

                        <!-- 3D Pedestal Body (Gold, Taller) -->
                        <div class="pedestal-3d-gold w-full pt-5 pb-3 px-2 rounded-2xl text-slate-950 h-32 sm:h-36 flex flex-col justify-center">
                            <span class="text-sm sm:text-base font-black text-amber-950 leading-tight flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-coins text-amber-600"></i>
                                <span>{{ number_format($p1->total_score) }}</span>
                            </span>
                            <span class="text-[10px] font-black text-amber-900 uppercase">Points</span>
                            <span class="text-[10px] font-black text-amber-950 bg-amber-200/90 rounded-full px-2 py-0.5 mt-1 border border-amber-400 flex items-center justify-center gap-1">
                                <i class="fa-solid fa-bullseye text-emerald-700"></i> {{ $p1->booths_voted_count }}/3 Booth Selesai!
                            </span>
                        </div>
                    </div>

                    <!-- ================= JUARA 3 PESERTA (BRONZE) ================= -->
                    @if ($p3)
                        <div class="flex flex-col items-center text-center">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-2">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-indigo-200 via-purple-100 to-indigo-300 border-3 border-indigo-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if($p3->photo)
                                        <img src="{{ $p3->photo }}" alt="{{ $p3->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user-graduate text-2xl text-indigo-500"></i>
                                    @endif
                                </div>
                                <!-- Roman Pillar Capital Cap -->
                                <div class="absolute -bottom-2 -left-2 -right-2 h-2.5 bg-gradient-to-r from-indigo-500 via-purple-200 to-indigo-500 rounded-full border border-indigo-600 shadow-xs"></div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div class="w-full px-1.5 py-1 rounded-xl bg-white dark:bg-slate-800 border-2 border-indigo-300 dark:border-slate-600 shadow-sm mb-1.5 max-w-[130px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[11px] sm:text-xs truncate" title="{{ $p3->name }}">{{ $p3->name }}</h4>
                                <span class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 block truncate">{{ $p3->department ?? 'Umum' }}</span>
                            </div>

                            <!-- 3D Shield Badge #3 -->
                            <div class="shield-3d-3 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-white text-xs sm:text-sm -mb-3 z-10 gap-0.5 shadow-sm">
                                <i class="fa-solid fa-medal text-white"></i> 3
                            </div>

                            <!-- 3D Pedestal Body (Bronze) -->
                            <div class="pedestal-3d-bronze w-full pt-4 pb-2.5 px-2 rounded-2xl text-slate-900 h-24 sm:h-28 flex flex-col justify-center">
                                <span class="text-xs sm:text-sm font-black text-orange-950 leading-tight flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-coins text-amber-600"></i>
                                    <span>{{ number_format($p3->total_score) }}</span>
                                </span>
                                <span class="text-[9px] font-extrabold text-orange-900 uppercase">Pts</span>
                                <span class="text-[9px] font-bold text-orange-950 bg-white/70 rounded-full px-1.5 py-0.2 mt-1 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-bullseye text-emerald-700"></i> {{ $p3->booths_voted_count }}/3 Booth
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                </div>

                <!-- 3D Reward Bar below Podium -->
                <div class="mt-2 py-1.5 px-3 rounded-2xl bg-cyan-400 text-slate-950 font-black text-xs text-center border-2 border-cyan-600 shadow-sm flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-crown text-cyan-950"></i>
                    <span>KUIS MASTER SOLVER QCC 2026</span>
                    <i class="fa-solid fa-crown text-cyan-950"></i>
                </div>
            </div>
        @else
            <div class="py-12 text-center text-slate-400 text-xs italic">
                Belum ada peserta yang melakukan tapping dan menjawab kuis booth.
            </div>
        @endif

        <!-- ===================================================== -->
        <!-- 3D GAME RANKING PILL CARDS (RANK 4 KE BAWAH)          -->
        <!-- ===================================================== -->
        <div class="mt-auto">
            <div class="text-[11px] font-black text-cyan-900 dark:text-cyan-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                <span>Peserta Lainnya yang Menyusul</span>
                <span class="text-slate-500 dark:text-slate-400 font-bold">{{ max(0, $participants->count() - 3) }} Peserta Mengejar</span>
            </div>
            
            <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                @forelse($participants->slice(3) as $otherPart)
                    <!-- Casual Game 3D Pill Card -->
                    <div class="p-2 sm:p-2.5 rounded-2xl bg-white hover:bg-cyan-50/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border-2 border-slate-200 dark:border-slate-700/80 shadow-[0_4px_0_#cbd5e1] dark:shadow-[0_4px_0_#1e293b] flex items-center justify-between text-xs transition-all active:translate-y-1 active:shadow-none">
                        
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <!-- 3D Rank Embossed Circle -->
                            <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 font-black text-slate-700 dark:text-slate-200 text-xs flex items-center justify-center flex-shrink-0 shadow-inner">
                                #{{ $loop->iteration + 3 }}
                            </div>

                            <!-- Circular Avatar with 3D Ring Frame -->
                            <div class="w-9 h-9 rounded-full bg-cyan-100 dark:bg-slate-700 border-2 border-cyan-400 flex items-center justify-center text-sm overflow-hidden flex-shrink-0 shadow-sm text-cyan-600">
                                @if($otherPart->photo)
                                    <img src="{{ $otherPart->photo }}" alt="{{ $otherPart->name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-user text-xs"></i>
                                @endif
                            </div>

                            <!-- Name & Dept -->
                            <div class="truncate">
                                <span class="font-black text-slate-900 dark:text-white truncate block text-xs">{{ $otherPart->name }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold truncate block">
                                    {{ $otherPart->department ?? 'Umum' }} &bull; NIK: {{ $otherPart->nik ?? '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Right: Game Helmet & Points Badge -->
                        <div class="flex items-center space-x-2 flex-shrink-0 pl-2">
                            <span class="text-sm text-cyan-500" title="Poin Peserta">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <div class="px-2.5 py-1 rounded-xl bg-gradient-to-r from-cyan-100 to-cyan-200 dark:from-slate-700 dark:to-slate-800 border-2 border-cyan-300 dark:border-slate-600 text-right">
                                <span class="font-black text-cyan-700 dark:text-cyan-300 leading-tight text-xs flex items-center justify-end gap-1">
                                    <i class="fa-solid fa-coins text-amber-500 text-[10px]"></i>
                                    <span>{{ number_format($otherPart->total_score) }}</span>
                                </span>
                                <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold block leading-none">
                                    {{ $otherPart->booths_voted_count }}/3 Booth
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    @if ($participants->count() <= 3 && $participants->count() > 0)
                        <div class="text-center py-3 text-xs text-slate-400 italic font-bold">
                            Semua peserta saat ini masuk jajaran podium 🚀
                        </div>
                    @endif
                @endforelse
            </div>
        </div>
    </div>

</div>
