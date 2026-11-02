@extends('pool_qcc.layout')

@section('title', 'Manajemen Booth - Pool QCC')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center space-x-2 text-xs text-indigo-600 dark:text-indigo-400 font-bold uppercase tracking-wider mb-1">
                <a href="{{ route('pool-qcc.admin.dashboard') }}" class="hover:underline">Admin</a>
                <span>&bull;</span>
                <span class="text-slate-500">Master Data</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">Manajemen Booth QCC</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftarkan booth peserta exhibition, atur profil booth, dan pastikan minimal 5 pertanyaan tersedia.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <button onclick="document.getElementById('modalAddBooth').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition flex items-center space-x-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Booth Baru</span>
            </button>
        </div>
    </div>

    <!-- Alert / Requirements Note -->
    <div class="mb-6 p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-900 dark:bg-indigo-500/10 dark:border-indigo-500/20 dark:text-indigo-300 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-2.5">
            <i class="fa-solid fa-circle-info text-base text-indigo-600 dark:text-indigo-400"></i>
            <span><strong>Ketentuan:</strong> Setiap booth harus memiliki <strong>minimal 5 pertanyaan aktif</strong> agar dapat dipilih dan di-randomize saat peserta melakukan pooling.</span>
        </div>
        <span class="px-3 py-1 rounded-xl bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-300 font-bold">Total: {{ $booths->count() }} Booth</span>
    </div>

    <!-- Booths Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($booths as $b)
        <div class="glass-card rounded-2xl overflow-hidden flex flex-col justify-between border {{ $b->active_questions_count >= 5 ? 'border-slate-200 dark:border-slate-800' : 'border-amber-400 dark:border-amber-500/50' }}">
            <!-- Card Header / Image -->
            <div class="relative h-44 bg-slate-100 dark:bg-slate-900 overflow-hidden">
                @if($b->image)
                    <img src="{{ asset($b->image) }}" alt="{{ $b->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-tr from-slate-100 to-indigo-50 dark:from-slate-900 dark:to-indigo-950/60 text-slate-400">
                        <i class="fa-solid fa-store text-5xl mb-2 text-indigo-400/50"></i>
                        <span class="text-xs text-slate-500 font-medium">Belum ada foto booth</span>
                    </div>
                @endif
                <!-- Code Badge -->
                <div class="absolute top-3 left-3">
                    <span class="px-3 py-1 rounded-xl text-xs font-black bg-slate-900/90 text-white backdrop-blur shadow-sm tracking-wider">
                        {{ $b->code }}
                    </span>
                </div>
                <!-- Status Badge -->
                <div class="absolute top-3 right-3 flex items-center space-x-1">
                    @if($b->active_questions_count >= 5)
                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-md flex items-center space-x-1">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            <span>{{ $b->active_questions_count }} Soal</span>
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-500 text-slate-950 shadow-md flex items-center space-x-1">
                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                            <span>{{ $b->active_questions_count }}/5 Soal</span>
                        </span>
                    @endif
                </div>
                <!-- Category overlay -->
                <div class="absolute bottom-3 left-3">
                    <span class="text-xs px-2.5 py-0.5 rounded-lg bg-indigo-600 text-white font-bold shadow-sm backdrop-blur">
                        {{ $b->category ?? 'General' }}
                    </span>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5 leading-snug">{{ $b->name }}</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-400 line-clamp-2 mb-4">
                        {{ $b->description ?? 'Tidak ada deskripsi booth.' }}
                    </p>

                    <div class="text-xs text-slate-700 dark:text-slate-300 py-2 border-y border-slate-100 dark:border-slate-800 mb-4 flex items-center justify-between">
                        <span class="text-slate-500 text-xs">PIC Booth:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $b->pic_name ?? '-' }}</span>
                    </div>

                    <!-- Scores & Voters -->
                    <div class="flex items-center justify-between text-xs mb-4 bg-slate-50 dark:bg-slate-900/60 p-3 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div>
                            <span class="text-slate-500 block text-[11px] font-medium">Total Skor Poin:</span>
                            <span class="text-base font-black text-emerald-600 dark:text-emerald-400">{{ number_format($b->total_points) }} Pts</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-500 block text-[11px] font-medium">Total Peserta Pool:</span>
                            <span class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ $b->total_voters }} Peserta</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 pt-2">
                    <a href="{{ route('pool-qcc.admin.questions', ['booth_id' => $b->id]) }}" class="flex-1 py-2 px-3 rounded-xl text-xs font-bold text-center bg-indigo-600 hover:bg-indigo-500 text-white transition flex items-center justify-center space-x-1 shadow-sm">
                        <i class="fa-solid fa-clipboard-question mr-1"></i>
                        <span>Kelola Soal ({{ $b->active_questions_count }})</span>
                    </a>
                    <button onclick="editBooth({{ json_encode($b) }})" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-300 dark:hover:text-white border border-slate-200 dark:border-slate-700 transition" title="Edit Booth">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </button>
                    <form action="{{ route('pool-qcc.admin.booths.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus booth ini beserta seluruh soal dan data pollingnya?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 transition" title="Hapus Booth">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center glass-panel rounded-2xl">
            <i class="fa-solid fa-store-slash text-5xl text-slate-400 mb-3 block"></i>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Belum Ada Booth Terdaftar</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Klik tombol "Tambah Booth Baru" di atas untuk mendaftarkan booth QCC dan mulai mengisi 5 pertanyaan bank soal.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Tambah Booth -->
