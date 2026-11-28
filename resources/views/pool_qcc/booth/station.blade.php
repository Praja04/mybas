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
                        <input type="number" id="rfidBoothInput" name="rfid"
                            placeholder="Tap Kartu RFID atau Ketik NIK lalu Enter..." autocomplete="off" autofocus
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
                    class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-center space-x-3 text-xs text-slate-500 dark:text-slate-400">
                    <span>Kendala scanner atau kartu belum terdaftar?</span>
                    <button type="button" onclick="openManualModal()"
                        class="px-3.5 py-1.5 rounded-xl font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 border border-indigo-200 dark:border-indigo-800/60 transition flex items-center space-x-1 cursor-pointer">
                        <i class="fa-solid fa-keyboard mr-1 text-xs"></i>
                        <span>Input Manual (NIK & Nama)</span>
                    </button>
                </div>
            </div>

            <!-- Rules Footer Pill -->
            <div
                class="mt-8 pt-4 border-t border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-500 dark:text-slate-400">
                <span class="flex items-center"><i
                        class="fa-solid fa-check text-emerald-600 dark:text-emerald-400 mr-1.5 font-bold"></i> Maksimal
                    {{ $totalBooths ?? 10 }}
                    Booth / Peserta</span>
                <span class="flex items-center"><i
                        class="fa-solid fa-1 text-indigo-600 dark:text-indigo-400 mr-1.5 font-bold"></i> 1x Vote per
                    Booth</span>
                <span class="flex items-center"><i class="fa-solid fa-trophy text-amber-500 mr-1.5 font-bold"></i> Jawaban
                    Benar = +1 Poin Peserta & Booth</span>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL: INPUT MANUAL PESERTA QCC (NIK + NAMA)                  -->
    <!-- ============================================================== -->
    <div id="modalManualInput"
        class="fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div
            class="glass-panel w-full max-w-md rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl p-6 sm:p-8 relative overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Close Button -->
            <button type="button" onclick="closeManualModal()"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Header -->
            <div class="flex items-center space-x-3 mb-5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div
                    class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-md shadow-indigo-600/30 flex-shrink-0">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight">
                        Input Manual Peserta
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Booth <strong>{{ $booth->code }}</strong> &bull; {{ $booth->name }}
                    </p>
                </div>
            </div>

            <!-- Notice -->
            <div
                class="mb-4 p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 text-xs text-indigo-700 dark:text-indigo-300 flex items-start space-x-2">
                <i class="fa-solid fa-circle-info mt-0.5 text-indigo-500 flex-shrink-0"></i>
                <span class="leading-relaxed">
                    Masukkan NIK, Nama lengkap, dan pilih Departemen peserta untuk pendaftaran manual di booth ini.
                </span>
            </div>

            <!-- Error Banner -->
            <div id="manualInputError"
                class="hidden mb-4 p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-700 dark:text-rose-300 font-bold flex items-start space-x-2">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-500 flex-shrink-0"></i>
                <span id="manualInputErrorMessage" class="leading-relaxed">Terjadi kesalahan.</span>
            </div>

            <!-- Form -->
            <form id="formManualParticipant" onsubmit="handleManualSubmit(event)" class="space-y-4">
                <input type="hidden" id="manualRfidInput" value="">

                <!-- RFID Terdeteksi Box (Muncul jika dibuka via Tapping RFID) -->
                <div id="manualRfidBox"
                    class="hidden p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-id-card text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">RFID Terdeteksi:</span>
                    </div>
                    <span id="manualRfidDisplay"
                        class="font-mono font-black text-xs text-indigo-700 dark:text-indigo-300 bg-white dark:bg-slate-900 px-2.5 py-1 rounded-lg border border-indigo-200 dark:border-indigo-800"></span>
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Nomor NIK / ID Peserta <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="manualNikInput" required autocomplete="off"
                        placeholder="Contoh: 120000086 atau NIK Peserta..."
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-300 dark:border-slate-700 text-sm font-mono font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Nama Lengkap Peserta <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="manualNameInput" required autocomplete="off"
                        placeholder="Masukkan nama lengkap peserta..."
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-300 dark:border-slate-700 text-sm font-bold text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Departemen <span class="text-rose-500">*</span>
                    </label>
                    <select id="manualDeptInput" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/80 border border-slate-300 dark:border-slate-700 text-sm font-bold text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 cursor-pointer">
                        <option value="" disabled selected>-- Pilih Departemen --</option>
                        <option value="ENG">ENG</option>
                        <option value="WRH">WRH</option>
                        <option value="QCB">QCB</option>
                        <option value="PRD">PRD</option>
                        <option value="HRGA">HRGA</option>
                        <option value="EXP">EXP</option>
                        <option value="HSE">HSE</option>
                        <option value="FACTORY">FACTORY</option>
                    </select>
                </div>

                <div class="pt-3 flex items-center space-x-3">
                    <button type="button" onclick="closeManualModal()"
                        class="w-1/3 py-3 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitManual"
                        class="w-2/3 py-3 rounded-xl text-xs font-black bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white shadow-lg shadow-indigo-600/25 transition flex items-center justify-center space-x-2 cursor-pointer">
                        <i class="fa-solid fa-play text-xs"></i>
                        <span>Mulai Kuis Booth</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const rfidInput = document.getElementById('rfidBoothInput');
        const scanStatus = document.getElementById('scanStatus');
        const boothId = {{ $booth->id }};

        // Auto focus terus pada input scanner jika modal manual tidak terbuka dan tidak ada dialog alert
        setInterval(() => {
            const modal = document.getElementById('modalManualInput');
            const isModalOpen = modal && !modal.classList.contains('hidden');
            const isSwalOpen = (typeof Swal !== 'undefined' && typeof Swal.isVisible === 'function' && Swal
                .isVisible());
            if (!isModalOpen && !isSwalOpen && document.activeElement !== rfidInput && !document.querySelector(
                    'input:focus')) {
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
                .then(res => res.json().then(data => ({
                    httpStatus: res.status,
                    data
                })))
                .then(({
                    httpStatus,
                    data
                }) => {
                    rfidInput.disabled = false;

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
                    } else if (data.not_registered) {
                        // KARTU TIDAK TERDAFTAR DI DB PUSAT -> TAMPILKAN CONFIRM MODAL INPUT MANUAL
                        scanStatus.className =
                            'my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold bg-amber-50 text-amber-800 border border-amber-300 shadow-sm';
                        scanStatus.innerHTML =
                            `<i class="fa-solid fa-triangle-exclamation text-amber-600 mr-2"></i> Kartu RFID '${data.rfid || rfidVal}' belum terdaftar di database pusat.`;

                        rfidInput.value = '';
                        rfidInput.blur();

                        Swal.fire({
                            icon: 'question',
                            title: 'Kartu Belum Terdaftar',
                            html: `
                                <div class="text-left text-xs text-slate-600 dark:text-slate-300 space-y-2">
                                    <p>Nomor Kartu RFID berikut tidak ditemukan di database pusat (MSIDCARD):</p>
                                    <div class="p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-center font-mono font-black text-sm text-indigo-600 dark:text-indigo-400 border border-slate-200 dark:border-slate-700">
                                        ${data.rfid || rfidVal}
                                    </div>
                                    <p class="font-bold text-slate-800 dark:text-slate-200 pt-1">
                                        Apakah Anda ingin mendaftarkan manual (NIK & Nama) untuk kartu RFID ini sekarang?
                                    </p>
                                    <p class="text-[11px] text-slate-400">
                                        *Data akan disimpan di database lokal. Selanjutnya kartu ini bisa langsung digunakan untuk tap di seluruh booth!
                                    </p>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: '<i class="fa-solid fa-user-pen mr-1.5"></i> Ya, Input Manual',
                            cancelButtonText: 'Batal / Tap Ulang',
                            confirmButtonColor: '#4f46e5',
                            cancelButtonColor: '#64748b',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                openManualModalWithRfid(data.rfid || rfidVal);
                            } else {
                                rfidInput.focus();
                            }
                        });
                    } else {
                        // Error validasi lainnya (misal: sudah pernah vote, kuota penuh)
                        scanStatus.className =
                            'my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold bg-rose-50 text-rose-800 border border-rose-300 shadow-sm';
                        scanStatus.innerHTML =
                            `<i class="fa-solid fa-circle-xmark text-rose-600 mr-2 text-base"></i> ${data.message || 'Kartu tidak dapat digunakan pada booth ini.'}`;
                        rfidInput.value = '';
                        rfidInput.focus();
                    }
                })
                .catch(err => {
                    rfidInput.disabled = false;
                    scanStatus.className =
                        'my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold bg-rose-50 text-rose-800 border border-rose-300 shadow-sm';
                    scanStatus.innerHTML =
                        `<i class="fa-solid fa-circle-xmark text-rose-600 mr-2 text-base"></i> ${err.message || 'Terjadi kesalahan jaringan.'}`;
                    rfidInput.value = '';
                    rfidInput.focus();
                });
        }

        // ==============================================================
        // MODAL MANUAL INPUT HANDLERS
        // ==============================================================
        function openManualModal() {
            openManualModalWithRfid('');
        }

        function openManualModalWithRfid(rfid = '') {
            document.getElementById('manualInputError').classList.add('hidden');
            document.getElementById('manualRfidInput').value = rfid || '';
            const rfidBox = document.getElementById('manualRfidBox');
            const rfidDisplay = document.getElementById('manualRfidDisplay');
            if (rfid) {
                rfidDisplay.textContent = rfid;
                rfidBox.classList.remove('hidden');
            } else {
                rfidBox.classList.add('hidden');
            }
            document.getElementById('manualNikInput').value = '';
            document.getElementById('manualNameInput').value = '';
            document.getElementById('manualDeptInput').value = '';
            document.getElementById('modalManualInput').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('manualNikInput').focus();
            }, 150);
        }

        function closeManualModal() {
            document.getElementById('modalManualInput').classList.add('hidden');
            rfidInput.disabled = false;
            rfidInput.focus();
        }

        function handleManualSubmit(e) {
            e.preventDefault();
            const rfid = document.getElementById('manualRfidInput').value.trim();
            const nik = document.getElementById('manualNikInput').value.trim();
            const name = document.getElementById('manualNameInput').value.trim();
            const department = document.getElementById('manualDeptInput').value.trim();

            if (!nik || !name) {
                showManualError('NIK dan Nama lengkap wajib diisi.');
                return;
            }

            if (!department) {
                showManualError('Departemen wajib dipilih.');
                return;
            }

            const btn = document.getElementById('btnSubmitManual');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i> Memproses...';
            document.getElementById('manualInputError').classList.add('hidden');

            fetch(`/pool-qcc/booth/${boothId}/scan-rfid`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        rfid: rfid || nik,
                        nik: nik,
                        name: name,
                        department: department,
                        is_manual: true
                    })
                })
                .then(res => res.json().then(data => ({
                    httpStatus: res.status,
                    data
                })))
                .then(({
                    httpStatus,
                    data
                }) => {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;

                    if (data.status === 'success') {
                        closeManualModal();
                        scanStatus.className =
                            'my-4 p-4 rounded-2xl text-xs sm:text-sm font-bold bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-sm';
                        scanStatus.innerHTML =
                            `<i class="fa-solid fa-circle-check text-emerald-600 mr-2"></i> ${data.message} &bull; Memuat kuis...`;
                        scanStatus.classList.remove('hidden');

                        confetti({
                            particleCount: 50,
                            spread: 60,
                            origin: {
                                y: 0.6
                            }
                        });

                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 600);
                    } else {
                        showManualError(data.message || 'Gagal memproses peserta.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                    showManualError(err.message || 'Terjadi kesalahan jaringan.');
                });
        }

        function showManualError(msg) {
            const errBox = document.getElementById('manualInputError');
            const errMsg = document.getElementById('manualInputErrorMessage');
            errMsg.textContent = msg;
            errBox.classList.remove('hidden');
        }
    </script>
@endsection
