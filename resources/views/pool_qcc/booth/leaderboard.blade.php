@extends('pool_qcc.layout')

@section('title', '🏆 Exhibition QCC 2026 - Live 3D Leaderboard!')

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
            border: 5px solid #fbbf24;
            box-shadow: inset 0 3px 5px rgba(255, 255, 255, 0.4), 0 8px 0 #b45309, 0 16px 28px rgba(0, 0, 0, 0.35);
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

        /* 3D Cartoon Cards */
        .game-card-3d {
            box-shadow: 0 6px 0 rgba(0, 0, 0, 0.15), 0 10px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.15s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.15s ease;
        }

        .game-card-3d:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 0 rgba(0, 0, 0, 0.15), 0 14px 24px rgba(0, 0, 0, 0.12);
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
        .takeover-overtaker-active {
            box-shadow: 0 0 35px 5px rgba(245, 158, 11, 0.95), 0 8px 24px rgba(0, 0, 0, 0.45) !important;
            border-color: #f59e0b !important;
            z-index: 60 !important;
            position: relative !important;
        }

        .takeover-overtaken-active {
            opacity: 0.85;
            z-index: 20 !important;
            position: relative !important;
        }

        @keyframes nitro-pulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 4px #f59e0b); }
            50% { transform: scale(1.08); filter: drop-shadow(0 0 10px #ea580c); }
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
            0% { transform: scale(0) rotate(-10deg); opacity: 0; }
            70% { transform: scale(1.2) rotate(4deg); opacity: 1; }
            100% { transform: scale(1) rotate(0deg); opacity: 1; }
        }

        .anim-badge-pop {
            animation: badge-pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
    </style>
@endsection

@section('content')
    <div
        class="min-h-screen px-3 sm:px-5 lg:px-7 py-4 flex flex-col justify-between relative overflow-hidden game-sky-bg text-slate-900 dark:text-slate-100 transition-colors font-game">

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
        <!-- TOP 3D TOURNAMENT SIGNBOARD & GAME HUB BAR               -->
        <!-- ======================================================== -->
        <div class="relative z-20 flex flex-col items-center mb-3">

            <!-- Hanging Chain Rings from Ceiling -->
            <div class="flex justify-between w-72 max-w-full px-10 -mb-2.5 z-10 pointer-events-none">
                <div
                    class="w-3.5 h-6 rounded-full border-2 border-amber-600 bg-gradient-to-b from-amber-300 to-amber-500 shadow-sm">
                </div>
                <div
                    class="w-3.5 h-6 rounded-full border-2 border-amber-600 bg-gradient-to-b from-amber-300 to-amber-500 shadow-sm">
                </div>
            </div>

            <!-- Main Wooden/Purple Signboard -->
            <div
                class="game-signboard relative px-6 sm:px-14 py-3 sm:py-4 rounded-3xl text-center flex flex-col items-center max-w-xl w-full">
                <!-- Info Subtitle Ribbon -->
                <div
                    class="px-3.5 py-0.5 rounded-full bg-gradient-to-r from-amber-400 to-yellow-300 text-slate-950 font-black text-[10px] sm:text-xs tracking-wider uppercase border border-amber-500 shadow-sm flex items-center space-x-1 mb-1">
                    <span>👑</span>
                    <span>QCC/QCP PT BUMI ALAM SEGAR 2026</span>
                    <span>👑</span>
                </div>

                <!-- Grand Title -->
                <h1
                    class="text-2xl sm:text-4xl font-black text-white tracking-tight drop-shadow-[0_4px_3px_rgba(0,0,0,0.85)] font-heading leading-tight">
                    EXHIBITION QCC/QCP
                </h1>

                <!-- Hanging Live Countdown / Sync Clock -->
                {{-- <div onclick="reloadLeaderboardData()" title="Klik untuk refresh instan"
                    class="absolute -bottom-4 bg-gradient-to-b from-amber-300 via-amber-400 to-amber-500 hover:from-amber-200 hover:to-amber-400 text-slate-950 font-black px-4 sm:px-5 py-1 rounded-full text-xs border-2 border-amber-700 shadow-md flex items-center space-x-2 cursor-pointer game-btn-3d">
                    <span class="anim-coin text-sm">⏱️</span>
                    <span class="tracking-wide">SYNC:</span>
                    <span id="refreshTimer" class="font-mono text-sm sm:text-base font-black">15s</span>
                    <span id="syncIndicator" class="hidden text-emerald-900 animate-spin text-xs">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </span>
                </div> --}}
            </div>

            <!-- Top Left & Right Game Bar (Resources & Action Buttons) -->
            <div class="w-full flex items-center justify-between mt-3 px-1 sm:px-2">
                <!-- Left: Game Resource Badges (Coins & Gems) -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- Total Votes (Coin Chest) -->
                    <div
                        class="flex items-center bg-white/95 dark:bg-slate-900/95 border-2 border-amber-400 dark:border-amber-500 rounded-2xl px-3 py-1 shadow-md">
                        <span class="text-base text-amber-500 mr-2 anim-coin"><i class="fa-solid fa-coins"></i></span>
                        <div>
                            <span
                                class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block leading-none">Total
                                Suara</span>
                            <span id="statTotalVotes"
                                class="text-xs sm:text-sm font-black text-amber-700 dark:text-amber-300">{{ number_format($totalVotes) }}</span>
                        </div>
                    </div>

                    <!-- Total Participants (Gems) -->
                    <div
                        class="flex items-center bg-white/95 dark:bg-slate-900/95 border-2 border-cyan-400 dark:border-cyan-500 rounded-2xl px-3 py-1 shadow-md">
                        <span class="text-base text-cyan-500 mr-2 anim-game-float"><i class="fa-solid fa-gem"></i></span>
                        <div>
                            <span
                                class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block leading-none">Peserta
                                Tap</span>
                            <span id="statTotalParticipants"
                                class="text-xs sm:text-sm font-black text-cyan-700 dark:text-cyan-300">{{ number_format($totalParticipants) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Utility Controls (Theme, Fullscreen, Portal) -->
                <div class="flex items-center space-x-2">
                    <!-- Simulasi Tes Takeover -->
                    <button type="button" onclick="testTakeoverSimulation()"
                        class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-400 hover:to-pink-400 text-white font-black text-xs border-2 border-rose-700 game-btn-3d flex items-center space-x-1.5 cursor-pointer"
                        title="Simulasi Efek Takeover Peringkat">
                        <i class="fa-solid fa-bolt text-yellow-300"></i>
                        <span class="hidden sm:inline text-[11px]">Tes Takeover</span>
                    </button>

                    <!-- Theme Toggle -->
                    <button type="button" onclick="toggleTheme()"
                        class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs border-2 border-amber-600 game-btn-3d flex items-center space-x-1.5 cursor-pointer"
                        title="Ganti Tema">
                        <span class="theme-icon-sun hidden text-sm"><i class="fa-solid fa-sun text-amber-600"></i></span>
                        <span class="theme-icon-moon text-sm"><i class="fa-solid fa-moon text-slate-900"></i></span>
                        <span class="theme-label hidden sm:inline text-[11px]">Tema</span>
                    </button>

                    <!-- Fullscreen -->
                    <button onclick="toggleFullScreen()"
                        class="py-1 px-3 rounded-2xl bg-white hover:bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white border-2 border-slate-300 dark:border-slate-700 game-btn-3d cursor-pointer"
                        title="Layar Penuh">
                        <i class="fa-solid fa-expand text-xs"></i>
                    </button>

                    <!-- Portal -->
                    <a href="{{ route('pool-qcc.portal-home') }}"
                        class="p-2 sm:px-3 sm:py-1.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs border-2 border-indigo-800 game-btn-3d flex items-center space-x-1.5"
                        title="Kembali ke Portal BAS">
                        <i class="fa-solid fa-house text-xs"></i>
                        <span class="hidden sm:inline text-[11px]">Portal</span>
                    </a>
                </div>
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
        <div id="arenaContainer" class="flex-1 flex flex-col justify-stretch relative z-10">
            @include('pool_qcc.booth.leaderboard_arena', [
                'booths' => $booths,
                'participants' => $participants,
            ])
        </div>

        <!-- ======================================================== -->
        <!-- BOTTOM TOURNAMENT TICKER / STAGES NOTICE                 -->
        <!-- ======================================================== -->
        <div
            class="mt-3 pt-2.5 border-t-2 border-amber-400/30 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-700 dark:text-slate-300 gap-2 relative z-10 bg-white/60 dark:bg-slate-950/70 backdrop-blur-md rounded-2xl p-2.5 shadow-sm">
            <div class="flex items-center space-x-2">
                <span
                    class="px-2.5 py-0.5 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 text-slate-950 font-black text-[10px] border border-amber-600 shadow-xs">
                    🎮 QUEST
                </span>
                <span class="font-bold text-[11px] sm:text-xs">
                    Kunjungi minimal 3 booth, jawab challenge dengan benar, & kumpulkan skor setinggi-tingginya! 🚀
                </span>
            </div>

            <!-- Auto Refresh Indicator -->
            <div class="flex items-center space-x-3 text-[11px] font-bold">
                <span class="flex items-center text-emerald-700 dark:text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping mr-1.5"></span>
                    Arena Aktif
                </span>
                <span class="text-slate-500 dark:text-slate-400 cursor-pointer hover:underline"
                    onclick="reloadLeaderboardData()">
                    Auto-Refresh 15s <i class="fa-solid fa-arrows-rotate ml-1"></i>
                </span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Confetti kecil ceria berkala
        function randomFunCelebration() {
            try {
                if (typeof confetti === 'function') {
                    confetti({
                        particleCount: 22,
                        angle: 60,
                        spread: 50,
                        origin: { x: 0, y: 0.8 },
                        colors: ['#f59e0b', '#06b6d4', '#10b981', '#ec4899', '#8b5cf6'],
                        zIndex: 999999,
                        useWorker: false,
                        disableForReducedMotion: true
                    });
                    confetti({
                        particleCount: 22,
                        angle: 120,
                        spread: 50,
                        origin: { x: 1, y: 0.8 },
                        colors: ['#f59e0b', '#06b6d4', '#10b981', '#ec4899', '#8b5cf6'],
                        zIndex: 999999,
                        useWorker: false,
                        disableForReducedMotion: true
                    });
                }
            } catch (err) {}
        }

        setTimeout(randomFunCelebration, 1200);
        setInterval(randomFunCelebration, 8000);

        // ========================================================
        // WEB AUDIO API ARCADE SYNTHESIZER (ZERO ASSET / INSTANT)
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
            playNitroSwoosh() {
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;
                    const osc = this.ctx.createOscillator();
                    const gain = this.ctx.createGain();
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(140, now);
                    osc.frequency.exponentialRampToValueAtTime(580, now + 0.35);
                    gain.gain.setValueAtTime(0.01, now);
                    gain.gain.linearRampToValueAtTime(0.18, now + 0.08);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.38);
                    osc.connect(gain);
                    gain.connect(this.ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.4);
                } catch(e) {}
            },
            playTakeoverChime() {
                try {
                    this.init();
                    if (!this.ctx) return;
                    const now = this.ctx.currentTime;
                    
                    // Arcade Victory Fanfare Arpeggio: C5 -> E5 -> G5 -> C6
                    const notes = [
                        { f: 523.25, time: 0, dur: 0.12 },
                        { f: 659.25, time: 0.1, dur: 0.12 },
                        { f: 783.99, time: 0.2, dur: 0.14 },
                        { f: 1046.50, time: 0.32, dur: 0.45 }
                    ];

                    notes.forEach(n => {
                        const osc = this.ctx.createOscillator();
                        const gain = this.ctx.createGain();
                        osc.type = 'triangle';
                        osc.frequency.setValueAtTime(n.f, now + n.time);
                        gain.gain.setValueAtTime(0.001, now + n.time);
                        gain.gain.linearRampToValueAtTime(0.3, now + n.time + 0.02);
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
        // ASYNC JQUERY AJAX RELOAD DATA (WITH REAL TAKEOVER SLIDE)
        // ========================================================
        let countdown = 15;
        let isSyncing = false;
        let isFirstLoad = true;
        let isAnimatingTakeover = false;
        const previousBoothRanks = {};
        const previousParticipantRanks = {};

        const timerEl = document.getElementById('refreshTimer');
        const syncIndicator = document.getElementById('syncIndicator');

        // Record initial ranks directly from DOM
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
                    countdown = 15;
                    if (timerEl) timerEl.textContent = countdown + 's';
                }
            });
        }

        function processLeaderboardUpdate(response) {
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

            // 2. Check if we should execute physical sliding overtake animation first
            if (primaryOvertaker) {
                const overtakerEl = document.querySelector(`[data-participant-id="${primaryOvertaker.id}"]`);
                const overtakenEl = document.querySelector(`[data-participant-rank="${primaryOvertaker.rank}"]`);

                if (overtakerEl && overtakenEl && overtakerEl !== overtakenEl) {
                    isAnimatingTakeover = true;
                    animateParticipantOvertake(overtakerEl, overtakenEl, function() {
                        commitNewArenaHTML(response, takeovers);
                        isAnimatingTakeover = false;
                    });
                    return;
                }
            }

            // If no physical sliding animation needed, commit HTML immediately
            commitNewArenaHTML(response, takeovers);
        }

        function commitNewArenaHTML(response, takeovers) {
            $('#arenaContainer').html(response.arena_html);

            // Update cached ranks
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

            // Update metric badges
            if (response.total_votes !== undefined) {
                $('#statTotalVotes').text(response.total_votes);
            }
            if (response.total_participants !== undefined) {
                $('#statTotalParticipants').text(response.total_participants);
            }

            // Trigger HUD (hanya tampilkan takeover paling utama agar langsung menimpa tanpa tumpuk)
            if (takeovers.length > 0) {
                const topTakeover = takeovers.sort((a, b) => a.newRank - b.newRank)[0];
                showTakeoverHUD(topTakeover);
            }
        }

        // ========================================================
        // PHYSICAL OVERTAKE SLIDING ANIMATION (RACE PASS MANEUVER)
        // ========================================================
        function animateParticipantOvertake(overtakerEl, overtakenEl, onComplete) {
            const $overtaker = $(overtakerEl);
            const $overtaken = $(overtakenEl);

            if (!$overtaker.length || !$overtaken.length) {
                if (onComplete) onComplete();
                return;
            }

            // Calculate vertical distance between the two cards
            const rectOvertaker = overtakerEl.getBoundingClientRect();
            const rectOvertaken = overtakenEl.getBoundingClientRect();
            const dy = rectOvertaker.top - rectOvertaken.top; // Positive distance to travel upwards

            if (Math.abs(dy) < 5) {
                if (onComplete) onComplete();
                return;
            }

            // Sound 1: Nitro Turbo Swoosh
            GameAudio.playNitroSwoosh();

            // Set Initial Stacking
            $overtaker.css({
                'position': 'relative',
                'z-index': '60',
                'transition': 'transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.28s ease'
            }).addClass('takeover-overtaker-active');

            $overtaken.css({
                'position': 'relative',
                'z-index': '20',
                'transition': 'transform 0.28s ease, opacity 0.28s ease'
            }).addClass('takeover-overtaken-active');

            // Add dynamic turbo boost badge
            const $turboBadge = $(`
                <span class="nitro-pill anim-badge-pop inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black bg-gradient-to-r from-amber-400 via-orange-500 to-amber-500 text-slate-950 border border-yellow-200 shadow-lg ml-1.5 anim-nitro-pulse">
                    ⚡ MENYALIP...
                </span>
            `);
            const $titleRow = $overtaker.find('.participant-title-row');
            if ($titleRow.length) $titleRow.append($turboBadge);

            // PHASE 1: Pull out to the lane (0 - 280ms)
            // Overtaker pulls right & lifts up, overtaken shifts slightly left
            $overtaker.css('transform', 'translate(18px, 0px) scale(1.03)');
            $overtaken.css('transform', 'translate(-10px, 0px) scale(0.98)');

            // PHASE 2: The Pass / Rush past each other (after 280ms)
            setTimeout(() => {
                $overtaker.css({
                    'transition': 'transform 0.68s cubic-bezier(0.2, 0.9, 0.3, 1.25)',
                    'transform': `translate(18px, -${dy}px) scale(1.04)`
                });
                $overtaken.css({
                    'transition': 'transform 0.68s cubic-bezier(0.2, 0.9, 0.3, 1.25)',
                    'transform': `translate(-10px, ${dy}px) scale(0.98)`
                });

                // PHASE 3: Dock back into the column (after 960ms)
                setTimeout(() => {
                    // Play victory sound as it docks in
                    GameAudio.playTakeoverChime();

                    $overtaker.css({
                        'transition': 'transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1)',
                        'transform': `translate(0px, -${dy}px) scale(1.02)`
                    });
                    $overtaken.css({
                        'transition': 'transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1)',
                        'transform': `translate(0px, ${dy}px) scale(1)`
                    });

                    // PHASE 4: Finalize & Swap DOM (after 1320ms)
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

                        $turboBadge.html('🔥 TAKEOVER!').removeClass('anim-nitro-pulse');

                        // Physically insert overtaker before overtaken in the DOM
                        $overtaker.insertBefore($overtaken);

                        // Confetti if rank 1 was reached!
                        if (typeof confetti === 'function') {
                            confetti({
                                particleCount: 85,
                                spread: 90,
                                origin: { y: 0.4 },
                                colors: ['#fbbf24', '#f59e0b', '#ef4444', '#10b981', '#06b6d4'],
                                zIndex: 9999999
                            });
                        }

                        if (onComplete) onComplete();
                    }, 370);
                }, 680);
            }, 280);
        }

        // ========================================================
        // SIMULASI TAKEOVER UNTUK TESTING / DEMO
        // ========================================================
        function testTakeoverSimulation() {
            if (isAnimatingTakeover) return;

            const $cards = $('#participantsListWrapper [data-participant-id]');
            if ($cards.length < 2) {
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

            // Target: Card 2 takes over Card 1
            const cardUpper = $cards[0];
            const cardLower = $cards[1];

            const overtakerName = $(cardLower).data('participant-name') || 'Peserta';

            isAnimatingTakeover = true;
            animateParticipantOvertake(cardLower, cardUpper, function() {
                // Swap rank badge numbers & Juara labels between the two cards
                swapParticipantCardBadges(cardLower, cardUpper);

                // Show Takeover HUD Toast
                showTakeoverHUD({
                    type: 'participant',
                    id: $(cardLower).data('participant-id'),
                    name: overtakerName,
                    oldRank: 2,
                    newRank: 1,
                    delta: 1
                });

                isAnimatingTakeover = false;
            });
        }

        function swapParticipantCardBadges(cardNewRank1, cardNewRank2) {
            $(cardNewRank1).attr('data-participant-rank', 1);
            $(cardNewRank2).attr('data-participant-rank', 2);

            // Swap rank badge icons & numbers
            const $badge1 = $(cardNewRank1).find('.flex-shrink-0.shadow-md').first();
            const $badge2 = $(cardNewRank2).find('.flex-shrink-0.shadow-md').first();
            if ($badge1.length && $badge2.length) {
                $badge1.html('<i class="fa-solid fa-crown text-xs mr-0.5"></i> 1')
                       .removeClass('from-slate-300 to-slate-100 border-slate-400 text-slate-800')
                       .addClass('from-amber-400 to-yellow-300 border-amber-500 text-slate-950');
                $badge2.html('<i class="fa-solid fa-medal text-xs mr-0.5"></i> 2')
                       .removeClass('from-amber-400 to-yellow-300 border-amber-500 text-slate-950')
                       .addClass('from-slate-300 to-slate-100 border-slate-400 text-slate-800');
            }

            // Swap Juara pills
            const $pill1 = $(cardNewRank1).find('.participant-title-row span:last-child');
            const $pill2 = $(cardNewRank2).find('.participant-title-row span:last-child');
            if ($pill1.length && $pill2.length) {
                $pill1.text('Juara 1').removeClass('bg-slate-300 text-slate-800').addClass('bg-amber-400 text-slate-950');
                $pill2.text('Juara 2').removeClass('bg-amber-400 text-slate-950').addClass('bg-slate-300 text-slate-800');
            }
        }

        let takeoverHUDTimer = null;

        function showTakeoverHUD(item) {
            const isBooth = item.type === 'booth';
            const typeLabel = isBooth ? '🎪 STAND INOVASI' : '🧠 PESERTA';
            const bgGradient = isBooth 
                ? 'from-amber-500 via-yellow-400 to-amber-600' 
                : 'from-cyan-500 via-teal-400 to-cyan-600';
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

            // Menimpa notifikasi lama di tempat yang sama (tidak menumpuk ke bawah)
            $('#takeoverHUD').html(toast);

            takeoverHUDTimer = setTimeout(() => {
                toast.addClass('opacity-0 -translate-y-4 scale-90');
                setTimeout(() => {
                    toast.remove();
                }, 400);
            }, 4500);
        }

        // Countdown timer setiap detik
        setInterval(() => {
            if (isAnimatingTakeover) return;
            countdown--;
            if (timerEl) timerEl.textContent = countdown + 's';
            if (countdown <= 0) {
                reloadLeaderboardData();
            }
        }, 1000);

        // Toggle Fullscreen helper
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
