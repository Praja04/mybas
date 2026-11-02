@extends('pool_qcc.layout')

@section('title', 'Layar Stand - ' . $booth->name)

@section('content')
    <div class="min-h-[calc(100vh-140px)] flex flex-col justify-center max-w-4xl mx-auto px-4 sm:px-6 py-8">
        <!-- Top Bar: Nav to another booth if needed -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('pool-qcc.booths-index') }}"
                class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white flex items-center space-x-1.5 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Daftar Seluruh Booth</span>
            </a>
            <div class="flex items-center space-x-3 text-xs text-slate-500">
                <span class="flex items-center"><i
                        class="fa-solid fa-circle text-[8px] text-emerald-500 mr-1.5 animate-pulse"></i> Standby Mode</span>
                <span>&bull;</span>
                <span class="font-bold text-slate-700 dark:text-slate-300">Station ID: {{ $booth->code }}</span>
            </div>
        </div>

        <!-- Main Stand Booth Card -->
        <div
            class="glass-panel rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-indigo-500/30 shadow-2xl relative overflow-hidden">
            <!-- Booth Header Profile -->
            <div
                class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6 pb-6 mb-8 border-b border-slate-200 dark:border-slate-800 text-center sm:text-left">
                @if ($booth->image)
                    <img src="{{ asset($booth->image) }}" alt="{{ $booth->name }}"
                        class="w-24 h-24 rounded-2xl object-cover border-2 border-indigo-400 shadow-md">
                @else
                    <div
                        class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white text-3xl font-black shadow-md flex-shrink-0">
                        {{ $booth->code }}
                    </div>
                @endif

                <div class="flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1.5">
                        <span class="px-3 py-1 rounded-xl text-xs font-black bg-slate-900 text-white tracking-wider">
                            {{ $booth->code }}
                        </span>
                        <span
                            class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300">
                            Dept: {{ $booth->category }}
                        </span>
                        @if ($booth->pic_name)
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                PIC: <strong class="text-slate-800 dark:text-slate-200">{{ $booth->pic_name }}</strong>
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white leading-tight mb-2">
                        {{ $booth->name }}</h1>
                    <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 line-clamp-2 max-w-2xl">
                        {{ $booth->description ?? 'Selamat datang di stand inovasi QCC kami. Silakan tempelkan ID card Anda untuk mengikuti pooling kuis.' }}
                    </p>
                </div>

                <!-- Total Booth Score Badge -->
                <div
                    class="bg-slate-50 dark:bg-slate-900/80 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-center min-w-[130px] flex-shrink-0 shadow-xs">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Skor Booth</span>
                    <span
                        class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($booth->total_points) }}
                        <span class="text-xs font-semibold">Pts</span></span>
                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $booth->total_voters }} Peserta Memilih</span>
                </div>
            </div>

            <!-- Scanner Tapping Area -->
            <div class="max-w-md mx-auto text-center py-4">
                <!-- Pulsing Scanner Radar Icon -->
                <div class="relative w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-2 border-indigo-400/40 animate-ping opacity-60"></div>
                    <div class="absolute -inset-3 rounded-full border border-cyan-400/30 animate-pulse"></div>
                    <div
                        class="w-24 h-24 rounded-full bg-gradient-to-tr from-indigo-600 to-cyan-500 flex items-center justify-center text-white text-4xl shadow-xl shadow-indigo-500/40">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                </div>

                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2">
                    TEMPELKAN ID CARD ANDA
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mb-6">
                    Dekatkan kartu ke sensor scanner untuk mem-pool <strong>Booth {{ $booth->code }}</strong> dan menjawab
                    pertanyaan acak.
                </p>

                <!-- Autofocused Scanner Input -->
                <form id="rfidBoothForm" onsubmit="event.preventDefault(); processBoothScan();" class="relative mb-4">
                    <div class="relative flex items-center">
                        <input type="text" id="rfidBoothInput" name="rfid" placeholder="Tap Kartu RFID atau Ketik NIK lalu Enter..."
                            autocomplete="off" autofocus
                            class="w-full pl-12 pr-4 py-3.5 rounded-2xl bg-white dark:bg-slate-900 border-2 border-indigo-400 dark:border-indigo-500/50 text-slate-900 dark:text-white placeholder-slate-400 text-center font-mono text-base tracking-wider focus:outline-none focus:border-indigo-600 shadow-inner">
                        <div class="absolute left-4 text-indigo-500">
                            <i class="fa-solid fa-barcode text-xl"></i>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-center space-x-2 text-xs text-emerald-600 dark:text-emerald-400 mt-2.5 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Scanner Aktif di Booth {{ $booth->code }}</span>
                    </div>
                </form>

                <!-- Feedback / Alert Status Box -->
                <div id="scanStatus"
                    class="hidden my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold shadow-sm leading-relaxed"></div>

                <!-- Manual input trigger if needed -->
                <div
                    class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-center space-x-4 text-xs text-slate-500 dark:text-slate-400">
                    <span>Kendala scanner?</span>
                    <button type="button"
                        onclick="document.getElementById('rfidBoothInput').focus(); document.getElementById('rfidBoothInput').placeholder='Ketik NIK/No Kartu lalu Enter';"
                        class="text-indigo-600 dark:text-indigo-400 hover:underline font-bold">
                        Input Manual
                    </button>
                </div>
            </div>

            <!-- Rules Footer Pill -->
            <div
                class="mt-8 pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-500 dark:text-slate-400">
                <span class="flex items-center"><i
                        class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mr-1.5 font-bold"></i> Maksimal 3
                    Booth / Peserta</span>
                <span class="flex items-center"><i
                        class="fa-solid fa-1 text-indigo-600 dark:text-indigo-400 mr-1.5 font-bold"></i> 1x Vote per
                    Booth</span>
                <span class="flex items-center"><i class="fa-solid fa-trophy text-amber-500 mr-1.5 font-bold"></i> Jawaban
                    Benar = +1 Poin Peserta & Booth</span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const rfidInput = document.getElementById('rfidBoothInput');
        const scanStatus = document.getElementById('scanStatus');
        const boothId = {{ $booth->id }};

        // Auto focus terus pada input scanner
        setInterval(() => {
            if (document.activeElement !== rfidInput && !document.querySelector('input:focus')) {
                rfidInput.focus();
            }
        }, 2000);

        // Deteksi Enter dari scanner RFID
        rfidInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                processBoothScan();
            }
        });

        function processBoothScan() {
            const rfidVal = rfidInput.value.trim();
            if (!rfidVal) return;

            // Tampilkan loading feedback
            scanStatus.className =
                'my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold bg-indigo-50 text-indigo-800 border border-indigo-200 shadow-sm';
            scanStatus.innerHTML =
                '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Memvalidasi ID Card pada Booth {{ $booth->code }}...';
            scanStatus.classList.remove('hidden');
            rfidInput.disabled = true;

            fetch(`/pool-qcc/booth/${boothId}/scan-rfid`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        rfid: rfidVal
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        scanStatus.className =
                            'my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-sm';
                        scanStatus.innerHTML =
                            `<i class="fa-solid fa-circle-check text-emerald-600 mr-2"></i> ${data.message} &bull; Memuat kuis...`;

                        confetti({
                            particleCount: 50,
                            spread: 60,
                            origin: {
                                y: 0.6
                            }
                        });

                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 700);
                    } else {
                        throw new Error(data.message || 'Kartu tidak dapat digunakan pada booth ini.');
                    }
                })
                .catch(err => {
                    scanStatus.className =
                        'my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold bg-rose-50 text-rose-800 border border-rose-300 shadow-sm';
                    scanStatus.innerHTML =
                        `<i class="fa-solid fa-circle-xmark text-rose-600 mr-2 text-base"></i> ${err.message}`;
                    rfidInput.disabled = false;
                    rfidInput.value = '';
                    rfidInput.focus();
                });
        }
    </script>
@endsection
