@extends('pool_qcc.layout')

@section('title', 'Manajemen Booth - Pool QCC')

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
                    <span class="text-slate-500">Master Data</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Manajemen Booth QCC</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftarkan booth peserta exhibition, atur profil
                    booth, dan pastikan minimal 5 pertanyaan tersedia.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <button type="button" onclick="openImportBoothModal()"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 transition flex items-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-file-excel text-xs"></i>
                    <span>Upload Excel</span>
                </button>
                <button onclick="$('#modalAddBooth').removeClass('hidden')"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition flex items-center space-x-2 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Booth Baru</span>
                </button>
            </div>
        </div>

        <!-- Alert / Requirements Note -->
        <div
            class="mb-6 p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-300 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-2.5">
                <i class="fa-solid fa-circle-info text-base text-indigo-600 dark:text-indigo-400"></i>
                <span><strong>Ketentuan:</strong> Setiap booth harus memiliki <strong>minimal 5 pertanyaan aktif</strong>
                    agar dapat dipilih dan di-randomize saat peserta melakukan pooling.</span>
            </div>
            <span id="totalBoothBadge"
                class="px-3 py-1 rounded-xl bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300 font-bold">Total: Memuat...</span>
        </div>

        <!-- Booths Grid Container (Pure Client-side rendered via jQuery) -->
        <div id="boothsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Loading Skeletons will appear here on initial load -->
        </div>
    </div>
@endsection

