<!-- ===================================================== -->
<!-- MAIN TWO-COLUMN 3D ARENA (BOOTH PODIUM vs 5-COLUMN REWARD CANDIDATES) -->
<!-- ===================================================== -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-3 relative z-10 flex-1 items-stretch min-h-0 h-full pb-0.5">

    <!-- ===================================================== -->
    <!-- KOLOM 1: THE KING OF BOOTHS (STAND TERFAVORIT) 🎪      -->
    <!-- ===================================================== -->
    <div
        class="lg:col-span-4 xl:col-span-4 rounded-3xl p-3 sm:p-3.5 border-4 border-amber-400/80 bg-white/95 dark:bg-slate-900/95 flex flex-col shadow-2xl relative transition-all game-card-3d h-full min-h-0 overflow-hidden">

        <!-- Column 3D Game Title Header -->
        <div
            class="flex items-center justify-between pb-2 mb-2 border-b-2 border-amber-300/40 dark:border-amber-500/20 flex-shrink-0">
            <div class="flex items-center space-x-2.5">
                <div
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-amber-400 to-yellow-300 border-2 border-amber-500 shadow-md flex items-center justify-center text-lg text-amber-950 anim-game-float">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h2
                        class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center gap-1.5 uppercase tracking-wide">
                        <span>THE KING OF BOOTHS</span>
                    </h2>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-bold leading-tight">
                        Peringkat akumulasi poin & pooling booth inovasi</p>
                </div>
            </div>
            <span
                class="text-[11px] font-black text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/60 px-2.5 py-1 rounded-xl border border-amber-300 dark:border-amber-700/50 flex items-center gap-1">
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
                class="bg-gradient-to-b from-amber-100/70 via-amber-50/50 to-orange-100/70 dark:from-slate-800/80 dark:via-slate-900/90 dark:to-slate-950 p-2 sm:p-2.5 rounded-2xl border-2 border-amber-300 dark:border-amber-600/40 shadow-inner mb-2 flex-shrink-0">

                <div class="grid grid-cols-3 gap-1.5 sm:gap-2.5 items-end pt-3 pb-1">

                    <!-- ================= JUARA 2 BOOTH (SILVER) ================= -->
                    @if ($b2)
                        <div id="booth-card-{{ $b2->id }}" data-booth-id="{{ $b2->id }}"
                            data-booth-name="{{ $b2->name }}" data-booth-rank="2"
                            class="flex flex-col items-center text-center relative transition-all">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-1">
                                <div
                                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-slate-200 via-white to-slate-300 border-2 border-slate-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if ($b2->image)
                                        <img src="{{ Str::startsWith($b2->image, ['http://', 'https://']) ? $b2->image : (Str::startsWith($b2->image, ['uploads/', 'storage/']) ? asset($b2->image) : asset('storage/' . $b2->image)) }}"
                                            alt="{{ $b2->name }}" class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-rocket text-lg text-slate-600\'></i>';">
                                    @else
                                        <i class="fa-solid fa-rocket text-lg text-slate-600"></i>
                                    @endif
                                </div>
                                <div
                                    class="absolute -bottom-1 -left-1 -right-1 h-1.5 bg-gradient-to-r from-slate-400 via-slate-200 to-slate-400 rounded-full border border-slate-500 shadow-xs">
                                </div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div
                                class="w-full px-1 py-0.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 shadow-xs mb-0.5 max-w-[105px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[9.5px] sm:text-[10px] truncate"
                                    title="{{ $b2->name }}">{{ $b2->name }}</h4>
                                <span
                                    class="text-[7.5px] font-bold text-indigo-600 dark:text-indigo-400 block truncate">{{ $b2->category }}</span>
                            </div>

                            <!-- 3D Shield Badge #2 -->
                            <div
                                class="shield-3d-2 w-7 h-7 rounded-full flex items-center justify-center font-black text-slate-900 text-[11px] -mb-1 z-10 gap-0.5 shadow-xs">
                                <i class="fa-solid fa-medal text-slate-900 text-[9px]"></i> 2
                            </div>

                            <!-- 3D Pedestal Body (Silver) -->
                            <div
                                class="pedestal-3d-silver w-full pt-3 pb-1.5 px-1 rounded-2xl text-slate-900 dark:text-white h-13 sm:h-14 flex flex-col justify-end items-center">
                                <span
                                    class="text-xs sm:text-sm font-black text-slate-800 dark:text-slate-900 leading-none flex items-center justify-center gap-1 mb-1">
                                    <i class="fa-solid fa-coins text-amber-500 text-[10px]"></i>
                                    <span>{{ number_format($b2->total_points) }}</span>
                                </span>
                                <span
                                    class="text-[7.5px] font-bold text-slate-700 bg-white/70 rounded-full px-1.5 py-0.5 flex items-center justify-center gap-0.5 leading-none">
                                    <i class="fa-solid fa-check-to-slot text-slate-500 text-[7px]"></i>
                                    {{ $b2->total_voters }} Pool
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                    <!-- ================= JUARA 1 BOOTH (GOLD SULTAN) ================= -->
                    <div id="booth-card-{{ $b1->id }}" data-booth-id="{{ $b1->id }}"
                        data-booth-name="{{ $b1->name }}" data-booth-rank="1"
                        class="flex flex-col items-center text-center transform -translate-y-1 relative transition-all">
                        <!-- Floating Golden Crown -->
                        <div class="anim-crown text-xl sm:text-2xl text-amber-500 filter drop-shadow-md -mb-0.5">
                            <i class="fa-solid fa-crown"></i>
                        </div>

                        <!-- 3D Avatar Frame (Gold Ornament) -->
                        <div class="relative mb-1">
                            <div
                                class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-gradient-to-tr from-amber-200 via-yellow-100 to-amber-300 border-2 sm:border-3 border-amber-400 shadow-md flex items-center justify-center overflow-hidden">
                                @if ($b1->image)
                                    <img src="{{ Str::startsWith($b1->image, ['http://', 'https://']) ? $b1->image : (Str::startsWith($b1->image, ['uploads/', 'storage/']) ? asset($b1->image) : asset('storage/' . $b1->image)) }}"
                                        alt="{{ $b1->name }}" class="w-full h-full object-cover"
                                        onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-store text-xl text-amber-600\'></i>';">
                                @else
                                    <i class="fa-solid fa-store text-xl text-amber-600"></i>
                                @endif
                            </div>
                            <div
                                class="absolute -bottom-1 -left-1 -right-1 h-2 bg-gradient-to-r from-amber-500 via-yellow-200 to-amber-500 rounded-full border border-amber-600 shadow-xs">
                            </div>
                        </div>

                        <!-- 3D Name Tag Pill (Gold) -->
                        <div
                            class="w-full px-1 py-0.5 rounded-xl bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-300 border border-amber-500 shadow-xs mb-0.5 max-w-[115px]">
                            <h4 class="font-black text-slate-950 text-[10px] sm:text-[11px] truncate"
                                title="{{ $b1->name }}">{{ $b1->name }}</h4>
                            <span
                                class="text-[8px] font-extrabold text-amber-900 block truncate">{{ $b1->category }}</span>
                        </div>

                        <!-- 3D Shield Badge #1 -->
                        <div
                            class="shield-3d-1 w-8 h-8 sm:w-8.5 sm:h-8.5 rounded-full flex items-center justify-center font-black text-amber-950 text-xs -mb-1.5 z-10 anim-coin gap-0.5 shadow-xs">
                            <i class="fa-solid fa-trophy text-slate-950 text-[9px]"></i> <span
                                class="text-slate-950 font-black">1</span>
                        </div>

                        <!-- 3D Pedestal Body (Gold, Taller) -->
                        <div
                            class="pedestal-3d-gold w-full pt-3.5 pb-1.5 px-1 rounded-2xl text-slate-950 h-16 sm:h-18 flex flex-col justify-end items-center">
                            <span
                                class="text-xs sm:text-sm font-black text-amber-950 leading-none flex items-center justify-center gap-1 mb-1">
                                <i class="fa-solid fa-coins text-amber-600 text-[10px]"></i>
                                <span>{{ number_format($b1->total_points) }}</span>
                            </span>
                            <span
                                class="text-[8px] font-black text-amber-950 bg-amber-200/90 rounded-full px-2 py-0.5 border border-amber-400 flex items-center justify-center gap-0.5 leading-none">
                                <i class="fa-solid fa-check-to-slot text-amber-700 text-[7.5px]"></i>
                                {{ $b1->total_voters }} Pemilih
                            </span>
                        </div>
                    </div>

                    <!-- ================= JUARA 3 BOOTH (BRONZE) ================= -->
                    @if ($b3)
                        <div id="booth-card-{{ $b3->id }}" data-booth-id="{{ $b3->id }}"
                            data-booth-name="{{ $b3->name }}" data-booth-rank="3"
                            class="flex flex-col items-center text-center relative transition-all">
                            <!-- 3D Avatar Frame with Roman Pillar Capital -->
                            <div class="relative mb-1">
                                <div
                                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-orange-200 via-amber-100 to-orange-300 border-2 border-orange-400 shadow-md flex items-center justify-center overflow-hidden">
                                    @if ($b3->image)
                                        <img src="{{ Str::startsWith($b3->image, ['http://', 'https://']) ? $b3->image : (Str::startsWith($b3->image, ['uploads/', 'storage/']) ? asset($b3->image) : asset('storage/' . $b3->image)) }}"
                                            alt="{{ $b3->name }}" class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-lightbulb text-lg text-amber-500\'></i>';">
                                    @else
                                        <i class="fa-solid fa-lightbulb text-lg text-amber-500"></i>
                                    @endif
                                </div>
                                <div
                                    class="absolute -bottom-1 -left-1 -right-1 h-1.5 bg-gradient-to-r from-orange-500 via-amber-200 to-orange-500 rounded-full border border-orange-600 shadow-xs">
                                </div>
                            </div>

                            <!-- 3D Name Tag Pill -->
                            <div
                                class="w-full px-1 py-0.5 rounded-xl bg-white dark:bg-slate-800 border border-orange-300 dark:border-slate-600 shadow-xs mb-0.5 max-w-[105px]">
                                <h4 class="font-black text-slate-900 dark:text-white text-[9.5px] sm:text-[10px] truncate"
                                    title="{{ $b3->name }}">{{ $b3->name }}</h4>
                                <span
                                    class="text-[7.5px] font-bold text-indigo-600 dark:text-indigo-400 block truncate">{{ $b3->category }}</span>
                            </div>

                            <!-- 3D Shield Badge #3 -->
                            <div
                                class="shield-3d-3 w-7 h-7 rounded-full flex items-center justify-center font-black text-white text-[11px] -mb-1 z-10 gap-0.5 shadow-xs">
                                <i class="fa-solid fa-medal text-white text-[9px]"></i> 3
                            </div>

                            <!-- 3D Pedestal Body (Bronze) -->
                            <div
                                class="pedestal-3d-bronze w-full pt-3 pb-1.5 px-1 rounded-2xl text-slate-900 h-11 sm:h-12 flex flex-col justify-end items-center">
                                <span
                                    class="text-xs sm:text-sm font-black text-amber-950 leading-none flex items-center justify-center gap-1 mb-1">
                                    <i class="fa-solid fa-coins text-amber-700 text-[10px]"></i>
                                    <span>{{ number_format($b3->total_points) }}</span>
                                </span>
                                <span
                                    class="text-[7.5px] font-bold text-amber-950 bg-amber-200/80 rounded-full px-1.5 py-0.5 flex items-center justify-center gap-0.5 leading-none">
                                    <i class="fa-solid fa-check-to-slot text-amber-800 text-[7px]"></i>
                                    {{ $b3->total_voters }} Pool
                                </span>
                            </div>
                        </div>
                    @else
                        <div></div>
                    @endif

                </div>
            </div>
        @else
            <div class="py-10 text-center text-slate-400 text-xs italic">
                Belum ada data booth yang terdaftar.
            </div>
        @endif

        <!-- ===================================================== -->
        <!-- 3D GAME RANKING PILL CARDS (RANK 4 KE BAWAH)          -->
        <!-- ===================================================== -->
        <div class="flex-1 min-h-0 flex flex-col mt-1 overflow-hidden">
            <div
                class="text-[10px] font-black text-amber-900 dark:text-amber-300 uppercase tracking-wider mb-1 flex items-center justify-between flex-shrink-0">
                <span>Peringkat Booth Lainnya</span>
                <span class="text-slate-500 dark:text-slate-400 font-bold">{{ max(0, $booths->count() - 3) }} Booth
                    Bertanding</span>
            </div>

            <div class="infinite-scroll-container flex-1 min-h-0 overflow-hidden py-1 px-1 relative">
                <div class="infinite-scroll-content flex flex-col gap-1.5 pb-2">
                    @forelse($booths->slice(3) as $otherBooth)
                        <div id="booth-card-{{ $otherBooth->id }}" data-booth-id="{{ $otherBooth->id }}"
                            data-booth-name="{{ $otherBooth->name }}" data-booth-rank="{{ $loop->iteration + 3 }}"
                            class="relative hover:z-10 p-2 rounded-xl bg-white hover:bg-amber-50/80 dark:bg-slate-800/80 dark:hover:bg-slate-800 border-2 border-slate-200 dark:border-slate-700/80 shadow-xs flex items-center justify-between text-xs transition-all duration-150 hover:-translate-y-0.5">

                            <div class="flex items-center space-x-2 min-w-0">
                                <!-- 3D Rank Embossed Circle -->
                                <div
                                    class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 font-black text-slate-700 dark:text-slate-200 text-[11px] flex items-center justify-center flex-shrink-0 shadow-inner">
                                    #{{ $loop->iteration + 3 }}
                                </div>

                                <!-- Circular Avatar with 3D Ring Frame -->
                                <div
                                    class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-slate-700 border border-indigo-400 flex items-center justify-center text-xs overflow-hidden flex-shrink-0 shadow-xs text-indigo-600">
                                    @if ($otherBooth->image)
                                        <img src="{{ Str::startsWith($otherBooth->image, ['http://', 'https://']) ? $otherBooth->image : (Str::startsWith($otherBooth->image, ['uploads/', 'storage/']) ? asset($otherBooth->image) : asset('storage/' . $otherBooth->image)) }}"
                                            alt="{{ $otherBooth->name }}" class="w-full h-full object-cover"
                                            onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-store text-[10px]\'></i>';">
                                    @else
                                        <i class="fa-solid fa-store text-[10px]"></i>
                                    @endif
                                </div>

                                <!-- Name & Category -->
                                <div class="truncate">
                                    <span
                                        class="font-black text-slate-900 dark:text-white truncate block text-[11px]">{{ $otherBooth->name }}</span>
                                    <span
                                        class="text-[9px] text-slate-500 dark:text-slate-400 font-bold truncate block">
                                        {{ $otherBooth->code }} &bull; {{ $otherBooth->category }}
                                    </span>
                                </div>
                            </div>

                            <!-- Right: Points Badge -->
                            <div class="flex items-center space-x-1.5 flex-shrink-0 pl-1.5">
                                <div
                                    class="px-2 py-0.5 rounded-lg bg-gradient-to-r from-amber-100 to-amber-200 dark:from-slate-700 dark:to-slate-800 border border-amber-300 dark:border-slate-600 text-right">
                                    <span
                                        class="font-black text-amber-700 dark:text-amber-300 leading-tight text-xs flex items-center justify-end gap-1">
                                        <i class="fa-solid fa-coins text-amber-500 text-[9px]"></i>
                                        <span>{{ number_format($otherBooth->total_points) }}</span>
                                    </span>
                                    <span
                                        class="text-[8px] text-slate-500 dark:text-slate-400 font-bold block leading-none">
                                        {{ $otherBooth->total_voters }} Pool
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        @if ($booths->count() <= 3)
                            <div class="text-center py-3 text-[11px] text-slate-400 italic font-bold">
                                Semua booth sudah masuk ke podium di atas! 🚀
                            </div>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ===================================================== -->
    <!-- KOLOM 2: AWARD NOMINEES (5 COLUMNS SIDE-BY-SIDE) 🏆   -->
    <!-- ===================================================== -->
    <div
        class="lg:col-span-8 xl:col-span-8 rounded-3xl p-3 sm:p-3.5 border-4 border-cyan-400/80 bg-white/95 dark:bg-slate-900/95 flex flex-col shadow-2xl relative transition-all game-card-3d h-full min-h-0 overflow-hidden">

        <!-- Column Header -->
        <div
            class="flex items-center justify-between pb-2.5 mb-2.5 border-b-2 border-cyan-300/40 dark:border-cyan-500/20 flex-shrink-0">
            <div class="flex items-center space-x-2.5">
                <div
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-cyan-400 to-teal-300 border-2 border-cyan-500 shadow-md flex items-center justify-center text-lg text-cyan-950 anim-game-float">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div>
                    <h2
                        class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center gap-1.5 uppercase tracking-wide">
                        <span>AWARD NOMINEES</span>
                        <span
                            class="text-[9px] px-2 py-0.5 rounded-full bg-cyan-400 text-slate-950 font-black border border-cyan-600 shadow-xs">Kandidat
                            Undian</span>
                    </h2>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-bold leading-tight">
                        Peringkat kandidat per kategori reward &bull; {{ $participants->count() }} Peserta
                        Berpartisipasi
                    </p>
                </div>
            </div>
        </div>

        <!-- 5 REWARD TIERS SIDE-BY-SIDE GRID (FORMAT VIEW MATCHING REFERENCE IMAGE) -->
        <div id="participantsListWrapper"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-2.5 flex-1 min-h-0 items-stretch overflow-hidden">
            @foreach ($rewardTiers as $tierKey => $tier)
                @php
                    $tColor = $tier['color'];
                    $tParticipants = $tier['participants'];
                    $tCount = $tParticipants->count();
                @endphp

                <!-- TIER COLUMN: {{ $tier['name'] }} -->
                <div
                    class="rounded-2xl border-2 border-{{ $tColor }}-400/80 dark:border-{{ $tColor }}-500/50 bg-gradient-to-b from-{{ $tColor }}-50/50 via-white to-{{ $tColor }}-50/30 dark:from-slate-800/90 dark:via-slate-900/90 dark:to-slate-850 p-2 sm:p-2.5 shadow-md flex flex-col flex-1 min-h-0 overflow-hidden">

                    <!-- Header Bagian Atas Tiap Kolom -->
                    <div
                        class="flex flex-col items-center text-center pb-2 border-b border-{{ $tColor }}-300/40 dark:border-{{ $tColor }}-700/40 flex-shrink-0">
                        <!-- Bulatan Icon / Inisial Badge -->
                        <div
                            class="w-9 h-9 rounded-full bg-gradient-to-tr from-{{ $tColor }}-400 to-amber-300 border-2 border-white shadow-md flex items-center justify-center text-slate-950 font-black text-sm anim-game-float mb-1">
                            <i class="fa-solid {{ $tier['icon'] }}"></i>
                        </div>

                        <!-- Label Nominasi & Judul Reward -->
                        <span
                            class="text-[9px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider block leading-none">NOMINASI</span>
                        <h4
                            class="text-xs sm:text-sm font-black text-slate-950 dark:text-white uppercase tracking-tight leading-tight mt-0.5">
                            {{ $tier['name'] }}
                        </h4>
                        <span
                            class="text-[9px] font-bold text-slate-500 dark:text-slate-400 block leading-tight mt-0.5 truncate max-w-full"
                            title="{{ $tier['criteria_text'] }}">
                            {{ $tier['criteria_text'] }}
                        </span>

                        <!-- Yellow / Gold Pill: Jumlah Kandidat & Kuota Pemenang -->
                        <div
                            class="w-full my-1.5 py-1 px-1.5 rounded-xl bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-300 dark:from-amber-400 dark:via-yellow-300 dark:to-amber-400 text-slate-950 text-center border border-amber-400 dark:border-amber-500 shadow-xs leading-tight">
                            <span class="text-[10px] font-black block tracking-wide">{{ $tCount }}
                                KANDIDAT</span>
                            <span class="text-[8.5px] font-extrabold text-amber-900 block">Akan dipilih
                                {{ $tier['quota_winners'] }} pemenang</span>
                        </div>
                    </div>

                    <!-- Infinite Scrollable Candidate Rows -->
                    <div class="infinite-scroll-container flex-1 min-h-0 overflow-hidden mt-1.5 px-0.5 relative">
                        <div class="infinite-scroll-content flex flex-col gap-1 py-0.5 pb-2">
                            @forelse($tParticipants as $p)
                                @php
                                    $nameParts = explode(' ', trim($p->name));
                                    $initials = strtoupper(
                                        substr($nameParts[0], 0, 1) .
                                            (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''),
                                    );
                                @endphp
                                <div id="participant-card-{{ $p->id }}"
                                    data-participant-id="{{ $p->id }}"
                                    data-participant-name="{{ $p->name }}"
                                    data-participant-rank="{{ $loop->iteration }}"
                                    class="p-1.5 rounded-xl bg-white dark:bg-slate-800/90 border border-{{ $tColor }}-200 dark:border-slate-700 shadow-xs flex items-center justify-between text-xs hover:-translate-y-0.5 transition-transform">

                                    <!-- Left: Rank + Circle Avatar + Name/Dept -->
                                    <div class="flex items-center space-x-1.5 min-w-0">
                                        <!-- Rank Number -->
                                        <span
                                            class="text-[10px] font-black text-slate-600 dark:text-slate-300 w-3.5 text-center flex-shrink-0">
                                            {{ $loop->iteration }}
                                        </span>

                                        <!-- Circular Avatar with 2-Letter Initials or Photo -->
                                        <div
                                            class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-black text-[9px] flex items-center justify-center flex-shrink-0 shadow-xs uppercase overflow-hidden">
                                            @if ($p->photo)
                                                <img src="{{ $p->photo }}" alt="{{ $p->name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                {{ $initials ?: 'P' }}
                                            @endif
                                        </div>

                                        <!-- Name & Department -->
                                        <div class="min-w-0 truncate">
                                            <div class="font-black text-slate-900 dark:text-white text-[11px] truncate leading-tight"
                                                title="{{ $p->name }}">
                                                {{ $p->name }}
                                            </div>
                                            <div
                                                class="text-[9px] text-slate-500 dark:text-slate-400 font-bold truncate leading-tight">
                                                {{ $p->department ?? 'Umum' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Score -->
                                    <div class="flex-shrink-0 pl-1 text-right">
                                        <span
                                            class="text-xs font-black text-slate-900 dark:text-white">{{ $p->total_score }}</span>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="py-6 text-center text-slate-400 dark:text-slate-500 text-[10px] italic font-semibold border border-dashed border-slate-300 dark:border-slate-700 rounded-xl">
                                    Belum ada kandidat
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</div>
