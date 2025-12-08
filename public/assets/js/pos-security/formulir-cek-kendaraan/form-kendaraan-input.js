const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const captureBtn = document.getElementById("captureBtn");
const retakeBtn = document.getElementById("retakeBtn");
const saveBtn = document.getElementById("saveBtn");
const startCamera = document.getElementById("startCamera");
const capturedImage = document.getElementById("capturedImage");
const capturedImageContainer = document.getElementById(
    "capturedImageContainer"
);

let activePhotoKey = null;

function formatDate(dateStr) {
    if (!dateStr) return "-";
    const parts = dateStr.split("-");
    return parts[2] + "/" + parts[1] + "/" + parts[0];
}

function formatTime(datetimeStr) {
    if (!datetimeStr) return "-";
    return datetimeStr.substr(11, 8);
}

function toggleElements(elements = []) {
    elements.forEach(({ el, show }) => {
        if (el) el.style.display = show ? "inline-block" : "none";
    });
}

async function startWebcam(options = {}) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 400 },
                height: { ideal: 300 },
                facingMode: "environment",
                ...options,
            },
        });
        if (video) {
            video.srcObject = stream;
            video.style.display = "block";
        }
        toggleElements([
            { el: startCamera, show: false },
            { el: captureBtn, show: true },
            { el: retakeBtn, show: false },
            { el: saveBtn, show: false },
        ]);
    } catch (err) {
        alert("Gagal mengakses kamera: " + err.message);
        console.error(err);
    }
}

function captureImage() {
    if (!video || !canvas) return;

    const context = canvas.getContext("2d");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);

    const dataURL = canvas.toDataURL("image/jpeg", 0.8);
    if (capturedImage) capturedImage.src = dataURL;
    if (capturedImageContainer) capturedImageContainer.style.display = "block";

    toggleElements([
        { el: video, show: false },
        { el: captureBtn, show: false },
        { el: retakeBtn, show: true },
        { el: saveBtn, show: true },
    ]);
    stopStream();
}

function retakePhoto() {
    if (capturedImageContainer) capturedImageContainer.style.display = "none";
    toggleElements([
        { el: video, show: true },
        { el: captureBtn, show: true },
        { el: retakeBtn, show: false },
        { el: saveBtn, show: false },
    ]);
    startCamera.click();
}

function stopStream() {
    const stream = video?.srcObject;
    if (stream && typeof stream.getTracks === "function") {
        stream.getTracks().forEach((track) => track.stop());
        video.srcObject = null;
    }
}