@section('modals')
    <!-- Modal Import Excel Booth -->
    <div id="modalImportBooth" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 9000;">
        <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
            <div class="glass-panel w-full max-w-lg rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col relative animate-scale-up"
                style="max-height: calc(100vh - 2rem);">
                <!-- Header -->
                <div class="px-5 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white flex items-center justify-between flex-shrink-0">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-file-excel"></i>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-base leading-tight">Import Data Booth dari Excel</h3>
                            <p class="text-[11px] text-white/80">Upload massal data booth & inovasi QCC</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeImportBoothModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white text-xs transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Body -->
                <form id="formImportBooth" onsubmit="handleImportBoothSubmit(event)" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto">
                    <!-- Template Download Card -->
                    <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="font-extrabold text-emerald-900 dark:text-emerald-200 block mb-0.5">Format Template Import</span>
                                <p class="text-[11px] text-emerald-700 dark:text-emerald-300">
                                    Gunakan template resmi agar format kolom dan data terbaca sempurna oleh sistem.
                                </p>
                            </div>
                            <a href="{{ route('pool-qcc.admin.booths.template') }}"
                                class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm transition flex items-center space-x-1.5 flex-shrink-0 cursor-pointer">
                                <i class="fa-solid fa-download text-[11px]"></i>
                                <span>Download Template</span>
                            </a>
                        </div>
                    </div>

                    <!-- File Input Box -->
                    <div>
                        <span class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                            Pilih File Excel / CSV (.xlsx, .xls, .csv) <span class="text-rose-500">*</span>:
                        </span>
                        <label for="importBoothFileInput"
                            class="block relative border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl p-6 text-center hover:border-emerald-500 dark:hover:border-emerald-500 transition group cursor-pointer bg-slate-50/50 dark:bg-slate-800/40">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 group-hover:text-emerald-500 transition-colors mb-2 block"></i>
                            <span id="importBoothFileName" class="text-xs font-bold text-slate-700 dark:text-slate-300 block">
                                Klik untuk memilih file Excel dari komputer Anda
                            </span>
                            <span class="text-[11px] text-slate-400 block mt-1">Maksimal ukuran file: 5MB</span>
                            <input type="file" id="importBoothFileInput" name="excel_file" accept=".xlsx,.xls,.csv" required class="hidden"
                                onchange="onBoothFileSelected(this)" onclick="event.stopPropagation()">
                        </label>
                    </div>

                    <!-- Notes -->
                    <div class="text-[11px] text-slate-400 space-y-1">
                        <p>&bull; Kolom <strong>Nama Booth</strong> dan <strong>Departemen</strong> wajib diisi.</p>
                        <p>&bull; <strong>Kode Booth</strong> boleh dikosongkan untuk digenerate otomatis (B01, B02, dst).</p>
                    </div>

                    <!-- Footer -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeImportBoothModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitImportBooth" class="px-5 py-2.5 rounded-xl text-xs font-extrabold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/30 transition flex items-center space-x-2 cursor-pointer">
                            <i class="fa-solid fa-upload"></i>
                            <span>Upload & Proses Import</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Booth -->
    <div id="modalAddBooth" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 9000;">
        <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
            <div class="glass-panel w-full max-w-lg rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col relative"
                style="max-height: calc(100vh - 2rem);">
                <!-- Header (Fixed at top of modal card) -->
                <div
                    class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900 z-10">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fa-solid fa-store text-indigo-600 dark:text-indigo-400 mr-2.5"></i> Tambah Booth QCC Baru
                    </h3>
                    <button type="button" onclick="$('#modalAddBooth').addClass('hidden')"
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Form Body & Fixed Footer -->
                <form id="formAddBooth" action="{{ route('pool-qcc.admin.booths.store') }}" method="POST"
                    enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <div class="p-5 overflow-y-auto space-y-4 text-sm flex-1 overscroll-contain">
                        <!-- Auto-generated Code Badge -->
                        <div
                            class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 dark:bg-indigo-950/40 dark:border-indigo-500/30 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">Kode Booth
                                    (Auto-Generated Backend)</span>
                                <span class="text-sm font-black text-indigo-700 dark:text-indigo-300">Akan dibuat otomatis:
                                    {{ $nextCode }}</span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-600 text-white"><i
                                    class="fa-solid fa-wand-magic-sparkles mr-1"></i> Auto</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Booth /
                                Judul Inovasi *</label>
                            <input type="text" name="name" placeholder="Misal: Circle Kaizen Line Produksi" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Departemen
                                    *</label>
                                <select name="category" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama PIC
                                    Booth</label>
                                <input type="text" name="pic_name" placeholder="Nama penanggung jawab"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi
                                Inovasi / Booth</label>
                            <textarea name="description" rows="3"
                                placeholder="Jelaskan secara singkat inovasi atau tema yang diangkat oleh booth ini..."
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Foto Booth /
                                Poster (Opsional)</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                        </div>

                        <div class="flex items-center space-x-2 pt-2">
                            <input type="checkbox" name="is_active" id="is_active_new" value="1" checked
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="is_active_new"
                                class="text-xs text-slate-700 dark:text-slate-300 font-medium">Booth Aktif & Siap Menerima
                                Pooling</label>
                        </div>
                    </div>

                    <!-- Footer (Fixed at bottom of modal card) -->
                    <div
                        class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3 flex-shrink-0 bg-slate-50 dark:bg-slate-800/80 z-10">
                        <button type="button" onclick="$('#modalAddBooth').addClass('hidden')"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition cursor-pointer">Simpan
                            Booth</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Booth -->
    <div id="modalEditBooth" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 9000;">
        <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
            <div class="glass-panel w-full max-w-lg rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col relative"
                style="max-height: calc(100vh - 2rem);">
                <!-- Header (Fixed at top of modal card) -->
                <div
                    class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900 z-10">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center">
                        <i class="fa-solid fa-pen-to-square text-cyan-600 dark:text-cyan-400 mr-2.5"></i> Edit Data Booth
                    </h3>
                    <button type="button" onclick="$('#modalEditBooth').addClass('hidden')"
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Form Body & Fixed Footer -->
                <form id="formEditBooth" action="" method="POST" enctype="multipart/form-data"
                    class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="p-5 overflow-y-auto space-y-4 text-sm flex-1 overscroll-contain">
                        <!-- Code Display (Readonly) -->
                        <div
                            class="p-3.5 rounded-2xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold text-slate-500 block">Kode Booth (Tetap)</span>
                                <span id="edit_code_badge"
                                    class="text-base font-black text-indigo-600 dark:text-indigo-400">B01</span>
                            </div>
                            <span class="text-xs text-slate-400 font-mono"><i class="fa-solid fa-lock text-xs mr-1"></i>
                                Locked</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Booth /
                                Judul Inovasi *</label>
                            <input type="text" id="edit_name" name="name" required
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Departemen
                                    *</label>
                                <select id="edit_category" name="category" required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama PIC
                                    Booth</label>
                                <input type="text" id="edit_pic_name" name="pic_name"
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi
                                Inovasi / Booth</label>
                            <textarea id="edit_description" name="description" rows="3"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Foto Booth
                                (Kosongkan jika tidak diubah)</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                        </div>

                        <div class="flex items-center space-x-2 pt-2">
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="edit_is_active"
                                class="text-xs text-slate-700 dark:text-slate-300 font-medium">Booth Aktif & Siap Menerima
                                Pooling</label>
                        </div>
                    </div>

                    <!-- Footer (Fixed at bottom of modal card) -->
                    <div
                        class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3 flex-shrink-0 bg-slate-50 dark:bg-slate-800/80 z-10">
                        <button type="button" onclick="$('#modalEditBooth').addClass('hidden')"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition cursor-pointer">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
