@extends('pool_qcc.layout')

@section('title', 'Leaderboard Exhibition QCC 2026')

@section('hide_nav', true)

@section('styles')
    <style>
        /* ========================================================
                                                                                   CASUAL 3D MOBILE GAME THEME (Royal Match / Arcade Style)
                                                                                   ======================================================== */
        @keyframes floatSlow {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-7px) rotate(1deg);
            }
        }

        @keyframes pulseCoin {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.08);
                filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.6));
            }
        }

        @keyframes crownShine {

            0%,
            100% {
                transform: rotate(-5deg) scale(1);
            }

            50% {
                transform: rotate(5deg) scale(1.12);
            }
        }

        .anim-game-float {
            animation: floatSlow 3s ease-in-out infinite;
        }

        .anim-coin {
            animation: pulseCoin 2s ease-in-out infinite;
        }

        .anim-crown {
            animation: crownShine 2.2s ease-in-out infinite;
        }

        /* 3D Hanging Game Signboard Header */
        .game-signboard {
            background: linear-gradient(180deg, #581c87 0%, #3b0764 60%, #2e0854 100%);
            border: 3.5px solid #fbbf24;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.4), 0 3px 0 #b45309, 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        /* 3D Game Buttons & Resource Badges */
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

        /* 3D Cartoon Layout Cards - No translateY on hover to avoid collision with header */
        .game-card-3d {
            box-shadow: 0 6px 0 rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.15s ease;
        }

        .game-card-3d:hover {
            box-shadow: 0 8px 0 rgba(0, 0, 0, 0.15), 0 12px 22px rgba(0, 0, 0, 0.1);
        }

        /* 3D Cartoon Pedestals */
        .pedestal-3d-gold {
            background: linear-gradient(180deg, #fef3c7 0%, #fde68a 35%, #f59e0b 100%);
            border: 4px solid #fef08a;
            box-shadow: inset 0 3px 6px #ffffff, 0 9px 0 #b45309, 0 16px 24px rgba(180, 83, 9, 0.35);
        }

        .pedestal-3d-silver {
            background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 40%, #94a3b8 100%);
            border: 4px solid #ffffff;
            box-shadow: inset 0 3px 6px #ffffff, 0 8px 0 #475569, 0 14px 20px rgba(71, 85, 105, 0.25);
        }

        .pedestal-3d-bronze {
            background: linear-gradient(180deg, #ffedd5 0%, #fed7aa 40%, #ea580c 100%);
            border: 4px solid #fff7ed;
            box-shadow: inset 0 3px 6px #ffffff, 0 8px 0 #9a3412, 0 14px 20px rgba(154, 52, 18, 0.25);
        }

        /* 3D Shield Medals */
        .shield-3d-1 {
            background: radial-gradient(circle at 35% 30%, #fef08a, #f59e0b 65%, #b45309);
            border: 3.5px solid #fffbeb;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.9), 0 4px 0 #92400e, 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .shield-3d-2 {
            background: radial-gradient(circle at 35% 30%, #ffffff, #cbd5e1 65%, #64748b);
            border: 3.5px solid #f8fafc;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.9), 0 4px 0 #334155, 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .shield-3d-3 {
            background: radial-gradient(circle at 35% 30%, #fed7aa, #fb923c 65%, #c2410c);
            border: 3.5px solid #fff7ed;
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.9), 0 4px 0 #9a3412, 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        /* Cartoon Clouds & Sky Texture for Light Mode */
        .game-sky-bg {
            background: radial-gradient(circle at 50% 10%, #60a5fa 0%, #38bdf8 40%, #e0f2fe 100%);
        }

        .dark .game-sky-bg {
            background: radial-gradient(circle at 50% 10%, #1e1b4b 0%, #0f172a 50%, #020617 100%);
        }

        /* ======================================================== */
        /* RACE TAKEOVER / MENYALIP ANIMATION STYLES                */
        /* ======================================================== */
        @keyframes flameAura {
            0%, 100% {
                box-shadow: 0 0 25px 6px rgba(245, 158, 11, 0.85), 0 0 50px 12px rgba(239, 68, 68, 0.4), 0 10px 30px rgba(0, 0, 0, 0.5);
                border-color: #fbbf24;
            }
            50% {
                box-shadow: 0 0 40px 10px rgba(251, 191, 36, 1), 0 0 75px 20px rgba(249, 115, 22, 0.65), 0 12px 35px rgba(0, 0, 0, 0.6);
                border-color: #f97316;
            }
        }

        .takeover-overtaker-active {
            animation: flameAura 0.35s ease-in-out infinite alternate !important;
            border: 2.5px solid #fbbf24 !important;
            background: linear-gradient(135deg, rgba(254, 243, 199, 0.98), rgba(255, 237, 213, 0.95)) !important;
            z-index: 100 !important;
            position: relative !important;
            filter: drop-shadow(0 6px 16px rgba(245, 158, 11, 0.5));
        }

        .dark .takeover-overtaker-active {
            background: linear-gradient(135deg, rgba(69, 26, 3, 0.95), rgba(41, 37, 36, 0.98)) !important;
        }

        .takeover-overtaken-active {
            opacity: 0.6 !important;
            filter: grayscale(45%) contrast(0.9) !important;
            z-index: 20 !important;
            position: relative !important;
            transform-origin: center center;
            transition: transform 0.28s ease, opacity 0.28s ease, filter 0.28s ease !important;
        }

        /* Screen Shake effect on takeover dock */
        @keyframes screenRumble {
            0% { transform: translate(0, 0); }
            20% { transform: translate(-3px, 2px); }
            40% { transform: translate(3px, -2px); }
            60% { transform: translate(-2px, -1px); }
            80% { transform: translate(2px, 1px); }
            100% { transform: translate(0, 0); }
        }

        .anim-screen-shake {
            animation: screenRumble 0.35s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        /* Rank Swap Pop Animation */
        @keyframes rankSwapPop {
            0% {
                transform: scale(0.6) rotate(-15deg);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.4) rotate(8deg);
                background-color: #f59e0b;
                color: #000;
            }
            75% {
                transform: scale(0.95) rotate(-3deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .anim-rank-swap {
            animation: rankSwapPop 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) forwards !important;
        }

        @keyframes nitro-pulse {

            0%,
            100% {
                transform: scale(1);
                filter: drop-shadow(0 0 4px #f59e0b);
            }

            50% {
                transform: scale(1.08);
                filter: drop-shadow(0 0 10px #ea580c);
            }
        }

        .anim-nitro-pulse {
            animation: nitro-pulse 0.4s ease-in-out infinite;
        }

        @keyframes takeover-banner-slide {
            0% {
                opacity: 0;
                transform: translateY(-50px) scale(0.85);
            }

            60% {
                opacity: 1;
                transform: translateY(6px) scale(1.02);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .anim-takeover-banner {
            animation: takeover-banner-slide 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
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

        /* Infinite Vertical Auto-Scroll Containers */
        .infinite-scroll-container {
            overflow: hidden !important;
            scroll-behavior: auto !important;
        }

        .infinite-scroll-container::-webkit-scrollbar {
            display: none;
        }

        .infinite-scroll-content {
            will-change: transform;
            transform: translate3d(0, 0, 0);
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        /* Soft edge fade/blur mask for top & bottom boundaries during infinite scroll */
        .infinite-scroll-mask {
            -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 14px, black calc(100% - 14px), transparent 100%);
            mask-image: linear-gradient(to bottom, transparent 0%, black 14px, black calc(100% - 14px), transparent 100%);
        }

        /* Hover unmask: when user hovers over list, show all cards sharply without clipping */
        .infinite-scroll-container:hover {
            -webkit-mask-image: none !important;
            mask-image: none !important;
        }
    </style>
@endsection

@section('content')
    <div
        class="min-h-screen lg:h-screen lg:max-h-screen w-full px-2.5 sm:px-4 lg:px-6 pt-1 pb-2 flex flex-col justify-start relative overflow-y-auto lg:overflow-hidden game-sky-bg text-slate-900 dark:text-slate-100 transition-colors font-game">

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

        <!-- ======================================================== -->
        <!-- TOP 3D TOURNAMENT SIGNBOARD & GAME HUB BAR (SEJAJAR)      -->
        <!-- ======================================================== -->
        <div
            class="relative z-20 w-full flex items-center justify-between gap-2 sm:gap-4 mb-3 sm:mb-3.5 px-1 sm:px-2 flex-shrink-0">

            <!-- KIRI TITLE: Lucky Spin Wheel & Tes Takeover -->
            <div class="flex items-center space-x-2 flex-1 justify-start">
                <!-- Lucky Spin Wheel Direct Link -->
                <a href="{{ route('pool-qcc.spin-wheel') }}"
                    class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-gradient-to-r from-amber-400 via-yellow-300 to-amber-500 hover:from-amber-300 hover:to-yellow-200 text-slate-950 font-black text-xs border-2 border-amber-600 game-btn-3d flex items-center space-x-1.5 cursor-pointer shadow-md"
                    title="Buka Undian Roda Keberuntungan (Spin Wheel)">
                    <i class="fa-solid fa-dharmachakra text-amber-900 animate-spin-slow"></i>
                    <span class="hidden sm:inline text-[11px]">Lucky Spin Wheel</span>
                </a>

                <!-- Simulasi Tes Takeover -->
                <button type="button" onclick="testTakeoverSimulation()"
                    class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-400 hover:to-pink-400 text-white font-black text-xs border-2 border-rose-700 game-btn-3d flex items-center space-x-1.5 cursor-pointer shadow-md"
                    title="Simulasi Efek Takeover Peringkat">
                    <i class="fa-solid fa-bolt text-yellow-300"></i>
                    <span class="hidden sm:inline text-[11px]">Tes Takeover</span>
                </button>
            </div>

            <!-- TENGAH: SIGNBOARD JUDUR UTAMA -->
            <div class="flex flex-col items-center flex-shrink-0">
                <!-- Hanging Chain Rings from Ceiling -->
                <div class="flex justify-between w-36 max-w-full px-4 -mb-1 z-10 pointer-events-none">
                    <div
                        class="w-2 h-3.5 rounded-full border-2 border-amber-600 bg-gradient-to-b from-amber-300 to-amber-500 shadow-xs">
                    </div>
                    <div
                        class="w-2 h-3.5 rounded-full border-2 border-amber-600 bg-gradient-to-b from-amber-300 to-amber-500 shadow-xs">
                    </div>
                </div>

                <!-- Main Wooden/Purple Signboard -->
                <div
                    class="game-signboard relative px-3 sm:px-6 py-0.5 sm:py-1 rounded-2xl text-center flex flex-col items-center max-w-xs sm:max-w-md">
                    <!-- Info Subtitle Ribbon -->
                    <div
                        class="px-2.5 py-0.5 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300 text-slate-950 font-black text-[8.5px] sm:text-[9px] tracking-wider uppercase border border-amber-500 shadow-xs flex items-center space-x-1 mb-0.5 leading-none">
                        <span>👑</span>
                        <span>QCC/QCP PT BUMI ALAM SEGAR 2026</span>
                        <span>👑</span>
                    </div>

                    <!-- Grand Title -->
                    <h1
                        class="text-sm sm:text-base lg:text-lg font-black text-white tracking-tight drop-shadow-[0_3px_2px_rgba(0,0,0,0.85)] font-heading leading-tight">
                        EXHIBITION QCC/QCP
                    </h1>
                </div>
            </div>

            <!-- KANAN TITLE: Theme, Fullscreen, Portal -->
            <div class="flex items-center space-x-2 flex-1 justify-end">
                <!-- Theme Toggle -->
                <button type="button" onclick="toggleTheme()"
                    class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs border-2 border-amber-600 game-btn-3d flex items-center space-x-1.5 cursor-pointer shadow-md"
                    title="Ganti Tema">
                    <span class="theme-icon-sun hidden text-sm"><i class="fa-solid fa-sun text-amber-600"></i></span>
                    <span class="theme-icon-moon text-sm"><i class="fa-solid fa-moon text-slate-900"></i></span>
                    <span class="theme-label hidden sm:inline text-[11px]">Tema</span>
                </button>

                <!-- Fullscreen -->
                <button onclick="toggleFullScreen()"
                    class="py-1 px-3 rounded-2xl bg-white hover:bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white border-2 border-slate-300 dark:border-slate-700 game-btn-3d cursor-pointer shadow-md"
                    title="Layar Penuh">
                    <i class="fa-solid fa-expand text-xs"></i>
                </button>

                <!-- Portal -->
                <a href="{{ route('pool-qcc.portal-home') }}"
                    class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs border-2 border-indigo-800 game-btn-3d flex items-center space-x-1.5 shadow-md"
                    title="Kembali ke Portal BAS">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span class="hidden sm:inline text-[11px]">Portal</span>
                </a>
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- TAKEOVER HUD ALERT BANNER CONTAINER                      -->
        <!-- ======================================================== -->
        <div id="takeoverHUD"
            class="fixed top-4 left-1/2 -translate-x-1/2 z-[999999] flex flex-col items-center gap-2 pointer-events-none w-full max-w-md sm:max-w-lg px-4">
        </div>

        <!-- ======================================================== -->
        <!-- MAIN TWO-COLUMN ARENA (AJAX TARGET CONTAINER)            -->
        <!-- ======================================================== -->
        <div id="arenaContainer" class="flex-1 min-h-0 flex flex-col relative z-10 overflow-hidden">
            @include('pool_qcc.booth.leaderboard_arena', [
                'booths' => $booths,
                'participants' => $participants,
                'rewardTiers' => $rewardTiers,
                'otherParticipants' => $otherParticipants,
            ])
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Safe confetti helper preventing worker & resize getBoundingClientRect exceptions
        function triggerConfetti(options) {
            try {
                if (typeof confetti === 'function') {
                    confetti(Object.assign({
                        useWorker: false,
                        disableForReducedMotion: true,
                        zIndex: 99999
                    }, options || {}));
                }
            } catch (err) {
                console.warn('Confetti error suppressed:', err);
            }
        }

        // Confetti kecil berkala di background
        function randomFunCelebration() {
            if (isAnimatingTakeover) return;
            triggerConfetti({
                particleCount: 22,
                angle: 60,
                spread: 50,
                origin: {
                    x: 0,
                    y: 0.8
                },
                colors: ['#f59e0b', '#06b6d4', '#10b981', '#ec4899', '#8b5cf6']
            });
            triggerConfetti({
                particleCount: 22,
                angle: 120,
                spread: 50,
                origin: {
                    x: 1,
                    y: 0.8
                },
                colors: ['#f59e0b', '#06b6d4', '#10b981', '#ec4899', '#8b5cf6']
            });
        }

        setTimeout(randomFunCelebration, 1200);
        setInterval(randomFunCelebration, 8500);

        // ========================================================
        // WEB AUDIO API ARCADE SYNTHESIZER
        // ========================================================
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
            playNitroSwoosh() {
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(110, now);
                    osc.frequency.exponentialRampToValueAtTime(750, now + 0.38);
                    gain.gain.setValueAtTime(0.01, now);
                    gain.gain.linearRampToValueAtTime(0.28, now + 0.1);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.42);
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.45);
                } catch (e) {}
            },
            playImpactThud() {
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(160, now);
                    osc.frequency.exponentialRampToValueAtTime(32, now + 0.16);
                    gain.gain.setValueAtTime(0.45, now);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + 0.16);
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.17);
                } catch (e) {}
            },
            playTakeoverChime() {
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;

                    // Arcade Victory Fanfare: C5 -> E5 -> G5 -> C6
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
                            dur: 0.45
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

        // ========================================================
        // ASYNC JQUERY AJAX RELOAD DATA (WITH TAKEOVER ANIMATION)
        // ========================================================
        let countdown = 5;
        let isSyncing = false;
        let isFirstLoad = true;
        let isAnimatingTakeover = false;
        var isSpinning = false; // Fallback definition to avoid ReferenceError
        let currentArenaHtml = '';
        const previousBoothRanks = {};
        const previousParticipantRanks = {};

        const timerEl = document.getElementById('refreshTimer');
        const syncIndicator = document.getElementById('syncIndicator');

        // Initial candidates cache
        window.qccRewardCandidates = {!! json_encode($rewardCandidates ?? []) !!};

        function cacheInitialRanks() {
            $('[data-booth-id]').each(function() {
                const id = $(this).data('booth-id');
                const rank = parseInt($(this).data('booth-rank'));
                if (id && rank) previousBoothRanks[id] = rank;
            });
            $('[data-participant-id]').each(function() {
                const id = $(this).data('participant-id');
                const rank = parseInt($(this).data('participant-rank'));
                if (id && rank) previousParticipantRanks[id] = rank;
            });
        }

        $(document).ready(function() {
            cacheInitialRanks();
            currentArenaHtml = $('#arenaContainer').html() || '';
            // Start infinite scroll once layout is ready, with an initial 1.2s stationary hold so items don't jerk
            setTimeout(function() {
                initInfiniteScroll(1200);
            }, 300);
        });

        function reloadLeaderboardData() {
            if (isSyncing || isAnimatingTakeover) return;
            isSyncing = true;
            if (syncIndicator) syncIndicator.classList.remove('hidden');

            $.ajax({
                url: '{{ route('pool-qcc.leaderboard') }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response && response.status === 'success') {
                        processLeaderboardUpdate(response);
                    }
                },
                error: function(xhr, status, error) {
                    console.warn('Leaderboard AJAX reload warning:', error);
                },
                complete: function() {
                    isSyncing = false;
                    if (syncIndicator) syncIndicator.classList.add('hidden');
                    countdown = 5;
                    if (timerEl) timerEl.textContent = countdown + 's';
                }
            });
        }

        function processLeaderboardUpdate(response) {
            // Update reward candidates cache
            if (response.reward_candidates) {
                window.qccRewardCandidates = response.reward_candidates;
            }

            // 1. Detect Takeovers
            let primaryOvertaker = null;
            const takeovers = [];

            if (response.participant_ranks && !isFirstLoad) {
                response.participant_ranks.forEach(p => {
                    const oldRank = previousParticipantRanks[p.id];
                    if (oldRank && p.rank < oldRank) {
                        takeovers.push({
                            type: 'participant',
                            id: p.id,
                            name: p.name,
                            oldRank: oldRank,
                            newRank: p.rank,
                            delta: oldRank - p.rank
                        });
                        if (!primaryOvertaker) {
                            primaryOvertaker = p;
                        }
                    }
                });
            }

            if (response.booth_ranks && !isFirstLoad) {
                response.booth_ranks.forEach(b => {
                    const oldRank = previousBoothRanks[b.id];
                    if (oldRank && b.rank < oldRank) {
                        takeovers.push({
                            type: 'booth',
                            id: b.id,
                            name: b.name,
                            oldRank: oldRank,
                            newRank: b.rank,
                            delta: oldRank - b.rank
                        });
                    }
                });
            }

            // 2. Physical overtake animation if applicable
            let animOvertakerEl = null;
            let animOvertakenEl = null;

            if (primaryOvertaker) {
                animOvertakerEl = document.querySelector(`[data-participant-id="${primaryOvertaker.id}"]`);
                animOvertakenEl = document.querySelector(`[data-participant-rank="${primaryOvertaker.rank}"]`);
            } else if (takeovers.length > 0) {
                const boothTakeover = takeovers.find(t => t.type === 'booth');
                if (boothTakeover) {
                    animOvertakerEl = document.querySelector(`[data-booth-id="${boothTakeover.id}"]`);
                    animOvertakenEl = document.querySelector(`[data-booth-rank="${boothTakeover.newRank}"]`);
                }
            }

            if (animOvertakerEl && animOvertakenEl && animOvertakerEl !== animOvertakenEl) {
                isAnimatingTakeover = true;
                animateParticipantOvertake(animOvertakerEl, animOvertakenEl, function() {
                    commitNewArenaHTML(response, takeovers);
                    isAnimatingTakeover = false;
                });
                return;
            }

            // SMOOTH LIVE SYNC: If HTML and ranks have not changed, DO NOT wipe DOM!
            // This prevents resetting scroll and allows the infinite animation to glide uninterrupted!
            if (!isFirstLoad && currentArenaHtml && response.arena_html && response.arena_html.trim() === currentArenaHtml
                .trim()) {
                if (response.total_votes !== undefined) {
                    $('#statTotalVotes').text(response.total_votes);
                }
                if (response.total_participants !== undefined) {
                    $('#statTotalParticipants').text(response.total_participants);
                }
                return;
            }

            commitNewArenaHTML(response, takeovers);
        }

        function commitNewArenaHTML(response, takeovers) {
            // Snapshot current scroll positions by persistent string key so re-renders NEVER reset to 0
            const savedPositions = new Map();
            infiniteScrollTracks.forEach(t => {
                if (t.key && typeof t.pos === 'number') {
                    savedPositions.set(t.key, t.pos);
                }
            });

            $('#arenaContainer').html(response.arena_html);
            currentArenaHtml = response.arena_html;

            if (response.participant_ranks) {
                response.participant_ranks.forEach(p => {
                    previousParticipantRanks[p.id] = p.rank;
                });
            }
            if (response.booth_ranks) {
                response.booth_ranks.forEach(b => {
                    previousBoothRanks[b.id] = b.rank;
                });
            }
            isFirstLoad = false;

            if (response.total_votes !== undefined) {
                $('#statTotalVotes').text(response.total_votes);
            }
            if (response.total_participants !== undefined) {
                $('#statTotalParticipants').text(response.total_participants);
            }

            if (takeovers.length > 0) {
                const topTakeover = takeovers.sort((a, b) => a.newRank - b.newRank)[0];
                showTakeoverHUD(topTakeover);
            }

            // Immediately restore scroll positions with 0 initial delay so animation never stutters
            initInfiniteScroll(0, savedPositions);
        }

        // ========================================================
        // PHYSICAL OVERTAKE SLIDING ANIMATION
        // ========================================================
        function animateParticipantOvertake(overtakerEl, overtakenEl, onComplete) {
            const $overtaker = $(overtakerEl);
            const $overtaken = $(overtakenEl);

            if (!$overtaker.length || !$overtaken.length) {
                if (onComplete) onComplete();
                return;
            }

            const rawOvertaker = $overtaker[0];
            const rawOvertaken = $overtaken[0];

            if (!rawOvertaker || !rawOvertaken || typeof rawOvertaker.getBoundingClientRect !== 'function' ||
                typeof rawOvertaken.getBoundingClientRect !== 'function') {
                if (onComplete) onComplete();
                return;
            }

            const rectOvertaker = rawOvertaker.getBoundingClientRect();
            const rectOvertaken = rawOvertaken.getBoundingClientRect();
            const dy = rectOvertaker.top - rectOvertaken.top;

            if (Math.abs(dy) < 5) {
                if (onComplete) onComplete();
                return;
            }

            // Synthesize powerful revving audio
            GameAudio.playNitroSwoosh();

            $overtaker.css({
                'position': 'relative',
                'z-index': '100',
                'transition': 'transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.28s ease'
            }).addClass('takeover-overtaker-active');

            $overtaken.css({
                'position': 'relative',
                'z-index': '20',
                'transition': 'transform 0.28s ease, opacity 0.28s ease, filter 0.28s ease'
            }).addClass('takeover-overtaken-active');

            // User requirement: Cukup 1 badge takeover saja di seluruh leaderboard, timpa jika berulang kali
            $('.takeover-badge-pill, .nitro-pill').remove();
            $('.takeover-slot').empty();

            const $turboBadge = $(`
                <span class="takeover-badge-pill nitro-pill anim-badge-pop inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-gradient-to-r from-amber-400 via-orange-500 to-amber-500 text-slate-950 border border-yellow-200 shadow-lg anim-nitro-pulse flex-shrink-0">
                    ⚡ MENYALIP...
                </span>
            `);
            const $slot = $overtaker.find('.takeover-slot');
            if ($slot.length) {
                $slot.html($turboBadge);
            } else {
                const $titleRow = $overtaker.find('.participant-title-row, .truncate').first();
                if ($titleRow.length) $titleRow.append($turboBadge);
            }

            // Phase 1: Pull out sideways with nitro flare
            $overtaker.css('transform', 'translate(22px, 0px) scale(1.05)');
            $overtaken.css('transform', 'translate(-12px, 0px) scale(0.96)');

            // Phase 2: High-speed surge past the rival
            setTimeout(() => {
                $overtaker.css({
                    'transition': 'transform 0.65s cubic-bezier(0.12, 0.8, 0.2, 1.25)',
                    'transform': `translate(22px, -${dy}px) scale(1.06)`
                });
                $overtaken.css({
                    'transition': 'transform 0.65s cubic-bezier(0.12, 0.8, 0.2, 1.25)',
                    'transform': `translate(-12px, ${dy}px) scale(0.96)`
                });

                // Midpoint nitro fiery sparks burst
                setTimeout(() => {
                    try {
                        const oRect = rawOvertaker.getBoundingClientRect();
                        const posX = (oRect.left + oRect.width / 2) / window.innerWidth;
                        const posY = (oRect.top + oRect.height / 2) / window.innerHeight;
                        triggerConfetti({
                            particleCount: 30,
                            spread: 55,
                            startVelocity: 18,
                            ticks: 45,
                            origin: { x: posX, y: posY },
                            colors: ['#fbbf24', '#f59e0b', '#ef4444', '#ffffff'],
                            zIndex: 999999
                        });
                    } catch (e) {}
                }, 300);

                // Phase 3: Slam & Dock into new rank position with tactile thud & screen rumble
                setTimeout(() => {
                    GameAudio.playImpactThud();
                    GameAudio.playTakeoverChime();

                    // Camera screen rumble
                    const $arena = $('#arenaContainer');
                    $arena.removeClass('anim-screen-shake');
                    if ($arena[0]) void $arena[0].offsetWidth; // trigger reflow
                    $arena.addClass('anim-screen-shake');
                    setTimeout(() => $arena.removeClass('anim-screen-shake'), 400);

                    $overtaker.css({
                        'transition': 'transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1)',
                        'transform': `translate(0px, -${dy}px) scale(1.02)`
                    });
                    $overtaken.css({
                        'transition': 'transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1)',
                        'transform': `translate(0px, ${dy}px) scale(1)`
                    });

                    // Phase 4: Finalize, Swap DOM, Swap Rank Numbers with pop animation
                    setTimeout(() => {
                        $overtaker.css({
                            'transform': '',
                            'transition': '',
                            'z-index': ''
                        }).removeClass('takeover-overtaker-active');

                        $overtaken.css({
                            'transform': '',
                            'transition': '',
                            'z-index': ''
                        }).removeClass('takeover-overtaken-active');

                        // Transform badge to celebratory TAKEOVER
                        $turboBadge.html('🔥 TAKEOVER!').removeClass('anim-nitro-pulse').addClass('bg-gradient-to-r from-yellow-300 via-amber-400 to-orange-500 border-amber-300 shadow-xl');
                        setTimeout(() => {
                            $turboBadge.fadeOut(500, function() {
                                $(this).remove();
                            });
                        }, 12000);

                        // Swap DOM elements
                        $overtaker.insertBefore($overtaken);

                        // Swap Rank Numbers with Pop Animation
                        const $overtakerRankEl = $overtaker.find('.rank-badge-box');
                        const $overtakenRankEl = $overtaken.find('.rank-badge-box');
                        if ($overtakerRankEl.length && $overtakenRankEl.length) {
                            const overtakerRankTxt = $overtakerRankEl.text().trim();
                            const overtakenRankTxt = $overtakenRankEl.text().trim();

                            $overtakerRankEl.text(overtakenRankTxt).addClass('anim-rank-swap');
                            $overtakenRankEl.text(overtakerRankTxt).addClass('anim-rank-swap');

                            setTimeout(() => {
                                $overtakerRankEl.removeClass('anim-rank-swap');
                                $overtakenRankEl.removeClass('anim-rank-swap');
                            }, 700);
                        }

                        // Swap data attributes
                        const bRank1 = $overtaker.attr('data-booth-rank');
                        const bRank2 = $overtaken.attr('data-booth-rank');
                        if (bRank1 !== undefined && bRank2 !== undefined) {
                            $overtaker.attr('data-booth-rank', bRank2);
                            $overtaken.attr('data-booth-rank', bRank1);
                        }
                        const pRank1 = $overtaker.attr('data-participant-rank');
                        const pRank2 = $overtaken.attr('data-participant-rank');
                        if (pRank1 !== undefined && pRank2 !== undefined) {
                            $overtaker.attr('data-participant-rank', pRank2);
                            $overtaken.attr('data-participant-rank', p1);
                        }

                        // Victory confetti celebration
                        triggerConfetti({
                            particleCount: 90,
                            spread: 95,
                            origin: {
                                y: 0.4
                            },
                            colors: ['#fbbf24', '#f59e0b', '#ef4444', '#10b981', '#06b6d4'],
                            zIndex: 9999999
                        });

                        if (onComplete) onComplete();
                    }, 370);
                }, 650);
            }, 280);
        }

        // ========================================================
        // TAKEOVER HUD NOTIFICATION (OVERWRITE MODE)
        // ========================================================
        let takeoverHUDTimer = null;

        function showTakeoverHUD(item) {
            const isBooth = item.type === 'booth';
            const typeLabel = isBooth ? '🎪 STAND INOVASI' : '🧠 PESERTA';
            const bgGradient = isBooth ?
                'from-amber-500 via-yellow-400 to-amber-600' :
                'from-cyan-500 via-teal-400 to-cyan-600';
            const icon = isBooth ? '🏆' : '⚡';

            if (takeoverHUDTimer) {
                clearTimeout(takeoverHUDTimer);
                takeoverHUDTimer = null;
            }

            const toast = $(`
                <div class="anim-takeover-banner pointer-events-auto flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-gradient-to-r ${bgGradient} text-slate-950 font-black shadow-[0_10px_30px_rgba(0,0,0,0.35)] border-3 border-white/90 backdrop-blur-md transition-all duration-300">
                    <div class="w-10 h-10 rounded-xl bg-white/95 text-slate-950 flex items-center justify-center text-xl shadow-inner flex-shrink-0 animate-bounce">
                        ${icon}
                    </div>
                    <div class="min-w-0 pr-2">
                        <div class="flex items-center gap-1.5 text-[10px] tracking-wider uppercase font-black">
                            <span class="bg-slate-950 text-white px-2 py-0.5 rounded-md shadow-xs">${typeLabel}</span>
                            <span class="text-slate-950 font-black">TAKEOVER! (+${item.delta} POSISI)</span>
                        </div>
                        <div class="text-xs sm:text-sm font-black truncate text-slate-950 mt-0.5">
                            <span class="underline decoration-2">${item.name}</span> merebut Peringkat #${item.newRank}!
                        </div>
                    </div>
                    <button type="button" onclick="$(this).parent().remove()" class="text-slate-950 hover:opacity-70 p-1 text-xs ml-auto cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            `);

            $('#takeoverHUD').html(toast);

            takeoverHUDTimer = setTimeout(() => {
                toast.addClass('opacity-0 -translate-y-4 scale-90');
                setTimeout(() => {
                    toast.remove();
                }, 400);
            }, 4500);
        }

        function testTakeoverSimulation() {
            if (isAnimatingTakeover) return;

            // Pause auto-refresh and give user 15s to view simulated takeover before next auto-sync
            countdown = 15;
            if (timerEl) timerEl.textContent = countdown + 's';

            // 1. Cari kolom peserta yang memiliki minimal 2 kandidat dalam 1 kolom reward
            let $cards = null;
            let isBooth = false;

            $('#participantsListWrapper .infinite-scroll-content').each(function() {
                const $c = $(this).children('[data-participant-id]:not(.infinite-clone-item)');
                if ($c.length >= 2) {
                    $cards = $c;
                    return false;
                }
            });

            // 2. Jika peserta belum ada minimal 2 dalam 1 kolom, gunakan list booth yang bertanding
            if (!$cards || $cards.length < 2) {
                const $boothCards = $('.infinite-scroll-content [data-booth-id]:not(.infinite-clone-item)');
                if ($boothCards.length >= 2) {
                    $cards = $boothCards;
                    isBooth = true;
                }
            }

            // 3. Fallback jika benar-benar tidak ada 2 kartu
            if (!$cards || $cards.length < 2) {
                showTakeoverHUD({
                    type: 'participant',
                    id: 999,
                    name: 'Peserta Bintang',
                    oldRank: 2,
                    newRank: 1,
                    delta: 1
                });
                return;
            }

            const cardUpper = $cards[0];
            const cardLower = $cards[1];
            const overtakerName = isBooth ?
                ($(cardLower).attr('data-booth-name') || $(cardLower).data('booth-name') || 'Booth Inovasi') :
                ($(cardLower).attr('data-participant-name') || $(cardLower).data('participant-name') || 'Peserta');
            const oldRank = isBooth ?
                (parseInt($(cardLower).attr('data-booth-rank') || $(cardLower).data('booth-rank')) || 5) :
                (parseInt($(cardLower).attr('data-participant-rank') || $(cardLower).data('participant-rank')) || 2);
            const newRank = isBooth ?
                (parseInt($(cardUpper).attr('data-booth-rank') || $(cardUpper).data('booth-rank')) || 4) :
                (parseInt($(cardUpper).attr('data-participant-rank') || $(cardUpper).data('participant-rank')) || 1);

            isAnimatingTakeover = true;
            animateParticipantOvertake(cardLower, cardUpper, function() {
                showTakeoverHUD({
                    type: isBooth ? 'booth' : 'participant',
                    id: isBooth ? $(cardLower).data('booth-id') : $(cardLower).data('participant-id'),
                    name: overtakerName,
                    oldRank: oldRank,
                    newRank: newRank,
                    delta: Math.max(1, oldRank - newRank)
                });
                isAnimatingTakeover = false;
            });
        }

        // ========================================================
        // SEAMLESS INFINITE VERTICAL AUTO-SCROLL ENGINE
        // (Hardware-accelerated translate3d with exact offsetTop wrapping)
        // ========================================================
        let infiniteScrollTracks = [];
        let infiniteScrollRafId = null;
        let infiniteScrollLastTime = null;

        function initInfiniteScroll(initialDelayMs = 0, explicitPositions = null) {
            if (infiniteScrollRafId) {
                cancelAnimationFrame(infiniteScrollRafId);
                infiniteScrollRafId = null;
            }

            // Preserve current scroll offsets using String Key so DOM updates NEVER reset position
            const prevOffsets = new Map();
            if (explicitPositions instanceof Map) {
                explicitPositions.forEach((val, key) => prevOffsets.set(key, val));
            } else {
                infiniteScrollTracks.forEach(t => {
                    if (t.key && typeof t.pos === 'number') {
                        prevOffsets.set(t.key, t.pos);
                    }
                });
            }

            infiniteScrollTracks = [];

            $('.infinite-scroll-container').each(function(index) {
                const container = this;
                const $container = $(container);
                const scrollKey = $container.data('scroll-key') || container.id || ('track-' + index);
                const $content = $container.find('.infinite-scroll-content');
                if (!$content.length) return;

                // Remove existing clones to accurately measure base content height
                $content.find('.infinite-clone-item').remove();
                $content[0].style.transform = 'translate3d(0, 0, 0)';

                const $cards = $content.children('[data-participant-id], [data-booth-id]');
                // If fewer than 2 cards, no need to loop
                if ($cards.length < 2) {
                    $container.removeClass('infinite-scroll-mask');
                    $container.off('mouseenter mouseleave touchstart touchend wheel');
                    return;
                }

                const baseHeight = $content[0].scrollHeight;
                const containerHeight = $container[0].clientHeight || 250;

                // User Requirement: Jika view muat untuk semua data, TIDAK PERLU auto-scroll.
                // Jika view kurang (konten melimpah/overflow dari container), barulah auto-scroll!
                if (baseHeight <= containerHeight + 2) {
                    $container.removeClass('infinite-scroll-mask');
                    $container.off('mouseenter mouseleave touchstart touchend wheel');
                    return;
                }

                // Aktifkan efek blur fade-out batas atas dan bawah saat auto scroll berjalan
                $container.addClass('infinite-scroll-mask');

                // 1. Clone original cards to create the seamless loop
                const $clone1 = $cards.clone().addClass('infinite-clone-item');
                $clone1.removeAttr('id');
                $clone1.find('[id]').removeAttr('id');
                $clone1.removeAttr('data-participant-id data-booth-id');
                $clone1.find('[data-participant-id], [data-booth-id]').removeAttr(
                    'data-participant-id data-booth-id');
                $clone1.find('.takeover-badge-pill, .nitro-pill').remove();
                $clone1.find('.takeover-slot').empty();
                $content.append($clone1);

                // Compute exact subpixel distance between original item 1 and clone item 1
                const firstOrig = $cards.first()[0];
                const firstClone = $clone1.first()[0];
                let loopDistance = 0;

                try {
                    if (firstOrig && firstClone && typeof firstOrig.getBoundingClientRect === 'function' &&
                        typeof firstClone.getBoundingClientRect === 'function') {
                        const origRect = firstOrig.getBoundingClientRect();
                        const cloneRect = firstClone.getBoundingClientRect();
                        loopDistance = cloneRect.top - origRect.top;
                    }
                } catch (e) {}

                if (!loopDistance || loopDistance <= 0) {
                    loopDistance = (firstClone.offsetTop - firstOrig.offsetTop) || baseHeight;
                }
                if (loopDistance <= 0) loopDistance = 50;

                // 2. Ensure total content is tall enough to cover screen buffer
                let currentHeight = $content[0].scrollHeight;
                let cloneBatches = 0;
                while (currentHeight < containerHeight + loopDistance * 1.5 && cloneBatches < 5) {
                    const $extraClone = $cards.clone().addClass('infinite-clone-item');
                    $extraClone.removeAttr('id');
                    $extraClone.find('[id]').removeAttr('id');
                    $extraClone.removeAttr('data-participant-id data-booth-id');
                    $extraClone.find('[data-participant-id], [data-booth-id]').removeAttr(
                        'data-participant-id data-booth-id');
                    $extraClone.find('.takeover-badge-pill, .nitro-pill').remove();
                    $extraClone.find('.takeover-slot').empty();
                    $content.append($extraClone);
                    currentHeight = $content[0].scrollHeight;
                    cloneBatches++;
                }

                const prevPos = prevOffsets.has(scrollKey) ? prevOffsets.get(scrollKey) : 0;
                const startingPos = (loopDistance > 0 ? (prevPos % loopDistance) : 0);

                const trackObj = {
                    key: scrollKey,
                    container: container,
                    content: $content[0],
                    loopDistance: loopDistance,
                    pos: startingPos,
                    speed: 0.35, // Steady, calm, readable, and velvety smooth (~21px/sec)
                    isHovered: false,
                    startAfter: Date.now() + initialDelayMs
                };

                trackObj.content.style.transform = `translate3d(0, -${startingPos.toFixed(2)}px, 0)`;

                // Mouse / Touch pause controls
                $container.off('mouseenter mouseleave touchstart touchend wheel');
                $container.on('mouseenter touchstart', function() {
                    trackObj.isHovered = true;
                });
                $container.on('mouseleave touchend', function() {
                    trackObj.isHovered = false;
                });

                // Support manual wheel scrolling with infinite wrap-around
                $container.on('wheel', function(e) {
                    e.preventDefault();
                    trackObj.pos += e.originalEvent.deltaY * 0.45;
                    if (trackObj.pos >= trackObj.loopDistance) {
                        trackObj.pos %= trackObj.loopDistance;
                    } else if (trackObj.pos < 0) {
                        trackObj.pos = (trackObj.pos % trackObj.loopDistance) + trackObj.loopDistance;
                    }
                    trackObj.content.style.transform = `translate3d(0, -${trackObj.pos.toFixed(2)}px, 0)`;
                });

                infiniteScrollTracks.push(trackObj);
            });

            if (infiniteScrollTracks.length > 0) {
                runInfiniteScrollLoop();
            }
        }

        function runInfiniteScrollLoop() {
            infiniteScrollLastTime = performance.now();

            function loop(nowTimestamp) {
                if (!isAnimatingTakeover) {
                    const elapsed = nowTimestamp - (infiniteScrollLastTime || nowTimestamp);
                    // Delta time normalized to 60fps (16.67ms per frame), clamped between 0.5 and 2.0
                    const dt = Math.max(0.5, Math.min(elapsed / 16.67, 2.0));
                    const nowWall = Date.now();

                    for (let i = 0; i < infiniteScrollTracks.length; i++) {
                        const item = infiniteScrollTracks[i];
                        if (item.isHovered) continue;
                        if (item.startAfter && nowWall < item.startAfter) continue;

                        item.pos += item.speed * dt;

                        // Exact seamless loop: when pos exceeds loopDistance,
                        // reset by subtracting loopDistance.
                        // Because item 1' is at offset `loopDistance` and is identical to item 1 at offset 0,
                        // the wrap is 100% mathematically indistinguishable and completely invisible!
                        if (item.pos >= item.loopDistance) {
                            item.pos -= item.loopDistance;
                        }

                        item.content.style.transform = `translate3d(0, -${item.pos.toFixed(2)}px, 0)`;
                    }
                }
                infiniteScrollLastTime = nowTimestamp;
                infiniteScrollRafId = requestAnimationFrame(loop);
            }
            infiniteScrollRafId = requestAnimationFrame(loop);
        }

        // Auto-recalculate on window resize or fullscreen toggle
        $(window).on('resize', function() {
            clearTimeout(window.qccResizeTimer);
            window.qccResizeTimer = setTimeout(initInfiniteScroll, 200);
        });

        // Countdown timer setiap detik
        setInterval(() => {
            if (isAnimatingTakeover) return;
            countdown--;
            if (timerEl) timerEl.textContent = countdown + 's';
            if (countdown <= 0) {
                reloadLeaderboardData();
            }
        }, 1000);

        function toggleFullScreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }
    </script>
@endsection
