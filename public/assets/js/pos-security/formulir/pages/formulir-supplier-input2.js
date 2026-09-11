// =====================================================
// VALIDASI 5 FIELD WAJIB untuk mengaktifkan RFID:
// keterangan, namavisitor, namacomp, nomorktp, nopol
// =====================================================

function checkAllRequiredElements() {
    const rfidField = document.querySelector('input[name="rfid"]');
    if (!rfidField) return;

    const keterangan = document.querySelector('[name="keterangan"]');
    const namaVisitor = document.querySelector('[name="namavisitor"]');
    const namaComp = document.querySelector('[name="namacomp"]');
    const nomorKtp = document.querySelector('[name="nomorktp"]');
    const nopol = document.querySelector('[name="nopol"]');

    const hasKeterangan = keterangan && keterangan.value.trim() !== "";
    const hasNama = namaVisitor && namaVisitor.value.trim() !== "";
    const hasComp = namaComp && namaComp.value.trim() !== "";
    const hasNomorKtp = nomorKtp && nomorKtp.value.trim() !== "";
    const hasNopol = nopol && nopol.value.trim() !== "";

    const allCompleted = hasKeterangan && hasNama && hasComp && hasNomorKtp && hasNopol;

    rfidField.disabled = !allCompleted;

    updateRfidFieldMessage(allCompleted, hasKeterangan, hasNama, hasComp, hasNomorKtp, hasNopol);
}

function updateRfidFieldMessage(allCompleted, hasKeterangan, hasNama, hasComp, hasNomorKtp, hasNopol) {
    let existingMessage = document.getElementById("rfidFieldMessage");

    if (allCompleted) {
        if (existingMessage) existingMessage.remove();
        return;
    }

    if (!existingMessage) {
        existingMessage = document.createElement("small");
        existingMessage.id = "rfidFieldMessage";
        existingMessage.className = "text-warning mt-1 d-block";
        const rfidField = document.querySelector('input[name="rfid"]');
        rfidField.parentNode.appendChild(existingMessage);
    }

    const missingItems = [];
    if (!hasKeterangan) missingItems.push("Keterangan");
    if (!hasNama)       missingItems.push("Nama Supir/Kernet");
    if (!hasComp)       missingItems.push("Nama Perusahaan");
    if (!hasNomorKtp)   missingItems.push("Nomor KTP/SIM");
    if (!hasNopol)      missingItems.push("Nomor Polisi");

    existingMessage.innerHTML = `<i class="mdi mdi-information-outline me-1"></i> Lengkapi terlebih dahulu: ${missingItems.join(", ")}`;
}

// =====================================================
// TOGGLE VISIBILITY AREA PARKIR
// Hanya muncul jika Keterangan Pengunjung adalah 'supir'
// =====================================================

function toggleParkingSlot() {
    const keterangan = document.querySelector('[name="keterangan"]');
    const parkingContainer = document.getElementById("parkingSlotContainer");
    const parkingSlotSelect = document.getElementById("parking_slot_id");

    if (!parkingContainer) return;

    if (keterangan && keterangan.value.toLowerCase() === "supir") {
        $(parkingContainer).slideDown(200);
    } else {
        $(parkingContainer).slideUp(200);
        if (parkingSlotSelect) {
            parkingSlotSelect.value = "";
        }
        if (typeof clearParkingSlotSelection === "function") {
            clearParkingSlotSelection();
        }
    }
}

// =====================================================
// RESET FORM
// =====================================================

function resetForm() {
    $("#visitorForm")[0].reset();

    // Reset visibility plotting area parkir & UI selection
    if (typeof clearParkingSlotSelection === "function") {
        clearParkingSlotSelection();
    }
    toggleParkingSlot();

    // Hapus pesan RFID jika ada
    const existingMessage = document.getElementById("rfidFieldMessage");
    if (existingMessage) existingMessage.remove();

    // Disable RFID setelah reset
    const rfidField = document.querySelector('input[name="rfid"]');
    if (rfidField) rfidField.disabled = true;

    // Reset alert form
    $("#formAlert").stop(true).hide().removeClass("alert-success alert-danger").html("");

    Swal.fire({
        icon: "success",
        title: "Form berhasil direset",
        text: "Semua data sudah dibersihkan.",
        timer: 2000,
        showConfirmButton: false,
    });
}

// =====================================================
// INIT - pasang listener ke 5 field wajib & keterangan
// =====================================================

document.addEventListener("DOMContentLoaded", function () {
    const keteranganEl = document.querySelector('[name="keterangan"]');
    if (keteranganEl) {
        keteranganEl.addEventListener("change", toggleParkingSlot);
    }

    const watchedInputs = [
        document.querySelector('[name="keterangan"]'),
        document.querySelector('[name="namavisitor"]'),
        document.querySelector('[name="namacomp"]'),
        document.querySelector('[name="nomorktp"]'),
        document.querySelector('[name="nopol"]'),
    ].filter((el) => el !== null);

    watchedInputs.forEach((input) => {
        input.addEventListener("input", checkAllRequiredElements);
        input.addEventListener("change", checkAllRequiredElements);
    });

    // Inisialisasi awal saat halaman load
    toggleParkingSlot();
    checkAllRequiredElements();
});