<div id="modalAddBooth" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 99999;">
    <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
        <div class="glass-panel w-full max-w-lg rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col relative" style="max-height: calc(100vh - 2rem);">
            <!-- Header (Fixed at top of modal card) -->
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center">
                    <i class="fa-solid fa-store text-indigo-600 dark:text-indigo-400 mr-2.5"></i> Tambah Booth QCC Baru
                </h3>
                <button type="button" onclick="document.getElementById('modalAddBooth').classList.add('hidden')" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Form Body & Fixed Footer -->
            <form action="{{ route('pool-qcc.admin.booths.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf
                <div class="p-5 overflow-y-auto space-y-4 text-sm flex-1 overscroll-contain">
                    <!-- Auto-generated Code Badge -->
                    <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 dark:bg-indigo-950/40 dark:border-indigo-500/30 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 block">Kode Booth (Auto-Generated Backend)</span>
                            <span class="text-sm font-black text-indigo-700 dark:text-indigo-300">Akan dibuat otomatis: {{ $nextCode }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-600 text-white"><i class="fa-solid fa-wand-magic-sparkles mr-1"></i> Auto</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Booth / Judul Inovasi *</label>
                        <input type="text" name="name" placeholder="Misal: Circle Kaizen Line Produksi" required class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Departemen *</label>
                            <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama PIC Booth</label>
                            <input type="text" name="pic_name" placeholder="Nama penanggung jawab" class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Inovasi / Booth</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan secara singkat inovasi atau tema yang diangkat oleh booth ini..." class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Foto Booth / Poster (Opsional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" name="is_active" id="is_active_new" value="1" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="is_active_new" class="text-xs text-slate-700 dark:text-slate-300 font-medium">Booth Aktif & Siap Menerima Pooling</label>
                    </div>
                </div>

                <!-- Footer (Fixed at bottom of modal card) -->
                <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3 flex-shrink-0 bg-slate-50 dark:bg-slate-800/80 z-10">
                    <button type="button" onclick="document.getElementById('modalAddBooth').classList.add('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition cursor-pointer">Simpan Booth</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Booth -->
<div id="modalEditBooth" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm hidden" style="z-index: 99999;">
    <div class="w-full h-full flex items-center justify-center p-3 sm:p-4 overflow-hidden">
        <div class="glass-panel w-full max-w-lg rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 flex flex-col relative" style="max-height: calc(100vh - 2rem);">
            <!-- Header (Fixed at top of modal card) -->
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0 bg-white dark:bg-slate-900 z-10">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white flex items-center">
                    <i class="fa-solid fa-pen-to-square text-cyan-600 dark:text-cyan-400 mr-2.5"></i> Edit Data Booth
                </h3>
                <button type="button" onclick="document.getElementById('modalEditBooth').classList.add('hidden')" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-800 dark:text-slate-300 dark:hover:text-white flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Form Body & Fixed Footer -->
            <form id="formEditBooth" action="" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf
                @method('PUT')
                <div class="p-5 overflow-y-auto space-y-4 text-sm flex-1 overscroll-contain">
                    <!-- Code Display (Readonly) -->
                    <div class="p-3.5 rounded-2xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-slate-500 block">Kode Booth (Tetap)</span>
                            <span id="edit_code_badge" class="text-base font-black text-indigo-600 dark:text-indigo-400">B01</span>
                        </div>
                        <span class="text-xs text-slate-400 font-mono"><i class="fa-solid fa-lock text-xs mr-1"></i> Locked</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Booth / Judul Inovasi *</label>
                        <input type="text" id="edit_name" name="name" required class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Departemen *</label>
                            <select id="edit_category" name="category" required class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama PIC Booth</label>
                            <input type="text" id="edit_pic_name" name="pic_name" class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi Inovasi / Booth</label>
                        <textarea id="edit_description" name="description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white focus:outline-none focus:border-indigo-500 shadow-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ganti Foto Booth (Kosongkan jika tidak diubah)</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500">
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="edit_is_active" class="text-xs text-slate-700 dark:text-slate-300 font-medium">Booth Aktif & Siap Menerima Pooling</label>
                    </div>
                </div>

                <!-- Footer (Fixed at bottom of modal card) -->
                <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3 flex-shrink-0 bg-slate-50 dark:bg-slate-800/80 z-10">
                    <button type="button" onclick="document.getElementById('modalEditBooth').classList.add('hidden')" class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 transition cursor-pointer">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md shadow-indigo-600/30 transition cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editBooth(booth) {
    document.getElementById('formEditBooth').action = `/pool-qcc/admin/booths/${booth.id}`;
    document.getElementById('edit_code_badge').textContent = booth.code;
    document.getElementById('edit_name').value = booth.name;
    document.getElementById('edit_category').value = booth.category || '';
    document.getElementById('edit_pic_name').value = booth.pic_name || '';
    document.getElementById('edit_description').value = booth.description || '';
    document.getElementById('edit_is_active').checked = !!booth.is_active;

    document.getElementById('modalEditBooth').classList.remove('hidden');
}
</script>
@endsection
