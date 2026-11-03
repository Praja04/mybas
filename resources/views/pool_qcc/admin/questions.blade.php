@extends('pool_qcc.layout')

@section('title', 'Bank Pertanyaan Booth - Pool QCC')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Page -->
        <div
            class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div
                    class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                    <a href="{{ route('pool-qcc.admin.dashboard') }}" class="hover:underline">Admin</a>
                    <span>&bull;</span>
                    <span class="text-slate-500">Bank Soal</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Bank Pertanyaan Booth</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola pertanyaan kuis untuk setiap booth.
                    Pastikan tiap booth memiliki minimal 5 pertanyaan aktif (Default 1 Poin per soal).</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <button type="button" onclick="$('#modalAddQuestion').removeClass('hidden')"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-md shadow-amber-500/20 transition flex items-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Pertanyaan Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar & Booth Status Summary -->
        <div class="glass-panel rounded-2xl p-4 sm:p-5 mb-8">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3 flex-1">
                    <label for="filter_booth"
                        class="text-xs font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">Filter Booth:</label>
                    <select name="booth_id" id="filter_booth"
                        class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-indigo-500 flex-1 max-w-md shadow-sm">
                        <option value="">-- Semua Booth --</option>
                        @foreach ($booths as $b)
                            <option value="{{ $b->id }}" {{ ($selectedBoothId == $b->id) ? 'selected' : '' }}>
                                [{{ $b->code }}] {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="btnResetFilter"
                        class="p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white cursor-pointer {{ empty($selectedBoothId) ? 'hidden' : '' }}"
                        title="Reset Filter">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Selected Booth Status Summary (client-side dynamic) -->
                <div id="selectedBoothSummary" class="flex items-center space-x-2"></div>
            </div>
        </div>

        <!-- Questions List Container (Pure Client-side rendered via jQuery) -->
        <div id="questionsContainer" class="space-y-4">
            <!-- Loading Skeletons will appear here on initial load -->
        </div>

        <!-- Custom Tailwind Pagination Container (Pure Client-side rendered via jQuery) -->
        <div id="questionsPagination" class="mt-6"></div>
    </div>
@endsection

@section('modals')
    <!-- Modal Tambah Pertanyaan -->
    <div id="modalAddQuestion" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 9000;">
        <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
            <div class="glass-panel w-full max-w-2xl rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col relative"
                style="max-height: calc(100vh - 2rem);">
                <!-- Header (Fixed at top of modal card) -->
                <div
                    class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900 z-10">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fa-solid fa-circle-question text-amber-500 mr-2.5"></i> Tambah Pertanyaan Baru
                    </h3>
                    <button type="button" onclick="$('#modalAddQuestion').addClass('hidden')"
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Form Body & Fixed Footer -->
                <form id="formAddQuestion" action="{{ route('pool-qcc.admin.questions.store') }}" method="POST"
                    enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <div class="p-5 overflow-y-auto space-y-4 text-sm flex-1 overscroll-contain">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Booth
                                *</label>
                            <select name="booth_id" id="add_q_booth_id" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 text-sm shadow-sm">
                                <option value="">-- Pilih Booth --</option>
                                @foreach ($booths as $b)
                                    <option value="{{ $b->id }}"
                                        {{ $selectedBoothId == $b->id ? 'selected' : '' }}>
                                        [{{ $b->code }}] {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teks Pertanyaan
                                *</label>
                            <textarea name="question_text" rows="3" required
                                placeholder="Tuliskan pertanyaan yang berkaitan dengan inovasi/lingkup booth ini..."
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan A
                                    *</label>
                                <input type="text" name="option_a" required placeholder="Jawaban A"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan B
                                    *</label>
                                <input type="text" name="option_b" required placeholder="Jawaban B"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan C
                                    *</label>
                                <input type="text" name="option_c" required placeholder="Jawaban C"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan D
                                    *</label>
                                <input type="text" name="option_d" required placeholder="Jawaban D"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan E
                                    (Opsional)</label>
                                <input type="text" name="option_e" placeholder="Jawaban E (boleh kosong)"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 mb-1">Kunci
                                    Jawaban Benar *</label>
                                <select name="correct_answer" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-500 text-emerald-900 dark:text-emerald-200 font-bold focus:outline-none text-sm shadow-sm">
                                    <option value="A">Pilihan A</option>
                                    <option value="B">Pilihan B</option>
                                    <option value="C">Pilihan C</option>
                                    <option value="D">Pilihan D</option>
                                    <option value="E">Pilihan E</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bobot Poin
                                    (Default 1) *</label>
                                <input type="number" name="points" value="1" min="1" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-bold focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Gambar Soal
                                    (Opsional)</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Penjelasan /
                                Pembahasan (Opsional)</label>
                            <textarea name="explanation" rows="2"
                                placeholder="Catatan pembahasan yang akan muncul setelah peserta menjawab..."
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                        </div>

                        <div class="flex items-center space-x-2 pt-2">
                            <input type="checkbox" name="is_active" id="q_is_active_new" value="1" checked
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="q_is_active_new"
                                class="text-xs text-slate-700 dark:text-slate-300 font-medium">Pertanyaan Aktif (Dapat
                                Diundi)</label>
                        </div>
                    </div>

                    <!-- Footer (Fixed at bottom of modal card) -->
                    <div
                        class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3 flex-shrink-0 bg-slate-50 dark:bg-slate-800/80 z-10">
                        <button type="button" onclick="$('#modalAddQuestion').addClass('hidden')"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-950 bg-amber-400 hover:bg-amber-300 shadow-md transition cursor-pointer">Simpan
                            Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pertanyaan -->
    <div id="modalEditQuestion" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 9000;">
        <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
            <div class="glass-panel w-full max-w-2xl rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col relative"
                style="max-height: calc(100vh - 2rem);">
                <!-- Header (Fixed at top of modal card) -->
                <div
                    class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900 z-10">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fa-solid fa-pen-to-square text-cyan-600 dark:text-cyan-400 mr-2.5"></i> Edit Pertanyaan
                    </h3>
                    <button type="button" onclick="$('#modalEditQuestion').addClass('hidden')"
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Form Body & Fixed Footer -->
                <form id="formEditQuestion" action="" method="POST" enctype="multipart/form-data"
                    class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="p-5 overflow-y-auto space-y-4 text-sm flex-1 overscroll-contain">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Booth
                                *</label>
                            <select name="booth_id" id="edit_q_booth_id" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 text-sm shadow-sm">
                                @foreach ($booths as $b)
                                    <option value="{{ $b->id }}">[{{ $b->code }}] {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Teks Pertanyaan
                                *</label>
                            <textarea id="edit_question_text" name="question_text" rows="3" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan A
                                    *</label>
                                <input type="text" id="edit_option_a" name="option_a" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan B
                                    *</label>
                                <input type="text" id="edit_option_b" name="option_b" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan C
                                    *</label>
                                <input type="text" id="edit_option_c" name="option_c" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan D
                                    *</label>
                                <input type="text" id="edit_option_d" name="option_d" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilihan E
                                    (Opsional)</label>
                                <input type="text" id="edit_option_e" name="option_e"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-emerald-700 dark:text-emerald-400 mb-1">Kunci
                                    Jawaban Benar *</label>
                                <select id="edit_correct_answer" name="correct_answer" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-400 dark:border-emerald-500 text-emerald-900 dark:text-emerald-200 font-bold focus:outline-none text-sm shadow-sm">
                                    <option value="A">Pilihan A</option>
                                    <option value="B">Pilihan B</option>
                                    <option value="C">Pilihan C</option>
                                    <option value="D">Pilihan D</option>
                                    <option value="E">Pilihan E</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Bobot Poin
                                    *</label>
                                <input type="number" id="edit_points" name="points" min="1" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-bold focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Gambar
                                    Soal (Opsional)</label>
                                <input type="file" name="image" accept="image/*"
                                    class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 dark:file:bg-slate-800 file:text-slate-700 dark:file:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Penjelasan /
                                Pembahasan (Opsional)</label>
                            <textarea id="edit_explanation" name="explanation" rows="2"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                        </div>

                        <div class="flex items-center space-x-2 pt-2">
                            <input type="checkbox" name="is_active" id="edit_q_is_active" value="1"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="edit_q_is_active"
                                class="text-xs text-slate-700 dark:text-slate-300 font-medium">Pertanyaan Aktif</label>
                        </div>
                    </div>

                    <!-- Footer (Fixed at bottom of modal card) -->
                    <div
                        class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3 flex-shrink-0 bg-slate-50 dark:bg-slate-800/80 z-10">
                        <button type="button" onclick="$('#modalEditQuestion').addClass('hidden')"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-md transition cursor-pointer">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
let questionsCache = [];
let currentPage = 1;
let currentBoothId = "{{ $selectedBoothId ?? '' }}";

function escapeHtml(str) {
    if (!str) return '';
    return $('<div>').text(str).html();
}

function escapeAttr(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

const assetBaseUrl = "{{ asset('') }}";
function getImageUrl(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    return assetBaseUrl + path.replace(/^\/+/, '');
}

function renderSkeletonQuestions() {
    let skeletons = '';
    for (let i = 0; i < 4; i++) {
        skeletons += `
            <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 animate-pulse space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-200 dark:bg-slate-800"></div>
                    <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-1/4"></div>
                </div>
                <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div class="h-10 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                    <div class="h-10 bg-slate-200 dark:bg-slate-800 rounded-xl"></div>
                </div>
            </div>
        `;
    }
    $('#questionsContainer').html(skeletons);
    $('#questionsPagination').empty();
}

function updateBoothSummary(totalCount) {
    if (!currentBoothId) {
        $('#selectedBoothSummary').empty();
        return;
    }

    if (totalCount >= 5) {
        $('#selectedBoothSummary').html(`
            <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border-emerald-500/30 flex items-center space-x-1.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                <span>Syarat Minimal Terpenuhi (${totalCount} / 5 Soal)</span>
            </span>
        `);
    } else {
        const needed = 5 - totalCount;
        $('#selectedBoothSummary').html(`
            <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:border-amber-500/30 flex items-center space-x-1.5 shadow-sm">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400"></i>
                <span>Belum Cukup Soal (${totalCount} / 5 Soal) - Butuh ${needed} soal lagi!</span>
            </span>
        `);
    }
}

function renderPagination(pagination) {
    if (!pagination || pagination.total === 0) {
        $('#questionsPagination').empty();
        return;
    }

    const current = pagination.current_page;
    const last = pagination.last_page;
    const from = pagination.from || 0;
    const to = pagination.to || 0;
    const total = pagination.total || 0;

    let pagesHtml = '';

    // Tombol Sebelumnya
    if (current > 1) {
        pagesHtml += `
            <button type="button" onclick="goToPage(${current - 1})"
                class="px-3 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-sm transition flex items-center gap-1 cursor-pointer">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                <span class="hidden sm:inline">Sebelumnya</span>
            </button>
        `;
    } else {
        pagesHtml += `
            <span class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-800 cursor-not-allowed flex items-center gap-1">
                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                <span class="hidden sm:inline">Sebelumnya</span>
            </span>
        `;
    }

    // Numbered buttons
    let startPage = Math.max(1, current - 2);
    let endPage = Math.min(last, current + 2);

    if (startPage > 1) {
        pagesHtml += `<button type="button" onclick="goToPage(1)" class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">1</button>`;
        if (startPage > 2) {
            pagesHtml += `<span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">...</span>`;
        }
    }

    for (let p = startPage; p <= endPage; p++) {
        if (p === current) {
            pagesHtml += `
                <span class="w-8 h-8 rounded-xl text-xs font-black bg-indigo-600 text-white shadow-md shadow-indigo-600/30 flex items-center justify-center">
                    ${p}
                </span>
            `;
        } else {
            pagesHtml += `
                <button type="button" onclick="goToPage(${p})"
                    class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    ${p}
                </button>
            `;
        }
    }

    if (endPage < last) {
        if (endPage < last - 1) {
            pagesHtml += `<span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">...</span>`;
        }
        pagesHtml += `<button type="button" onclick="goToPage(${last})" class="w-8 h-8 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">${last}</button>`;
    }

    // Tombol Berikutnya
    if (current < last) {
        pagesHtml += `
            <button type="button" onclick="goToPage(${current + 1})"
                class="px-3 py-2 rounded-xl text-xs font-bold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-sm transition flex items-center gap-1 cursor-pointer">
                <span class="hidden sm:inline">Berikutnya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </button>
        `;
    } else {
        pagesHtml += `
            <span class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800/50 text-slate-400 dark:text-slate-600 border border-slate-200 dark:border-slate-800 cursor-not-allowed flex items-center gap-1">
                <span class="hidden sm:inline">Berikutnya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </span>
        `;
    }

    const html = `
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl glass-panel">
            <div class="text-xs text-slate-600 dark:text-slate-400">
                Menampilkan <span class="font-bold text-slate-900 dark:text-white">${from}</span> sampai <span class="font-bold text-slate-900 dark:text-white">${to}</span> dari <span class="font-bold text-slate-900 dark:text-white">${total}</span> soal
            </div>
            <div class="flex items-center space-x-1.5">
                ${pagesHtml}
            </div>
        </div>
    `;

    $('#questionsPagination').html(html);
}

function goToPage(page) {
    currentPage = page;
    loadQuestionsData(page, currentBoothId);
    $('html, body').animate({ scrollTop: $('#questionsContainer').offset().top - 80 }, 200);
}

function loadQuestionsData(page = 1, boothId = '') {
    renderSkeletonQuestions();

    $.ajax({
        url: "{{ route('pool-qcc.admin.api.questions') }}",
        type: 'GET',
        data: {
            page: page,
            booth_id: boothId
        },
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            questionsCache = response.data || [];
            const pagination = response.pagination || {};
            currentPage = pagination.current_page || page;

            updateBoothSummary(pagination.total || 0);

            if (questionsCache.length === 0) {
                $('#questionsContainer').html(`
                    <div class="py-16 text-center glass-panel rounded-2xl">
                        <i class="fa-solid fa-clipboard-question text-5xl text-slate-400 mb-3 block"></i>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Belum Ada Pertanyaan</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Klik tombol "Tambah Pertanyaan Baru" untuk mengisi bank soal bagi booth ini.</p>
                    </div>
                `);
                $('#questionsPagination').empty();
                return;
            }

            let questionsHtml = '';
            const startIndex = pagination.from || 1;

            questionsCache.forEach(function(q, idx) {
                const itemNum = startIndex + idx;
                const boothCode = q.booth ? escapeHtml(q.booth.code) : 'N/A';
                const boothName = q.booth ? escapeHtml(q.booth.name) : 'Booth';
                const points = q.points || 1;
                const questionText = escapeHtml(q.question_text);
                const explanation = q.explanation ? escapeHtml(q.explanation) : '';

                let statusBadge = '';
                if (!q.is_active) {
                    statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">Nonaktif</span>';
                }

                let imgHtml = '';
                if (q.image) {
                    imgHtml = `
                        <div class="mt-3 mb-2 max-w-md rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
                            <img src="${getImageUrl(q.image)}" alt="Lampiran Pertanyaan" class="max-h-48 w-auto object-cover">
                        </div>
                    `;
                }

                // Render options A, B, C, D, E
                const opts = [
                    { key: 'A', text: q.option_a },
                    { key: 'B', text: q.option_b },
                    { key: 'C', text: q.option_c },
                    { key: 'D', text: q.option_d },
                ];
                if (q.option_e) {
                    opts.push({ key: 'E', text: q.option_e, isSpan2: true });
                }

                let optionsHtml = '';
                opts.forEach(function(opt) {
                    const isCorrect = q.correct_answer === opt.key;
                    const containerClass = isCorrect
                        ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold dark:bg-emerald-500/20 dark:border-emerald-500/50 dark:text-emerald-200'
                        : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-900/60 dark:border-slate-800 dark:text-slate-300';
                    const badgeClass = isCorrect
                        ? 'bg-emerald-600 text-white'
                        : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-400';
                    const spanClass = opt.isSpan2 ? 'sm:col-span-2' : '';
                    const checkIcon = isCorrect
                        ? '<i class="fa-solid fa-circle-check ml-auto text-emerald-600 dark:text-emerald-400 text-base"></i>'
                        : '';

                    optionsHtml += `
                        <div class="p-3 rounded-xl border flex items-center space-x-2.5 ${containerClass} ${spanClass}">
                            <span class="w-6 h-6 rounded-lg flex items-center justify-center font-black ${badgeClass}">${opt.key}</span>
                            <span>${escapeHtml(opt.text)}</span>
                            ${checkIcon}
                        </div>
                    `;
                });

                let explanationHtml = '';
                if (explanation) {
                    explanationHtml = `
                        <div class="mt-3.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 text-xs text-slate-600 dark:text-slate-400 flex items-start space-x-2">
                            <i class="fa-solid fa-lightbulb text-amber-500 mt-0.5"></i>
                            <span><strong>Penjelasan:</strong> ${explanation}</span>
                        </div>
                    `;
                }

                questionsHtml += `
                    <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div class="flex items-start space-x-3">
                                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5 border border-indigo-200 dark:border-indigo-500/30">
                                    #${itemNum}
                                </span>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold bg-indigo-100 text-indigo-800 dark:bg-indigo-600/30 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-500/30">
                                            ${boothCode}
                                        </span>
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300">${boothName}</span>
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                            +${points} Poin
                                        </span>
                                        ${statusBadge}
                                    </div>
                                    <h4 class="text-base font-bold text-slate-900 dark:text-white leading-relaxed">
                                        ${questionText}
                                    </h4>
                                    ${imgHtml}
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 self-end md:self-start">
                                <button type="button" onclick="editQuestionById(${q.id})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:hover:text-white border border-slate-200 dark:border-slate-700 transition flex items-center space-x-1 cursor-pointer">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </button>
                                <button type="button" onclick="confirmDeleteQuestion(${q.id})"
                                    class="px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 transition cursor-pointer"
                                    title="Hapus Soal">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Options Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-4 text-xs">
                            ${optionsHtml}
                        </div>

                        ${explanationHtml}
                    </div>
                `;
            });

            $('#questionsContainer').html(questionsHtml);
            renderPagination(pagination);
        },
        error: function(xhr) {
            $('#questionsContainer').html(`
                <div class="py-12 text-center text-rose-500 glass-panel rounded-2xl">
                    <i class="fa-solid fa-triangle-exclamation text-4xl mb-2"></i>
                    <p class="font-bold">Gagal memuat daftar pertanyaan</p>
                    <button onclick="loadQuestionsData(currentPage, currentBoothId)" class="mt-3 px-4 py-1.5 text-xs bg-indigo-600 text-white rounded-xl cursor-pointer">Coba Lagi</button>
                </div>
            `);
            $('#questionsPagination').empty();
        }
    });
}

function editQuestionById(id) {
    const q = questionsCache.find(item => item.id == id);
    if (!q) return;

    $('#formEditQuestion').find('.border-rose-500').removeClass('border-rose-500');
    $('#formEditQuestion').find('.field-error-msg').remove();

    $('#formEditQuestion').attr('action', `/pool-qcc/admin/questions/${q.id}`);
    $('#edit_q_booth_id').val(q.booth_id);
    $('#edit_question_text').val(q.question_text);
    $('#edit_option_a').val(q.option_a);
    $('#edit_option_b').val(q.option_b);
    $('#edit_option_c').val(q.option_c);
    $('#edit_option_d').val(q.option_d);
    $('#edit_option_e').val(q.option_e || '');
    $('#edit_correct_answer').val(q.correct_answer);
    $('#edit_points').val(q.points || 1);
    $('#edit_explanation').val(q.explanation || '');
    $('#edit_q_is_active').prop('checked', Boolean(q.is_active));

    $('#modalEditQuestion').removeClass('hidden');
}

function handleQuestionAjaxSubmit(formSelector, isEdit = false) {
    const $form = $(formSelector);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnText = $submitBtn.html();

    $form.find('.border-rose-500').removeClass('border-rose-500');
    $form.find('.field-error-msg').remove();

    $submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Menyimpan...');

    const formData = new FormData($form[0]);

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            $submitBtn.prop('disabled', false).html(originalBtnText);

            if (isEdit) {
                $('#modalEditQuestion').addClass('hidden');
            } else {
                $('#modalAddQuestion').addClass('hidden');
                $form[0].reset();
                if (currentBoothId) {
                    $('#add_q_booth_id').val(currentBoothId);
                }
            }

            // Client-side reload via jQuery
            loadQuestionsData(currentPage, currentBoothId);

            Swal.fire({
                icon: 'success',
                title: isEdit ? 'Berhasil Diperbarui!' : 'Soal Ditambahkan!',
                text: response.message || 'Pertanyaan berhasil disimpan.',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800'
                }
            });
        },
        error: function(xhr) {
            $submitBtn.prop('disabled', false).html(originalBtnText);

            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                let errorHtml = '<ul class="text-left text-xs text-rose-600 dark:text-rose-400 space-y-1.5 mt-2 list-disc list-inside">';

                for (const field in errors) {
                    const message = errors[field][0];
                    errorHtml += `<li><b>${field.toUpperCase()}</b>: ${message}</li>`;

                    const $input = $form.find(`[name="${field}"]`);
                    if ($input.length) {
                        $input.addClass('border-rose-500 focus:border-rose-500');
                        $input.after(`<p class="field-error-msg text-[11px] text-rose-500 font-bold mt-1 flex items-center"><i class="fa-solid fa-circle-exclamation mr-1"></i> ${message}</p>`);
                    }
                }
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal!',
                    html: `Periksa kolom yang ditandai merah:<br>${errorHtml}`,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Perbaiki',
                    customClass: {
                        popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800',
                        confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses soal.',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800',
                        confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs'
                    }
                });
            }
        }
    });
}