let boothsCache = [];

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

function renderSkeleton() {
    let skeletons = '';
    for (let i = 0; i < 6; i++) {
        skeletons += `
            <div class="glass-card rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 animate-pulse">
                <div class="h-44 bg-slate-200 dark:bg-slate-800"></div>
                <div class="p-5 space-y-3">
                    <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-3/4"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-full"></div>
                    <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                    <div class="h-10 bg-slate-200 dark:bg-slate-800 rounded mt-4"></div>
                </div>
            </div>
        `;
    }
    $('#boothsGrid').html(skeletons);
}

function loadBoothsData() {
    renderSkeleton();

    $.ajax({
        url: "{{ route('pool-qcc.admin.api.booths') }}",
        type: 'GET',
        dataType: 'json',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        success: function(response) {
            boothsCache = response.data || [];
            const total = response.total !== undefined ? response.total : boothsCache.length;
            $('#totalBoothBadge').text('Total: ' + total + ' Booth');

            if (boothsCache.length === 0) {
                $('#boothsGrid').html(`
                    <div class="col-span-full py-16 text-center glass-panel rounded-2xl">
                        <i class="fa-solid fa-store-slash text-5xl text-slate-400 mb-3 block"></i>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Belum Ada Booth Terdaftar</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Klik tombol "Tambah Booth Baru" di atas untuk mendaftarkan booth QCC dan mulai mengisi 5 pertanyaan bank soal.</p>
                    </div>
                `);
                return;
            }

            let cardsHtml = '';
            boothsCache.forEach(function(b) {
                const activeCount = b.active_questions_count !== undefined ? parseInt(b.active_questions_count) : 0;
                const isComplete = activeCount >= 5;
                const borderClass = isComplete ? 'border-slate-200 dark:border-slate-800' : 'border-amber-400 dark:border-amber-500/50';

                let imgHtml = '';
                if (b.image) {
                    const imgUrl = getImageUrl(b.image);
                    imgHtml = `<img src="${imgUrl}" alt="${escapeAttr(b.name)}" class="w-full h-full object-cover">`;
                } else {
                    imgHtml = `
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-tr from-slate-100 to-indigo-50 dark:from-slate-900 dark:to-indigo-950/60 text-slate-400">
                            <i class="fa-solid fa-store text-5xl mb-2 text-indigo-400/50"></i>
                            <span class="text-xs text-slate-500 font-medium">Belum ada foto booth</span>
                        </div>
                    `;
                }

                let statusBadgeHtml = '';
                if (isComplete) {
                    statusBadgeHtml = `
                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-md flex items-center space-x-1">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            <span>${activeCount} Soal</span>
                        </span>
                    `;
                } else {
                    statusBadgeHtml = `
                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 shadow-md flex items-center space-x-1">
                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                            <span>${activeCount}/5 Soal</span>
                        </span>
                    `;
                }

                const totalPoints = Number(b.total_points || 0).toLocaleString();
                const totalVoters = Number(b.total_voters || 0).toLocaleString();
                const category = escapeHtml(b.category || 'General');
                const name = escapeHtml(b.name);
                const desc = escapeHtml(b.description || 'Tidak ada deskripsi booth.');
                const pic = escapeHtml(b.pic_name || '-');
                const questionsUrl = `{{ url('/pool-qcc/admin/questions') }}?booth_id=${b.id}`;

                cardsHtml += `
                    <div class="glass-card rounded-2xl overflow-hidden flex flex-col justify-between border ${borderClass}">
                        <!-- Card Header / Image -->
                        <div class="relative h-44 bg-slate-100 dark:bg-slate-900 overflow-hidden">
                            ${imgHtml}
                            <!-- Code Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="px-3 py-1 rounded-xl text-xs font-black bg-slate-900/90 text-white backdrop-blur shadow-sm tracking-wider">
                                    ${escapeHtml(b.code)}
                                </span>
                            </div>
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3 flex items-center space-x-1">
                                ${statusBadgeHtml}
                            </div>
                            <!-- Category overlay -->
                            <div class="absolute bottom-3 left-3">
                                <span class="text-xs px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white font-bold shadow-sm backdrop-blur">
                                    ${category}
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 leading-snug">
                                    ${name}
                                </h3>
                                <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">
                                    ${desc}
                                </p>

                                <div class="text-xs text-slate-700 dark:text-slate-300 py-2 border-y border-slate-100 dark:border-slate-800 mb-4 flex items-center justify-between">
                                    <span class="text-slate-500 text-xs">PIC Booth:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">${pic}</span>
                                </div>

                                <!-- Scores & Voters -->
                                <div class="flex items-center justify-between text-xs mb-4 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                                    <div>
                                        <span class="text-slate-500 block text-[11px] font-medium">Total Skor Poin:</span>
                                        <span class="text-base font-black text-emerald-600 dark:text-emerald-400">${totalPoints} Pts</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-slate-500 block text-[11px] font-medium">Total Peserta Pool:</span>
                                        <span class="text-base font-black text-indigo-600 dark:text-indigo-400">${totalVoters} Peserta</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2 pt-2">
                                <a href="${questionsUrl}"
                                    class="flex-1 py-2 px-3 rounded-xl text-xs font-bold text-center bg-indigo-600 hover:bg-indigo-500 text-white transition flex items-center justify-center space-x-1 shadow-sm">
                                    <i class="fa-solid fa-clipboard-question mr-1"></i>
                                    <span>Kelola Soal (${activeCount})</span>
                                </a>
                                <button type="button" onclick="editBoothById(${b.id})"
                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:hover:text-white border border-slate-200 dark:border-slate-700 transition cursor-pointer"
                                    title="Edit Booth">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </button>
                                <button type="button" onclick="confirmDeleteBooth(${b.id}, '${escapeAttr(b.name)}')"
                                    class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 transition cursor-pointer"
                                    title="Hapus Booth">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#boothsGrid').html(cardsHtml);
        },
        error: function(xhr) {
            $('#boothsGrid').html(`
                <div class="col-span-full py-12 text-center text-rose-500 glass-panel rounded-2xl">
                    <i class="fa-solid fa-triangle-exclamation text-4xl mb-2"></i>
                    <p class="font-bold">Gagal memuat data booth</p>
                    <button onclick="loadBoothsData()" class="mt-3 px-4 py-1.5 text-xs bg-indigo-600 text-white rounded-xl cursor-pointer">Coba Lagi</button>
                </div>
            `);
        }
    });
}

function editBoothById(id) {
    const booth = boothsCache.find(item => item.id == id);
    if (!booth) return;

    $('#formEditBooth').find('.border-rose-500').removeClass('border-rose-500');
    $('#formEditBooth').find('.field-error-msg').remove();

    $('#formEditBooth').attr('action', `/pool-qcc/admin/booths/${booth.id}`);
    $('#edit_code_badge').text(booth.code);
    $('#edit_name').val(booth.name);
    $('#edit_category').val(booth.category || '');
    $('#edit_pic_name').val(booth.pic_name || '');
    $('#edit_description').val(booth.description || '');
    $('#edit_is_active').prop('checked', Boolean(booth.is_active));

    $('#modalEditBooth').removeClass('hidden');
}

function handleBoothAjaxSubmit(formSelector, isEdit = false) {
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
                $('#modalEditBooth').addClass('hidden');
            } else {
                $('#modalAddBooth').addClass('hidden');
                $form[0].reset();
            }

            // Client-side reload via jQuery!
            loadBoothsData();

            Swal.fire({
                icon: 'success',
                title: isEdit ? 'Berhasil Diperbarui!' : 'Booth Ditambahkan!',
                text: response.message || 'Data booth berhasil disimpan.',
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
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses data booth.',
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

function confirmDeleteBooth(id, name) {
    Swal.fire({
        title: 'Hapus Booth?',
        html: `Apakah Anda yakin ingin menghapus booth <b>"${name}"</b>?<br><span class="text-rose-500 text-xs font-semibold mt-1.5 block">⚠️ Perhatian: Seluruh soal dan data vote polling terkait booth ini akan ikut terhapus permanen!</span>`,
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
                url: `/pool-qcc/admin/booths/${id}`,
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
                Swal.showValidationMessage(xhr.responseJSON?.message || 'Gagal menghapus booth.');
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
            // Client-side reload via jQuery!
            loadBoothsData();

            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: result.value?.message || 'Booth berhasil dihapus.',
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
    loadBoothsData();

    $('#formAddBooth').on('submit', function(e) {
        e.preventDefault();
        handleBoothAjaxSubmit('#formAddBooth', false);
    });

    $('#formEditBooth').on('submit', function(e) {
        e.preventDefault();
        handleBoothAjaxSubmit('#formEditBooth', true);
    });
});

// ==============================================================
// MODAL IMPORT EXCEL BOOTH HANDLERS
// ==============================================================
function openImportBoothModal() {
    $('#formImportBooth')[0].reset();
    $('#importBoothFileName').text('Klik untuk memilih file Excel dari komputer Anda');
    $('#modalImportBooth').removeClass('hidden');
}

function closeImportBoothModal() {
    $('#modalImportBooth').addClass('hidden');
}

function onBoothFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        $('#importBoothFileName').html(`
            <span class="text-emerald-600 dark:text-emerald-400 font-extrabold flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> ${escapeHtml(file.name)} (${(file.size / 1024).toFixed(1)} KB)
            </span>
        `);
    } else {
        $('#importBoothFileName').text('Klik untuk memilih file Excel dari komputer Anda');
    }
}

function handleImportBoothSubmit(e) {
    e.preventDefault();
    const fileInput = document.getElementById('importBoothFileInput');
    if (!fileInput.files || !fileInput.files[0]) {
        Swal.fire({
            icon: 'warning',
            title: 'File Belum Dipilih',
            text: 'Silakan pilih file Excel (.xlsx, .xls, .csv) terlebih dahulu.',
            confirmButtonColor: '#059669'
        });
        return;
    }

    const formData = new FormData();
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    formData.append('excel_file', fileInput.files[0]);

    const $btn = $('#btnSubmitImportBooth');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Memproses...');

    $.ajax({
        url: "{{ route('pool-qcc.admin.booths.import') }}",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html(origHtml);
            closeImportBoothModal();

            Swal.fire({
                icon: 'success',
                title: 'Import Berhasil!',
                text: res.message,
                confirmButtonColor: '#059669'
            });

            loadBoothsData();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html(origHtml);
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal memproses file import.';
            Swal.fire({
                icon: 'error',
                title: 'Import Gagal',
                text: msg,
                confirmButtonColor: '#059669'
            });
        }
    });
}
</script>
@endsection
