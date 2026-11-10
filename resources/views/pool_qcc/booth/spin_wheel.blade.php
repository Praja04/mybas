@extends('pool_qcc.layout')

@section('title', 'Lucky Draw Machine - Pool QCC 2026')

@section('hide_nav', true)

@section('styles')
    <style>
        /* 3D Hanging Game Signboard Header */
        .game-signboard {
            background: linear-gradient(180deg, #581c87 0%, #3b0764 60%, #2e0854 100%);
            border: 3.5px solid #fbbf24;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.4), 0 3px 0 #b45309, 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .game-btn-3d {
            box-shadow: 0 4px 0 rgba(0, 0, 0, 0.25), 0 6px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.12s ease;
        }

        .game-btn-3d:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 0 rgba(0, 0, 0, 0.25), 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .game-btn-3d:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.25);
        }

        /* 3D Cartoon Layout Cards - No translateY on hover to avoid collision */
        .game-card-3d {
            box-shadow: 0 6px 0 rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.15s ease;
        }

        .game-card-3d:hover {
            box-shadow: 0 8px 0 rgba(0, 0, 0, 0.15), 0 12px 22px rgba(0, 0, 0, 0.1);
        }

        .game-sky-bg {
            background: radial-gradient(circle at 50% 10%, #60a5fa 0%, #38bdf8 40%, #e0f2fe 100%);
        }

        .dark .game-sky-bg {
            background: radial-gradient(circle at 50% 10%, #1e1b4b 0%, #0f172a 50%, #020617 100%);
        }

        @keyframes badge-pop {
            0% {
                transform: scale(0) rotate(-10deg);
                opacity: 0;
            }

            70% {
                transform: scale(1.2) rotate(4deg);
                opacity: 1;
            }

            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .anim-badge-pop {
            animation: badge-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        /* Soft edge fade/blur mask for scrollable candidate list */
        .infinite-scroll-mask {
            -webkit-mask-image: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.4) 6px, black 22px, black calc(100% - 22px), rgba(0, 0, 0, 0.4) calc(100% - 6px), transparent 100%);
            mask-image: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.4) 6px, black 22px, black calc(100% - 22px), rgba(0, 0, 0, 0.4) calc(100% - 6px), transparent 100%);
        }

        .spin-tier-tab {
            position: relative;
            z-index: 1;
        }

        .spin-tier-tab:hover {
            position: relative;
            z-index: 40 !important;
        }

        /* Slot Machine Motion Blur & Highlights */
        .slot-blur {
            filter: blur(2.5px);
        }

        /* 3D Slot Machine Drum Cylinder & Bezel Effects */
        .slot-viewport-3d {
            box-shadow: inset 0 35px 30px -15px rgba(0, 0, 0, 0.95),
                inset 0 -35px 30px -15px rgba(0, 0, 0, 0.95),
                0 20px 40px rgba(0, 0, 0, 0.7);
        }

        .slot-cabinet-3d {
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8),
                0 0 40px rgba(245, 158, 11, 0.25),
                inset 0 2px 4px rgba(255, 255, 255, 0.5),
                inset 0 -4px 8px rgba(0, 0, 0, 0.6);
        }

        .slot-token-3d {
            box-shadow: 0 8px 0 rgba(0, 0, 0, 0.45),
                0 12px 20px rgba(0, 0, 0, 0.35),
                inset 0 2px 3px rgba(255, 255, 255, 0.6);
        }

        @keyframes payline-glow {
            0% {
                box-shadow: 0 0 15px rgba(251, 191, 36, 0.6), inset 0 0 12px rgba(251, 191, 36, 0.35);
            }

            100% {
                box-shadow: 0 0 38px rgba(251, 191, 36, 1), inset 0 0 24px rgba(251, 191, 36, 0.7);
            }
        }

        .anim-payline-win {
            animation: payline-glow 0.35s ease-in-out infinite alternate;
        }

        @keyframes winner-card-glow {
            0% {
                background-color: rgba(245, 158, 11, 0.25);
                border-color: rgba(251, 191, 36, 0.85);
                box-shadow: inset 0 0 18px rgba(251, 191, 36, 0.4);
            }

            100% {
                background-color: rgba(245, 158, 11, 0.5);
                border-color: #fef08a;
                box-shadow: inset 0 0 30px rgba(251, 191, 36, 0.7), 0 0 25px rgba(251, 191, 36, 0.8);
            }
        }

        .anim-winner-card {
            animation: winner-card-glow 0.45s ease-in-out infinite alternate !important;
        }
    </style>
@endsection

