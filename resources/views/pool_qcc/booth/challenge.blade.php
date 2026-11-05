@extends('pool_qcc.layout')

@section('title', 'Tantangan Kuis - Booth ' . $booth->code)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 min-h-[calc(100vh-140px)] flex flex-col justify-center">
        <!-- Header Booth Context -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <span
                    class="px-3.5 py-1.5 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-md shadow-indigo-600/30">
                    {{ $booth->code }}
                </span>
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white leading-tight">{{ $booth->name }}</h2>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Dept: {{ $booth->category }} &bull; Pertanyaan
                        Acak</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Reward Vote</span>
                @if(!empty($isJudge))
                    <div class="flex flex-col items-end">
                        <span class="text-xs sm:text-sm font-black text-amber-500 flex items-center justify-end gap-1">
                            <i class="fa-solid fa-crown text-[10px]"></i> Booth: +{{ $judgeVoteWeight ?? 10 }} Poin ({{ $judgeTitle ?? 'Dewan Juri' }})
                        </span>
                        <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                            Peserta: +1 Poin
                        </span>
                    </div>
                @else
                    <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">+{{ $question->points > 0 ? $question->points : 1 }} Poin</span>
                @endif
            </div>
        </div>

        <!-- Question Card -->
        <div
            class="glass-panel rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-indigo-500/30 shadow-2xl relative overflow-hidden">
            <!-- Question Badge & Participant Info -->
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100 dark:border-slate-800">
                <span
                    class="inline-flex items-center text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                    <i class="fa-solid fa-circle-question mr-1.5 text-base"></i> Kuis Inovasi Booth
                </span>
                <span class="text-xs text-slate-600 dark:text-slate-400 flex items-center gap-1.5">
                    @if(!empty($isJudge))
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white shadow-xs">
                            <i class="fa-solid fa-crown mr-1"></i> {{ strtoupper($judgeTitle ?? 'DEWAN JURI') }}
                        </span>
                    @endif
                    <span>Peserta: <strong class="text-slate-900 dark:text-white font-bold">{{ $participant->name }}</strong>
                    (Booth ke-{{ $participant->booths_voted_count + 1 }} dari 10)</span>
                </span>
            </div>

            <!-- Question Image if available -->
            @if ($question->image)
                <div
                    class="mb-6 rounded-2xl overflow-hidden max-h-72 border border-slate-200 dark:border-slate-800 flex justify-center bg-slate-50 dark:bg-slate-900/60">
                    <img src="{{ asset($question->image) }}" alt="Lampiran Soal" class="object-contain max-h-72">
                </div>
            @endif

            <!-- Question Text -->
            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white leading-relaxed mb-8">
                {{ $question->question_text }}
            </h3>

            <!-- Options Buttons (Posisi Diacak/Shuffled) -->
            <div class="space-y-3 mb-8" id="optionsContainer">
                @php
                    $displayOptions = $shuffledOptions ?? [
                        ['label' => 'A', 'text' => $question->option_a],
                        ['label' => 'B', 'text' => $question->option_b],
                        ['label' => 'C', 'text' => $question->option_c],
                        ['label' => 'D', 'text' => $question->option_d],
                    ];
                    if (empty($shuffledOptions) && !empty($question->option_e)) {
                        $displayOptions[] = ['label' => 'E', 'text' => $question->option_e];
                    }
                @endphp

                @foreach ($displayOptions as $opt)
                    <button type="button" onclick="selectOption('{{ $opt['label'] }}')" id="btn-option-{{ $opt['label'] }}"
                        class="option-btn w-full p-4 rounded-2xl bg-white dark:bg-slate-900/70 hover:bg-indigo-50/50 dark:hover:bg-slate-800 border-2 border-slate-200 dark:border-slate-800 hover:border-indigo-500 text-left transition flex items-center space-x-4 group shadow-sm">
                        <div
                            class="option-badge w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-black text-sm flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                            {{ $opt['label'] }}
                        </div>
                        <div
                            class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-900 dark:group-hover:text-white">
                            {{ $opt['text'] }}
                        </div>
                    </button>
                @endforeach
            </div>

            <!-- Submit Action Bar -->
            <div
                class="pt-6 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('pool-qcc.booth-station', $booth->id) }}"
                    class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white flex items-center space-x-1.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Batalkan & Kembali ke Layar Tap</span>
                </a>

                <button type="button" id="btnSubmitAnswer" onclick="submitFinalAnswer()" disabled
                    class="w-full sm:w-auto px-8 py-3.5 rounded-2xl text-sm font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 dark:text-slate-500 cursor-not-allowed transition flex items-center justify-center space-x-2">
                    <span>Kunci & Kirim Jawaban</span>
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Modal Hasil Jawaban & Auto-Redirect ke Layar Booth -->
    <div id="modalResult"
        class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 9000;">
        <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
            <div class="glass-panel w-full max-w-lg rounded-2xl sm:rounded-3xl overflow-y-auto shadow-2xl border p-6 sm:p-8 text-center bg-white dark:bg-slate-900 relative"
                id="resultCard" style="max-height: calc(100vh - 2rem);">
                <!-- Result Icon -->
                <div id="resultIconWrapper"
                    class="w-20 h-20 sm:w-24 sm:h-24 rounded-full mx-auto mb-4 sm:mb-5 flex items-center justify-center text-4xl sm:text-5xl"></div>

                <!-- Result Title -->
                <h3 id="resultTitle" class="text-xl sm:text-3xl font-black text-slate-900 dark:text-white mb-2"></h3>
                <p id="resultSubtitle" class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 mb-4 sm:mb-5"></p>

                <!-- Points Animation Banner -->
                <div id="resultPointsBox" class="p-3.5 sm:p-4 rounded-2xl mb-4 sm:mb-5">
                    <span class="text-xs uppercase font-bold block" id="resultPointsLabel"></span>
                    <span class="text-2xl sm:text-3xl font-black" id="resultPointsValue"></span>
                </div>

                <!-- Explanation if any -->
                <div id="resultExplanationBox"
                    class="hidden mb-4 sm:mb-5 p-3.5 sm:p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 text-left text-xs text-slate-700 dark:text-slate-300">
                    <div class="font-bold text-indigo-600 dark:text-indigo-400 mb-1 flex items-center">
                        <i class="fa-solid fa-lightbulb mr-1.5"></i> Pembahasan:
                    </div>
                    <p id="resultExplanationText"></p>
                </div>

                <!-- Auto Redirect Countdown Notice -->
                <div
                    class="mb-4 sm:mb-5 p-3 rounded-xl bg-slate-100 dark:bg-slate-800/80 text-xs font-bold text-slate-600 dark:text-slate-300 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-500 animate-spin"></i>
                    <span>Kembali ke layar tap booth dalam <span id="countdownTimer"
                            class="text-indigo-600 dark:text-indigo-400 font-black text-sm">5</span> detik...</span>
                </div>

                <!-- Action Button: Manual Immediate Return -->
                <div class="flex items-center justify-center">
                    <a id="btnReturnNow" href="{{ route('pool-qcc.booth-station', $booth->id) }}"
                        class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition text-sm flex items-center justify-center space-x-2">
                        <span>Kembali ke Layar Tap Booth Sekarang</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let selectedAnswer = null;
        const boothId = {{ $booth->id }};
        const questionId = {{ $question->id }};

        function selectOption(opt) {
            selectedAnswer = opt;

            // Reset styles on all options
            document.querySelectorAll('.option-btn').forEach(btn => {
                btn.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-950/40');
                btn.classList.add('border-slate-200', 'dark:border-slate-800', 'bg-white', 'dark:bg-slate-900/70');
                const badge = btn.querySelector('.option-badge');
                badge.classList.remove('bg-indigo-600', 'text-white');
                badge.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
            });

            // Apply active style to selected
            const activeBtn = document.getElementById(`btn-option-${opt}`);
            activeBtn.classList.remove('border-slate-200', 'dark:border-slate-800', 'bg-white', 'dark:bg-slate-900/70');
            activeBtn.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-950/40');
            const activeBadge = activeBtn.querySelector('.option-badge');
            activeBadge.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-300');
            activeBadge.classList.add('bg-indigo-600', 'text-white');

            // Enable submit button
            const submitBtn = document.getElementById('btnSubmitAnswer');
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'dark:text-slate-500',
                'cursor-not-allowed');
            submitBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-500', 'text-white', 'shadow-md',
                'shadow-indigo-600/30');
        }

        function submitFinalAnswer() {
            if (!selectedAnswer) return;

            const submitBtn = document.getElementById('btnSubmitAnswer');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Mengunci Jawaban...';

            fetch(`/pool-qcc/booth/${boothId}/submit`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        answer: selectedAnswer
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        showResultModal(data);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Submit',
                            text: data.message || 'Terjadi kesalahan saat memproses jawaban.',
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Kunci & Kirim Jawaban';
                    }
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Jaringan',
                        text: err.message,
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Kunci & Kirim Jawaban';
                });
        }

        function showResultModal(data) {
            const modal = document.getElementById('modalResult');
            const resultCard = document.getElementById('resultCard');
            const iconWrapper = document.getElementById('resultIconWrapper');
            const title = document.getElementById('resultTitle');
            const subtitle = document.getElementById('resultSubtitle');
            const pointsBox = document.getElementById('resultPointsBox');
            const pointsLabel = document.getElementById('resultPointsLabel');
            const pointsValue = document.getElementById('resultPointsValue');
            const explBox = document.getElementById('resultExplanationBox');
            const explText = document.getElementById('resultExplanationText');
            const btnReturnNow = document.getElementById('btnReturnNow');

            if (data.return_url) {
                btnReturnNow.href = data.return_url;
            }

            if (data.is_correct) {
                resultCard.className =
                    'glass-panel w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border-2 border-emerald-400 p-8 text-center';
                iconWrapper.className =
                    'w-24 h-24 rounded-full mx-auto mb-5 flex items-center justify-center text-5xl bg-emerald-100 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-500/40';
                iconWrapper.innerHTML = '<i class="fa-solid fa-check"></i>';
                title.innerHTML = 'JAWABAN BENAR! 🎉';
                if (data.is_judge) {
                    const roleTitle = data.judge_title || 'Dewan Juri';
                    const boothPts = data.booth_points_earned || data.judge_vote_weight || 10;
                    const personalPts = data.points_earned || 1;
                    subtitle.innerHTML =
                        `Luar biasa <strong>${data.participant.name}</strong> (${roleTitle})! Vote Anda memberikan bobot <strong>+${boothPts} Poin</strong> untuk booth <strong>${data.booth.name}</strong>. Skor reward personal Anda bertambah <strong>+${personalPts} Poin</strong>.`;
                    pointsBox.className =
                        'p-4 rounded-2xl mb-5 bg-amber-50 border border-amber-300 text-amber-800 dark:bg-amber-500/20 dark:border-amber-500/40 dark:text-amber-300';
                    pointsLabel.innerHTML = `👑 Bobot Vote Booth (${roleTitle})`;
                    pointsValue.innerHTML = `+${boothPts} Poin Booth <div class="text-xs font-bold text-emerald-700 dark:text-emerald-400 mt-1">(Reward Pribadi: +${personalPts} Poin)</div>`;
                } else {
                    subtitle.innerHTML =
                        `Selamat <strong>${data.participant.name}</strong>! Anda mendapat <strong>+${data.points_earned} Poin</strong> dan <strong>${data.booth.name}</strong> juga mendapat <strong>+${data.points_earned} Poin</strong>.`;
                    pointsBox.className =
                        'p-4 rounded-2xl mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-500/20 dark:border-emerald-500/40 dark:text-emerald-300';
                    pointsLabel.innerHTML = 'Poin Diperoleh (Ganda)';
                    pointsValue.innerHTML = `+${data.points_earned} Poin`;
                }

                // Confetti meriah di atas layer modal (zIndex tinggi)
                confetti({
                    particleCount: 120,
                    spread: 80,
                    origin: {
                        y: 0.6
                    },
                    zIndex: 999999
                });

                // Ledakan confetti kiri & kanan tambahan
                setTimeout(() => {
                    confetti({
                        particleCount: 60,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0, y: 0.7 },
                        zIndex: 999999
                    });
                    confetti({
                        particleCount: 60,
                        angle: 120,
                        spread: 55,
                        origin: { x: 1, y: 0.7 },
                        zIndex: 999999
                    });
                }, 200);
            } else {
                resultCard.className =
                    'glass-panel w-full max-w-lg rounded-3xl overflow-hidden shadow-2xl border-2 border-rose-300 p-8 text-center';
                iconWrapper.className =
                    'w-24 h-24 rounded-full mx-auto mb-5 flex items-center justify-center text-5xl bg-rose-100 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/40';
                iconWrapper.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                title.innerHTML = 'JAWABAN KURANG TEPAT';
                const correctLabel = data.correct_answer_display || data.correct_answer || '';
                const correctText = data.correct_answer_text ? ` (${data.correct_answer_text})` : '';
                subtitle.innerHTML =
                    `Kunci jawaban yang tepat adalah opsi <strong>${correctLabel}</strong>${correctText}. Poin tidak bertambah.`;

                pointsBox.className =
                    'p-4 rounded-2xl mb-5 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400';
                pointsLabel.innerHTML = 'Poin Diperoleh';
                pointsValue.innerHTML = '0 Poin';
            }

            if (data.explanation) {
                explBox.classList.remove('hidden');
                explText.innerHTML = data.explanation;
            }

            modal.classList.remove('hidden');

            // Countdown 5 detik untuk otomatis redirect kembali ke standby booth
            let countdown = 5;
            const timerEl = document.getElementById('countdownTimer');
            const interval = setInterval(() => {
                countdown--;
                if (timerEl) timerEl.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = data.return_url || `/pool-qcc/booth/${boothId}`;
                }
            }, 1000);
        }
    </script>
@endsection
