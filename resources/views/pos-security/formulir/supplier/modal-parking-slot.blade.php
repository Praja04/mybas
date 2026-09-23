<!-- Modal Pemilihan Slot Parkir -->
<div class="modal fade" id="modalParkingSlotPicker" tabindex="-1" aria-labelledby="modalParkingSlotPickerLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            {{-- Modal Header --}}
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div
                        class="avatar-sm bg-primary text-white rounded-3 d-flex align-items-center justify-content-center fs-4 shadow-sm">
                        <i class="mdi mdi-car-brake-parking"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalParkingSlotPickerLabel">
                            Pilih Slot Area Parkir Transporter
                        </h5>
                        <small class="text-muted">Pilih kotak slot yang berwarna <strong class="text-success">Hijau
                                (Tersedia)</strong> untuk supir/transporter.</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1"
                        id="btnRefreshSlotModal" onclick="refreshParkingSlotsData()"
                        title="Muat ulang status slot parkir">
                        <i class="mdi mdi-refresh" id="iconRefreshSlot"></i>
                        <span class="d-none d-sm-inline">Refresh Data</span>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-4 bg-light-subtle">

                {{-- Quick Stats & Legend Bar --}}
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            {{-- Summary Counters --}}
                            <div class="col-12 col-md-6">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="text-muted small fw-semibold me-1"><i
                                            class="mdi mdi-information-outline me-1"></i>Status Slot:</span>
                                    <span
                                        class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-12"><span
                                            id="badgeCountKosong">0</span> Kosong / Tersedia
                                    </span>
                                    <span
                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-12">
                                        <i class="mdi mdi-car me-1"></i><span id="badgeCountTerisi">0</span> Terisi
                                    </span>
                                    <span
                                        class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fs-12"
                                        id="badgeOtherContainer" style="display: none;">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i><span
                                            id="badgeCountOther">0</span> Lainnya
                                    </span>
                                </div>
                            </div>

                            {{-- Filter & Search --}}
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center gap-2 justify-content-md-end">
                                    <div class="input-group input-group-sm" style="max-width: 250px;">
                                        <span class="input-group-text bg-white border-end-0"><i
                                                class="mdi mdi-magnify text-muted"></i></span>
                                        <input type="text" id="inputSearchSlot" class="form-control border-start-0"
                                            placeholder="Cari kode slot / nopol..." oninput="filterParkingSlots()">
                                    </div>
                                    <select id="selectFilterStatus" class="form-select form-select-sm"
                                        style="width: auto;" onchange="filterParkingSlots()">
                                        <option value="all">Semua Status</option>
                                        <option value="kosong" selected>Hanya Tersedia (Kosong)</option>
                                        <option value="terisi">Hanya Terisi</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Legend Visual Note --}}
                        <div class="d-flex flex-wrap align-items-center gap-3 pt-2 mt-2 border-top small text-muted">
                            <span class="fw-semibold text-dark"><i class="mdi mdi-palette me-1"></i>Legenda:</span>
                            <div class="d-flex align-items-center gap-1">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-1 border border-success tile-kosong"
                                    style="width: 20px; height: 20px; font-size: 11px;">
                                    <i class="mdi mdi-parking"></i>
                                </span>
                                <span class="fw-semibold text-success">Kosong / Tersedia (Bisa Dipilih)</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-1 border border-danger tile-terisi"
                                    style="width: 20px; height: 20px; font-size: 11px;">
                                    <i class="mdi mdi-truck"></i>
                                </span>
                                <span class="fw-semibold text-danger">Terisi Kendaraan (Terkunci)</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-1 border border-info tile-reserved"
                                    style="width: 20px; height: 20px; font-size: 11px;">
                                    <i class="mdi mdi-bookmark"></i>
                                </span>
                                <span class="text-info fw-semibold">Reserved</span>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-1 border border-warning tile-maintenance"
                                    style="width: 20px; height: 20px; font-size: 11px;">
                                    <i class="mdi mdi-wrench"></i>
                                </span>
                                <span class="text-warning fw-semibold">Maintenance</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Zone Tabs (if multiple zones) --}}
                @if (isset($parkingZones) && count($parkingZones) > 1)
                    <div class="mb-3">
                        <ul class="nav nav-pills gap-1" id="parkingZoneTabs" role="tablist">
                            <li class="nav-item">
                                <button type="button" class="nav-link active py-1 px-3 fs-13" data-zone-filter="all"
                                    onclick="filterByZone('all', this)">
                                    <i class="mdi mdi-view-grid me-1"></i> Semua Zona
                                </button>
                            </li>
                            @foreach ($parkingZones as $zone)
                                <li class="nav-item">
                                    <button type="button" class="nav-link py-1 px-3 fs-13"
                                        data-zone-filter="{{ $zone->id }}"
                                        onclick="filterByZone('{{ $zone->id }}', this)">
                                        Zona {{ $zone->kode_zona }} - {{ $zone->nama_zona }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Slot Containers per Zone --}}
                <div id="parkingZonesContainer">
                    @if (isset($parkingZones) && count($parkingZones) > 0)
                        @foreach ($parkingZones as $zone)
                            @php
                                $zTotal = count($zone->slots);
                                $zKosong = $zone->slots->where('status_slot', 'kosong')->count();
                                $zTerisi = $zone->slots->where('status_slot', 'terisi')->count();
                            @endphp
                            <div class="parking-zone-section card border-0 shadow-sm mb-3"
                                data-zone-id="{{ $zone->id }}" id="zone-section-{{ $zone->id }}">
                                <div class="card-body p-3">
                                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                        {{-- Left: Zone Info Box --}}
                                        <div class="zone-info-box d-flex flex-row flex-lg-column justify-content-between justify-content-lg-center align-items-start flex-shrink-0"
                                            style="min-width: 200px; max-width: 240px;">
                                            <div class="w-100">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-primary fs-13 px-2 py-1">Zona {{ $zone->kode_zona }}</span>
                                                    <h6 class="mb-0 fw-bold text-dark text-truncate" title="{{ $zone->nama_zona }}">{{ $zone->nama_zona }}</h6>
                                                </div>
                                                @if ($zone->keterangan)
                                                    <small class="text-muted d-block text-truncate mb-1" title="{{ $zone->keterangan }}">{{ $zone->keterangan }}</small>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 mt-1 w-100 flex-wrap">
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">
                                                    <i class="mdi mdi-check-circle me-1"></i>{{ $zKosong }} Tersedia
                                                </span>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">
                                                    <i class="mdi mdi-car me-1"></i>{{ $zTerisi }} Terisi
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Vertical separator on desktop --}}
                                        <div class="vr d-none d-lg-block opacity-25" style="align-self: stretch; min-height: 50px;"></div>

                                        {{-- Right: Slots Row (Mini Tiles) --}}
                                        <div class="zone-slots-container flex-grow-1 overflow-x-auto py-1">
                                            @if ($zTotal > 0)
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    @foreach ($zone->slots as $slot)
                                                        @php
                                                            $isKosong = $slot->status_slot === 'kosong';
                                                            $isTerisi = $slot->status_slot === 'terisi';
                                                            $isReserved = $slot->status_slot === 'reserved';
                                                            $isMaintenance = $slot->status_slot === 'maintenance';
                                                            $active = $slot->activeAssignment;

                                                            $tileClass = 'tile-kosong';
                                                            $iconClass = 'mdi-parking';
                                                            if ($isTerisi) {
                                                                $tileClass = 'tile-terisi';
                                                                $iconClass = 'mdi-truck';
                                                            } elseif ($isReserved) {
                                                                $tileClass = 'tile-reserved';
                                                                $iconClass = 'mdi-bookmark';
                                                            } elseif ($isMaintenance) {
                                                                $tileClass = 'tile-maintenance';
                                                                $iconClass = 'mdi-wrench';
                                                            }

                                                            $tooltip = '<strong>Slot ' . e($slot->kode_slot) . '</strong>';
                                                            if ($isKosong) {
                                                                $tooltip .= '<br><span class="text-success">Tersedia (Klik untuk memilih)</span>';
                                                                if ($slot->jenis_kendaraan) {
                                                                    $tooltip .= '<br><small class="text-muted">' . e($slot->jenis_kendaraan) . '</small>';
                                                                }
                                                            } elseif ($isTerisi && $active) {
                                                                $tooltip .= '<br><span class="text-danger fw-bold font-mono">' . e($active->no_polisi) . '</span>';
                                                                if ($active->nama_driver) {
                                                                    $tooltip .= '<br><small>' . e($active->nama_driver) . '</small>';
                                                                }
                                                                $tooltip .= '<br><span class="badge bg-danger text-white mt-1">Terisi</span>';
                                                            } elseif ($isReserved) {
                                                                $tooltip .= '<br><span class="text-info">Reserved</span>';
                                                            } else {
                                                                $tooltip .= '<br><span class="text-warning">Maintenance</span>';
                                                            }
                                                        @endphp

                                                        <div class="slot-item-wrapper mini-slot-tile {{ $tileClass }}"
                                                            data-slot-id="{{ $slot->id }}"
                                                            data-slot-code="{{ $slot->kode_slot }}"
                                                            data-zone-id="{{ $zone->id }}"
                                                            data-zone-name="{{ $zone->nama_zona }} ({{ $zone->kode_zona }})"
                                                            data-status="{{ $slot->status_slot }}"
                                                            data-nopol="{{ $active ? $active->no_polisi : '' }}"
                                                            data-driver="{{ $active ? $active->nama_driver : '' }}"
                                                            data-vehicle="{{ $slot->jenis_kendaraan ?? '' }}"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-html="true"
                                                            data-bs-placement="top"
                                                            data-bs-title="{{ $tooltip }}"
                                                            id="slot-card-{{ $slot->id }}"
                                                            @if ($isKosong)
                                                                onclick="selectSlotInModal({{ $slot->id }}, '{{ $slot->kode_slot }}', '{{ $zone->nama_zona }} ({{ $zone->kode_zona }})', '{{ $slot->jenis_kendaraan ?? '' }}')"
                                                            @endif
                                                        >
                                                            <i class="mdi {{ $iconClass }} tile-icon"></i>
                                                            <span class="tile-code">{{ $slot->kode_slot }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted small py-2">
                                                    <i class="mdi mdi-alert-circle-outline me-1"></i>Belum ada slot parkir yang terdaftar di zona ini.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="card border-0 shadow-sm text-center py-5">
                            <div class="card-body">
                                <i class="mdi mdi-car-parking-lot fs-1 text-muted d-block mb-2"></i>
                                <h5 class="fw-bold text-dark">Tidak Ada Data Zona Parkir Aktif</h5>
                                <p class="text-muted mb-0">Silakan pastikan master kantong parkir sudah dikonfigurasi
                                    dan berstatus aktif.</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Empty Search Result Alert --}}
                <div id="slotSearchEmptyState" class="card border-0 shadow-sm text-center py-4 my-3"
                    style="display: none;">
                    <div class="card-body">
                        <i class="mdi mdi-magnify-close fs-1 text-muted d-block mb-2"></i>
                        <h6 class="fw-bold text-dark">Tidak Ada Slot Parkir yang Cocok</h6>
                        <p class="text-muted small mb-2">Coba ubah kata kunci pencarian atau ganti filter status di
                            atas.</p>
                        <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="resetSlotFilters()">Reset Filter</button>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2 text-start">
                    <span class="text-muted small">Pilihan Anda:</span>
                    <div id="modalSelectedSlotPreview"
                        class="badge bg-light text-secondary border px-3 py-2 fs-13 fw-normal">
                        <i class="mdi mdi-cursor-default-outline me-1"></i> Silakan klik salah satu slot hijau di atas
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-primary px-4 d-flex align-items-center gap-2"
                        id="btnConfirmParkingSlot" onclick="confirmParkingSlotSelection()" disabled>
                        <i class="mdi mdi-check-bold"></i>
                        <span>Gunakan Slot Ini</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Mini Slot Tile Base (Cinema/Airplane Seat Style) */
    .mini-slot-tile {
        width: 52px;
        height: 56px;
        border-radius: 8px;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        user-select: none;
        transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        flex-shrink: 0;
    }

    .mini-slot-tile:hover {
        transform: translateY(-3px) scale(1.08);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        z-index: 5;
    }

    .mini-slot-tile .tile-icon {
        font-size: 18px;
        line-height: 1;
        margin-bottom: 3px;
    }

    .mini-slot-tile .tile-code {
        font-size: 11px;
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.2px;
        font-family: 'JetBrains Mono', monospace !important;
    }

    /* Top Notch Accent */
    .mini-slot-tile::before {
        content: '';
        position: absolute;
        top: 2px;
        width: 22px;
        height: 3px;
        border-radius: 2px;
        background-color: currentColor;
        opacity: 0.45;
    }

    /* Kosong / Tersedia */
    .tile-kosong {
        background-color: #dcfce7;
        border: 2px solid #22c55e;
        color: #15803d;
        cursor: pointer;
    }
    .tile-kosong:hover {
        background-color: #bbf7d0;
        border-color: #16a34a;
        box-shadow: 0 6px 14px rgba(34, 197, 94, 0.3);
    }

    /* SELECTED STATE in Picker Modal */
    .tile-kosong.is-selected {
        background-color: #0d6efd !important;
        border-color: #0b5ed7 !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.4) !important;
        transform: translateY(-3px) scale(1.08);
    }
    .tile-kosong.is-selected::before {
        background-color: #ffffff !important;
        opacity: 0.8 !important;
    }

    /* Terisi */
    .tile-terisi {
        background-color: #fee2e2;
        border: 2px solid #ef4444;
        color: #b91c1c;
        cursor: not-allowed;
        opacity: 0.85;
    }

    /* Reserved */
    .tile-reserved {
        background-color: #e0f2fe;
        border: 2px solid #0ea5e9;
        color: #0369a1;
        cursor: not-allowed;
        opacity: 0.85;
    }

    /* Maintenance */
    .tile-maintenance {
        background-color: #fef3c7;
        border: 2px solid #f59e0b;
        color: #b45309;
        cursor: not-allowed;
        opacity: 0.85;
    }

    /* Hover on empty state trigger card in form */
    #emptySlotPickerCard:hover {
        border-color: #0d6efd !important;
        background-color: #e7f1ff !important;
        transform: translateY(-2px);
    }
