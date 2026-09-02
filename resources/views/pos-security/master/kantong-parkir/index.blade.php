@extends('pos-security.layouts.base')

@section('title', 'Master Kantong & Slot Parkir')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <style>
        body,
        .card,
        .table,
        .modal,
        .form-control,
        .form-select,
        .btn,
        input,
        select,
        textarea,
        button,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- NAV TABS --}}
        <div class="row mb-3">
            <div class="col-lg-12">
                <ul class="nav nav-tabs nav-tabs-custom nav-success" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-zones" role="tab"
                            onclick="loadZones()">
                            <i class="mdi mdi-map-marker-radius me-1"></i> 1. Master Zona Parkir (Utama)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-slots" role="tab" onclick="loadSlots()">
                            <i class="mdi mdi-view-grid me-1"></i> 2. Detail Slot Parkir per Zona
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- TAB CONTENT --}}
        <div class="tab-content text-muted">
            {{-- TAB 1: ZONA PARKIR --}}
            <div class="tab-pane active" id="tab-zones" role="tabpanel">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <h5 class="card-title mb-0"><i class="mdi mdi-parking me-2 text-primary"></i>Daftar Zona
                                    Parkir Utama</h5>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <div class="w-auto">
                                        <input type="text" id="search_zone" class="form-control"
                                            placeholder="Cari Kode / Nama Zona..." onkeyup="loadZones()">
                                    </div>
                                    <div class="w-auto">
                                        <select id="filter_zone_status" class="form-select" onchange="loadZones()">
                                            <option value="">-- Semua Status --</option>
                                            <option value="aktif">Aktif</option>
                                            <option value="non_aktif">Non-Aktif</option>
                                            <option value="maintenance">Maintenance</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-primary" onclick="openCreateZoneModal()">
                                        <i class="mdi mdi-plus me-1"></i> Tambah Zona Baru
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0 text-nowrap" id="tableZones">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Kode Zona</th>
                                        <th>Nama Zona</th>
                                        <th>Kapasitas Slot</th>
                                        <th>Status Slot (Terisi / Kosong)</th>
                                        <th>Status Zona</th>
                                        <th class="text-center" style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyZones">
                                    <tr>
                                        <td colspan="8" class="text-center py-4"><i
                                                class="mdi mdi-spin mdi-loading me-1"></i> Memuat data zona...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: SLOT PARKIR DETAIL --}}
            <div class="tab-pane" id="tab-slots" role="tabpanel">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <h5 class="card-title mb-0"><i class="mdi mdi-grid me-2 text-primary"></i>Detail Slot Parkir
                                </h5>
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    <div class="w-auto">
                                        <select id="filter_slot_zone" class="form-select" onchange="loadSlots()">
                                            <option value="">-- Semua Zona --</option>
                                            @foreach ($zones as $z)
                                                <option value="{{ $z->id }}">{{ $z->kode_zona }} -
                                                    {{ $z->nama_zona }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-auto">
                                        <select id="filter_slot_status" class="form-select" onchange="loadSlots()">
                                            <option value="">-- Semua Status Slot --</option>
                                            <option value="kosong">Kosong</option>
                                            <option value="terisi">Terisi</option>
                                            <option value="reserved">Reserved</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="non_aktif">Non-Aktif</option>
                                        </select>
                                    </div>
                                    <div class="w-auto">
                                        <input type="text" id="search_slot" class="form-control"
                                            placeholder="Cari Slot / Jenis Kendaraan..." onkeyup="loadSlots()">
                                    </div>
                                    <button type="button" class="btn btn-success" onclick="openGenerateSlotsModal()">
                                        <i class="mdi mdi-auto-fix me-1"></i> Bulk Generate Slot
                                    </button>
                                    <button type="button" class="btn btn-primary" onclick="openCreateSlotModal()">
                                        <i class="mdi mdi-plus me-1"></i> Tambah Slot
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle mb-0" id="tableSlots">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th>Kode</th>
                                        <th>Zona</th>
                                        <th>Peruntukan Kendaraan</th>
                                        <th>Status Slot</th>
                                        <th>Kendaraan Terparkir</th>
                                        <th class="text-center" style="width: 18%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodySlots">
                                    <tr>
                                        <td colspan="7" class="text-center py-4"><i
                                                class="mdi mdi-spin mdi-loading me-1"></i> Memuat data slot...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CREATE / EDIT ZONA --}}
    <div class="modal fade" id="modalZone" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formZone">
                @csrf
                <input type="hidden" name="id" id="zone_id">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="modalZoneTitle">Tambah Zona Parkir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Zona <span class="text-danger">*</span></label>
                            <input type="text" name="kode_zona" id="zone_kode" class="form-control"
                                placeholder="Contoh: ZONA-A" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Zona <span class="text-danger">*</span></label>
                            <input type="text" name="nama_zona" id="zone_nama" class="form-control"
                                placeholder="Contoh: Zona A (Loading Dock)" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Operasional <span class="text-danger">*</span></label>
                            <select name="status" id="zone_status" class="form-select" required>
                                <option value="aktif">Aktif</option>
                                <option value="non_aktif">Non-Aktif</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="zone_keterangan" class="form-control" rows="2"
                                placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSaveZone" class="btn btn-success"><i
                                class="mdi mdi-content-save me-1"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL CREATE / EDIT SLOT --}}
    <div class="modal fade" id="modalSlot" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="formSlot">
                @csrf
                <input type="hidden" name="id" id="slot_id">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="modalSlotTitle">Tambah Slot Parkir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Zona Parkir <span class="text-danger">*</span></label>
                                <select name="parking_zone_id" id="slot_zone_id" class="form-select" required>
                                    <option value="">-- Pilih Zona --</option>
                                    @foreach ($zones as $z)
                                        <option value="{{ $z->id }}">{{ $z->kode_zona }} - {{ $z->nama_zona }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Slot <span class="text-danger">*</span></label>
                                <input type="text" name="kode_slot" id="slot_kode" class="form-control"
                                    placeholder="Contoh: A-01" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status Slot <span class="text-danger">*</span></label>
                                <select name="status_slot" id="slot_status" class="form-select" required>
                                    <option value="kosong">Kosong</option>
                                    <option value="terisi">Terisi</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="non_aktif">Non-Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Peruntukan Jenis Kendaraan</label>
                                <div class="d-flex flex-wrap gap-3 p-2 border rounded bg-light">
                                    <div class="form-check">
                                        <input class="form-check-input check-slot-jk" type="checkbox"
                                            name="jenis_kendaraan[]" value="Truk Tronton" id="sjk1">
                                        <label class="form-check-label" for="sjk1">Truk Tronton</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input check-slot-jk" type="checkbox"
                                            name="jenis_kendaraan[]" value="Truk Fuso" id="sjk2">
                                        <label class="form-check-label" for="sjk2">Truk Fuso</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input check-slot-jk" type="checkbox"
                                            name="jenis_kendaraan[]" value="Truk Engkel" id="sjk3">
                                        <label class="form-check-label" for="sjk3">Truk Engkel</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input check-slot-jk" type="checkbox"
                                            name="jenis_kendaraan[]" value="Mobil Box" id="sjk4">
                                        <label class="form-check-label" for="sjk4">Mobil Box</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input check-slot-jk" type="checkbox"
                                            name="jenis_kendaraan[]" value="Kontainer 20/40ft" id="sjk5">
                                        <label class="form-check-label" for="sjk5">Kontainer 20/40ft</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input check-slot-jk" type="checkbox"
                                            name="jenis_kendaraan[]" value="Mobil Operasional" id="sjk6">
                                        <label class="form-check-label" for="sjk6">Mobil Operasional</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="slot_keterangan" class="form-control" rows="2"
                                    placeholder="Catatan tambahan..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSaveSlot" class="btn btn-success"><i
                                class="mdi mdi-content-save me-1"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL BULK GENERATE SLOTS --}}
    <div class="modal fade" id="modalGenerateSlots" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formGenerateSlots">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">Bulk Generate Slot Parkir</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih Zona Parkir <span class="text-danger">*</span></label>
                            <select name="parking_zone_id" class="form-select" required>
                                <option value="">-- Pilih Zona --</option>
                                @foreach ($zones as $z)
                                    <option value="{{ $z->id }}">{{ $z->kode_zona }} - {{ $z->nama_zona }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prefix Kode (Contoh: A, B, T) <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="prefix" class="form-control" placeholder="A" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Nomor Awal <span class="text-danger">*</span></label>
                                <input type="number" name="start_number" class="form-control" value="1"
                                    min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Nomor Akhir <span class="text-danger">*</span></label>
                                <input type="number" name="end_number" class="form-control" value="10"
                                    min="1" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peruntukan Kendaraan</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="jenis_kendaraan[]"
                                        value="Truk Tronton" id="gjk1">
                                    <label class="form-check-label" for="gjk1">Tronton</label>
                                </div>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="jenis_kendaraan[]"
                                        value="Truk Fuso" id="gjk2">
                                    <label class="form-check-label" for="gjk2">Fuso</label>
                                </div>
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="jenis_kendaraan[]"
                                        value="Truk Engkel" id="gjk3">
                                    <label class="form-check-label" for="gjk3">Engkel</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="jenis_kendaraan[]"
                                        value="Mobil Box" id="gjk4">
                                    <label class="form-check-label" for="gjk4">Mobil Box</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnGenerateSlots" class="btn btn-success"><i
                                class="mdi mdi-auto-fix me-1"></i> Generate Slot</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PENUGASAN PARKIR (ASSIGNMENT) --}}
    <div class="modal fade" id="modalAssignParking" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formAssignParking">
                @csrf
                <input type="hidden" name="parking_slot_id" id="assign_slot_id">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white p-3">
                        <h5 class="modal-title text-white" id="modalAssignTitle">Penugasan Parkir Kendaraan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2" id="assignSlotInfo">
                            <strong>Slot:</strong> <span id="assignSlotCode">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Polisi <span class="text-danger">*</span></label>
                            <input type="text" name="no_polisi" id="assign_nopol" class="form-control text-uppercase"
                                placeholder="Contoh: B 1234 CD" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kendaraan</label>
                            <input type="text" name="jenis_kendaraan" id="assign_jk" class="form-control"
                                placeholder="Contoh: Truk Tronton">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Driver / Supir</label>
                            <input type="text" name="nama_driver" id="assign_driver" class="form-control"
                                placeholder="Nama Supir">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP Driver</label>
                            <input type="text" name="no_hp_driver" id="assign_nohp" class="form-control"
                                placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="catatan" id="assign_catatan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitAssign" class="btn btn-success"><i
                                class="mdi mdi-check me-1"></i> Tugaskan Parkir</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HISTORI SLOT --}}
    <div class="modal fade" id="modalSlotHistory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="historyModalTitle">Histori Perubahan Status Slot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Status Lama</th>
                                    <th>Status Baru</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="tbodySlotHistory">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Direct Inline URL Endpoints
        const URL_ZONE_GET = "{{ route('pos-security.kantong-parkir.zones.get') }}";
        const URL_ZONE_STORE = "{{ route('pos-security.kantong-parkir.zones.store') }}";
        const URL_ZONE_SHOW = "{{ url('/pos-security/master/kantong-parkir/zones/show') }}";
        const URL_ZONE_DESTROY = "{{ url('/pos-security/master/kantong-parkir/zones/destroy') }}";

        const URL_SLOT_GET = "{{ route('pos-security.kantong-parkir.slots.get') }}";
        const URL_SLOT_STORE = "{{ route('pos-security.kantong-parkir.slots.store') }}";
        const URL_SLOT_GENERATE = "{{ route('pos-security.kantong-parkir.slots.generate') }}";
        const URL_SLOT_SHOW = "{{ url('/pos-security/master/kantong-parkir/slots/show') }}";
        const URL_SLOT_DESTROY = "{{ url('/pos-security/master/kantong-parkir/slots/destroy') }}";

        const URL_ASSIGN = "{{ route('pos-security.kantong-parkir.assignment.assign') }}";
        const URL_RELEASE = "{{ url('/pos-security/master/kantong-parkir/assignment/release') }}";

        $(document).ready(function() {
            loadZones();

            function parseErrorMessage(xhr, defaultMsg) {
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let messages = [];
                        for (let key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                messages.push(errors[key].join('<br>'));
                            }
                        }
                        if (messages.length > 0) {
                            return messages.join('<br>');
                        }
                    }
                    if (xhr.responseJSON.message && xhr.responseJSON.message !== "The given data was invalid.") {
                        return xhr.responseJSON.message;
                    }
                }
                return defaultMsg || 'Terjadi kesalahan pada server.';
            }

            // Submit Zone Form
            $('#formZone').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnSaveZone');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-spin mdi-loading me-1"></i> Menyimpan...');

                $.ajax({
                    url: URL_ZONE_STORE,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan');
                        if (res.status === 'success') {
                            $('#modalZone').modal('hide');
                            swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadZones();
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                html: res.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan');
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal Simpan Zona',
                            html: parseErrorMessage(xhr,
                                'Terjadi kesalahan pada server.'),
                        });
                    }
                });
            });

            // Submit Slot Form
            $('#formSlot').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnSaveSlot');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-spin mdi-loading me-1"></i> Menyimpan...');

                $.ajax({
                    url: URL_SLOT_STORE,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan');
                        if (res.status === 'success') {
                            $('#modalSlot').modal('hide');
                            swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadSlots();
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                html: res.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-content-save me-1"></i> Simpan');
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal Simpan Slot',
                            html: parseErrorMessage(xhr,
                                'Terjadi kesalahan pada server.'),
                        });
                    }
                });
            });

            // Submit Generate Slots Form
            $('#formGenerateSlots').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnGenerateSlots');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-spin mdi-loading me-1"></i> Generating...');

                $.ajax({
                    url: URL_SLOT_GENERATE,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-auto-fix me-1"></i> Generate Slot');
                        if (res.status === 'success') {
                            $('#modalGenerateSlots').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadSlots();
                            loadZones();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                html: res.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-auto-fix me-1"></i> Generate Slot');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Generate Slot',
                            html: parseErrorMessage(xhr,
                                'Terjadi kesalahan pada server.'),
                        });
                    }
                });
            });

            // Submit Assign Parking Form
            $('#formAssignParking').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#btnSubmitAssign');
                btn.prop('disabled', true).html(
                    '<i class="mdi mdi-spin mdi-loading me-1"></i> Memproses...');

                $.ajax({
                    url: URL_ASSIGN,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-check me-1"></i> Tugaskan Parkir');
                        if (res.status === 'success') {
                            $('#modalAssignParking').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Penugasan Berhasil!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            loadSlots();
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(
                            '<i class="mdi mdi-check me-1"></i> Tugaskan Parkir');
                        let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Gagal menugaskan parkir.';
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });
        });

        // --- ZONES JQUERY RENDER ---

        function loadZones() {
            let search = $('#search_zone').val();
            let status = $('#filter_zone_status').val();

            $.get(URL_ZONE_GET, {
                search: search,
                status: status
            }, function(res) {
                if (res.status === 'success') {
                    let html = '';
                    if (res.data.length === 0) {
                        html = '<tr><td colspan="8" class="text-center py-3">Tidak ada data zona parkir.</td></tr>';
                    } else {
                        res.data.forEach(function(z, idx) {
                            let statusBadge = z.status === 'aktif' ?
                                '<span class="badge bg-success">Aktif</span>' :
                                (z.status === 'maintenance' ?
                                    '<span class="badge bg-warning text-dark">Maintenance</span>' :
                                    '<span class="badge bg-secondary">Non-Aktif</span>');

                            html += `
                                <tr>
                                    <td>${idx + 1}</td>
                                    <td><strong>${z.kode_zona}</strong></td>
                                    <td>${z.nama_zona}</td>
                                    <td><span class="badge bg-soft-info text-info fs-12">${z.total_slots} Slot</span></td>
                                    <td>
                                        <span class="text-danger fw-bold">${z.filled_slots} Terisi</span> / 
                                        <span class="text-success fw-bold">${z.empty_slots} Kosong</span>
                                    </td>
                                    <td>${statusBadge}</td>
                                    <td class="text-nowrap text-center">
                                        <div class="d-flex align-items-center gap-1">
                                            <button 
                                                class="btn btn-sm btn-soft-primary"
                                                onclick="editZone(${z.id})">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </button>

                                            <button 
                                                class="btn btn-sm btn-soft-info"
                                                onclick="filterSlotByZone(${z.id})">
                                                <i class="mdi mdi-eye"></i> Lihat Slot
                                            </button>

                                            <button 
                                                class="btn btn-sm btn-soft-danger"
                                                onclick="deleteZone(${z.id})">
                                                <i class="mdi mdi-trash-can"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    $('#tbodyZones').html(html);
                }
            });
        }

        function openCreateZoneModal() {
            $('#formZone')[0].reset();
            $('#zone_id').val('');
            $('#modalZoneTitle').text('Tambah Zona Parkir Baru');
            $('#modalZone').modal('show');
        }

        function editZone(id) {
            $.get(`${URL_ZONE_SHOW}/${id}`, function(res) {
                if (res.status === 'success') {
                    let d = res.data;
                    $('#zone_id').val(d.id);
                    $('#zone_kode').val(d.kode_zona);
                    $('#zone_nama').val(d.nama_zona);
                    $('#zone_status').val(d.status);
                    $('#zone_keterangan').val(d.keterangan);

                    $('#modalZoneTitle').text('Edit Zona Parkir: ' + d.kode_zona);
                    $('#modalZone').modal('show');
                }
            });
        }

        function deleteZone(id) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Menghapus Zona Parkir ini akan me-remove semua slot di dalamnya.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${URL_ZONE_DESTROY}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Berhasil!', res.message, 'success');
                                loadZones();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON
                                .message : 'Gagal menghapus zona.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        }

        function filterSlotByZone(zoneId) {
            $('#filter_slot_zone').val(zoneId);
            $('.nav-tabs a[href="#tab-slots"]').tab('show');
            loadSlots();
        }

        // --- SLOTS JQUERY RENDER ---

        function loadSlots() {
            let zoneId = $('#filter_slot_zone').val();
            let status = $('#filter_slot_status').val();
            let search = $('#search_slot').val();

            $.get(URL_SLOT_GET, {
                parking_zone_id: zoneId,
                status_slot: status,
                search: search
            }, function(res) {
                if (res.status === 'success') {
                    let html = '';
                    if (res.data.length === 0) {
                        html = '<tr><td colspan="7" class="text-center py-3">Tidak ada data slot parkir.</td></tr>';
                    } else {
                        res.data.forEach(function(s, idx) {
                            let statusBadge = '';
                            switch (s.status_slot) {
                                case 'kosong':
                                    statusBadge =
                                        '<span class="badge bg-success"><i class="mdi mdi-check-circle me-1"></i>Kosong</span>';
                                    break;
                                case 'terisi':
                                    statusBadge =
                                        '<span class="badge bg-danger"><i class="mdi mdi-car me-1"></i>Terisi</span>';
                                    break;
                                case 'reserved':
                                    statusBadge =
                                        '<span class="badge bg-info"><i class="mdi mdi-bookmark me-1"></i>Reserved</span>';
                                    break;
                                case 'maintenance':
                                    statusBadge =
                                        '<span class="badge bg-warning text-dark"><i class="mdi mdi-wrench me-1"></i>Maintenance</span>';
                                    break;
                                default:
                                    statusBadge = '<span class="badge bg-secondary">Non-Aktif</span>';
                            }

                            let parkirInfo = '-';
                            let actionButtons = '';

                            if (s.active_assignment) {
                                let a = s.active_assignment;
                                parkirInfo =
                                    `<strong>${a.no_polisi}</strong><br><small class="text-muted">${a.nama_driver ? a.nama_driver : ''} ${a.jenis_kendaraan ? '('+a.jenis_kendaraan+')' : ''}</small>`;
                                actionButtons +=
                                    `<button class="btn btn-sm btn-warning me-1" onclick="releaseParking(${a.id})"><i class="mdi mdi-logout"></i> Keluar</button>`;
                            } else if (s.status_slot === 'kosong') {
                                actionButtons +=
                                    `<button class="btn btn-sm btn-success me-1" onclick="openAssignModal(${s.id}, '${s.kode_slot}')"><i class="mdi mdi-login"></i> Parkirkan</button>`;
                            }

                            actionButtons += `
                                <button class="btn btn-sm btn-soft-primary me-1" onclick="editSlot(${s.id})"><i class="mdi mdi-pencil"></i></button>
                                <button class="btn btn-sm btn-soft-info me-1" onclick="viewSlotHistory(${s.id})"><i class="mdi mdi-history"></i></button>
                                <button class="btn btn-sm btn-soft-danger" onclick="deleteSlot(${s.id})"><i class="mdi mdi-trash-can"></i></button>
                            `;

                            let jkBadges = '-';
                            if (s.jenis_kendaraan) {
                                jkBadges = s.jenis_kendaraan.split(',').map(jk =>
                                    `<span class="badge bg-soft-info text-info me-1">${jk.trim()}</span>`
                                ).join('');
                            }

                            html += `
                                <tr>
                                    <td>${idx + 1}</td>
                                    <td><strong>${s.kode_slot}</strong></td>
                                    <td>${s.zone ? s.zone.nama_zona : '-'}</td>
                                    <td>${jkBadges}</td>
                                    <td>${statusBadge}</td>
                                    <td>${parkirInfo}</td>
                                    <td>${actionButtons}</td>
                                </tr>
                            `;
                        });
                    }
                    $('#tbodySlots').html(html);
                }
            });
        }

        function openCreateSlotModal() {
            $('#formSlot')[0].reset();
            $('#slot_id').val('');
            $('.check-slot-jk').prop('checked', false);
            $('#modalSlotTitle').text('Tambah Slot Parkir Baru');
            $('#modalSlot').modal('show');
        }

        function openGenerateSlotsModal() {
            $('#formGenerateSlots')[0].reset();
            $('#modalGenerateSlots').modal('show');
        }

        function editSlot(id) {
            $.get(`${URL_SLOT_SHOW}/${id}`, function(res) {
                if (res.status === 'success') {
                    let d = res.data;
                    $('#slot_id').val(d.id);
                    $('#slot_zone_id').val(d.parking_zone_id);
                    $('#slot_kode').val(d.kode_slot);
                    $('#slot_status').val(d.status_slot);
                    $('#slot_keterangan').val(d.keterangan);

                    $('.check-slot-jk').prop('checked', false);
                    if (d.jenis_kendaraan) {
                        let jks = d.jenis_kendaraan.split(',').map(item => item.trim());
                        $('.check-slot-jk').each(function() {
                            if (jks.includes($(this).val())) {
                                $(this).prop('checked', true);
                            }
                        });
                    }

                    $('#modalSlotTitle').text('Edit Slot: ' + d.kode_slot);
                    $('#modalSlot').modal('show');
                }
            });
        }

        function deleteSlot(id) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Slot Parkir ini akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${URL_SLOT_DESTROY}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Berhasil!', res.message, 'success');
                                loadSlots();
                                loadZones();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON
                                .message : 'Gagal menghapus slot.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        }

        function openAssignModal(slotId, slotCode) {
            $('#formAssignParking')[0].reset();
            $('#assign_slot_id').val(slotId);
            $('#assignSlotCode').text(slotCode);
            $('#modalAssignParking').modal('show');
        }

        function releaseParking(assignmentId) {
            Swal.fire({
                title: 'Konfirmasi Pelepasan Parkir',
                text: 'Apakah kendaraan telah selesai parkir dan melepaskan slot?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ffb800',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Release Slot',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${URL_RELEASE}/${assignmentId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Berhasil!', res.message, 'success');
                                loadSlots();
                                loadZones();
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON
                                .message : 'Gagal melepaskan parkir.';
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                }
            });
        }

        function viewSlotHistory(slotId) {
            $.get(`${URL_SLOT_SHOW}/${slotId}`, function(res) {
                if (res.status === 'success') {
                    let d = res.data;
                    let html = '';

                    if (d.histories && d.histories.length > 0) {
                        d.histories.forEach(function(h) {
                            html += `
                            <tr>
                                <td>${new Date(h.created_at).toLocaleString('id-ID')}</td>
                                <td><span class="badge bg-secondary">${h.status_sebelumnya ? h.status_sebelumnya : '-'}</span></td>
                                <td><span class="badge bg-primary">${h.status_baru}</span></td>
                                <td>${h.keterangan ? h.keterangan : '-'}</td>
                            </tr>
                        `;
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center">Belum ada riwayat histori.</td></tr>';
                    }

                    $('#historyModalTitle').text('Histori Status Slot: ' + d.kode_slot);
                    $('#tbodySlotHistory').html(html);
                    $('#modalSlotHistory').modal('show');
                }
            });
        }
    </script>
@endpush