@section('content')
    <div
        class="min-h-screen lg:h-screen lg:max-h-screen w-full px-2.5 sm:px-4 lg:px-6 py-2 sm:py-2.5 flex flex-col justify-start relative overflow-y-auto lg:overflow-hidden game-sky-bg text-slate-900 dark:text-slate-100 transition-colors font-game">

        <!-- Cartoon Game Background Ornaments (Clouds & Ambient Glow) -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <!-- Cloud 1 -->
            <div class="absolute -top-10 left-8 w-64 h-24 bg-white/40 dark:bg-indigo-950/30 rounded-full blur-xl"></div>
            <!-- Cloud 2 -->
            <div class="absolute top-12 right-12 w-80 h-28 bg-white/35 dark:bg-purple-950/25 rounded-full blur-xl"></div>
            <!-- Ground Castle Glow -->
            <div
                class="absolute -bottom-24 left-1/2 -translate-x-1/2 w-[700px] h-60 bg-amber-400/20 dark:bg-amber-500/10 rounded-full blur-3xl">
            </div>
        </div>

        <!-- Top Header & Navigation Bar (SEJAJAR) -->
        <div
            class="relative z-20 w-full flex items-center justify-between gap-2 sm:gap-4 mb-3 sm:mb-4 px-1 sm:px-2 flex-shrink-0">
            <!-- Left: Back to Leaderboard Button -->
            <div class="flex items-center space-x-2 flex-1 justify-start">
                <a href="{{ route('pool-qcc.leaderboard') }}"
                    class="py-1.5 px-3 sm:px-4 rounded-2xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white font-black text-xs border-2 border-slate-300 dark:border-slate-700 game-btn-3d flex items-center space-x-2 shadow-md hover:bg-slate-50">
                    <i class="fa-solid fa-arrow-left text-amber-500"></i>
                    <span class="hidden sm:inline">Kembali ke Leaderboard</span>
                    <span class="sm:hidden">Leaderboard</span>
                </a>
            </div>

            <!-- Center: Main Signboard with Hanging Rings -->
            <div class="flex flex-col items-center flex-shrink-0">
                <!-- Hanging Chain Rings from Ceiling -->
                <div class="flex justify-between w-36 max-w-full px-4 -mb-1 z-10 pointer-events-none">
                    <div
                        class="w-2 h-3.5 rounded-full bg-gradient-to-b from-amber-600 via-amber-400 to-amber-700 border border-amber-900 shadow-xs">
                    </div>
                    <div
                        class="w-2 h-3.5 rounded-full bg-gradient-to-b from-amber-600 via-amber-400 to-amber-700 border border-amber-900 shadow-xs">
                    </div>
                </div>
                <div
                    class="game-signboard relative px-4 sm:px-7 py-1 rounded-2xl text-center flex flex-col items-center max-w-xs sm:max-w-md">
                    <div
                        class="px-2.5 py-0.5 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300 text-slate-950 font-black text-[8.5px] sm:text-[9px] tracking-wider uppercase border border-amber-500 shadow-xs flex items-center space-x-1 mb-0.5 leading-none">
                        <span>🎬</span>
                        <span>QCC/QCP PT BUMI ALAM SEGAR 2026</span>
                        <span>🎬</span>
                    </div>
                    <h1
                        class="text-sm sm:text-base lg:text-lg font-black text-white tracking-tight drop-shadow-[0_3px_2px_rgba(0,0,0,0.85)] font-heading leading-tight flex items-center gap-2">
                        <span>LUCKY DRAW</span>
                    </h1>
                </div>
            </div>

            <!-- Right: Utility Controls (Theme, Fullscreen, Portal) -->
            <div class="flex items-center space-x-2 flex-1 justify-end">
                <button type="button" onclick="toggleTheme()"
                    class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs border-2 border-amber-600 game-btn-3d flex items-center space-x-1.5 cursor-pointer shadow-md"
                    title="Ganti Tema">
                    <span class="theme-icon-sun hidden text-sm"><i class="fa-solid fa-sun text-amber-600"></i></span>
                    <span class="theme-icon-moon text-sm"><i class="fa-solid fa-moon text-slate-900"></i></span>
                    <span class="theme-label hidden sm:inline text-[11px]">Tema</span>
                </button>

                <button onclick="toggleFullScreen()"
                    class="py-1 px-3 rounded-2xl bg-white hover:bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white border-2 border-slate-300 dark:border-slate-700 game-btn-3d cursor-pointer shadow-md"
                    title="Layar Penuh">
                    <i class="fa-solid fa-expand text-xs"></i>
                </button>

                <a href="{{ route('pool-qcc.portal-home') }}"
                    class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs border-2 border-indigo-800 game-btn-3d flex items-center space-x-1.5 shadow-md"
                    title="Kembali ke Portal BAS">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span class="hidden sm:inline text-[11px]">Portal</span>
                </a>
            </div>
        </div>

        <!-- 5 Reward Category Selector Tabs -->
        <div class="relative z-30 w-full mb-2.5 px-1 sm:px-2 flex-shrink-0">
            <div class="flex items-center gap-2 overflow-x-auto py-2 px-1" id="spinWheelTabs">
                @foreach ($rewardTiers as $key => $tier)
                    @php
                        $tColor = $tier['color'];
                        $tCount = $tier['participants']->count();
                        $isActive = $activeTier === $key;
                    @endphp
                    <button type="button" onclick="selectSpinTier('{{ $key }}')" data-tier="{{ $key }}"
                        class="spin-tier-tab flex-1 min-w-[150px] py-1.5 px-3 rounded-2xl font-black text-xs border-3 transition-all flex items-center justify-between cursor-pointer game-btn-3d {{ $isActive ? 'bg-amber-400 border-amber-600 text-slate-950 shadow-md' : 'bg-white/95 dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white' }}">
                        <div class="flex items-center space-x-2 truncate">
                            <span
                                class="w-6 h-6 rounded-lg bg-{{ $tColor }}-400 text-slate-950 flex items-center justify-center text-xs flex-shrink-0">
                                <i class="fa-solid {{ $tier['icon'] }}"></i>
                            </span>
                            <div class="text-left truncate">
                                <span class="tier-title-text block truncate leading-tight uppercase font-black {{ $isActive ? 'text-slate-950' : 'text-slate-900 dark:text-white' }}">{{ $tier['name'] }}</span>
                                <span
                                    class="tier-rule-text text-[9px] font-bold {{ $isActive ? 'text-slate-950/80' : 'text-slate-500 dark:text-slate-400' }} block leading-none">{{ $tier['rule_text'] }}</span>
                            </div>
                        </div>
                        <span id="tabBadge_{{ $key }}"
                            class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $isActive ? 'bg-slate-950 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200' }}">
                            {{ $tCount }}
                        </span>
                    </button>
                @endforeach

                <!-- Winners Log Tab -->
                <button type="button" onclick="selectSpinTier('winners_log')" data-tier="winners_log"
                    class="spin-tier-tab py-1.5 px-3.5 rounded-2xl font-black text-xs border-3 transition-all flex items-center space-x-1.5 cursor-pointer game-btn-3d bg-white/95 dark:bg-slate-800 border-amber-400 dark:border-amber-500/80 text-amber-700 dark:text-amber-300">
                    <i class="fa-solid fa-trophy text-amber-500"></i>
                    <span>Pemenang Sah</span>
                </button>
            </div>
        </div>

        <!-- Main Arena Grid (Wheel Stage on Left, Candidate Showcase on Right) -->
        <div
            class="relative z-10 flex-1 min-h-0 grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-stretch overflow-hidden mb-1">

            <!-- Standard Wheel Stage -->
            <div id="wheelInteractiveView"
                class="lg:col-span-12 grid grid-cols-1 lg:grid-cols-12 gap-3.5 items-stretch h-full min-h-0 overflow-hidden">

                <!-- Left Stage: Slot Machine Reel (8 cols) -->
                <div
                    class="lg:col-span-8 rounded-3xl p-3 sm:p-4 border-4 border-amber-400/90 bg-gradient-to-b from-slate-50 via-white to-amber-50/40 dark:from-slate-900 dark:via-slate-900/95 dark:to-purple-950/40 flex flex-col justify-between shadow-2xl relative game-card-3d h-full min-h-0 overflow-hidden">

                    <!-- Stage Title Header -->
                    <div
                        class="w-full flex items-center justify-between pb-2 mb-1 border-b-2 border-amber-300/40 dark:border-amber-500/20 flex-shrink-0">
                        <div class="flex items-center space-x-2">
                            <span
                                class="w-8 h-8 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-sm shadow-sm font-black">
                                <i class="fa-solid fa-dice"></i>
                            </span>
                            <div>
                                <h3 id="stageTierTitle"
                                    class="font-black text-sm sm:text-base text-slate-900 dark:text-white uppercase leading-tight">
                                    NOMINASI REWARD 1</h3>
                                <p id="stageTierSubtitle"
                                    class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400">Bisa
                                    menjawab benar > 8 booth &bull; Akan dipilih 1 pemenang</p>
                            </div>
                        </div>
                        <span id="stageCandidatePill"
                            class="px-3 py-1 rounded-full text-xs font-black bg-gradient-to-r from-amber-400 to-yellow-300 text-slate-950 border border-amber-500 shadow-sm">
                            0 Kandidat
                        </span>
                    </div>

                    <!-- Slot Machine Center Cabinet Display -->
                    <div
                        class="flex-1 min-h-0 flex flex-col items-center justify-center relative w-full my-auto px-1 sm:px-2">

                        <!-- Slot Machine Outer Casing (Maximized width & 3D Arcade Bezel) -->
                        <div
                            class="w-full max-w-4xl lg:max-w-5xl bg-gradient-to-b from-purple-950 via-slate-900 to-indigo-950 border-[6px] sm:border-[8px] border-amber-400 rounded-[2.5rem] p-3 sm:p-5 shadow-2xl relative slot-cabinet-3d">

                            <!-- Top Marquee Bar with Blinking Bulbs -->
                            <div
                                class="flex items-center justify-between px-4 py-2 mb-2.5 rounded-2xl bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-500 border-2 border-amber-300 shadow-lg text-slate-950 select-none">
                                <div class="flex items-center space-x-2 text-xs">
                                    <span class="w-3 h-3 rounded-full bg-rose-600 animate-pulse shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-200 shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs"></span>
                                </div>
                                <div
                                    class="text-xs sm:text-base font-black uppercase tracking-widest flex items-center gap-2 drop-shadow-sm">
                                    <span>LUCKY DRAW MACHINE</span>
                                </div>
                                <div class="flex items-center space-x-2 text-xs">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-200 shadow-xs"></span>
                                    <span class="w-3 h-3 rounded-full bg-rose-600 animate-pulse shadow-xs"></span>
                                </div>
                            </div>

                            <!-- 3 Reels Arcade Column Headers -->
                            <div
                                class="flex items-center gap-2 mb-2 text-center text-xs sm:text-sm font-black uppercase tracking-wider select-none">
                                <div
                                    class="w-28 sm:w-36 flex-shrink-0 py-1.5 rounded-xl bg-indigo-950/90 border-2 border-indigo-500/60 text-indigo-300 shadow-sm">
                                    1. Inisial
                                </div>
                                <div
                                    class="flex-1 min-w-0 py-1.5 rounded-xl bg-purple-950/90 border-2 border-purple-500/60 text-amber-300 shadow-sm">
                                    2. Nama & NIK
                                </div>
                                <div
                                    class="w-40 sm:w-56 md:w-64 flex-shrink-0 py-1.5 rounded-xl bg-cyan-950/90 border-2 border-cyan-500/60 text-cyan-300 shadow-sm">
                                    3. Departemen
                                </div>
                            </div>

                            <!-- Reel Viewport Window (with Side Lever on desktop) -->
                            <div class="flex items-center gap-3 sm:gap-4 relative">

                                <!-- The Vertical Slot Window (Height: 300px, 3D Curved Drum Cylinder Effect) -->
                                <div id="slotViewport"
                                    class="flex-1 relative rounded-3xl border-[5px] sm:border-[6px] border-amber-500 bg-slate-950 shadow-2xl overflow-hidden select-none slot-viewport-3d"
                                    style="height: 300px;">

                                    <!-- Top & Bottom Deep Curved Drum Vignette -->
                                    <div
                                        class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-950 via-slate-950/60 to-transparent pointer-events-none z-20">
                                    </div>
                                    <div
                                        class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent pointer-events-none z-20">
                                    </div>

                                    <!-- Left Neon 3D Arrow Indicator -->
                                    <div
                                        class="absolute left-1.5 sm:left-2 top-[150px] -translate-y-1/2 z-30 pointer-events-none flex items-center">
                                        <i
                                            class="fa-solid fa-caret-right text-amber-400 text-2xl sm:text-3xl drop-shadow-[0_0_12px_rgba(251,191,36,1)] animate-pulse"></i>
                                    </div>

                                    <!-- Right Neon 3D Arrow Indicator -->
                                    <div
                                        class="absolute right-1.5 sm:right-2 top-[150px] -translate-y-1/2 z-30 pointer-events-none flex items-center">
                                        <i
                                            class="fa-solid fa-caret-left text-amber-400 text-2xl sm:text-3xl drop-shadow-[0_0_12px_rgba(251,191,36,1)] animate-pulse"></i>
                                    </div>

                                    <!-- Center Payline Winning Zone (Spans across all 3 reels with 3D glass highlight) -->
                                    <div id="slotPaylineFrame"
                                        class="absolute inset-x-0 top-[100px] h-[100px] border-y-[3px] border-amber-400 bg-amber-400/[0.08] pointer-events-none z-20 shadow-[0_0_30px_rgba(251,191,36,0.35),inset_0_0_20px_rgba(251,191,36,0.2)] transition-all duration-300">
                                    </div>

                                    <!-- 3 Reels Side-by-Side Container with 3D Pillars -->
                                    <div
                                        class="w-full h-full flex items-stretch divide-x-[3px] divide-amber-500/50 relative z-10">

                                        <!-- Reel 1: Inisial Nama (w-28 sm:w-36) -->
                                        <div
                                            class="w-28 sm:w-36 flex-shrink-0 h-full overflow-hidden relative bg-slate-950/65">
                                            <div id="slotReelStrip1" class="w-full flex flex-col will-change-transform"
                                                style="transform: translateY(0px);">
                                                <!-- Populated dynamically via JS -->
                                            </div>
                                        </div>

                                        <!-- Reel 2: Nama & NIK (Center Flex-1 Maximized) -->
                                        <div
                                            class="flex-1 min-w-0 h-full overflow-hidden relative bg-slate-950/45 text-center">
                                            <div id="slotReelStrip2"
                                                class="w-full flex flex-col will-change-transform text-center"
                                                style="transform: translateY(0px);">
                                                <!-- Populated dynamically via JS -->
                                            </div>
                                        </div>

                                        <!-- Reel 3: Departemen (w-40 sm:w-56 md:w-64) -->
                                        <div
                                            class="w-40 sm:w-56 md:w-64 flex-shrink-0 h-full overflow-hidden relative bg-slate-950/65">
                                            <div id="slotReelStrip3" class="w-full flex flex-col will-change-transform"
                                                style="transform: translateY(0px);">
                                                <!-- Populated dynamically via JS -->
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- Classic Arcade 3D Pull Lever (Desktop Interactive) -->
                                {{-- <div
                                    class="hidden sm:flex flex-col items-center justify-center flex-shrink-0 relative select-none pl-1">
                                    <div id="slotLeverKnob" onclick="spinSlotMachine()"
                                        class="w-13 h-13 sm:w-14 sm:h-14 rounded-full bg-gradient-to-tr from-rose-700 via-rose-500 to-red-300 border-4 border-amber-300 shadow-[0_10px_20px_rgba(0,0,0,0.6),inset_0_4px_8px_rgba(255,255,255,0.7)] cursor-pointer hover:scale-105 active:scale-95 transition-all flex items-center justify-center text-white text-sm font-black z-10"
                                        title="Tarik Tuas untuk Spin!">
                                        <i class="fa-solid fa-bolt text-amber-200"></i>
                                    </div>
                                    <div id="slotLeverShaft"
                                        class="w-4 h-28 bg-gradient-to-r from-slate-400 via-white to-slate-500 rounded-b-md shadow-xl border border-slate-600 origin-bottom transition-transform duration-300 -mt-1.5">
                                    </div>
                                    <div
                                        class="w-10 h-7 rounded-2xl bg-gradient-to-b from-amber-500 to-amber-800 border-2 border-amber-300 shadow-xl -mt-2">
                                    </div>
                                </div> --}}
                            </div>

                            <!-- Bottom Console Bar & Spin Trigger Button -->
                            <div class="mt-4 pt-3 border-t-2 border-amber-400/40 flex items-center justify-between gap-3">
                                <div class="text-xs sm:text-sm font-bold text-amber-300 flex items-center gap-2">
                                    <span
                                        class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-300 flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                    <span class="hidden sm:inline">Urutan Berhenti: <strong class="text-cyan-300">3.
                                            Dept</strong> &bull; <strong class="text-indigo-300">1. Inisial</strong> &bull;
                                        <strong class="text-amber-300">2. Nama & NIK</strong></span>
                                    <span class="sm:hidden">Urutan: 3.Dept ➔ 1.Inisial ➔ 2.Nama</span>
                                </div>

                                <button type="button" id="centerSpinBtn" onclick="spinSlotMachine()"
                                    class="py-3 px-8 sm:px-12 rounded-2xl bg-gradient-to-b from-amber-300 via-yellow-400 to-amber-500 hover:from-yellow-200 hover:to-amber-400 active:translate-y-1 text-slate-950 font-black text-sm sm:text-base border-b-[5px] border-amber-700 shadow-[0_8px_20px_rgba(251,191,36,0.5)] cursor-pointer flex items-center space-x-2.5 flex-shrink-0 transition-transform">
                                    <i class="fa-solid fa-play text-sm"></i>
                                    <span id="centerSpinBtnText">SPIN</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Stage: Candidates Sidebar (4 cols) -->
                <div
                    class="lg:col-span-4 rounded-3xl p-3 sm:p-4 border-4 border-cyan-400/90 bg-white/95 dark:bg-slate-900/95 flex flex-col justify-between shadow-2xl relative game-card-3d h-full min-h-0 overflow-hidden">

                    <div class="flex-1 min-h-0 flex flex-col overflow-hidden">
                        <div
                            class="flex items-center justify-between pb-2 mb-2 border-b-2 border-cyan-300/40 dark:border-cyan-500/20 flex-shrink-0">
                            <div>
                                <h4 class="font-black text-xs sm:text-sm text-slate-900 dark:text-white uppercase">DAFTAR
                                    KANDIDAT</h4>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">Peserta Memenuhi
                                    Syarat</span>
                            </div>
                            <span id="sideCandidateBadge"
                                class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-cyan-400 text-slate-950">
                                0 Peserta
                            </span>
                        </div>

                        <!-- Winner Banner if this tier already has winners -->
                        <div id="sideWinnerBanner"
                            class="hidden mb-2 p-3 rounded-2xl bg-gradient-to-r from-amber-100 via-amber-50 to-yellow-100 dark:from-amber-950/40 dark:via-slate-800 dark:to-slate-800 border-2 border-amber-400 text-slate-900 dark:text-white shadow-sm flex-shrink-0">
                            <div
                                class="flex items-center justify-between pb-1.5 border-b border-amber-300/60 dark:border-amber-800/60">
                                <span
                                    class="text-[10px] font-black uppercase text-amber-800 dark:text-amber-300 flex items-center gap-1"
                                    id="sideWinnerBannerTitle">
                                    <i class="fa-solid fa-trophy text-amber-500"></i> Pemenang Sah Terundi:
                                </span>
                                <button type="button" onclick="resetTierWinner()"
                                    class="text-[10px] font-bold text-rose-600 hover:underline cursor-pointer">Reset
                                    Semua</button>
                            </div>
                            <div id="sideWinnerList" class="mt-2 space-y-1.5 max-h-44 overflow-y-auto pr-1">
                                <!-- Populated dynamically via JS -->
                            </div>
                        </div>

                        <!-- Candidates Scroll List with soft fade mask -->
                        <div id="sideCandidateList"
                            class="space-y-1.5 overflow-y-auto flex-1 min-h-0 pr-1 infinite-scroll-mask">
                            <!-- Populated dynamically via JS -->
                        </div>
                    </div>

                    <!-- Bottom Notice -->
                    <div
                        class="mt-2 pt-1.5 border-t border-slate-200 dark:border-slate-800 text-[9px] sm:text-[10px] text-slate-400 italic text-center flex-shrink-0">
                        * Pemenang yang diundi sah menjadi peraih hadiah pada kategori ini.
                    </div>
                </div>
            </div>

            <!-- Winners Recap Full View -->
            <div id="wheelWinnersView"
                class="hidden lg:col-span-12 rounded-3xl p-3 sm:p-4 border-4 border-amber-400 bg-white/95 dark:bg-slate-900/95 flex flex-col shadow-2xl relative game-card-3d h-full min-h-0 overflow-hidden">
                <div
                    class="flex items-center justify-between pb-2 mb-3 border-b-2 border-amber-300/40 dark:border-amber-500/20 flex-shrink-0">
                    <div class="flex items-center space-x-2.5">
                        <span
                            class="w-9 h-9 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-lg shadow-md">
                            <i class="fa-solid fa-trophy"></i>
                        </span>
                        <div>
                            <h3
                                class="font-black text-sm sm:text-base text-slate-900 dark:text-white uppercase leading-tight">
                                REKAPITULASI PEMENANG UNDIAN REWARD</h3>
                            <p class="text-[10px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400">Daftar
                                pemenang resmi yang terpilih melalui Lucky Draw Spin Wheel</p>
                        </div>
                    </div>

                    <button type="button" onclick="clearAllWinners()"
                        class="px-3 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 font-black text-xs border border-rose-300 dark:border-rose-700 cursor-pointer">
                        <i class="fa-solid fa-trash-can mr-1"></i> Reset Semua Pemenang
                    </button>
                </div>

                <div id="winnersLogCards"
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 flex-1 min-h-0 overflow-y-auto pr-1 infinite-scroll-mask">
                    <!-- Populated dynamically -->
                </div>
            </div>
        </div>

        <!-- GRAND WINNER CELEBRATION MODAL OVERLAY -->
        <div id="winnerCelebrationOverlay"
            class="fixed inset-0 z-[9999999] hidden flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md">
            <div
                class="w-full max-w-md bg-gradient-to-b from-amber-100 via-white to-amber-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 border-4 border-amber-400 rounded-3xl p-6 text-center shadow-2xl relative transform scale-95 transition-all anim-takeover-banner">

                <!-- Floating Crown -->
                <div class="text-5xl text-amber-500 anim-crown mb-2">
                    <i class="fa-solid fa-crown"></i>
                </div>

                <div class="px-3.5 py-1 rounded-full bg-amber-400 text-slate-950 text-xs font-black inline-block mb-3 uppercase tracking-wider shadow-sm"
                    id="winModalTierTag">
                    PEMENANG REWARD 1
                </div>

                <!-- Avatar Frame -->
                <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-amber-400 to-yellow-300 border-4 border-amber-500 mx-auto mb-3 flex items-center justify-center overflow-hidden shadow-xl"
                    id="winModalAvatar">
                    <i class="fa-solid fa-user-astronaut text-4xl text-amber-950"></i>
                </div>

                <h3 class="text-2xl font-black text-slate-950 dark:text-white tracking-tight leading-tight"
                    id="winModalName">
                    Nama Pemenang
                </h3>
                <p class="text-xs font-bold text-slate-600 dark:text-slate-400 mt-1" id="winModalMeta">
                    Departemen &bull; NIK: 12345
                </p>

                <div
                    class="my-4 p-3 rounded-2xl bg-amber-200/80 dark:bg-slate-800 border border-amber-400 dark:border-slate-700 flex items-center justify-center gap-4">
                    <div>
                        <span
                            class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 block leading-none">Skor
                            Kuis</span>
                        <span class="text-lg font-black text-amber-700 dark:text-amber-400" id="winModalScore">10
                            Poin</span>
                    </div>
                    <div class="h-6 w-px bg-slate-300 dark:bg-slate-700"></div>
                    <div>
                        <span
                            class="text-[10px] uppercase font-bold text-slate-500 dark:text-slate-400 block leading-none">Status</span>
                        <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">Pemenang Sah</span>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-2">
                    <button type="button" onclick="confirmWinner()"
                        class="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-amber-400 to-yellow-400 hover:from-amber-300 hover:to-yellow-300 text-slate-950 font-black text-sm border-2 border-amber-600 shadow-md game-btn-3d cursor-pointer">
                        <i class="fa-solid fa-check mr-1"></i> Simpan Pemenang
                    </button>
                    <button type="button" onclick="spinAgain()"
                        class="py-3 px-4 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs border border-slate-300 dark:border-slate-700 hover:bg-slate-300 cursor-pointer">
                        Spin Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Web Audio API Synthesizer
        const GameAudio = {
            ctx: null,
            init() {
                if (!this.ctx) {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (AudioCtx) this.ctx = new AudioCtx();
                }
                if (this.ctx && this.ctx.state === 'suspended') {
                    this.ctx.resume().catch(() => {});
                }
            },
            playTick() {
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(750, now);
                    osc.frequency.exponentialRampToValueAtTime(120, now + 0.04);
                    gain.gain.setValueAtTime(0.12, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.04);
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.05);
                } catch (e) {}
            },
            playFanfare() {
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;
                    const notes = [{
                            f: 523.25,
                            time: 0,
                            dur: 0.12
                        },
                        {
                            f: 659.25,
                            time: 0.1,
                            dur: 0.12
                        },
                        {
                            f: 783.99,
                            time: 0.2,
                            dur: 0.14
                        },
                        {
                            f: 1046.50,
                            time: 0.32,
                            dur: 0.50
                        }
                    ];

                    notes.forEach(n => {
                        const osc = this.ctx.createOscillator();
                        const gain = this.ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(n.f, now + n.time);
                        gain.gain.setValueAtTime(0.001, now + n.time);
                        gain.gain.linearRampToValueAtTime(0.35, now + n.time + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.0001, now + n.time + n.dur);
                        osc.connect(gain);
                        gain.connect(this.ctx.destination);
                        osc.start(now + n.time);
                        osc.stop(now + n.time + n.dur + 0.05);
                    });
                } catch (e) {}
            }
        };

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Candidates Cache
        window.qccRewardCandidates = {!! json_encode($rewardCandidates ?? []) !!};
        let currentSpinTier = '{{ $activeTier ?? 'reward_1' }}';
        let currentCandidates = [];
        let isSpinning = false;
        let wheelAngle = 0;
        let pendingWinner = null;
        const wheelColors = [
            '#f59e0b', '#06b6d4', '#10b981', '#ec4899', '#8b5cf6',
            '#f97316', '#3b82f6', '#14b8a6', '#e11d48', '#84cc16'
        ];

        function getSavedWinners() {
            try {
                const raw = JSON.parse(localStorage.getItem('qcc_reward_winners') || '{}');
                const normalized = {};
                for (const k in raw) {
                    if (Array.isArray(raw[k])) {
                        normalized[k] = raw[k];
                    } else if (raw[k] && typeof raw[k] === 'object') {
                        normalized[k] = [raw[k]];
                    } else {
                        normalized[k] = [];
                    }
                }
                return normalized;
            } catch (e) {
                return {};
            }
        }

        function getTierWinners(tierKey) {
            const all = getSavedWinners();
            return all[tierKey] || [];
        }

        function saveWinner(tierKey, winner) {
            const all = getSavedWinners();
            let list = getTierWinners(tierKey);
            const tierData = window.qccRewardCandidates[tierKey] || {};
            const quota = tierData.quota_winners || 1;

            const alreadyWon = list.some(w => w.id == winner.id || (w.nik && w.nik == winner.nik));
            if (!alreadyWon && list.length < quota) {
                const newWinner = Object.assign({}, winner, {
                    order: list.length + 1,
                    won_at: new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    })
                });
                list.push(newWinner);
            }
            all[tierKey] = list;
            localStorage.setItem('qcc_reward_winners', JSON.stringify(all));
        }

        function removeSingleWinner(tierKey, winnerId) {
            const all = getSavedWinners();
            let list = getTierWinners(tierKey);
            list = list.filter(w => w.id != winnerId);
            list.forEach((w, idx) => w.order = idx + 1);
            all[tierKey] = list;
            localStorage.setItem('qcc_reward_winners', JSON.stringify(all));
            selectSpinTier(currentSpinTier);
        }

        function resetTierWinner() {
            const tierData = window.qccRewardCandidates[currentSpinTier] || {};
            const tierName = tierData.name || currentSpinTier;
            if (confirm(`Yakin ingin mereset pemenang undian untuk ${tierName}?`)) {
                const all = getSavedWinners();
                delete all[currentSpinTier];
                localStorage.setItem('qcc_reward_winners', JSON.stringify(all));
                selectSpinTier(currentSpinTier);
            }
        }

        function clearAllWinners() {
            if (confirm('Yakin ingin mereset semua pemenang undian yang telah tersimpan?')) {
                localStorage.removeItem('qcc_reward_winners');
                selectSpinTier('winners_log');
            }
        }

        function selectSpinTier(tierKey) {
            if (isSpinning) return;
            currentSpinTier = tierKey;

            // Update Tab Active Styles
            $('.spin-tier-tab').each(function() {
                const t = $(this).data('tier');
                const $badge = $(this).find('[id^="tabBadge_"]');
                const $title = $(this).find('.tier-title-text');
                const $rule = $(this).find('.tier-rule-text');

                if (t === tierKey) {
                    $(this).removeClass(
                            'bg-white/95 dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white border-amber-400 dark:border-amber-500/80 text-amber-700 dark:text-amber-300'
                        )
                        .addClass('bg-amber-400 border-amber-600 text-slate-950 shadow-md');
                    $badge.removeClass('bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200')
                        .addClass('bg-slate-950 text-white');
                    $title.removeClass('text-slate-900 dark:text-white').addClass('text-slate-950');
                    $rule.removeClass('text-slate-500 dark:text-slate-400').addClass('text-slate-950/80');
                } else {
                    $(this).removeClass('bg-amber-400 border-amber-600 text-slate-950 shadow-md');
                    if (t === 'winners_log') {
                        $(this).addClass('bg-white/95 dark:bg-slate-800 border-amber-400 dark:border-amber-500/80 text-amber-700 dark:text-amber-300');
                    } else {
                        $(this).addClass('bg-white/95 dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-white');
                    }
                    $badge.removeClass('bg-slate-950 text-white')
                        .addClass('bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200');
                    $title.removeClass('text-slate-950').addClass('text-slate-900 dark:text-white');
                    $rule.removeClass('text-slate-950/80').addClass('text-slate-500 dark:text-slate-400');
                }
            });

            if (tierKey === 'winners_log') {
                $('#wheelInteractiveView').addClass('hidden');
                $('#wheelWinnersView').removeClass('hidden');
                renderWinnersLogView();
                return;
            }

            $('#wheelInteractiveView').removeClass('hidden');
            $('#wheelWinnersView').addClass('hidden');

            const tierData = window.qccRewardCandidates[tierKey] || {
                title: tierKey.toUpperCase(),
                rule_text: '',
                quota_winners: 1,
                candidates: []
            };

            const quota = tierData.quota_winners || 1;
            const tierWinners = getTierWinners(tierKey);
            const allCandidates = tierData.candidates || [];

            // Filter kandidat yang belum pernah menang di tier ini
            const availableCandidates = allCandidates.filter(c => !tierWinners.some(w => w.id == c.id || (w.nik && w.nik ==
                c.nik)));

            const isCompleted = tierWinners.length >= quota;
            currentCandidates = isCompleted ? [] : availableCandidates;

            // Update Stage Titles
            $('#stageTierTitle').text(tierData.title || tierKey.toUpperCase());
            const quotaText = quota > 1 ?
                ` • Kuota: ${quota} Pemenang (${tierWinners.length}/${quota} Terpilih)` :
                ' • Kuota: 1 Pemenang';
            $('#stageTierSubtitle').text((tierData.criteria_text || '') + quotaText);
            $('#stageCandidatePill').text(availableCandidates.length + ' Sisa Kandidat');
            $('#sideCandidateBadge').text(allCandidates.length + ' Total Kandidat');

            // Update Center Spin Button & Lever Status
            const $spinHub = $('#centerSpinBtn');
            const $spinText = $('#centerSpinBtnText');
            const $leverKnob = $('#slotLeverKnob');

            if (isCompleted) {
                $spinHub.prop('disabled', true).addClass('opacity-75 cursor-not-allowed bg-emerald-500 text-white')
                    .html(
                        `<i class="fa-solid fa-check text-base"></i><span class="text-xs font-black uppercase">LENGKAP</span>`
                    );
                $leverKnob.addClass('opacity-50 cursor-not-allowed pointer-events-none');
            } else if (availableCandidates.length === 0) {
                $spinHub.prop('disabled', true).addClass('opacity-50 cursor-not-allowed')
                    .html(`<i class="fa-solid fa-ban text-xs"></i><span class="text-xs font-black uppercase">HABIS</span>`);
                $leverKnob.addClass('opacity-50 cursor-not-allowed pointer-events-none');
            } else {
                $spinHub.prop('disabled', false).removeClass(
                        'opacity-50 opacity-75 cursor-not-allowed bg-emerald-500 text-white')
                    .html(
                        `<i class="fa-solid fa-play text-xs"></i><span>SPIN</span><span class="text-[10px] font-black opacity-85 px-1.5 py-0.5 rounded bg-amber-950 text-amber-300">#${tierWinners.length + 1}</span>`
                    );
                $leverKnob.removeClass('opacity-50 cursor-not-allowed pointer-events-none');
            }

            // Check if this tier has saved winners
            if (tierWinners.length > 0) {
                $('#sideWinnerBannerTitle').html(
                    `<i class="fa-solid fa-trophy text-amber-500"></i> Pemenang Sah (${tierWinners.length}/${quota} Terpilih):`
                );
                const $wList = $('#sideWinnerList');
                $wList.empty();
                tierWinners.forEach((w, idx) => {
                    $wList.append(`
                        <div class="p-1.5 rounded-xl bg-white/90 dark:bg-slate-900/90 border border-amber-300 dark:border-amber-700/60 flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-2 min-w-0">
                                <span class="w-5 h-5 rounded-md bg-amber-400 text-slate-950 font-black text-[10px] flex items-center justify-center flex-shrink-0">
                                    #${w.order || (idx + 1)}
                                </span>
                                <div class="truncate">
                                    <span class="font-extrabold text-slate-900 dark:text-white text-[11px] truncate block leading-tight">${escapeHtml(w.name)}</span>
                                    <span class="text-[9px] text-slate-500 dark:text-slate-400 truncate block leading-none font-bold">${escapeHtml(w.department || 'Umum')} &bull; NIK: ${escapeHtml(w.nik || '-')}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1.5 flex-shrink-0 pl-1">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-black bg-amber-100 dark:bg-amber-950 text-amber-900 dark:text-amber-300">
                                    ${w.total_score} Pts
                                </span>
                                <button type="button" onclick="removeSingleWinner('${tierKey}', ${w.id})" title="Hapus pemenang ini untuk diundi ulang" class="text-slate-400 hover:text-rose-600 text-xs p-0.5 cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>
                    `);
                });
                $('#sideWinnerBanner').removeClass('hidden');
            } else {
                $('#sideWinnerBanner').addClass('hidden');
            }

            // Populate Sidebar Candidates List
            const $cList = $('#sideCandidateList');
            $cList.empty();

            if (allCandidates.length === 0) {
                $cList.html(`
                    <div class="py-12 text-center text-xs text-slate-400 dark:text-slate-500 italic rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700">
                        Belum ada peserta yang memenuhi syarat (${tierData.rule_text || ''}).
                    </div>
                `);
            } else {
                allCandidates.forEach((c, idx) => {
                    const winIdx = tierWinners.findIndex(w => w.id == c.id || (w.nik && w.nik == c.nik));
                    const isWin = winIdx !== -1;
                    const winOrder = isWin ? (tierWinners[winIdx].order || (winIdx + 1)) : 0;

                    const words = (c.name || '').trim().split(' ');
                    let initials = '';
                    words.slice(0, 2).forEach(w => initials += w.charAt(0).toUpperCase());

                    $cList.append(`
                        <div class="p-2.5 rounded-2xl bg-white dark:bg-slate-800 border-2 ${isWin ? 'border-amber-400 bg-amber-50/70 dark:bg-amber-950/40 shadow-sm' : 'border-slate-200 dark:border-slate-700'} flex items-center justify-between text-xs transition-all hover:-translate-y-0.5 shadow-xs">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <span class="w-6 text-[10px] font-black text-slate-500 text-center flex-shrink-0">
                                    #${idx + 1}
                                </span>
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-black text-[11px] flex items-center justify-center flex-shrink-0 shadow-xs uppercase">
                                    ${(c.name || 'P').trim().charAt(0).toUpperCase()}
                                </div>
                                <div class="truncate leading-tight">
                                    <span class="font-black text-slate-900 dark:text-white truncate block text-xs leading-tight">${escapeHtml(c.name)}</span>
                                    <span class="text-[9px] text-slate-500 dark:text-slate-400 truncate block leading-none font-bold mt-0.5">${escapeHtml(c.department || 'Umum')} &bull; NIK: ${escapeHtml(c.nik || '-')}</span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 pl-1">
                                <span class="px-2 py-0.5 rounded-lg bg-amber-100 dark:bg-slate-700 font-black text-amber-950 dark:text-amber-300 text-xs">
                                    ${c.total_score} Pts
                                </span>
                                ${isWin ? `<span class="text-[9px] font-black text-amber-700 dark:text-amber-300 block mt-0.5"><i class="fa-solid fa-crown text-[8px] mr-0.5"></i> SAH #${winOrder}</span>` : ''}
                            </div>
                        </div>
                    `);
                });
            }

            renderSlotInitialView();
        }

        function renderWinnersLogView() {
            const allWinners = getSavedWinners();
            const $container = $('#winnersLogCards');
            $container.empty();

            const singleTierKeys = ['reward_1', 'reward_2', 'reward_3', 'reward_4'];

            // 1. Render Reward 1 - 4 (Single winner each)
            singleTierKeys.forEach(key => {
                const list = getTierWinners(key);
                const win = list.length > 0 ? list[0] : null;
                const tierInfo = window.qccRewardCandidates[key] || {
                    title: key.toUpperCase(),
                    rule_text: ''
                };

                if (win) {
                    $container.append(`
                        <div class="p-4 rounded-3xl bg-gradient-to-br from-amber-50 to-yellow-100/70 dark:from-slate-800 dark:to-slate-900 border-3 border-amber-400 shadow-md">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-amber-400 text-slate-950 uppercase">${tierInfo.title || key.toUpperCase()}</span>
                                <span class="text-[10px] font-bold text-slate-500">${tierInfo.rule_text || ''}</span>
                            </div>
                            <div class="flex items-center space-x-3 mt-3">
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 border-2 border-white text-white font-black text-lg flex items-center justify-center shadow-sm flex-shrink-0 uppercase">
                                    ${(win.name || 'P').trim().charAt(0).toUpperCase()}
                                </div>
                                <div class="truncate">
                                    <h5 class="font-black text-sm text-slate-950 dark:text-white truncate">${escapeHtml(win.name)}</h5>
                                    <p class="text-xs text-slate-600 dark:text-slate-400 font-bold truncate">${escapeHtml(win.department || 'Umum')} &bull; NIK: ${escapeHtml(win.nik || '-')}</p>
                                    <span class="text-xs font-black text-amber-700 dark:text-amber-400">Skor: ${win.total_score || '-'} Poin</span>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $container.append(`
                        <div class="p-4 rounded-3xl bg-slate-100 dark:bg-slate-800/40 border-2 border-dashed border-slate-300 dark:border-slate-700 text-center flex flex-col justify-center items-center py-6">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-400 uppercase mb-2">${tierInfo.title || key.toUpperCase()}</span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 italic font-semibold">Belum Diundi</span>
                            <button type="button" onclick="selectSpinTier('${key}')" class="mt-3 px-4 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs game-btn-3d cursor-pointer shadow-sm">
                                <i class="fa-solid fa-dharmachakra mr-1"></i> Undi Sekarang
                            </button>
                        </div>
                    `);
                }
            });

            // 2. Render Reward 5 (16 Winners Full Master Section)
            const list5 = getTierWinners('reward_5');
            const tier5Info = window.qccRewardCandidates['reward_5'] || {
                title: 'NOMINASI REWARD 5',
                rule_text: '> 2 booth',
                quota_winners: 16
            };
            const quota5 = tier5Info.quota_winners || 16;

            let slotsHtml = '';
            for (let i = 1; i <= quota5; i++) {
                const w = list5[i - 1];
                if (w) {
                    const words = (w.name || '').trim().split(' ');
                    let initials = '';
                    words.slice(0, 2).forEach(s => initials += s.charAt(0).toUpperCase());
                    slotsHtml += `
                        <div class="p-2.5 rounded-2xl bg-white dark:bg-slate-800 border-2 border-purple-400 shadow-xs flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-2 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-purple-600 text-white font-black text-[11px] flex items-center justify-center flex-shrink-0">
                                    #${i}
                                </span>
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-black text-[11px] flex items-center justify-center flex-shrink-0 shadow-xs uppercase">
                                    ${(w.name || 'P').trim().charAt(0).toUpperCase()}
                                </div>
                                <div class="truncate">
                                    <h6 class="font-extrabold text-slate-900 dark:text-white text-xs truncate leading-tight">${escapeHtml(w.name)}</h6>
                                    <p class="text-[9px] text-slate-500 dark:text-slate-400 truncate leading-none font-bold">${escapeHtml(w.department || 'Umum')} &bull; NIK: ${escapeHtml(w.nik || '-')}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950 font-black text-purple-900 dark:text-purple-300 text-[11px] flex-shrink-0 ml-1">
                                ${w.total_score} Pts
                            </span>
                        </div>
                    `;
                } else {
                    slotsHtml += `
                        <div onclick="selectSpinTier('reward_5')" class="p-2.5 rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-purple-400 dark:hover:border-purple-500 bg-slate-50/50 dark:bg-slate-900/50 flex items-center justify-between text-xs text-slate-400 hover:text-purple-600 transition cursor-pointer group">
                            <div class="flex items-center space-x-2">
                                <span class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-700 group-hover:bg-purple-100 group-hover:text-purple-700 font-bold text-[11px] flex items-center justify-center text-slate-500 transition">
                                    #${i}
                                </span>
                                <span class="text-[11px] italic font-semibold">Slot Pemenang #${i}</span>
                            </div>
                            <i class="fa-solid fa-plus text-xs group-hover:scale-110 transition-transform"></i>
                        </div>
                    `;
                }
            }

            const lanjutUndiBtn =
                list5.length < quota5 ? `
                    <button type="button" onclick="selectSpinTier('reward_5')" class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-purple-600 hover:bg-purple-700 text-white shadow-md transition flex items-center space-x-1.5 cursor-pointer">
                        <i class="fa-solid fa-dharmachakra"></i>
                        <span>Lanjut Undi (#${list5.length + 1})</span>
                    </button> 
           ` : ''

            $container.append(`
                <div class="col-span-full mt-2 p-5 rounded-3xl bg-gradient-to-br from-purple-50/70 via-white to-amber-50/60 dark:from-slate-800/90 dark:via-slate-900 dark:to-slate-900 border-3 border-purple-400/90 shadow-xl">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 mb-4 border-b-2 border-purple-200 dark:border-purple-800">
                        <div class="flex items-center space-x-3">
                            <span class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center text-lg shadow-md">
                                <i class="fa-solid fa-gift"></i>
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-black text-base text-slate-900 dark:text-white uppercase leading-tight">
                                        ${escapeHtml(tier5Info.title || 'NOMINASI REWARD 5')}
                                    </h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-700">
                                        16 Pemenang Sah
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-bold mt-0.5">
                                    ${escapeHtml(tier5Info.criteria_text || 'Bisa menjawab benar > 2 booth')} &bull; Status: ${list5.length} dari ${quota5} Pemenang Terundi
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1.5 rounded-xl text-xs font-black ${list5.length === quota5 ? 'bg-emerald-500 text-white shadow-md' : 'bg-amber-400 text-slate-950 shadow-sm'}">
                                ${list5.length === quota5 ? '✓ 16 Pemenang Lengkap' : `${list5.length} / ${quota5} Pemenang Terpilih`}
                            </span>
                            ${lanjutUndiBtn}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                        ${slotsHtml}
                    </div>
                </div>
            `);
        }

        // ========================================================
        // 3-REEL SLOT MACHINE ENGINE & STAGGERED PHYSICS
        // Stop Sequence: Reel 3 (Dept) ➔ Reel 1 (Initial) ➔ Reel 2 (Name & NIK)
        // ========================================================
        const SLOT_ITEM_HEIGHT = 100; // 100px per card
        const SLOT_CENTER_OFFSET = 100; // Center payline is at top: 100px in 300px viewport

        function getParticipantInitial(name) {
            if (!name) return 'P';
            const clean = name.trim();
            return clean ? clean.charAt(0).toUpperCase() : 'P';
        }

        function getParticipantGradient(name) {
            const gradients = [
                'from-indigo-500 to-purple-600',
                'from-blue-500 to-cyan-500',
                'from-rose-500 to-pink-600',
                'from-amber-500 to-orange-600',
                'from-emerald-500 to-teal-600',
                'from-violet-600 to-indigo-700'
            ];
            let hash = 0;
            for (let i = 0; i < (name || '').length; i++) {
                hash = (hash + name.charCodeAt(i)) % gradients.length;
            }
            return gradients[hash];
        }

        // Reel 1: Inisial Nama (3D Casino Token Chip)
        function buildReel1Item(candidate, isWinner = false) {
            if (!candidate) return '';
            const initial = getParticipantInitial(candidate.name);
            const gradient = getParticipantGradient(candidate.name);
            const winnerStyle = isWinner ? 'anim-winner-card' : 'bg-slate-900/85';

            return `
                <div class="slot-reel1-card w-full h-[100px] flex items-center justify-center border-b border-slate-950/80 border-t border-slate-800/60 ${winnerStyle} transition-all duration-300 select-none flex-shrink-0 box-border px-1">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-gradient-to-br ${gradient} text-white font-black text-3xl sm:text-4xl flex items-center justify-center slot-token-3d border-[3px] border-white/60 uppercase">
                        ${initial}
                    </div>
                </div>
            `;
        }

        // Reel 2: Nama & NIK (Teks Nama Lebih Gede & Tebal 3D - Centered)
        function buildReel2Item(candidate, isWinner = false) {
            if (!candidate) return '';
            const winnerStyle = isWinner ? 'anim-winner-card' : 'bg-slate-900/85';

            return `
                <div class="slot-reel2-card w-full h-[100px] flex flex-col items-center justify-center px-3 sm:px-6 border-b border-slate-950/80 border-t border-slate-800/60 ${winnerStyle} transition-all duration-300 select-none flex-shrink-0 box-border min-w-0 text-center">
                    <h4 class="font-black text-white text-base sm:text-xl md:text-2xl tracking-wide truncate leading-tight drop-shadow-md text-center max-w-full">
                        ${escapeHtml(candidate.name || 'Peserta')}
                    </h4>
                    <div class="flex items-center justify-center mt-1.5">
                        <span class="px-2.5 py-0.5 sm:py-1 rounded-md bg-slate-800/90 text-amber-300 border border-slate-700 text-xs sm:text-sm font-mono font-black inline-block shadow-xs">
                            NIK: ${escapeHtml(candidate.nik || '-')}
                        </span>
                    </div>
                </div>
            `;
        }

        // Reel 3: Departemen (Teks Gede, Bersih Tanpa Icon & Poin)
        function buildReel3Item(candidate, isWinner = false) {
            if (!candidate) return '';
            const winnerStyle = isWinner ? 'anim-winner-card' : 'bg-slate-900/85';

            return `
                <div class="slot-reel3-card w-full h-[100px] flex items-center justify-center px-3 border-b border-slate-950/80 border-t border-slate-800/60 ${winnerStyle} transition-all duration-300 select-none flex-shrink-0 box-border text-center">
                    <h4 class="font-black text-base sm:text-xl md:text-2xl text-cyan-300 tracking-wider uppercase truncate leading-tight drop-shadow-md max-w-full">
                        ${escapeHtml(candidate.department || 'Umum')}
                    </h4>
                </div>
            `;
        }

        // Helper: Fisher-Yates Array Shuffle
        function shuffleArray(arr) {
            const a = [...arr];
            for (let i = a.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [a[i], a[j]] = [a[j], a[i]];
            }
            return a;
        }

        function renderSlotInitialView() {
            const $strip1 = $('#slotReelStrip1');
            const $strip2 = $('#slotReelStrip2');
            const $strip3 = $('#slotReelStrip3');
            const $payline = $('#slotPaylineFrame');
            $payline.removeClass('anim-payline-win');

            const tierData = window.qccRewardCandidates[currentSpinTier] || {};
            const quota = tierData.quota_winners || 1;
            const tierWinners = getTierWinners(currentSpinTier);
            const isCompleted = tierWinners.length >= quota;

            if (isCompleted) {
                const completedHtml = `
                    <div class="w-full h-[100px] flex items-center justify-center bg-emerald-950/90 border-2 border-emerald-400 text-emerald-200 px-2 text-center rounded-xl shadow-lg">
                        <div class="font-black text-xs text-emerald-300">LENGKAP</div>
                    </div>
                `;
                $strip1.css('transform', `translateY(${SLOT_CENTER_OFFSET}px)`).html(completedHtml);
                $strip2.css('transform', `translateY(${SLOT_CENTER_OFFSET}px)`).html(`
                    <div class="w-full h-[100px] flex items-center justify-center bg-emerald-950/90 border-2 border-emerald-400 text-emerald-200 px-3 text-center rounded-xl shadow-lg">
                        <div>
                            <div class="font-black text-sm text-emerald-300">SEMUA ${quota} PEMENANG LENGKAP TERUNDI!</div>
                            <p class="text-[10px] text-emerald-400/80 font-semibold mt-0.5">Lihat tab Rekap Pemenang</p>
                        </div>
                    </div>
                `);
                $strip3.css('transform', `translateY(${SLOT_CENTER_OFFSET}px)`).html(completedHtml);
                return;
            }

            if (!currentCandidates || currentCandidates.length === 0) {
                const emptyHtml = `
                    <div class="w-full h-[100px] flex items-center justify-center bg-slate-900/90 border-2 border-slate-700 text-slate-400 px-2 text-center rounded-xl">
                        <div class="font-black text-xs text-slate-400">-</div>
                    </div>
                `;
                $strip1.css('transform', `translateY(${SLOT_CENTER_OFFSET}px)`).html(emptyHtml);
                $strip2.css('transform', `translateY(${SLOT_CENTER_OFFSET}px)`).html(`
                    <div class="w-full h-[100px] flex items-center justify-center bg-slate-900/90 border-2 border-slate-700 text-slate-400 px-3 text-center rounded-xl">
                        <div class="font-black text-xs text-slate-300">Belum Ada Kandidat Tersedia</div>
                    </div>
                `);
                $strip3.css('transform', `translateY(${SLOT_CENTER_OFFSET}px)`).html(emptyHtml);
                return;
            }

            // Display 3 stacked rows across all 3 reels (Row 0: Top, Row 1: Center Payline, Row 2: Bottom)
            // Guarantee 3 distinct candidates whenever candidates >= 3
            const n = currentCandidates.length;
            let topCandidate, centerCandidate, bottomCandidate;
            if (n === 1) {
                topCandidate = centerCandidate = bottomCandidate = currentCandidates[0];
            } else if (n === 2) {
                topCandidate = currentCandidates[1];
                centerCandidate = currentCandidates[0];
                bottomCandidate = currentCandidates[1];
            } else {
                const previewShuffled = shuffleArray(currentCandidates);
                topCandidate = previewShuffled[0];
                centerCandidate = previewShuffled[1];
                bottomCandidate = previewShuffled[2];
            }

            // Reel 1: Inisial
            let html1 = '';
            html1 += buildReel1Item(topCandidate, false);
            html1 += buildReel1Item(centerCandidate, false);
            html1 += buildReel1Item(bottomCandidate, false);
            $strip1.html(html1).css('transform', 'translateY(0px)');

            // Reel 2: Nama & NIK
            let html2 = '';
            html2 += buildReel2Item(topCandidate, false);
            html2 += buildReel2Item(centerCandidate, false);
            html2 += buildReel2Item(bottomCandidate, false);
            $strip2.html(html2).css('transform', 'translateY(0px)');

            // Reel 3: Dept
            let html3 = '';
            html3 += buildReel3Item(topCandidate, false);
            html3 += buildReel3Item(centerCandidate, false);
            html3 += buildReel3Item(bottomCandidate, false);
            $strip3.html(html3).css('transform', 'translateY(0px)');
        }

        // Fair & Balanced Reel Sequence Generator:
        // 1. Equal-frequency deck distribution: Every candidate appears equally throughout the reel strip.
        // 2. No identical candidates ever placed next to each other.
        // 3. Absolute payline protection: Center payline is locked to pendingWinner,
        //    while top and bottom neighbors are strictly guaranteed to be DIFFERENT people from the winner!
        function generateReelSequence(candidates, winner, targetIndex, totalItems) {
            const n = candidates.length;
            if (n === 0) return [];
            if (n === 1) {
                return Array(totalItems).fill(candidates[0]);
            }

            // 1. Equal Frequency Distribution via Full Decks
            const decksNeeded = Math.ceil(totalItems / n) + 2;
            let pool = [];
            for (let d = 0; d < decksNeeded; d++) {
                pool.push(...shuffleArray(candidates));
            }

            // 2. Prevent adjacent identical candidates across deck joins
            for (let i = 1; i < pool.length; i++) {
                if (pool[i].id === pool[i - 1].id) {
                    for (let j = i + 1; j < pool.length; j++) {
                        if (pool[j].id !== pool[i - 1].id) {
                            [pool[i], pool[j]] = [pool[j], pool[i]];
                            break;
                        }
                    }
                }
            }

            const sequence = pool.slice(0, totalItems);

            // 3. Lock Winner at targetIndex
            if (sequence[targetIndex].id !== winner.id) {
                let swapIdx = -1;
                for (let i = 0; i < sequence.length; i++) {
                    if (i < targetIndex - 1 || i > targetIndex + 1) {
                        if (sequence[i].id === winner.id) {
                            swapIdx = i;
                            break;
                        }
                    }
                }
                if (swapIdx !== -1) {
                    [sequence[targetIndex], sequence[swapIdx]] = [sequence[swapIdx], sequence[targetIndex]];
                } else {
                    sequence[targetIndex] = winner;
                }
            }

            // 4. Guarantee top neighbor (targetIndex - 1) is NOT the winner
            if (sequence[targetIndex - 1].id === winner.id) {
                for (let i = 0; i < sequence.length; i++) {
                    if (i < targetIndex - 1 || i > targetIndex + 1) {
                        if (sequence[i].id !== winner.id) {
                            [sequence[targetIndex - 1], sequence[i]] = [sequence[i], sequence[targetIndex - 1]];
                            break;
                        }
                    }
                }
            }

            // 5. Guarantee bottom neighbor (targetIndex + 1) is NOT winner AND distinct from top neighbor (if n >= 3)
            const topId = sequence[targetIndex - 1].id;
            if (sequence[targetIndex + 1].id === winner.id || (n >= 3 && sequence[targetIndex + 1].id === topId)) {
                for (let i = 0; i < sequence.length; i++) {
                    if (i < targetIndex - 1 || i > targetIndex + 1) {
                        if (sequence[i].id !== winner.id && (n < 3 || sequence[i].id !== topId)) {
                            [sequence[targetIndex + 1], sequence[i]] = [sequence[i], sequence[targetIndex + 1]];
                            break;
                        }
                    }
                }
            }

            // 6. Guarantee targetIndex - 2 is not identical to targetIndex - 1
            if (targetIndex >= 2 && sequence[targetIndex - 2].id === sequence[targetIndex - 1].id) {
                for (let i = 0; i < targetIndex - 2; i++) {
                    if (sequence[i].id !== sequence[targetIndex - 1].id) {
                        [sequence[targetIndex - 2], sequence[i]] = [sequence[i], sequence[targetIndex - 2]];
                        break;
                    }
                }
            }

            // 7. Guarantee targetIndex + 2 is not identical to targetIndex + 1
            if (targetIndex + 2 < sequence.length && sequence[targetIndex + 2].id === sequence[targetIndex + 1].id) {
                for (let i = targetIndex + 3; i < sequence.length; i++) {
                    if (sequence[i].id !== sequence[targetIndex + 1].id) {
                        [sequence[targetIndex + 2], sequence[i]] = [sequence[i], sequence[targetIndex + 2]];
                        break;
                    }
                }
            }

            // 8. Final safety passes protecting the payline zone [targetIndex - 1 .. targetIndex + 1]
            const paylineProtected = new Set([targetIndex - 1, targetIndex, targetIndex + 1]);
            for (let pass = 0; pass < 5; pass++) {
                for (let i = 1; i < sequence.length; i++) {
                    if (paylineProtected.has(i)) continue;
                    if (sequence[i].id === sequence[i - 1].id) {
                        for (let j = 0; j < sequence.length; j++) {
                            if (paylineProtected.has(j)) continue;
                            const prevJ = j > 0 ? sequence[j - 1].id : null;
                            const nextJ = j < sequence.length - 1 ? sequence[j + 1].id : null;
                            const prevI = sequence[i - 1].id;
                            const nextI = i < sequence.length - 1 ? sequence[i + 1].id : null;

                            if (sequence[j].id !== prevI && (nextI === null || sequence[j].id !== nextI) &&
                                sequence[i].id !== prevJ && (nextJ === null || sequence[i].id !== nextJ)) {
                                [sequence[i], sequence[j]] = [sequence[j], sequence[i]];
                                break;
                            }
                        }
                    }
                }
            }

            return sequence;
        }

        // Weighted Random Picker:
        // Gives candidates with type other than Staff (Non Staff, Fortuna, KMJ, etc.)
        // a higher probability of winning (weight 2x vs 1x for Staff).
        function pickWeightedWinner(candidates) {
            if (!candidates || candidates.length === 0) return null;
            let totalWeight = 0;
            for (let i = 0; i < candidates.length; i++) {
                totalWeight += Math.max(1, Number(candidates[i].weight) || 1);
            }
            let rand = Math.random() * totalWeight;
            for (let i = 0; i < candidates.length; i++) {
                rand -= Math.max(1, Number(candidates[i].weight) || 1);
                if (rand <= 0) {
                    return candidates[i];
                }
            }
            return candidates[candidates.length - 1];
        }

        // Staggered Targets for 3-Reel Slot Machine (Extra Dramatic Suspense Timing)
        // Stop Order: Reel 3 (Dept) ➔ Reel 1 (Initial) ➔ Reel 2 (Name & NIK)
        const TARGET_INDEX_REEL3 = 28; // Stops first (5.0s)
        const TARGET_INDEX_REEL1 = 40; // Stops second (7.8s)
        const TARGET_INDEX_REEL2 = 54; // Stops last (11.5s - Ultimate Suspense Crawl!)

        function spinSlotMachine() {
            if (isSpinning) return;
            const tierData = window.qccRewardCandidates[currentSpinTier] || {};
            const quota = tierData.quota_winners || 1;
            const tierWinners = getTierWinners(currentSpinTier);

            if (tierWinners.length >= quota) {
                alert(
                    `Seluruh kuota (${quota} pemenang) untuk ${tierData.name || currentSpinTier} sudah lengkap terpilih!`
                );
                return;
            }

            if (!currentCandidates || currentCandidates.length === 0) {
                alert('Tidak ada sisa kandidat yang memenuhi syarat untuk diundi!');
                return;
            }

            isSpinning = true;

            // Trigger Arcade Lever Pull Animation
            const $leverKnob = $('#slotLeverKnob');
            const $leverShaft = $('#slotLeverShaft');
            $leverKnob.css('transform', 'translateY(36px) scale(0.92)');
            $leverShaft.css('transform', 'scaleY(0.5) translateY(12px)');
            setTimeout(() => {
                $leverKnob.css('transform', '');
                $leverShaft.css('transform', '');
            }, 350);

            // Disable controls during spin
            $('#centerSpinBtn, #slotLeverKnob').prop('disabled', true).addClass(
                'opacity-50 cursor-not-allowed pointer-events-none');
            $('#slotPaylineFrame').removeClass('anim-payline-win');

            // Pick winning candidate with Weighted Randomness (Non-Staff / Fortuna / KMJ have higher chances)
            pendingWinner = pickWeightedWinner(currentCandidates);

            // 1. Build Reel 3 Strip (Dept - Stops at TARGET_INDEX_REEL3 with smart non-consecutive shuffle)
            const seq3 = generateReelSequence(currentCandidates, pendingWinner, TARGET_INDEX_REEL3, TARGET_INDEX_REEL3 + 4);
            let html3 = '';
            for (let i = 0; i < seq3.length; i++) {
                html3 += buildReel3Item(seq3[i], false);
            }
            const $strip3 = $('#slotReelStrip3');
            $strip3.html(html3).addClass('slot-blur');

            // 2. Build Reel 1 Strip (Inisial - Stops at TARGET_INDEX_REEL1 with smart non-consecutive shuffle)
            const seq1 = generateReelSequence(currentCandidates, pendingWinner, TARGET_INDEX_REEL1, TARGET_INDEX_REEL1 + 4);
            let html1 = '';
            for (let i = 0; i < seq1.length; i++) {
                html1 += buildReel1Item(seq1[i], false);
            }
            const $strip1 = $('#slotReelStrip1');
            $strip1.html(html1).addClass('slot-blur');

            // 3. Build Reel 2 Strip (Nama & NIK - Stops at TARGET_INDEX_REEL2 with smart non-consecutive shuffle)
            const seq2 = generateReelSequence(currentCandidates, pendingWinner, TARGET_INDEX_REEL2, TARGET_INDEX_REEL2 + 4);
            let html2 = '';
            for (let i = 0; i < seq2.length; i++) {
                html2 += buildReel2Item(seq2[i], false);
            }
            const $strip2 = $('#slotReelStrip2');
            $strip2.html(html2).addClass('slot-blur');

            // Physics calculation for each reel:
            const targetY3 = SLOT_CENTER_OFFSET - (TARGET_INDEX_REEL3 * SLOT_ITEM_HEIGHT);
            const targetY1 = SLOT_CENTER_OFFSET - (TARGET_INDEX_REEL1 * SLOT_ITEM_HEIGHT);
            const targetY2 = SLOT_CENTER_OFFSET - (TARGET_INDEX_REEL2 * SLOT_ITEM_HEIGHT);

            // Extended Dramatic Staggered Timings (Order: 3 ➔ 1 ➔ 2):
            const duration3 = 5000;  // Reel 3 (Dept) stops at 5.0s (plenty of time to build excitement)
            const duration1 = 7800;  // Reel 1 (Inisial) stops at 7.8s (2.8s after Dept)
            const duration2 = 11500; // Reel 2 (Nama & NIK) stops at 11.5s (3.7s long suspense crawl!)

            const startTime = performance.now();
            let reel3Done = false;
            let reel1Done = false;
            let reel2Done = false;
            let lastItemPassed2 = -1;

            function animate3Reels(currentTime) {
                const elapsed = currentTime - startTime;

                // --- REEL 3 (DEPT - STOPS FIRST at 5.0s) ---
                if (!reel3Done) {
                    const p3 = Math.min(1, elapsed / duration3);
                    const ease3 = 1 - Math.pow(1 - p3, 2.5);
                    const currentY3 = targetY3 * ease3;
                    $strip3.css('transform', `translateY(${currentY3}px)`);

                    if (p3 > 0.42) {
                        $strip3.removeClass('slot-blur');
                    }

                    if (p3 >= 1) {
                        reel3Done = true;
                        $strip3.css('transform', `translateY(${targetY3}px)`);
                        $strip3.removeClass('slot-blur');
                        // Highlight winning Dept cell
                        $strip3.children().eq(TARGET_INDEX_REEL3).addClass('anim-winner-card');
                        GameAudio.playTick();
                    }
                }

                // --- REEL 1 (INISIAL - STOPS SECOND at 7.8s) ---
                if (!reel1Done) {
                    const p1 = Math.min(1, elapsed / duration1);
                    const ease1 = 1 - Math.pow(1 - p1, 2.4);
                    const currentY1 = targetY1 * ease1;
                    $strip1.css('transform', `translateY(${currentY1}px)`);

                    if (p1 > 0.40) {
                        $strip1.removeClass('slot-blur');
                    }

                    if (p1 >= 1) {
                        reel1Done = true;
                        $strip1.css('transform', `translateY(${targetY1}px)`);
                        $strip1.removeClass('slot-blur');
                        // Highlight winning Initial cell
                        $strip1.children().eq(TARGET_INDEX_REEL1).addClass('anim-winner-card');
                        GameAudio.playTick();
                    }
                }

                // --- REEL 2 (NAMA & NIK - STOPS LAST at 11.5s, THE GRAND SUSPENSE CRAWL) ---
                if (!reel2Done) {
                    const p2 = Math.min(1, elapsed / duration2);
                    // Ultra-smooth custom suspense deceleration with long crawling tail
                    const inv = 1 - p2;
                    const ease2 = 1 - (Math.pow(inv, 1.7) * (1 - 0.25 * p2));
                    const currentY2 = targetY2 * ease2;
                    $strip2.css('transform', `translateY(${currentY2}px)`);

                    // Sound sync ticker based on Reel 2 card transitions
                    const currentPassedItem2 = Math.floor((-currentY2 + SLOT_CENTER_OFFSET + 50) / SLOT_ITEM_HEIGHT);
                    if (currentPassedItem2 !== lastItemPassed2) {
                        lastItemPassed2 = currentPassedItem2;
                        GameAudio.playTick();
                    }

                    // Remove motion blur at 36% time mark (~4.1s) so names are clearly readable rolling and slowing down
                    if (p2 > 0.36) {
                        $strip2.removeClass('slot-blur');
                    }

                    if (p2 >= 1) {
                        reel2Done = true;
                        $strip2.css('transform', `translateY(${targetY2}px)`);
                        $strip2.removeClass('slot-blur');
                        // Highlight winning Name cell
                        $strip2.children().eq(TARGET_INDEX_REEL2).addClass('anim-winner-card');
                    }
                }

                if (!reel2Done) {
                    requestAnimationFrame(animate3Reels);
                } else {
                    isSpinning = false;
                    $('#centerSpinBtn, #slotLeverKnob').prop('disabled', false).removeClass(
                        'opacity-50 cursor-not-allowed pointer-events-none');
                    onAllReelsStopped(pendingWinner);
                }
            }

            requestAnimationFrame(animate3Reels);
        }

        // Aliases for compatibility
        window.spinTheWheel = spinSlotMachine;
        window.drawWheel = renderSlotInitialView;

        function onAllReelsStopped(winner) {
            // Flash whole winning payline across all 3 reels!
            $('#slotPaylineFrame').addClass('anim-payline-win');

            GameAudio.playFanfare();

            try {
                if (typeof confetti === 'function') {
                    confetti({
                        particleCount: 180,
                        spread: 130,
                        origin: {
                            y: 0.5
                        },
                        zIndex: 99999999,
                        useWorker: false,
                        disableForReducedMotion: true
                    });
                }
            } catch (e) {}

            const tierInfo = window.qccRewardCandidates[currentSpinTier] || {
                title: 'REWARD'
            };
            const tierWinners = getTierWinners(currentSpinTier);
            const quota = tierInfo.quota_winners || 1;
            const nextOrder = tierWinners.length + 1;

            if (quota > 1) {
                $('#winModalTierTag').text(
                    `PEMENANG #${nextOrder} DARI ${quota} • ${(tierInfo.name || 'REWARD').toUpperCase()}`);
            } else {
                $('#winModalTierTag').text('PEMENANG ' + (tierInfo.name || 'REWARD').toUpperCase());
            }

            $('#winModalName').text(winner.name);
            $('#winModalMeta').html(`${escapeHtml(winner.department || 'Umum')} &bull; NIK: ${escapeHtml(winner.nik || '-')}`);
            $('#winModalScore').text(winner.total_score + ' Poin');

            const winInitial = getParticipantInitial(winner.name);
            const winGradient = getParticipantGradient(winner.name);
            $('#winModalAvatar').html(`
                <div class="w-full h-full rounded-full bg-gradient-to-br ${winGradient} text-white font-black text-4xl flex items-center justify-center shadow-inner uppercase border-2 border-white/50">
                    ${winInitial}
                </div>
            `);

            // Reveal modal popup after a longer dramatic pause (1200ms) so audience can celebrate and read the winner on screen
            setTimeout(() => {
                $('#winnerCelebrationOverlay').removeClass('hidden');
            }, 1200);
        }

        function confirmWinner() {
            if (pendingWinner) {
                saveWinner(currentSpinTier, pendingWinner);
                const tierWinners = getTierWinners(currentSpinTier);
                const tierData = window.qccRewardCandidates[currentSpinTier] || {};
                const quota = tierData.quota_winners || 1;

                selectSpinTier(currentSpinTier);

                if (tierWinners.length < quota) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: `Pemenang #${tierWinners.length} Sah Disimpan!`,
                            text: `${pendingWinner.name} berhasil tersimpan. Silakan putar lagi untuk pemenang ke-${tierWinners.length + 1} dari ${quota}!`,
                            timer: 2800,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: `Lengkap! Seluruh ${quota} Pemenang Terpilih!`,
                            text: `Selamat kepada seluruh ${quota} pemenang undian ${tierData.name || currentSpinTier}!`,
                            confirmButtonColor: '#f59e0b'
                        });
                    }
                }
            }
            $('#winnerCelebrationOverlay').addClass('hidden');
        }

        function spinAgain() {
            $('#winnerCelebrationOverlay').addClass('hidden');
            setTimeout(spinSlotMachine, 300);
        }

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Initialize on load
        $(document).ready(function() {
            selectSpinTier(currentSpinTier);
        });
    </script>
@endsection