</style>

<script>
    // State management for modal parking slot picker
    let currentSelectedSlot = {
        id: null,
        code: null,
        zoneName: null,
        vehicleType: null
    };

    // Open the Parking Slot Picker Modal
    function openParkingSlotModal() {
        // If a slot is already selected in the form, preserve it in the modal
        const existingSlotId = document.getElementById("parking_slot_id")?.value;
        if (existingSlotId && currentSelectedSlot.id == existingSlotId) {
            highlightSlotInModal(currentSelectedSlot.id);
        }

        updateStatsCounters();

        const modalEl = document.getElementById('modalParkingSlotPicker');
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        }
    }

    // Select a slot inside the modal
    function selectSlotInModal(slotId, slotCode, zoneName, vehicleType) {
        currentSelectedSlot = {
            id: slotId,
            code: slotCode,
            zoneName: zoneName,
            vehicleType: vehicleType
        };

        highlightSlotInModal(slotId);

        // Update modal footer preview
        const previewEl = document.getElementById('modalSelectedSlotPreview');
        const confirmBtn = document.getElementById('btnConfirmParkingSlot');

        if (previewEl) {
            previewEl.className =
                'badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-13 fw-semibold';
            previewEl.innerHTML = `<i class="mdi mdi-check-circle me-1"></i> Slot ${slotCode} &bull; ${zoneName}`;
        }

        if (confirmBtn) {
            confirmBtn.disabled = false;
        }
    }

    // Visually highlight the chosen slot card in the modal
    function highlightSlotInModal(slotId) {
        // Remove selected state from all cards
        document.querySelectorAll('.slot-item-wrapper.tile-kosong').forEach(el => {
            el.classList.remove('is-selected');
        });

        // Add selected state to clicked card
        const targetCard = document.getElementById(`slot-card-${slotId}`);
        if (targetCard && targetCard.classList.contains('tile-kosong')) {
            targetCard.classList.add('is-selected');
        }
    }

    // Confirm the slot selection from modal into the main form
    function confirmParkingSlotSelection() {
        if (!currentSelectedSlot.id) return;

        // Custom hook jika ada (misal Form Cek Kendaraan Masuk)
        if (typeof window.onParkingSlotConfirmed === 'function') {
            window.onParkingSlotConfirmed(currentSelectedSlot);
            return;
        }

        // Set hidden input value
        const inputSlotId = document.getElementById('parking_slot_id');
        if (inputSlotId) {
            inputSlotId.value = currentSelectedSlot.id;
        }

        // Update form display elements
        const codeDisplay = document.getElementById('selectedSlotCodeDisplay');
        const zoneDisplay = document.getElementById('selectedSlotZoneDisplay');
        const vehicleDisplay = document.getElementById('selectedSlotVehicleDisplay');

        if (codeDisplay) codeDisplay.textContent = `Slot ${currentSelectedSlot.code}`;
        if (zoneDisplay) zoneDisplay.textContent = `Zona: ${currentSelectedSlot.zoneName}`;

        if (vehicleDisplay) {
            if (currentSelectedSlot.vehicleType) {
                vehicleDisplay.textContent = currentSelectedSlot.vehicleType;
                vehicleDisplay.style.display = 'inline-block';
            } else {
                vehicleDisplay.style.display = 'none';
            }
        }

        // Toggle cards in form
        const emptyCard = document.getElementById('emptySlotPickerCard');
        const selectedCard = document.getElementById('selectedSlotCard');

        if (emptyCard) emptyCard.style.display = 'none';
        if (selectedCard) $(selectedCard).fadeIn(200);

        // Close modal
        const modalEl = document.getElementById('modalParkingSlotPicker');
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        }

        // Toast notification
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: `Slot ${currentSelectedSlot.code} berhasil dipilih!`
            });
        }
    }

    // Clear / Remove the chosen slot
    function clearParkingSlotSelection() {
        currentSelectedSlot = {
            id: null,
            code: null,
            zoneName: null,
            vehicleType: null
        };

        const inputSlotId = document.getElementById('parking_slot_id');
        if (inputSlotId) inputSlotId.value = '';

        const emptyCard = document.getElementById('emptySlotPickerCard');
        const selectedCard = document.getElementById('selectedSlotCard');

        if (selectedCard) $(selectedCard).hide();
        if (emptyCard) $(emptyCard).fadeIn(200);

        // Reset inside modal
        document.querySelectorAll('.slot-item-wrapper.tile-kosong').forEach(el => {
            el.classList.remove('is-selected');
        });

        const previewEl = document.getElementById('modalSelectedSlotPreview');
        const confirmBtn = document.getElementById('btnConfirmParkingSlot');

        if (previewEl) {
            previewEl.className = 'badge bg-light text-secondary border px-3 py-2 fs-13 fw-normal';
            previewEl.innerHTML =
                `<i class="mdi mdi-cursor-default-outline me-1"></i> Silakan klik salah satu slot hijau di atas`;
        }

        if (confirmBtn) confirmBtn.disabled = true;
    }

    // Filter slots by search query & status
    function filterParkingSlots() {
        const searchVal = (document.getElementById('inputSearchSlot')?.value || '').toLowerCase().trim();
        const statusVal = document.getElementById('selectFilterStatus')?.value || 'all';

        let visibleCount = 0;

        document.querySelectorAll('.slot-item-wrapper').forEach(wrapper => {
            const slotCode = (wrapper.getAttribute('data-slot-code') || '').toLowerCase();
            const nopol = (wrapper.getAttribute('data-nopol') || '').toLowerCase();
            const driver = (wrapper.getAttribute('data-driver') || '').toLowerCase();
            const status = (wrapper.getAttribute('data-status') || '').toLowerCase();
            const zoneName = (wrapper.getAttribute('data-zone-name') || '').toLowerCase();

            const matchSearch = !searchVal ||
                slotCode.includes(searchVal) ||
                nopol.includes(searchVal) ||
                driver.includes(searchVal) ||
                zoneName.includes(searchVal);

            let matchStatus = true;
            if (statusVal === 'kosong') {
                matchStatus = (status === 'kosong');
            } else if (statusVal === 'terisi') {
                matchStatus = (status === 'terisi');
            }

            if (matchSearch && matchStatus) {
                wrapper.style.display = '';
                visibleCount++;
            } else {
                wrapper.style.display = 'none';
            }
        });

        // Handle zone visibility
        document.querySelectorAll('.parking-zone-section').forEach(section => {
            const hasVisibleSlots = Array.from(section.querySelectorAll('.slot-item-wrapper')).some(w => w.style
                .display !== 'none');
            section.style.display = hasVisibleSlots ? '' : 'none';
        });

        // Empty state
        const emptyState = document.getElementById('slotSearchEmptyState');
        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    // Filter by Zone Pill
    function filterByZone(zoneId, btnEl) {
        if (btnEl) {
            document.querySelectorAll('#parkingZoneTabs .nav-link').forEach(btn => btn.classList.remove('active'));
            btnEl.classList.add('active');
        }

        document.querySelectorAll('.parking-zone-section').forEach(section => {
            if (zoneId === 'all' || section.getAttribute('data-zone-id') == zoneId) {
                section.style.display = '';
            } else {
                section.style.display = 'none';
            }
        });

        // Re-apply search filter
        filterParkingSlots();
    }

    // Reset filters in modal
    function resetSlotFilters() {
        const inputSearch = document.getElementById('inputSearchSlot');
        const selectStatus = document.getElementById('selectFilterStatus');

        if (inputSearch) inputSearch.value = '';
        if (selectStatus) selectStatus.value = 'all';

        filterParkingSlots();
    }

    // Calculate and update counter badges in modal
    function updateStatsCounters() {
        const totalKosong = document.querySelectorAll('.slot-item-wrapper[data-status="kosong"]').length;
        const totalTerisi = document.querySelectorAll('.slot-item-wrapper[data-status="terisi"]').length;
        const totalOther = document.querySelectorAll(
            '.slot-item-wrapper:not([data-status="kosong"]):not([data-status="terisi"])').length;

        const elKosong = document.getElementById('badgeCountKosong');
        const elTerisi = document.getElementById('badgeCountTerisi');
        const elOther = document.getElementById('badgeCountOther');
        const containerOther = document.getElementById('badgeOtherContainer');

        if (elKosong) elKosong.textContent = totalKosong;
        if (elTerisi) elTerisi.textContent = totalTerisi;
        if (elOther) elOther.textContent = totalOther;
        if (containerOther) containerOther.style.display = totalOther > 0 ? 'inline-block' : 'none';
    }

    // Live refresh from API `/api/kantong-parkir`
    function refreshParkingSlotsData() {
        const refreshBtn = document.getElementById('btnRefreshSlotModal');
        const refreshIcon = document.getElementById('iconRefreshSlot');

        if (refreshIcon) refreshIcon.classList.add('mdi-spin');
        if (refreshBtn) refreshBtn.disabled = true;

        $.ajax({
            url: '/api/kantong-parkir',
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res && res.status === 'success' && res.data) {
                    renderDynamicParkingZones(res.data);
                    updateStatsCounters();
                    filterParkingSlots();

                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        Toast.fire({
                            icon: 'info',
                            title: 'Data slot parkir diperbarui!'
                        });
                    }
                }
            },
            error: function() {
                console.warn('Gagal memuat data slot parkir dari API.');
            },
            complete: function() {
                if (refreshIcon) refreshIcon.classList.remove('mdi-spin');
                if (refreshBtn) refreshBtn.disabled = false;
            }
        });
    }

    // Initialize tooltips inside modal
    function initSlotModalTooltips() {
        const modalEl = document.getElementById('modalParkingSlotPicker');
        if (!modalEl) return;
        const tooltipTriggerList = [].slice.call(modalEl.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            try {
                const existing = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(tooltipTriggerEl);
            } catch(e) {}
        });
    }

    // Re-render zones and slots dynamically from API response
    function renderDynamicParkingZones(zonesData) {
        const container = document.getElementById('parkingZonesContainer');
        if (!container || !zonesData) return;

        let html = '';

        zonesData.forEach(zone => {
            let zKosong = 0;
            let zTerisi = 0;
            if (zone.slots) {
                zone.slots.forEach(s => {
                    if (s.status_slot === 'kosong') zKosong++;
                    else if (s.status_slot === 'terisi') zTerisi++;
                });
            }

            html += `
            <div class="parking-zone-section card border-0 shadow-sm mb-3" data-zone-id="${zone.id}" id="zone-section-${zone.id}">
                <div class="card-body p-3">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        {{-- Left: Zone Info Box --}}
                        <div class="zone-info-box d-flex flex-row flex-lg-column justify-content-between justify-content-lg-center align-items-start flex-shrink-0"
                            style="min-width: 200px; max-width: 240px;">
                            <div class="w-100">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary fs-13 px-2 py-1">Zona ${zone.kode_zona}</span>
                                    <h6 class="mb-0 fw-bold text-dark text-truncate" title="${zone.nama_zona}">${zone.nama_zona}</h6>
                                </div>
                                ${zone.keterangan ? `<small class="text-muted d-block text-truncate mb-1" title="${zone.keterangan}">${zone.keterangan}</small>` : ''}
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1 w-100 flex-wrap">
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11">
                                    <i class="mdi mdi-check-circle me-1"></i>${zKosong} Tersedia
                                </span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-11">
                                    <i class="mdi mdi-car me-1"></i>${zTerisi} Terisi
                                </span>
                            </div>
                        </div>

                        {{-- Vertical separator on desktop --}}
                        <div class="vr d-none d-lg-block opacity-25" style="align-self: stretch; min-height: 50px;"></div>

                        {{-- Right: Slots Row (Mini Tiles) --}}
                        <div class="zone-slots-container flex-grow-1 overflow-x-auto py-1">
            `;

            if (zone.slots && zone.slots.length > 0) {
                html += `<div class="d-flex flex-wrap align-items-center gap-2">`;
                zone.slots.forEach(slot => {
                    const isKosong = (slot.status_slot === 'kosong');
                    const isTerisi = (slot.status_slot === 'terisi');
                    const isReserved = (slot.status_slot === 'reserved');
                    const active = slot.active_vehicle;

                    let tileClass = 'tile-kosong';
                    let iconClass = 'mdi-parking';
                    let tooltipText = `<strong>Slot ${slot.kode_slot}</strong><br><span class='text-success'>Tersedia (Klik untuk memilih)</span>`;
                    if (slot.jenis_kendaraan) {
                        tooltipText += `<br><small class='text-muted'>${slot.jenis_kendaraan}</small>`;
                    }
                    let clickAttr = `onclick="selectSlotInModal(${slot.id}, '${slot.kode_slot}', '${zone.nama_zona} (${zone.kode_zona})', '${slot.jenis_kendaraan || ''}')"`;

                    if (isTerisi) {
                        tileClass = 'tile-terisi';
                        iconClass = 'mdi-truck';
                        tooltipText = `<strong>Slot ${slot.kode_slot}</strong>`;
                        if (active) {
                            tooltipText += `<br><span class='text-danger fw-bold font-mono'>${active.no_polisi || '-'}</span>`;
                            if (active.nama_driver) {
                                tooltipText += `<br><small>${active.nama_driver}</small>`;
                            }
                        }
                        tooltipText += `<br><span class='badge bg-danger text-white mt-1'>Terisi</span>`;
                        clickAttr = '';
                    } else if (isReserved) {
                        tileClass = 'tile-reserved';
                        iconClass = 'mdi-bookmark';
                        tooltipText = `<strong>Slot ${slot.kode_slot}</strong><br><span class='text-info'>Reserved</span>`;
                        clickAttr = '';
                    } else if (!isKosong) {
                        tileClass = 'tile-maintenance';
                        iconClass = 'mdi-wrench';
                        tooltipText = `<strong>Slot ${slot.kode_slot}</strong><br><span class='text-warning'>Maintenance</span>`;
                        clickAttr = '';
                    }

                    const isSelected = (currentSelectedSlot.id && currentSelectedSlot.id == slot.id && isKosong);

                    html += `
                        <div class="slot-item-wrapper mini-slot-tile ${tileClass} ${isSelected ? 'is-selected' : ''}"
                             data-slot-id="${slot.id}"
                             data-slot-code="${slot.kode_slot}"
                             data-zone-id="${zone.id}"
                             data-zone-name="${zone.nama_zona} (${zone.kode_zona})"
                             data-status="${slot.status_slot}"
                             data-nopol="${active ? (active.no_polisi || '') : ''}"
                             data-driver="${active ? (active.nama_driver || '') : ''}"
                             data-vehicle="${slot.jenis_kendaraan || ''}"
                             data-bs-toggle="tooltip"
                             data-bs-html="true"
                             data-bs-placement="top"
                             data-bs-title="${tooltipText.replace(/"/g, '&quot;')}"
                             ${clickAttr}
                             id="slot-card-${slot.id}">
                            <i class="mdi ${iconClass} tile-icon"></i>
                            <span class="tile-code">${slot.kode_slot}</span>
                        </div>
                    `;
                });
                html += `</div>`;
            } else {
                html += `
                    <div class="text-muted small py-2">
                        <i class="mdi mdi-alert-circle-outline me-1"></i>Belum ada slot parkir yang terdaftar di zona ini.
                    </div>
                `;
            }

            html += `
                        </div>
                    </div>
                </div>
            </div>
            `;
        });

        container.innerHTML = html;
        initSlotModalTooltips();
    }

    // Initial calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateStatsCounters();
        // Set initial filter to show only kosong slots in modal by default so it's super easy to pick
        filterParkingSlots();
        initSlotModalTooltips();

        const modalEl = document.getElementById('modalParkingSlotPicker');
        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function() {
                initSlotModalTooltips();
            });
        }
    });
</script>