function saveCapture() {
    if (!activePhotoKey) {
        Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Slot foto tidak ditemukan",
        });
        return;
    }

    const canvas = document.getElementById("canvas");
    const imgData = canvas.toDataURL("image/jpeg", 0.8);

    const input = document.getElementById(`input-${activePhotoKey}`);
    input.value = imgData;

    const previewBox = document.getElementById(`preview-${activePhotoKey}`);
    previewBox.innerHTML = `
        <div class="position-relative">
            <img src="${imgData}" class="img-fluid rounded shadow" style="max-height:160px">
            <button type="button"
                class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-photo"
                data-key="${activePhotoKey}">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    const modal = bootstrap.Modal.getInstance(
        document.getElementById("myModal")
    );
    modal.hide();

    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Foto berhasil disimpan",
    });
}

function resetCameraModal() {
    stopStream();

    if (capturedImage) capturedImage.src = "";
    if (capturedImageContainer) capturedImageContainer.style.display = "none";

    toggleElements([
        { el: startCamera, show: true },
        { el: captureBtn, show: false },
        { el: retakeBtn, show: false },
        { el: saveBtn, show: false },
    ]);

    if (video) video.style.display = "none";

    if (canvas) {
        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const nopolInput = document.getElementById("nopol-search");
    if (nopolInput) nopolInput.focus();

    const modalElement = document.getElementById("myModal");

    if (modalElement) {
        modalElement.addEventListener("shown.bs.modal", () => {
            resetCameraModal();
            startWebcam();
        });

        modalElement.addEventListener("hidden.bs.modal", () => {
            resetCameraModal();
        });
    }

    if (startCamera) startCamera.addEventListener("click", startWebcam);
    if (captureBtn) captureBtn.addEventListener("click", captureImage);
    if (retakeBtn) retakeBtn.addEventListener("click", retakePhoto);
    if (saveBtn) saveBtn.addEventListener("click", saveCapture);
});

$("#nopol-search").on("keypress", function (e) {
    if (e.which === 13) {
        e.preventDefault();
        $("#searchVisitorData").click();
    }
});

// search data
$("#searchVisitorData").on("click", function () {
    const keyword = $("#nopol-search").val().trim();

    if (!keyword) {
        Swal.fire({
            icon: "error",
            title: "Oops!",
            text: "Nomor polisi wajib diisi",
        });
        return;
    }

    $.ajax({
        // todo: change to var
        // url: "{{ route('ajax.pos-security.cek-kendaraan.search') }}",
        url: "/search-kendaraan",
        type: "GET",
        data: {
            keyword: keyword,
        },
        beforeSend: function () {
            $("#searchVisitorData")
                .prop("disabled", true)
                .html('<i class="fas fa-spinner fa-spin me-2"></i>Mencari...');
        },
        success: function (res) {
            if (res.success) {
                const data = res.data;

                // show form view
                $("#cekKendaraanForm").show();
                $("#cekKendaraanForm")[0].scrollIntoView({
                    behavior: "smooth",
                });

                // autofill Field
                $("#nama-supir").val(data.namavisitor).prop("disabled", false);
                $("#company").val(data.namacomp).prop("disabled", false);
                $("#nomor-polisi").val(data.nopol).prop("disabled", false);
                $("#createdby").val(data.createdby || "");
            } else {
                $("#cekKendaraanForm").hide();
                Swal.fire({
                    icon: "warning",
                    title: "Tidak Ditemukan",
                    text: res.message,
                });
            }
        },
        error: function (xhr) {
            let msg = "Terjadi kesalahan saat mencari data";

            if (xhr.status === 422 || xhr.status === 409) {
                msg = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: "error",
                title: "Oops!",
                text: msg,
            });

            $("#searchResult").hide();
        },
        complete: function () {
            $("#searchVisitorData")
                .prop("disabled", false)
                .html('<i class="fas fa-search me-2"></i> Cari');
        },
    });
});

// slot handler
$(document).on("click", ".open-camera", function () {
    activePhotoKey = $(this).data("key");
});

$(document).on("click", ".remove-photo", function () {
    const key = $(this).data("key");

    $(`#preview-${key}`).html("");
    $(`#input-${key}`).val("");
});

let currentMode = "in";

$("#tab-in").on("click", function () {
    switchMode("in");
});

$("#tab-out").on("click", function () {
    switchMode("out");
});

function switchMode(mode) {
    currentMode = mode;
    $("#formMode").val(mode);

    $(".tab-card").removeClass("active");
    $(`#tab-${mode}`).addClass("active");

    if (mode === "in") {
        // =========================
        // MODE MASUK
        // =========================
        $("h2").text("Form Pengecekan Kendaraan (Masuk)");
        $("p.text-muted").text(
            "Silakan isi data kendaraan yang akan masuk ke area"
        );

        $("#cekKendaraanForm").attr(
            "action",
            "{{ route('ajax.pos-security.cek-kendaraan.store') }}"
        );

        // Aktifkan field pemeriksaan
        $("#tgl-periksa, #jam_periksa, #muatanType, #truckType").prop(
            "disabled",
            false
        );

        $("#fotoSection").show();

        $("#submitBtn span").text("Simpan Data (MASUK)");
    } else {
        // =========================
        // MODE KELUAR
        // =========================
        $("h2").text("Form Pengecekan Kendaraan (Keluar)");
        $("p.text-muted").text(
            "Silakan lakukan pengecekan kendaraan yang akan keluar"
        );

        $("#cekKendaraanForm").attr(
            "action",
            "{{ route('ajax.pos-security.cek-kendaraan.gate-out') }}"
        );

        // Nonaktifkan field yang hanya untuk IN
        $("#tgl-periksa, #jam_periksa, #muatanType, #truckType").prop(
            "disabled",
            true
        );

        $("#fotoSection").show(); // tetap tampil, tapi fotonya untuk OUT

        $("#submitBtn span").text("Simpan Data (KELUAR)");
    }
}
