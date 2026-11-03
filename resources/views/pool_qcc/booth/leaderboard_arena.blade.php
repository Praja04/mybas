<!-- ===================================================== -->
<!-- MAIN TWO-COLUMN 3D ARENA (BOOTH PODIUM vs PARTICIPANT PODIUM) -->
<!-- ===================================================== -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 relative z-10 flex-1 items-stretch mb-2">

    <!-- ===================================================== -->
    <!-- KOLOM 1: THE KING OF BOOTHS (STAND TERFAVORIT) 🎪      -->
    <!-- ===================================================== -->
    <div
        class="rounded-3xl p-4 sm:p-5 border-4 border-amber-400/80 bg-white/95 dark:bg-slate-900/95 flex flex-col justify-between shadow-2xl relative transition-all game-card-3d">

        <!-- Column 3D Game Title Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b-2 border-amber-300/40 dark:border-amber-500/20">
            <div class="flex items-center space-x-2.5">
                <div
                    class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-amber-400 to-yellow-300 border-2 border-amber-500 shadow-md flex items-center justify-center text-xl text-amber-950 anim-game-float">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h2
                        class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-1.5 uppercase tracking-wide">
                        <span>THE KING OF BOOTHS</span>
                        <span
                            class="text-[10px] px-2 py-0.5 rounded-full bg-amber-400 text-slate-950 font-black border border-amber-600 shadow-xs">Stand
                            Favorit</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold">Peringkat akumulasi poin &
                        pooling antar booth inovasi</p>
                </div>
            </div>
            <span
                class="text-xs font-black text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/60 px-2.5 py-1 rounded-xl border border-amber-300 dark:border-amber-700/50 flex items-center gap-1">
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
            <div
                class="bg-gradient-to-b from-amber-100/70 via-amber-50/50 to-orange-100/70 dark:from-slate-800/80 dark:via-slate-900/90 dark:to-slate-950 p-3 sm:p-4 rounded-3xl border-3 border-amber-300 dark:border-amber-600/40 shadow-inner mb-4">

                <div class="grid grid-cols-3 gap-2 sm:gap-3 items-end pt-4 pb-2">

                    <!-- ================= JUARA 2 BOOTH (SILVER) ================= -->
                    @if ($b2)
                        <div id="booth-card-{{ $b2->id }}" data-booth-id="{{ $b2->id }}" data-booth-name="{{ $b2->name }}" data-booth-rank="2" class="flex flex-col items-center text-center relative transition-all">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-2">
                                <div
                                    class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-slate-200 via-white to-slate-300 border-3 border-slate-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if ($b2->image)
                                        <img src="{{ Str::startsWith($b2->image, ['http://', 'https://']) ? $b2->image : (Str::startsWith($b2->image, ['uploads/', 'storage/']) ? asset($b2->image) : asset('storage/' . $b2->image)) }}"
                                            alt="{{ $b2->name }}" class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-rocket text-2xl text-slate-600\'></i>';">
                                    @else
                                        <i class="fa-solid fa-rocket text-2xl text-slate-600"></i>
                                    @endif
                                </div>
                                <!-- Roman Pillar Capital Cap -->
                                <div
                                    class="absolute -bottom-2 -left-2 -right-2 h-2.5 bg-gradient-to-r from-slate-400 via-slate-200 to-slate-400 rounded-full border border-slate-500 shadow-xs">
                                </div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div
                                class="w-full px-1.5 py-1 rounded-xl bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-600 shadow-sm mb-1.5 max-w-[130px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[11px] sm:text-xs truncate"
                                    title="{{ $b2->name }}">{{ $b2->name }}</h4>
                                <span
                                    class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 block truncate">{{ $b2->category }}</span>
                            </div>

                            <!-- 3D Shield Badge #2 -->
                            <div
                                class="shield-3d-2 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-slate-900 text-xs sm:text-sm -mb-3 z-10 gap-0.5 shadow-sm">
                                <i class="fa-solid fa-medal text-slate-900"></i> 2
                            </div>

                            <!-- 3D Pedestal Body (Silver) -->
                            <div
                                class="pedestal-3d-silver w-full pt-4 pb-2.5 px-2 rounded-2xl text-slate-900 dark:text-white h-24 sm:h-28 flex flex-col justify-center">
                                <span
                                    class="text-xs sm:text-sm font-black text-slate-800 dark:text-slate-900 leading-tight flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-coins text-amber-500"></i>
                                    <span>{{ number_format($b2->total_points) }}</span>
                                </span>
                                <span class="text-[9px] font-extrabold text-slate-600 uppercase">Pts</span>
                                <span
                                    class="text-[9px] font-bold text-slate-700 bg-white/70 rounded-full px-1.5 py-0.2 mt-1 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-check-to-slot text-slate-500"></i> {{ $b2->total_voters }}
                                    Pool
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                    <!-- ================= JUARA 1 BOOTH (GOLD SULTAN) ================= -->
                    <div id="booth-card-{{ $b1->id }}" data-booth-id="{{ $b1->id }}" data-booth-name="{{ $b1->name }}" data-booth-rank="1" class="flex flex-col items-center text-center transform -translate-y-2 relative transition-all">
                        <!-- Floating Golden Crown -->
                        <div class="anim-crown text-3xl sm:text-4xl text-amber-500 filter drop-shadow-md -mb-1">
                            <i class="fa-solid fa-crown"></i>
                        </div>

                        <!-- 3D Avatar Frame (Gold Ornament) -->
                        <div class="relative mb-2">
                            <div
                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-amber-200 via-yellow-100 to-amber-300 border-4 border-amber-400 shadow-lg flex items-center justify-center overflow-hidden">
                                @if ($b1->image)
                                    <img src="{{ Str::startsWith($b1->image, ['http://', 'https://']) ? $b1->image : (Str::startsWith($b1->image, ['uploads/', 'storage/']) ? asset($b1->image) : asset('storage/' . $b1->image)) }}"
                                        alt="{{ $b1->name }}" class="w-full h-full object-cover"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-store text-3xl text-amber-600\'></i>';">
                                @else
                                    <i class="fa-solid fa-store text-3xl text-amber-600"></i>
                                @endif
                            </div>
                            <!-- Roman Pillar Capital Cap (Golden curls) -->
                            <div
                                class="absolute -bottom-2 -left-2 -right-2 h-3 bg-gradient-to-r from-amber-500 via-yellow-200 to-amber-500 rounded-full border-2 border-amber-600 shadow-sm">
                            </div>
                        </div>

                        <!-- 3D Name Tag Pill (Gold) -->
                        <div
                            class="w-full px-2 py-1.5 rounded-xl bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-300 border-2 border-amber-500 shadow-md mb-1.5 max-w-[145px]">
                            <h4 class="font-black text-slate-950 text-xs sm:text-sm truncate"
                                title="{{ $b1->name }}">{{ $b1->name }}</h4>
                            <span
                                class="text-[10px] font-extrabold text-amber-900 block truncate">{{ $b1->category }}</span>
                        </div>

                        <!-- 3D Shield Badge #1 (With Laurel) -->
                        <div
                            class="shield-3d-1 w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center font-black text-amber-950 text-sm sm:text-base -mb-3.5 z-10 anim-coin gap-0.5 shadow-sm">
                            <i class="fa-solid fa-trophy text-slate-950 text-xs sm:text-sm drop-shadow-xs"></i> <span
                                class="text-slate-950 font-black">1</span>
                        </div>

                        <!-- 3D Pedestal Body (Gold, Taller) -->
                        <div
                            class="pedestal-3d-gold w-full pt-5 pb-3 px-2 rounded-2xl text-slate-950 h-32 sm:h-36 flex flex-col justify-center">
                            <span
                                class="text-sm sm:text-base font-black text-amber-950 leading-tight flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-coins text-amber-600"></i>
                                <span>{{ number_format($b1->total_points) }}</span>
                            </span>
                            <span class="text-[10px] font-black text-amber-900 uppercase">Points</span>
                            <span
                                class="text-[10px] font-black text-amber-950 bg-amber-200/90 rounded-full px-2 py-0.5 mt-1 border border-amber-400 flex items-center justify-center gap-1">
                                <i class="fa-solid fa-check-to-slot text-amber-700"></i> {{ $b1->total_voters }}
                                Pemilih Terbanyak!
                            </span>
                        </div>
                    </div>

                    <!-- ================= JUARA 3 BOOTH (BRONZE) ================= -->
                    @if ($b3)
                        <div id="booth-card-{{ $b3->id }}" data-booth-id="{{ $b3->id }}" data-booth-name="{{ $b3->name }}" data-booth-rank="3" class="flex flex-col items-center text-center relative transition-all">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-2">
                                <div
                                    class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-tr from-orange-200 via-amber-100 to-orange-300 border-3 border-orange-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if ($b3->image)
                                        <img src="{{ Str::startsWith($b3->image, ['http://', 'https://']) ? $b3->image : (Str::startsWith($b3->image, ['uploads/', 'storage/']) ? asset($b3->image) : asset('storage/' . $b3->image)) }}"
                                            alt="{{ $b3->name }}" class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-lightbulb text-2xl text-amber-500\'></i>';">
                                    @else
                                        <i class="fa-solid fa-lightbulb text-2xl text-amber-500"></i>
                                    @endif
                                </div>
                                <!-- Roman Pillar Capital Cap -->
                                <div
                                    class="absolute -bottom-2 -left-2 -right-2 h-2.5 bg-gradient-to-r from-orange-500 via-amber-200 to-orange-500 rounded-full border border-orange-600 shadow-xs">
                                </div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div
                                class="w-full px-1.5 py-1 rounded-xl bg-white dark:bg-slate-800 border-2 border-orange-300 dark:border-slate-600 shadow-sm mb-1.5 max-w-[130px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[11px] sm:text-xs truncate"
                                    title="{{ $b3->name }}">{{ $b3->name }}</h4>
                                <span
                                    class="text-[9px] font-bold text-indigo-600 dark:text-indigo-400 block truncate">{{ $b3->category }}</span>
                            </div>

                            <!-- 3D Shield Badge #3 -->
                            <div
                                class="shield-3d-3 w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-black text-white text-xs sm:text-sm -mb-3 z-10 gap-0.5 shadow-sm">
                                <i class="fa-solid fa-medal text-white"></i> 3
                            </div>

                            <!-- 3D Pedestal Body (Bronze) -->
                            <div
                                class="pedestal-3d-bronze w-full pt-4 pb-2.5 px-2 rounded-2xl text-slate-900 h-24 sm:h-28 flex flex-col justify-center">
                                <span
                                    class="text-xs sm:text-sm font-black text-orange-950 leading-tight flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-coins text-amber-600"></i>
                                    <span>{{ number_format($b3->total_points) }}</span>
                                </span>
                                <span class="text-[9px] font-extrabold text-orange-900 uppercase">Pts</span>
                                <span
                                    class="text-[9px] font-bold text-orange-950 bg-white/70 rounded-full px-1.5 py-0.2 mt-1 flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-check-to-slot text-orange-800"></i> {{ $b3->total_voters }}
                                    Pool
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                </div>

                <!-- 3D Reward Bar below Podium -->
                <div
                    class="mt-2 py-1.5 px-3 rounded-2xl bg-amber-400 text-slate-950 font-black text-xs text-center border-2 border-amber-600 shadow-sm flex items-center justify-center space-x-2">
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
            <div
                class="text-[11px] font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                <span>Peringkat Booth Lainnya</span>
                <span class="text-slate-500 dark:text-slate-400 font-bold">{{ max(0, $booths->count() - 3) }} Booth
                    Bertanding</span>
            </div>

            <div class="space-y-2 max-h-52 overflow-y-auto py-1 px-1 pr-1.5">
                @forelse($booths->slice(3) as $otherBooth)
                    <!-- Casual Game 3D Pill Card -->
                    <div id="booth-card-{{ $otherBooth->id }}" data-booth-id="{{ $otherBooth->id }}" data-booth-name="{{ $otherBooth->name }}" data-booth-rank="{{ $loop->iteration + 3 }}"
                        class="relative hover:z-10 p-2 sm:p-2.5 rounded-2xl bg-white hover:bg-amber-50/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border-2 border-slate-200 dark:border-slate-700/80 shadow-[0_4px_0_#cbd5e1] dark:shadow-[0_4px_0_#1e293b] hover:shadow-[0_6px_0_#cbd5e1] dark:hover:shadow-[0_6px_0_#1e293b] flex items-center justify-between text-xs transition-all duration-150 hover:-translate-y-0.5 active:translate-y-1 active:shadow-none">

                        <div class="flex items-center space-x-2.5 min-w-0">
                            <!-- 3D Rank Embossed Circle -->
                            <div
                                class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 font-black text-slate-700 dark:text-slate-200 text-xs flex items-center justify-center flex-shrink-0 shadow-inner">
                                #{{ $loop->iteration + 3 }}
                            </div>

                            <!-- Circular Avatar with 3D Ring Frame -->
                            <div
                                class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-slate-700 border-2 border-indigo-400 flex items-center justify-center text-sm overflow-hidden flex-shrink-0 shadow-sm text-indigo-600">
                                @if ($otherBooth->image)
                                    <img src="{{ Str::startsWith($otherBooth->image, ['http://', 'https://']) ? $otherBooth->image : (Str::startsWith($otherBooth->image, ['uploads/', 'storage/']) ? asset($otherBooth->image) : asset('storage/' . $otherBooth->image)) }}"
                                        alt="{{ $otherBooth->name }}" class="w-full h-full object-cover"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-store text-xs\'></i>';">
                                @else
                                    <i class="fa-solid fa-store text-xs"></i>
                                @endif
                            </div>

                            <!-- Name & Category -->
                            <div class="truncate">
                                <span
                                    class="font-black text-slate-900 dark:text-white truncate block text-xs">{{ $otherBooth->name }}</span>
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
                            <div
                                class="px-2.5 py-1 rounded-xl bg-gradient-to-r from-amber-100 to-amber-200 dark:from-slate-700 dark:to-slate-800 border-2 border-amber-300 dark:border-slate-600 text-right">
                                <span
                                    class="font-black text-amber-700 dark:text-amber-300 leading-tight text-xs flex items-center justify-end gap-1">
                                    <i class="fa-solid fa-coins text-amber-500 text-[10px]"></i>
                                    <span>{{ number_format($otherBooth->total_points) }}</span>
                                </span>
                                <span
                                    class="text-[9px] text-slate-500 dark:text-slate-400 font-bold block leading-none">
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
    <div
        class="rounded-3xl p-4 sm:p-5 border-4 border-cyan-400/80 bg-white/95 dark:bg-slate-900/95 flex flex-col justify-between shadow-2xl relative transition-all game-card-3d">

        <!-- Column 3D Game Title Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b-2 border-cyan-300/40 dark:border-cyan-500/20">
            <div class="flex items-center space-x-2.5">
                <div
                    class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-400 to-teal-300 border-2 border-cyan-500 shadow-md flex items-center justify-center text-xl text-cyan-950 anim-game-float">
                    <i class="fa-solid fa-brain"></i>
                </div>
                <div>
                    <h2
                        class="text-base sm:text-lg font-black text-slate-900 dark:text-white flex items-center gap-1.5 uppercase tracking-wide">
                        <span>AWARD NOMINEES</span>
                        <span
                            class="text-[10px] px-2 py-0.5 rounded-full bg-cyan-400 text-slate-950 font-black border border-cyan-600 shadow-xs">Top
                            Kuis</span>
                    </h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-bold">Peringkat akumulasi jawaban
                        kuis benar tertinggi peserta</p>
                </div>
            </div>
            <span
                class="text-xs font-black text-cyan-600 dark:text-cyan-400 bg-cyan-100 dark:bg-cyan-950/60 px-2.5 py-1 rounded-xl border border-cyan-300 dark:border-cyan-700/50 flex items-center gap-1">
                <i class="fa-solid fa-award text-cyan-500"></i> Top Solver
            </span>
        </div>

        <!-- ALL PARTICIPANTS LIST ROWS (NO PODIUM) -->
        <div class="flex-1 flex flex-col min-h-0 mt-1">
            <div class="text-[11px] font-black text-cyan-900 dark:text-cyan-300 uppercase tracking-wider mb-2.5 flex items-center justify-between">
                <span>Daftar Peringkat Peserta</span>
                <span class="text-slate-500 dark:text-slate-400 font-bold">{{ $participants->count() }} Peserta Berpartisipasi</span>
            </div>

            <div id="participantsListWrapper" class="space-y-2.5 overflow-y-auto overflow-x-hidden py-1.5 px-1 pr-2 flex-1 max-h-[620px]">
                @forelse($participants as $p)
                    @if ($loop->iteration == 1)
                        <!-- Rank #1 Gold Card -->
                        <div id="participant-card-{{ $p->id }}" data-participant-id="{{ $p->id }}" data-participant-name="{{ $p->name }}" data-participant-rank="1" class="relative hover:z-20 p-3 sm:p-3.5 rounded-2xl bg-gradient-to-r from-amber-100/90 via-yellow-50 to-amber-100/80 dark:from-amber-950/40 dark:via-slate-800 dark:to-amber-900/30 border-2 border-amber-400 dark:border-amber-500/60 shadow-[0_4px_0_#d97706] dark:shadow-[0_4px_0_#78350f] hover:shadow-[0_6px_0_#d97706] dark:hover:shadow-[0_6px_0_#78350f] flex items-center justify-between text-xs transition-all duration-150 hover:-translate-y-0.5">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-amber-400 to-yellow-300 border-2 border-amber-500 font-black text-slate-950 text-sm flex items-center justify-center flex-shrink-0 shadow-md">
                                    <i class="fa-solid fa-crown text-xs mr-0.5"></i> 1
                                </div>
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-amber-200 dark:bg-slate-700 border-2 border-amber-400 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-sm">
                                    @if($p->photo)
                                        <img src="{{ $p->photo }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user-ninja text-amber-700 dark:text-amber-400 text-sm"></i>
                                    @endif
                                </div>
                                <div class="truncate">
                                    <div class="flex items-center space-x-1.5 participant-title-row">
                                        <span class="font-black text-slate-950 dark:text-white truncate block text-sm">{{ $p->name }}</span>
                                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-amber-400 text-slate-950">Juara 1</span>
                                    </div>
                                    <span class="text-[10px] text-amber-900 dark:text-amber-300 font-bold truncate block">
                                        {{ $p->department ?? 'Umum' }} &bull; NIK: {{ $p->nik ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0 pl-2">
                                <div class="px-3 py-1.5 rounded-xl bg-amber-200/80 dark:bg-slate-700/80 border border-amber-400 dark:border-slate-600 text-right">
                                    <span class="font-black text-amber-950 dark:text-amber-300 leading-tight text-sm flex items-center justify-end gap-1">
                                        <i class="fa-solid fa-coins text-amber-600 text-xs"></i>
                                        <span>{{ number_format($p->total_score) }}</span>
                                    </span>
                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold block leading-none mt-0.5">
                                        {{ $p->booths_voted_count }}/3 Booth
                                    </span>
                                </div>
                            </div>
                        </div>

                    @elseif ($loop->iteration == 2)
                        <!-- Rank #2 Silver Card -->
                        <div id="participant-card-{{ $p->id }}" data-participant-id="{{ $p->id }}" data-participant-name="{{ $p->name }}" data-participant-rank="2" class="relative hover:z-20 p-3 sm:p-3.5 rounded-2xl bg-gradient-to-r from-slate-100 via-white to-slate-200/80 dark:from-slate-800 dark:via-slate-850 dark:to-slate-800/80 border-2 border-slate-300 dark:border-slate-600 shadow-[0_4px_0_#94a3b8] dark:shadow-[0_4px_0_#334155] hover:shadow-[0_6px_0_#94a3b8] dark:hover:shadow-[0_6px_0_#334155] flex items-center justify-between text-xs transition-all duration-150 hover:-translate-y-0.5">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-slate-300 to-slate-100 border-2 border-slate-400 font-black text-slate-800 text-sm flex items-center justify-center flex-shrink-0 shadow-md">
                                    <i class="fa-solid fa-medal text-xs mr-0.5"></i> 2
                                </div>
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-slate-200 dark:bg-slate-700 border-2 border-slate-300 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-sm">
                                    @if($p->photo)
                                        <img src="{{ $p->photo }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user-astronaut text-slate-600 dark:text-slate-300 text-sm"></i>
                                    @endif
                                </div>
                                <div class="truncate">
                                    <div class="flex items-center space-x-1.5 participant-title-row">
                                        <span class="font-black text-slate-900 dark:text-white truncate block text-sm">{{ $p->name }}</span>
                                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-slate-300 text-slate-800">Juara 2</span>
                                    </div>
                                    <span class="text-[10px] text-slate-600 dark:text-slate-400 font-bold truncate block">
                                        {{ $p->department ?? 'Umum' }} &bull; NIK: {{ $p->nik ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0 pl-2">
                                <div class="px-3 py-1.5 rounded-xl bg-slate-200/80 dark:bg-slate-700/80 border border-slate-300 dark:border-slate-600 text-right">
                                    <span class="font-black text-slate-800 dark:text-slate-200 leading-tight text-sm flex items-center justify-end gap-1">
                                        <i class="fa-solid fa-coins text-amber-500 text-xs"></i>
                                        <span>{{ number_format($p->total_score) }}</span>
                                    </span>
                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold block leading-none mt-0.5">
                                        {{ $p->booths_voted_count }}/3 Booth
                                    </span>
                                </div>
                            </div>
                        </div>

                    @elseif ($loop->iteration == 3)
                        <!-- Rank #3 Bronze Card -->
                        <div id="participant-card-{{ $p->id }}" data-participant-id="{{ $p->id }}" data-participant-name="{{ $p->name }}" data-participant-rank="3" class="relative hover:z-20 p-3 sm:p-3.5 rounded-2xl bg-gradient-to-r from-orange-100/90 via-amber-50 to-orange-100/70 dark:from-amber-950/30 dark:via-slate-800 dark:to-orange-900/30 border-2 border-orange-300 dark:border-orange-500/50 shadow-[0_4px_0_#ea580c] dark:shadow-[0_4px_0_#9a3412] hover:shadow-[0_6px_0_#ea580c] dark:hover:shadow-[0_6px_0_#9a3412] flex items-center justify-between text-xs transition-all duration-150 hover:-translate-y-0.5">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-gradient-to-tr from-orange-400 to-amber-300 border-2 border-orange-500 font-black text-slate-950 text-sm flex items-center justify-center flex-shrink-0 shadow-md">
                                    <i class="fa-solid fa-medal text-xs mr-0.5"></i> 3
                                </div>
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-orange-100 dark:bg-slate-700 border-2 border-orange-300 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-sm">
                                    @if($p->photo)
                                        <img src="{{ $p->photo }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user-graduate text-orange-600 dark:text-orange-400 text-sm"></i>
                                    @endif
                                </div>
                                <div class="truncate">
                                    <div class="flex items-center space-x-1.5 participant-title-row">
                                        <span class="font-black text-slate-900 dark:text-white truncate block text-sm">{{ $p->name }}</span>
                                        <span class="px-1.5 py-0.5 rounded-md text-[9px] font-black bg-orange-300 text-slate-950">Juara 3</span>
                                    </div>
                                    <span class="text-[10px] text-orange-900 dark:text-orange-300 font-bold truncate block">
                                        {{ $p->department ?? 'Umum' }} &bull; NIK: {{ $p->nik ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0 pl-2">
                                <div class="px-3 py-1.5 rounded-xl bg-orange-100/80 dark:bg-slate-700/80 border border-orange-300 dark:border-slate-600 text-right">
                                    <span class="font-black text-orange-950 dark:text-orange-300 leading-tight text-sm flex items-center justify-end gap-1">
                                        <i class="fa-solid fa-coins text-amber-500 text-xs"></i>
                                        <span>{{ number_format($p->total_score) }}</span>
                                    </span>
                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold block leading-none mt-0.5">
                                        {{ $p->booths_voted_count }}/3 Booth
                                    </span>
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- Rank 4+ Card -->
                        <div id="participant-card-{{ $p->id }}" data-participant-id="{{ $p->id }}" data-participant-name="{{ $p->name }}" data-participant-rank="{{ $loop->iteration }}" class="relative hover:z-20 p-2.5 sm:p-3 rounded-2xl bg-white hover:bg-cyan-50/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border-2 border-slate-200 dark:border-slate-700/80 shadow-[0_3px_0_#cbd5e1] dark:shadow-[0_3px_0_#1e293b] hover:shadow-[0_5px_0_#06b6d4] hover:border-cyan-300 dark:hover:border-cyan-600 flex items-center justify-between text-xs transition-all duration-150 hover:-translate-y-0.5 active:translate-y-0.5 active:shadow-none">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-700 border-2 border-slate-300 dark:border-slate-600 font-black text-slate-700 dark:text-slate-200 text-xs flex items-center justify-center flex-shrink-0 shadow-inner">
                                    #{{ $loop->iteration }}
                                </div>
                                <div class="w-9 h-9 rounded-full bg-cyan-100 dark:bg-slate-700 border-2 border-cyan-400 flex items-center justify-center text-sm overflow-hidden flex-shrink-0 shadow-sm text-cyan-600">
                                    @if ($p->photo)
                                        <img src="{{ $p->photo }}" alt="{{ $p->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-user text-xs"></i>
                                    @endif
                                </div>
                                <div class="truncate">
                                    <div class="flex items-center space-x-1.5 participant-title-row"><span class="font-black text-slate-900 dark:text-white truncate block text-xs">{{ $p->name }}</span></div>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold truncate block">
                                        {{ $p->department ?? 'Umum' }} &bull; NIK: {{ $p->nik ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 flex-shrink-0 pl-2">
                                <div class="px-2.5 py-1 rounded-xl bg-gradient-to-r from-cyan-100 to-cyan-200 dark:from-slate-700 dark:to-slate-800 border-2 border-cyan-300 dark:border-slate-600 text-right">
                                    <span class="font-black text-cyan-700 dark:text-cyan-300 leading-tight text-xs flex items-center justify-end gap-1">
                                        <i class="fa-solid fa-coins text-amber-500 text-[10px]"></i>
                                        <span>{{ number_format($p->total_score) }}</span>
                                    </span>
                                    <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold block leading-none">
                                        {{ $p->booths_voted_count }}/3 Booth
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="py-16 text-center text-slate-400 text-xs italic glass-panel rounded-2xl">
                        <i class="fa-solid fa-users-slash text-3xl mb-2 block text-slate-400"></i>
                        Belum ada peserta yang melakukan tapping dan menjawab kuis booth.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
