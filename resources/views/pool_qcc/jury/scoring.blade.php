@extends('pool_qcc.layout')

@section('title', 'Penilaian Booth - Dewan Juri QCC')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row items-start md:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800 gap-4">
            <div>
                <div
                    class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                    <span>Pool QCC</span>
                    <span>&bull;</span>
                    <span class="text-slate-500">Dewan Juri</span>
                    <span>&bull;</span>
                    <span class="text-amber-500 font-extrabold">Evaluasi Independen</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
                    <span>Penilaian Booth oleh Dewan Juri</span>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                        <i class="fa-solid fa-scale-balanced mr-1"></i> Skala 1 - 100
                    </span>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Pilih booth yang akan dievaluasi, lalu tap ID Card RFID atau ketikkan NIK Dewan Juri untuk memberikan
                    nilai dan catatan.
                </p>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('pool-qcc.jury.recap') }}"
                    class="px-5 py-2.5 rounded-2xl text-xs font-black bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white shadow-lg shadow-amber-500/25 transition flex items-center space-x-2">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Lihat Rekap & Pemenang</span>
                </a>
                @if (in_array('qcc_pool_admin', $permissions ?? []))
                    <a href="{{ route('pool-qcc.admin.judges') }}"
                        class="px-4 py-2.5 rounded-2xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition flex items-center space-x-1.5">
                        <i class="fa-solid fa-sliders text-indigo-500"></i>
                        <span>Kelola Juri</span>
                    </a>
                @endif
            </div>
        </div>

        @if (!empty($isJudgeAccount) && !empty($loggedInJudge))
            @php
                $evalCount = count($evaluatedBoothIds ?? []);
                $totalBooths = $totalBoothsCount ?? 0;
                $evalPercent = $totalBooths > 0 ? min(100, round(($evalCount / $totalBooths) * 100)) : 0;
                $judgeInit = strtoupper(mb_substr(trim($loggedInJudge->name), 0, 1)) ?: 'J';
            @endphp
            <!-- PERSONAL JUDGE WELCOME BANNER -->
            <div
                class="mb-8 p-6 rounded-3xl bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white shadow-2xl border border-purple-500/30 relative overflow-hidden">
                <div
                    class="absolute -right-10 -bottom-10 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl pointer-events-none">
                </div>
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-400 to-amber-200 text-slate-950 font-black text-2xl flex items-center justify-center shadow-lg border-2 border-white/80 flex-shrink-0">
                            @if ($loggedInJudge->photo)
                                <img src="{{ $loggedInJudge->photo }}" alt="{{ $loggedInJudge->name }}"
                                    class="w-full h-full object-cover rounded-2xl">
                            @else
                                <span>{{ $judgeInit }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950 uppercase tracking-wider shadow-xs">
                                    <i class="fa-solid fa-crown mr-1"></i> {{ $loggedInJudge->title ?: 'Dewan Juri' }}
                                </span>
                                <span class="text-xs text-purple-200 font-mono">
                                    NIK: {{ $loggedInJudge->nik }}
                                </span>
                            </div>
                            <h2 class="text-xl sm:text-2xl font-black text-white leading-tight">
                                Selamat datang, {{ $loggedInJudge->name }}!
                            </h2>
                            <p class="text-xs text-purple-200/80 mt-1">
                                Anda login dengan akun Dewan Juri. Anda dapat langsung memilih booth dan memberikan nilai
                                (1-100) secara independen tanpa perlu tap ID Card.
                            </p>
                        </div>
                    </div>

                    <!-- Evaluation Progress Box -->
                    <div
                        class="w-full md:w-72 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 shadow-inner">
                        <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                            <span class="text-purple-200">Progres Penilaian:</span>
                            <span class="text-amber-300 font-black" id="judgeProgressText">{{ $evalCount }} /
                                {{ $totalBooths }} Booth</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-slate-950/50 overflow-hidden mb-1.5">
                            <div id="judgeProgressBar"
                                class="h-full rounded-full bg-gradient-to-r from-amber-400 to-emerald-400 transition-all duration-500"
                                style="width: {{ $evalPercent }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-purple-200/80 font-semibold"
                            id="judgeProgressSubtext">
                            <span>{{ $evalPercent }}% selesai dinilai</span>
                            @if ($evalCount >= $totalBooths && $totalBooths > 0)
                                <span class="text-emerald-300 font-black"><i class="fa-solid fa-circle-check"></i>
                                    Lengkap</span>
                            @else
                                <span class="text-amber-300 font-black">{{ max(0, $totalBooths - $evalCount) }} booth
                                    tersisa</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filter & Search Toolbar -->
        <div
            class="glass-panel rounded-2xl p-4 sm:p-5 mb-8 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="relative w-full sm:w-96">
                <i
                    class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="searchBoothInput" placeholder="Cari kode booth, nama tim, departemen..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div
                class="flex items-center space-x-4 w-full sm:w-auto justify-between sm:justify-end text-xs font-bold text-slate-500 dark:text-slate-400">
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Total Booth: <strong class="text-slate-800 dark:text-white" id="statTotalBooths">0</strong></span>
                </div>
                <span>&bull;</span>
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span>Dewan Juri Terdaftar: <strong class="text-slate-800 dark:text-white"
                            id="statTotalJudges">0</strong></span>
                </div>
                <button type="button" onclick="loadBoothsList()" title="Segarkan Data"
                    class="p-2 rounded-xl text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-rotate"></i>
                </button>
            </div>
        </div>

        <!-- Booths Grid Container (Client-Side Injected via AJAX) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="boothsGrid">
            <div class="col-span-full py-16 text-center text-slate-400">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-indigo-500 mb-3 block"></i>
                <span class="text-sm font-semibold">Memuat daftar booth QCC...</span>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL STEP 1: TAP RFID / INPUT NIK DEWAN JURI                  -->
    <!-- ============================================================== -->
    <div id="judgeScanModal"
        class="fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div
            class="glass-panel w-full max-w-lg rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 relative overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Close Button -->
            <button type="button" onclick="closeJudgeScanModal()"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Target Booth Info -->
            <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span id="scanModalBoothCode"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-md shadow-indigo-600/30">
                    B01
                </span>
                <div>
                    <h3 id="scanModalBoothName"
                        class="text-base font-extrabold text-slate-900 dark:text-white leading-snug">
                        Nama Booth
                    </h3>
                    <p id="scanModalBoothCategory" class="text-xs text-slate-500 dark:text-slate-400">
                        Kategori / Departemen
                    </p>
                </div>
            </div>

            <!-- RFID Scanner Animation / Graphic -->
            <div class="text-center py-2 mb-4">
                <div
                    class="w-24 h-24 rounded-3xl bg-indigo-50 dark:bg-indigo-950/40 border-2 border-dashed border-indigo-400 dark:border-indigo-500/60 flex items-center justify-center text-3xl text-indigo-600 dark:text-indigo-400 mx-auto mb-3 shadow-inner relative group">
                    <i class="fa-solid fa-id-card group-hover:scale-110 transition-transform"></i>
                    <span class="absolute -top-1 -right-1 flex h-4 w-4">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-indigo-500"></span>
                    </span>
                </div>
                <h4 class="text-lg font-black text-slate-900 dark:text-white">
                    Verifikasi Dewan Juri
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                    Tempelkan ID Card RFID pada scanner atau ketikkan NIK Dewan Juri di bawah ini:
                </p>
            </div>

            <!-- Error Alert Container -->
            <div id="scanErrorAlert"
                class="hidden mb-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-700 dark:text-rose-300 text-xs flex items-start space-x-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-500 mt-0.5 flex-shrink-0 text-sm"></i>
                <div class="flex-1 font-semibold leading-relaxed" id="scanErrorMessage">
                    Bukan Dewan Juri terdaftar.
                </div>
            </div>

            <!-- Scan Input Form -->
            <form id="judgeScanForm" onsubmit="handleJudgeScanSubmit(event)" class="space-y-4">
                <div>
                    <label
                        class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Nomor Kartu RFID / NIK Juri
                    </label>
                    <div class="relative">
                        <i
                            class="fa-solid fa-barcode absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
                        <input type="text" id="judgeScanInput" autocomplete="off"
                            placeholder="Scan kartu atau ketik NIK (lalu tekan Enter)..."
                            class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border-2 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold text-sm tracking-wide focus:border-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition">
                    </div>
                    <span class="text-[11px] text-slate-400 mt-1 block">
                        Scanner RFID otomatis membaca input saat kartu ditempelkan.
                    </span>
                </div>

                <div class="pt-2 flex items-center space-x-3">
                    <button type="button" onclick="closeJudgeScanModal()"
                        class="w-1/3 py-3 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnVerifyJudge"
                        class="w-2/3 py-3 rounded-xl text-xs font-black bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white shadow-lg shadow-indigo-600/25 transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Verifikasi Juri</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL STEP 2: FORM PENILAIAN SKOR & CATATAN DEWAN JURI          -->
    <!-- ============================================================== -->
    <div id="judgeScoreModal"
        class="fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div
            class="glass-panel w-full max-w-2xl rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 relative overflow-hidden animate-in fade-in zoom-in-95 duration-200 max-h-[92vh] overflow-y-auto">
            <!-- Close Button -->
            <button type="button" onclick="closeJudgeScoreModal()"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Judge Profile Card Header -->
            <div
                class="p-4 rounded-2xl bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-amber-500/10 border border-indigo-500/20 mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div id="verifiedJudgeAvatarContainer"
                        class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-200 dark:bg-slate-800 border-2 border-white dark:border-slate-700 shadow-sm flex items-center justify-center text-xl font-black text-indigo-600 flex-shrink-0">
                        <span id="verifiedJudgeInitial">J</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span
                                class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white uppercase tracking-wider inline-flex items-center shadow-xs">
                                <i class="fa-solid fa-crown mr-1"></i> Dewan Juri
                            </span>
                            <span id="verifiedJudgeDept" class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Dept: -
                            </span>
                        </div>
                        <h3 id="verifiedJudgeName"
                            class="text-base sm:text-lg font-black text-slate-900 dark:text-white leading-tight mt-0.5">
                            Nama Dewan Juri
                        </h3>
                        <p id="verifiedJudgeNik" class="text-xs text-slate-400 font-mono">
                            NIK: -
                        </p>
                    </div>
                </div>

                @if (empty($isJudgeAccount))
                    <button type="button" id="switchJudgeBtn" onclick="reopenScanModalForCurrentBooth()"
                        title="Ganti Juri"
                        class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center space-x-1">
                        <i class="fa-solid fa-repeat text-indigo-500"></i>
                        <span class="hidden sm:inline">Ganti Juri</span>
                    </button>
                @endif
            </div>

            <!-- Target Booth Context -->
            <div class="mb-6 pb-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-extrabold uppercase text-slate-400 block tracking-wider">Menilai
                        Booth</span>
                    <div class="flex items-center space-x-2 mt-0.5">
                        <span id="scoreModalBoothCode"
                            class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-indigo-600 text-white">
                            B01
                        </span>
                        <h4 id="scoreModalBoothName" class="text-sm font-bold text-slate-800 dark:text-white">
                            Circle Kaizen Line Produksi
                        </h4>
                    </div>
                </div>
                <div id="evaluationStatusBadge" class="text-right">
                    <!-- Dynamically populated: 'Penilaian Baru' or 'Nilai Sebelumnya: XX' -->
                </div>
            </div>

            <!-- Scoring Form -->
            <form id="saveScoreForm" onsubmit="handleSaveScoreSubmit(event)" class="space-y-6">
                <input type="hidden" id="formJudgeId" value="">
                <input type="hidden" id="formBoothId" value="">

                <!-- Total Score Summary Card (Kalkulasi Akumulasi) -->
                <div
                    class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 via-slate-50 to-indigo-50/40 dark:from-slate-900/90 dark:via-slate-900/70 dark:to-indigo-950/30 border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-center sm:text-left">
                        <span
                            class="text-[11px] font-extrabold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">
                            <i class="fa-solid fa-calculator mr-1"></i> Total Nilai Terkalkulasi
                        </span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" id="criteriaCalculationNote">
                            Akumulasi otomatis terbobot dari kriteria di bawah
                        </p>
                    </div>
                    <div
                        class="flex items-baseline space-x-2 bg-white dark:bg-slate-800 px-5 py-2.5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <span id="scoreBigDisplay"
                            class="text-4xl sm:text-5xl font-black text-indigo-600 dark:text-indigo-400 tracking-tight">
                            85.0
                        </span>
                        <span class="text-sm font-bold text-slate-400">/ 100</span>
                    </div>
                </div>

                <!-- Container Dynamic Kriteria Penilaian -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label
                            class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-list-check text-indigo-500"></i>
                            <span>Aspek & Kriteria Penilaian Booth</span>
                        </label>
                        <span id="criteriaCountBadge"
                            class="text-[11px] font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-lg">
                            4 Kriteria
                        </span>
                    </div>

                    <!-- List Criteria Cards -->
                    <div id="criteriaListContainer" class="space-y-4">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Fallback Single Score Slider (Jika belum ada kriteria terdaftar) -->
                    <div id="singleScoreFallback"
                        class="hidden p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Skor Langsung (1 -
                                100):</span>
                            <input type="number" id="scoreNumberInput" min="1" max="100" value="85"
                                oninput="syncSingleScore(this.value)"
                                class="w-16 px-2 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-center font-bold text-sm text-slate-900 dark:text-white">
                        </div>
                        <input type="range" id="scoreRangeSlider" min="1" max="100" step="1"
                            value="85" oninput="syncSingleScore(this.value)"
                            class="w-full h-3 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                    </div>
                </div>

                <!-- Catatan / Feedback Juri -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Catatan & Masukan Dewan Juri
                        </label>
                        <span class="text-[11px] text-slate-400 font-semibold">(Opsional)</span>
                    </div>
                    <textarea id="judgeNotesInput" rows="3"
                        placeholder="Tuliskan apresiasi keunggulan inovasi, poin kelemahan, atau saran perbaikan untuk tim booth ini..."
                        class="w-full p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-700 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    <span class="text-[11px] text-slate-400 mt-1 block">
                        Catatan ini akan tersimpan dan dapat ditinjau pada Rekapitulasi Pemenang.
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeJudgeScoreModal()"
                        class="px-5 py-3 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" id="btnSaveScore"
                        class="px-6 py-3 rounded-xl text-xs font-black bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white shadow-lg shadow-emerald-600/25 transition flex items-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Nilai Juri</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.isJudgeAccount = {{ !empty($isJudgeAccount) ? 'true' : 'false' }};
        window.loggedInJudgeId = {{ !empty($loggedInJudge) ? $loggedInJudge->id : 'null' }};

        let allBooths = [];
        let totalActiveJudges = 0;
        let selectedBoothForEvaluation = null;
        let verifiedJudgeData = null;

        $(document).ready(function() {
            loadBoothsList();

            // Real-time search filter
            $('#searchBoothInput').on('input', function() {
                const keyword = $(this).val().toLowerCase().trim();
                filterBoothsGrid(keyword);
            });
        });

        // 1. Load Booths List via Client-Side AJAX
        function loadBoothsList() {
            $.ajax({
                url: "{{ route('pool-qcc.jury.api.booths') }}",
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    // Jika grid kosong, tampilkan spinner
                    if ($('#boothsGrid').children().length === 0) {
                        $('#boothsGrid').html(`
                            <div class="col-span-full py-16 text-center text-slate-400">
                                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-indigo-500 mb-3 block"></i>
                                <span class="text-sm font-semibold">Memuat daftar booth QCC...</span>
                            </div>
                        `);
                    }
                },
                success: function(res) {
                    if (res.status === 'success' && res.data) {
                        allBooths = res.data.booths || [];
                        totalActiveJudges = res.data.total_active_judges || 0;

                        $('#statTotalBooths').text(res.data.total_booths || allBooths.length);
                        $('#statTotalJudges').text(totalActiveJudges);

                        // Update personal judge progress banner if logged in
                        if (window.isJudgeAccount) {
                            const myEvaluatedCount = allBooths.filter(b => b.my_evaluation).length;
                            const totalB = allBooths.length;
                            const pct = totalB > 0 ? Math.min(100, Math.round((myEvaluatedCount / totalB) *
                                100)) : 0;
                            $('#judgeProgressText').text(`${myEvaluatedCount} / ${totalB} Booth`);
                            $('#judgeProgressBar').css('width', `${pct}%`);
                            if (myEvaluatedCount >= totalB && totalB > 0) {
                                $('#judgeProgressSubtext').html(
                                    `<span>${pct}% selesai dinilai</span><span class="text-emerald-300 font-black"><i class="fa-solid fa-circle-check"></i> Lengkap</span>`
                                    );
                            } else {
                                $('#judgeProgressSubtext').html(
                                    `<span>${pct}% selesai dinilai</span><span class="text-amber-300 font-black">${Math.max(0, totalB - myEvaluatedCount)} booth tersisa</span>`
                                    );
                            }
                        }

                        renderBoothsGrid(allBooths);
                    } else {
                        $('#boothsGrid').html(`
                            <div class="col-span-full py-12 text-center text-rose-500 text-sm font-bold">
                                Gagal memuat data booth: ${res.message || 'Terjadi kesalahan sistem.'}
                            </div>
                        `);
                    }
                },
                error: function(err) {
                    $('#boothsGrid').html(`
                        <div class="col-span-full py-12 text-center text-rose-500 text-sm font-bold">
                            Gagal menghubungi server untuk memuat booth. Silakan refresh halaman.
                        </div>
                    `);
                }
            });
        }

        // 2. Render Cards Grid
        function renderBoothsGrid(booths) {
            if (!booths || booths.length === 0) {
                $('#boothsGrid').html(`
                    <div class="col-span-full py-16 text-center text-slate-400">
                        <i class="fa-solid fa-box-open text-3xl mb-2 block"></i>
                        <span class="text-sm font-semibold">Tidak ada booth aktif yang tersedia.</span>
                    </div>
                `);
                return;
            }

            let html = '';
            booths.forEach(b => {
                const evalCount = b.total_evaluations || 0;
                const judgesTotal = b.total_active_judges || totalActiveJudges || 1;
                const avgScore = b.avg_score || 0;
                const isFullyEvaluated = (evalCount >= judgesTotal && judgesTotal > 0);

                // Progress percentage
                const progressPct = judgesTotal > 0 ? Math.min(100, Math.round((evalCount / judgesTotal) * 100)) :
                0;

                // Evaluated judges list
                let judgesBadgesHtml = '';
                if (b.evaluated_judges && b.evaluated_judges.length > 0) {
                    judgesBadgesHtml = b.evaluated_judges.map(j => `
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20" title="Skor: ${j.score}">
                            <i class="fa-solid fa-crown text-[9px] mr-1"></i> ${escapeHtml(j.judge_name)}: <strong>${j.score}</strong>
                        </span>
                    `).join('');
                }

                // Action button depending on logged in judge account vs admin/kiosk
                let actionBtnHtml = '';
                if (window.isJudgeAccount) {
                    if (b.my_evaluation) {
                        actionBtnHtml = `
                            <div class="mb-3 px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                                <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Sudah Anda Nilai
                                </span>
                                <span class="text-xs font-black text-emerald-800 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/60 px-2 py-0.5 rounded-lg">
                                    Skor: ${b.my_evaluation.score}
                                </span>
                            </div>
                            <button type="button" onclick="openDirectJudgeScoringModal(${b.id})"
                                class="w-full py-3 rounded-2xl text-xs font-black bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 text-white shadow-lg shadow-emerald-600/25 transition duration-150 flex items-center justify-center space-x-2 group cursor-pointer">
                                <i class="fa-solid fa-pen-to-square group-hover:scale-110 transition-transform"></i>
                                <span>Ubah Nilai (${b.my_evaluation.score})</span>
                            </button>
                        `;
                    } else {
                        actionBtnHtml = `
                            <div class="mb-3 px-3 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock"></i> Belum Anda Nilai
                                </span>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                    Perlu Dinilai
                                </span>
                            </div>
                            <button type="button" onclick="openDirectJudgeScoringModal(${b.id})"
                                class="w-full py-3 rounded-2xl text-xs font-black bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 hover:from-indigo-700 hover:to-purple-800 text-white shadow-lg shadow-indigo-600/25 transition duration-150 flex items-center justify-center space-x-2 group cursor-pointer">
                                <i class="fa-solid fa-star group-hover:scale-110 transition-transform"></i>
                                <span>Beri Nilai Booth Ini</span>
                            </button>
                        `;
                    }
                } else {
                    actionBtnHtml = `
                        <button type="button" onclick="openJudgeScanModal(${b.id})"
                            class="w-full py-3 rounded-2xl text-xs font-black bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-700 hover:to-indigo-900 text-white shadow-lg shadow-indigo-600/25 transition duration-150 flex items-center justify-center space-x-2 group cursor-pointer">
                            <i class="fa-solid fa-id-card group-hover:scale-110 transition-transform"></i>
                            <span>Nilai Booth Ini (Tap ID Card)</span>
                        </button>
                    `;
                }

                html += `
                    <div class="booth-card glass-panel rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-lg hover:shadow-xl hover:border-indigo-500/40 transition duration-200 flex flex-col justify-between"
                        data-booth-id="${b.id}" data-search="${escapeHtml((b.code + ' ' + b.name + ' ' + b.category).toLowerCase())}">
                        <div>
                            <!-- Header Card -->
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-md shadow-indigo-600/25">
                                    ${escapeHtml(b.code)}
                                </span>
                                <div class="text-right">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${isFullyEvaluated ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400'}">
                                        ${evalCount} / ${judgesTotal} Juri Menilai
                                    </span>
                                </div>
                            </div>

                            <!-- Name & Category -->
                            <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight mb-1">
                                ${escapeHtml(b.name)}
                            </h3>
                            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mb-3">
                                <i class="fa-solid fa-tag text-[10px] mr-1"></i> ${escapeHtml(b.category)}
                                ${b.location ? ` &bull; <span class="text-slate-400"><i class="fa-solid fa-location-dot text-[10px] mr-0.5"></i> ${escapeHtml(b.location)}</span>` : ''}
                            </p>

                            <!-- Description Snippet -->
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-4">
                                ${escapeHtml(b.description || 'Tidak ada deskripsi rinci untuk booth ini.')}
                            </p>
                        </div>

                        <div>
                            <!-- Evaluation Stats Bar -->
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 mb-4">
                                <div class="flex items-center justify-between text-xs mb-1.5 font-bold">
                                    <span class="text-slate-500 dark:text-slate-400">Rata-rata Skor:</span>
                                    <span class="text-sm font-black ${avgScore >= 85 ? 'text-emerald-600 dark:text-emerald-400' : (avgScore >= 70 ? 'text-indigo-600 dark:text-indigo-400' : 'text-amber-500')}">
                                        <i class="fa-solid fa-star text-amber-400 text-xs mr-0.5"></i> ${avgScore > 0 ? avgScore : '-'}
                                    </span>
                                </div>

                                <!-- Mini Progress -->
                                <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden mb-2">
                                    <div class="h-full rounded-full ${isFullyEvaluated ? 'bg-emerald-500' : 'bg-indigo-500'}" style="width: ${progressPct}%"></div>
                                </div>

                                <!-- Judges Chips -->
                                <div class="flex flex-wrap gap-1 mt-2">
                                    ${judgesBadgesHtml || '<span class="text-[10px] text-slate-400 italic">Belum ada juri yang menilai</span>'}
                                </div>
                            </div>

                            <!-- Action Button Area -->
                            ${actionBtnHtml}
                        </div>
                    </div>
                `;
            });

            $('#boothsGrid').html(html);
        }

        // Direct scoring modal trigger when judge is logged in directly
        function openDirectJudgeScoringModal(boothId) {
            const booth = allBooths.find(b => b.id == boothId);
            if (!booth) return;
            selectedBoothForEvaluation = booth;

            Swal.fire({
                title: 'Menyiapkan Form Penilaian...',
                text: 'Memuat data kriteria untuk ' + booth.name,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('pool-qcc.jury.verify-judge') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    booth_id: booth.id,
                    judge_id: window.loggedInJudgeId || ''
                },
                dataType: 'json',
                success: function(res) {
                    Swal.close();
                    if (res.status === 'success' && res.data) {
                        openJudgeScoreModal(res.data);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pemberitahuan',
                            text: res.message || 'Gagal memuat form penilaian booth.',
                            confirmButtonColor: '#4f46e5'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message :
                        'Terjadi kesalahan sistem saat memuat form penilaian.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: errMsg,
                        confirmButtonColor: '#4f46e5'
                    });
                }
            });
        }

        // 3. Search Filter in Client-side
        function filterBoothsGrid(keyword) {
            if (!keyword) {
                $('.booth-card').show();
                return;
            }
            $('.booth-card').each(function() {
                const searchData = $(this).attr('data-search') || '';
                if (searchData.includes(keyword)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // ==============================================================
        // STEP 1: MODAL TAP SCAN
        // ==============================================================
        function openJudgeScanModal(boothId) {
            const booth = allBooths.find(b => b.id == boothId);
            if (!booth) return;

            selectedBoothForEvaluation = booth;

            $('#scanModalBoothCode').text(booth.code);
            $('#scanModalBoothName').text(booth.name);
            $('#scanModalBoothCategory').text(booth.category + (booth.location ? ' • ' + booth.location : ''));

            $('#scanErrorAlert').addClass('hidden');
            $('#judgeScanInput').val('');

            $('#judgeScanModal').removeClass('hidden');

            setTimeout(() => {
                $('#judgeScanInput').focus();
            }, 100);
        }

        function closeJudgeScanModal() {
            $('#judgeScanModal').addClass('hidden');
            $('#judgeScanInput').val('');
            $('#scanErrorAlert').addClass('hidden');
        }

        function handleJudgeScanSubmit(e) {
            e.preventDefault();
            const inputVal = $('#judgeScanInput').val().trim();
            if (!inputVal) {
                showScanError('Silakan tap kartu RFID atau ketikkan NIK Dewan Juri.');
                $('#judgeScanInput').focus();
                return;
            }

            if (!selectedBoothForEvaluation) {
                showScanError('Booth belum dipilih.');
                return;
            }

            const $btn = $('#btnVerifyJudge');
            const originalBtnHtml = $btn.html();

            $btn.prop('disabled', true).html(
                '<i class="fa-solid fa-circle-notch fa-spin"></i><span>Memverifikasi...</span>');
            $('#scanErrorAlert').addClass('hidden');

            $.ajax({
                url: "{{ route('pool-qcc.jury.verify-judge') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    booth_id: selectedBoothForEvaluation.id,
                    rfid_or_nik: inputVal
                },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (res.status === 'success' && res.data) {
                        // Tutup modal scan
                        closeJudgeScanModal();

                        // Buka modal penilaian skor & catatan
                        openJudgeScoreModal(res.data);
                    } else {
                        showScanError(res.message || 'Data juri tidak valid.');
                        $('#judgeScanInput').select().focus();
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(originalBtnHtml);
                    let errMsg = 'Bukan Dewan Juri resmi QCC. Pastikan kartu atau NIK telah didaftarkan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    showScanError(errMsg);
                    $('#judgeScanInput').select().focus();
                }
            });
        }

        function showScanError(msg) {
            $('#scanErrorMessage').text(msg);
            $('#scanErrorAlert').removeClass('hidden');
        }

        // ==============================================================
        // STEP 2: MODAL PENILAIAN SKOR & KRITERIA DEWAN JURI
        // ==============================================================
        let activeCriteriaList = [];
        let computedTotalScore = 80;

        function openJudgeScoreModal(data) {
            verifiedJudgeData = data;
            const judge = data.judge;
            const booth = data.booth;
            const hasEvaluated = data.has_evaluated;
            const evalData = data.existing_evaluation;
            activeCriteriaList = data.criteria || [];

            // Form Hidden
            $('#formJudgeId').val(judge.id);
            $('#formBoothId').val(booth.id);

            // Header Judge
            $('#verifiedJudgeName').text(judge.name);
            $('#verifiedJudgeNik').text('NIK: ' + judge.nik);
            $('#verifiedJudgeDept').text('Dept: ' + (judge.department || 'Umum'));

            if (judge.photo) {
                $('#verifiedJudgeAvatarContainer').html(
                    `<img src="${judge.photo}" alt="${escapeHtml(judge.name)}" class="w-full h-full object-cover">`);
            } else {
                const initial = judge.name ? judge.name.charAt(0).toUpperCase() : 'J';
                $('#verifiedJudgeAvatarContainer').html(
                    `<span class="text-xl font-black text-indigo-600">${initial}</span>`);
            }

            // Booth info
            $('#scoreModalBoothCode').text(booth.code);
            $('#scoreModalBoothName').text(booth.name);

            // Evaluation status badge
            if (hasEvaluated && evalData) {
                $('#evaluationStatusBadge').html(`
                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">
                        <i class="fa-solid fa-clock-rotate-left mr-1"></i> Pernah Dinilai (${evalData.score})
                    </span>
                `);
                $('#judgeNotesInput').val(evalData.notes || '');
            } else {
                $('#evaluationStatusBadge').html(`
                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-black bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                        <i class="fa-solid fa-sparkles mr-1"></i> Penilaian Baru
                    </span>
                `);
                $('#judgeNotesInput').val('');
            }

            // Render Criteria or Fallback
            renderCriteriaInputs(activeCriteriaList, evalData);

            $('#judgeScoreModal').removeClass('hidden');
        }

        function closeJudgeScoreModal() {
            $('#judgeScoreModal').addClass('hidden');
            verifiedJudgeData = null;
        }

        function reopenScanModalForCurrentBooth() {
            closeJudgeScoreModal();
            if (selectedBoothForEvaluation) {
                openJudgeScanModal(selectedBoothForEvaluation.id);
            }
        }

        function renderCriteriaInputs(criteria, evalData) {
            const $container = $('#criteriaListContainer');
            $container.empty();

            if (!criteria || criteria.length === 0) {
                $('#criteriaCountBadge').text('Mode Langsung');
                $('#singleScoreFallback').removeClass('hidden');
                syncSingleScore(evalData ? evalData.score : 80);
                return;
            }

            $('#singleScoreFallback').addClass('hidden');
            $('#criteriaCountBadge').text(criteria.length + ' Butir Penilaian');

            // Kelompokkan kriteria berdasarkan Nama Aspek (case-insensitive & trimmed)
            const groups = [];
            const groupMap = new Map();

            criteria.forEach(c => {
                const rawName = (c.name || 'Umum').trim();
                const normKey = rawName.toLowerCase();
                if (!groupMap.has(normKey)) {
                    const grp = {
                        name: rawName,
                        items: [],
                        totalWeight: 0
                    };
                    groupMap.set(normKey, grp);
                    groups.push(grp);
                }
                const grp = groupMap.get(normKey);
                grp.items.push(c);
                grp.totalWeight += parseFloat(c.weight || 0);
            });

            let globalOrder = 0;
            groups.forEach(grp => {
                const isGrouped = grp.items.length > 1;
                const roundedWeight = (grp.totalWeight % 1 === 0) ? grp.totalWeight.toFixed(0) : grp.totalWeight
                    .toFixed(1);

                let innerItemsHtml = '';

                grp.items.forEach((c, itemIdx) => {
                    globalOrder++;
                    const maxScore = c.max_score || 100;
                    let initScore = (c.saved_score !== null && c.saved_score !== undefined) ?
                        parseFloat(c.saved_score) :
                        (evalData ? parseFloat(evalData.score) : 80);

                    if (initScore > maxScore) initScore = maxScore;
                    if (initScore < 1) initScore = 1;

                    const itemTitle = isGrouped ?
                        (c.description || `Indikator #${itemIdx + 1}`) :
                        (c.name);
                    const itemDesc = isGrouped ?
                        '' :
                        (c.description ?
                            `<p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 pl-8">${escapeHtml(c.description)}</p>` :
                            '');

                    innerItemsHtml += `
                        <div class="${isGrouped ? 'p-3.5 sm:p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60' : ''}">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-extrabold text-xs flex items-center justify-center">
                                            ${isGrouped ? `${itemIdx + 1}` : globalOrder}
                                        </span>
                                        <h5 class="text-sm font-extrabold text-slate-900 dark:text-white">
                                            ${escapeHtml(itemTitle)}
                                        </h5>
                                    </div>
                                    ${itemDesc}
                                </div>
                                <div class="flex items-center justify-end space-x-2 pl-8 sm:pl-0 flex-shrink-0">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                        Bobot: ${c.weight}%
                                    </span>
                                    <div class="flex items-center space-x-1">
                                        <input type="number" id="criterion_num_${c.id}" min="1" max="${maxScore}" value="${initScore}"
                                            oninput="onCriterionInputChange(${c.id}, this.value, 'num')"
                                            class="w-16 px-2 py-1 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-center font-black text-sm text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500">
                                        <span class="text-[11px] text-slate-400 font-bold">/ ${maxScore}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Slider -->
                            <div class="mt-3 px-1">
                                <input type="range" id="criterion_range_${c.id}" min="1" max="${maxScore}" step="1" value="${initScore}"
                                    oninput="onCriterionInputChange(${c.id}, this.value, 'range')"
                                    class="w-full h-2.5 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-indigo-600 focus:outline-none">
                            </div>

                            <!-- Preset Chips & Contrib Preview -->
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 pt-2 text-[11px]">
                                <div class="flex items-center space-x-1">
                                    <span class="text-[10px] font-bold text-slate-400 mr-1 hidden sm:inline">Pilihan:</span>
                                    <button type="button" onclick="setCriterionPreset(${c.id}, 70)" class="px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 transition">70</button>
                                    <button type="button" onclick="setCriterionPreset(${c.id}, 75)" class="px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 transition">75</button>
                                    <button type="button" onclick="setCriterionPreset(${c.id}, 80)" class="px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 transition">80</button>
                                    <button type="button" onclick="setCriterionPreset(${c.id}, 85)" class="px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 transition">85</button>
                                    <button type="button" onclick="setCriterionPreset(${c.id}, 90)" class="px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 transition">90</button>
                                    <button type="button" onclick="setCriterionPreset(${c.id}, 95)" class="px-2 py-0.5 rounded-lg font-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-indigo-50 transition">95</button>
                                    <button type="button" onclick="setCriterionPreset(${c.id}, 100)" class="px-2 py-0.5 rounded-lg font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition">100</button>
                                </div>
                                <div class="text-right text-slate-500 dark:text-slate-400 font-semibold">
                                    Kontribusi: <strong id="criterion_contrib_${c.id}" class="text-slate-800 dark:text-white font-extrabold">-</strong> Poin
                                </div>
                            </div>
                        </div>
                    `;
                });

                if (isGrouped) {
                    const cardHtml = `
                        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border-2 border-indigo-200 dark:border-indigo-900/60 shadow-xs space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-indigo-100 dark:border-indigo-900/60">
                                <div class="flex items-center space-x-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white font-black text-xs flex items-center justify-center">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <div>
                                        <h4 class="text-sm font-black text-indigo-950 dark:text-indigo-200">
                                            ${escapeHtml(grp.name)}
                                        </h4>
                                        <span class="text-[10px] font-bold text-slate-400">
                                            ${grp.items.length} Butir Penilaian (Aspek Gabungan)
                                        </span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                    Total Bobot Aspek: ${roundedWeight}%
                                </span>
                            </div>
                            <div class="space-y-3">
                                ${innerItemsHtml}
                            </div>
                        </div>
                    `;
                    $container.append(cardHtml);
                } else {
                    const cardHtml = `
                        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs hover:border-indigo-300 dark:hover:border-indigo-800 transition">
                            ${innerItemsHtml}
                        </div>
                    `;
                    $container.append(cardHtml);
                }
            });

            calculateTotalScore();
        }

        function onCriterionInputChange(criterionId, val, source) {
            let num = parseFloat(val);
            if (isNaN(num) || num < 1) num = 1;

            const crit = activeCriteriaList.find(c => c.id == criterionId);
            const max = crit ? (crit.max_score || 100) : 100;
            if (num < 1) num = 1;
            if (num > max) num = max;

            if (source === 'range') {
                $(`#criterion_num_${criterionId}`).val(num);
            } else {
                $(`#criterion_range_${criterionId}`).val(num);
            }

            calculateTotalScore();
        }

        function setCriterionPreset(criterionId, val) {
            const crit = activeCriteriaList.find(c => c.id == criterionId);
            const max = crit ? (crit.max_score || 100) : 100;
            let num = Math.min(val, max);

            $(`#criterion_num_${criterionId}`).val(num);
            $(`#criterion_range_${criterionId}`).val(num);
            calculateTotalScore();
        }

        function calculateTotalScore() {
            if (!activeCriteriaList || activeCriteriaList.length === 0) {
                return;
            }

            let totalWeighted = 0;
            let totalWeight = 0;

            activeCriteriaList.forEach(c => {
                const rawVal = parseFloat($(`#criterion_num_${c.id}`).val());
                const val = (isNaN(rawVal) || rawVal < 1) ? 1 : rawVal;
                const max = c.max_score || 100;
                const weight = c.weight || 0;

                const scorePct = max > 0 ? (val / max) * 100 : 0;
                const contrib = scorePct * (weight / 100);

                totalWeighted += contrib;
                totalWeight += weight;

                $(`#criterion_contrib_${c.id}`).text(contrib.toFixed(1));
            });

            if (totalWeight > 0 && Math.abs(totalWeight - 100) > 0.01) {
                computedTotalScore = (totalWeighted / totalWeight) * 100;
            } else {
                computedTotalScore = totalWeighted;
            }

            computedTotalScore = Math.min(100, Math.max(1, computedTotalScore));
            updateScoreDisplay(computedTotalScore);
        }

        function syncSingleScore(val) {
            let num = parseFloat(val);
            if (isNaN(num) || num < 1) num = 1;
            if (num > 100) num = 100;

            computedTotalScore = num;
            $('#scoreNumberInput').val(num);
            $('#scoreRangeSlider').val(num);
            updateScoreDisplay(num);
        }

        function updateScoreDisplay(score) {
            const rounded = Math.round(score * 10) / 10;
            const $disp = $('#scoreBigDisplay');
            $disp.text(rounded.toFixed(1));

            $disp.removeClass('text-emerald-600 dark:text-emerald-400 text-indigo-600 dark:text-indigo-400 text-amber-500');
            if (rounded >= 85) {
                $disp.addClass('text-emerald-600 dark:text-emerald-400');
            } else if (rounded >= 70) {
                $disp.addClass('text-indigo-600 dark:text-indigo-400');
            } else {
                $disp.addClass('text-amber-500');
            }
        }

        function handleSaveScoreSubmit(e) {
            e.preventDefault();
            const judgeId = $('#formJudgeId').val();
            const boothId = $('#formBoothId').val();
            const notes = $('#judgeNotesInput').val();

            if (!judgeId || !boothId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Tidak Lengkap',
                    text: 'Identitas juri atau booth belum terverifikasi dengan benar.'
                });
                return;
            }

            // Kumpulkan nilai per kriteria
            const criteriaScores = {};
            if (activeCriteriaList && activeCriteriaList.length > 0) {
                activeCriteriaList.forEach(c => {
                    const rawVal = parseFloat($(`#criterion_num_${c.id}`).val());
                    criteriaScores[c.id] = (isNaN(rawVal) || rawVal < 1) ? 1 : rawVal;
                });
            }

            if (computedTotalScore < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nilai Minimal 1',
                    text: 'Nilai evaluasi juri minimal adalah 1.'
                });
                return;
            }

            const $btn = $('#btnSaveScore');
            const originalBtnHtml = $btn.html();
            $btn.prop('disabled', true).html(
                '<i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i><span>Menyimpan...</span>');

            $.ajax({
                url: "{{ route('pool-qcc.jury.save-score') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    judge_id: judgeId,
                    booth_id: boothId,
                    score: computedTotalScore,
                    criteria_scores: criteriaScores,
                    notes: notes
                },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false).html(originalBtnHtml);

                    if (res.status === 'success') {
                        closeJudgeScoreModal();

                        Swal.fire({
                            icon: 'success',
                            title: 'Penilaian Berhasil!',
                            text: res.message || 'Nilai evaluasi berhasil disimpan.',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reload data booths client-side
                        loadBoothsList();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menyimpan',
                            text: res.message || 'Terjadi kesalahan saat menyimpan nilai.'
                        });
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(originalBtnHtml);
                    let errMsg = 'Gagal menyimpan nilai evaluasi dewan juri.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Sistem',
                        text: errMsg
                    });
                }
            });
        }

        // Helper escape HTML
        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    </script>
@endsection