function confirmDeleteQuestion(id) {
    Swal.fire({
        title: 'Hapus Soal?',
        html: `Apakah Anda yakin ingin menghapus soal ini dari bank pertanyaan booth?<br><span class="text-rose-500 text-xs font-semibold mt-1.5 block">Tindakan ini tidak dapat dibatalkan.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: `/pool-qcc/admin/questions/${id}`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).catch(xhr => {
                Swal.showValidationMessage(xhr.responseJSON?.message || 'Gagal menghapus pertanyaan.');
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
        customClass: {
            popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800',
            confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs',
            cancelButton: 'rounded-xl font-bold px-4 py-2 text-xs'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Client-side reload via jQuery
            loadQuestionsData(currentPage, currentBoothId);

            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: result.value?.message || 'Pertanyaan berhasil dihapus.',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl dark:bg-slate-900 dark:text-white border border-slate-200 dark:border-slate-800'
                }
            });
        }
    });
}

$(document).ready(function() {
    // Initial client-side load via jQuery
    loadQuestionsData(currentPage, currentBoothId);

    // Filter booth change
    $('#filter_booth').on('change', function() {
        currentBoothId = $(this).val();
        currentPage = 1;

        if (currentBoothId) {
            $('#btnResetFilter').removeClass('hidden');
            $('#add_q_booth_id').val(currentBoothId);
        } else {
            $('#btnResetFilter').addClass('hidden');
        }

        const url = new URL(window.location.href);
        if (currentBoothId) {
            url.searchParams.set('booth_id', currentBoothId);
        } else {
            url.searchParams.delete('booth_id');
        }
        url.searchParams.delete('page');
        window.history.pushState({}, '', url.toString());

        loadQuestionsData(1, currentBoothId);
    });

    // Reset filter button
    $('#btnResetFilter').on('click', function() {
        $('#filter_booth').val('').trigger('change');
    });

    $('#formAddQuestion').on('submit', function(e) {
        e.preventDefault();
        handleQuestionAjaxSubmit('#formAddQuestion', false);
    });

    $('#formEditQuestion').on('submit', function(e) {
        e.preventDefault();
        handleQuestionAjaxSubmit('#formEditQuestion', true);
    });
});
</script>
@endsection
