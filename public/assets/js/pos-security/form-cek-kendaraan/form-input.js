// Inisialisasi elemen DOM
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
const selfieVideo = document.getElementById("selfieVideo");
const selfieCanvas = document.getElementById("selfieCanvas");
const captureSelfieBtn = document.getElementById("captureSelfieBtn");
const retakeSelfieBtn = document.getElementById("retakeSelfieBtn");
const saveSelfieBtn = document.getElementById("saveSelfieBtn");
const startSelfieBtn = document.getElementById("startSelfieCamera");
const capturedSelfieContainer = document.getElementById(
    "capturedSelfieContainer"
);
const capturedSelfieImage = document.getElementById("capturedSelfieImage");
const openSelfieModalBtn = document.getElementById("openSelfieCamera");
const qrResult = document.getElementById("qrResult");
const qrResultInput = document.getElementById("qrResultInput");

let html5QrCode = null;
let isScannerRunning = false;
let selfieStream = null;
let currentSelfieIndex = -1;
let selfiePhotos = [];

function stopSelfieStream() {
    if (selfieStream) {
        selfieStream.getTracks().forEach((track) => track.stop());
        selfieStream = null;
    }
}

function isCameraSupported() {
    return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

async function startSelfieWebcam() {
    try {
        selfieStream = await navigator.mediaDevices.getUserMedia({
            video: { width: 320, height: 240, facingMode: "user" },
        });
        selfieVideo.srcObject = selfieStream;
        selfieVideo.style.display = "block";
        toggleElements([
            { el: startSelfieBtn, show: false },
            { el: captureSelfieBtn, show: true },
            { el: retakeSelfieBtn, show: false },
        ]);
    } catch (err) {
        alert("Gagal mengakses kamera: " + err.message);
        console.error(err);
    }
}

function captureSelfiePhoto() {
    const ctx = selfieCanvas.getContext("2d");
    selfieCanvas.width = selfieVideo.videoWidth;
    selfieCanvas.height = selfieVideo.videoHeight;
    ctx.drawImage(selfieVideo, 0, 0);
    const dataURL = selfieCanvas.toDataURL("image/jpeg", 0.8);
    capturedSelfieImage.src = dataURL;
    capturedSelfieContainer.style.display = "block";
    if (currentSelfieIndex >= 0) {
        selfiePhotos[currentSelfieIndex] = dataURL;
        currentSelfieIndex = -1;
    } else {
        selfiePhotos.push(dataURL);
    }
    toggleElements([
        { el: selfieVideo, show: false },
        { el: captureSelfieBtn, show: false },
        { el: retakeSelfieBtn, show: true },
        { el: saveSelfieBtn, show: true },
    ]);

    stopSelfieStream();
    renderSelfiePreviews();
    updateSelfieHiddenInput();

    checkAllRequiredElements();
}

function retakeSelfiePhoto() {
    capturedSelfieImage.src = "";
    capturedSelfieImage.removeAttribute("src");
    capturedSelfieContainer.style.display = "none";

    toggleElements([
        { el: selfieVideo, show: true },
        { el: captureSelfieBtn, show: true },
        { el: retakeSelfieBtn, show: false },
        { el: saveSelfieBtn, show: false },
    ]);
    startSelfieWebcam();
}

function renderSelfiePreviews() {
    const previewSelfieContainer = document.getElementById("selfiePreview");

    if (!previewSelfieContainer) {
        console.error("Preview container tidak ditemukan.");
        return;
    }

    previewSelfieContainer.innerHTML = "";

    if (selfiePhotos.length === 0) {
        const emptyMessage = document.createElement("div");
        emptyMessage.textContent = "Tidak ada foto yang diunggah.";
        emptyMessage.classList.add("text-muted", "fst-italic", "mt-2");
        previewSelfieContainer.appendChild(emptyMessage);
        return;
    }

    selfiePhotos.forEach((photo, index) => {
        const wrapper = document.createElement("div");
        wrapper.className = "position-relative d-inline-block me-2";

        const img = document.createElement("img");
        img.src = photo;
        img.alt = "Foto Diri";
        img.classList.add("rounded", "shadow-sm", "captured-selfie");
        img.style.height = "70px";
        img.style.width = "auto";

        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.classList.add(
            "btn",
            "btn-danger",
            "btn-sm",
            "position-absolute",
            "top-0",
            "end-0",
            "remove-selfie-btn"
        );
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.setAttribute("aria-label", "Hapus foto");
        removeBtn.onclick = () => removeSelfiePhoto(index);

        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        previewSelfieContainer.appendChild(wrapper);
    });
}

function removeSelfiePhoto(index) {
    Swal.fire({
        title: "Hapus foto ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            selfiePhotos.splice(index, 1);
            renderSelfiePreviews();
            updateSelfieHiddenInput();

            checkAllRequiredElements();
        }
    });
}

function updateSelfieHiddenInput() {
    const selfieInputField = document.getElementById("selfiePhotos");
    if (
        selfieInputField &&
        Array.isArray(selfiePhotos) &&
        selfiePhotos.length > 0
    ) {
        selfieInputField.value = JSON.stringify(selfiePhotos);
    } else {
        console.warn("selfiePhotos kosong atau bukan array.");
    }
}

function saveAllSelfies() {
    const modalElement = document.querySelector("#selfieModal");

    if (window.bootstrap && modalElement) {
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    } else if (typeof $ !== "undefined") {
        $("#selfieModal").modal("hide");
    }

    updateSelfieHiddenInput();
    checkAllRequiredElements();

    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Semua foto diri berhasil disimpan.",
        confirmButtonText: "Lanjutkan",
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
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

function toggleElements(elements = []) {
    elements.forEach(({ el, show }) => {
        if (el) el.style.display = show ? "inline-block" : "none";
    });
}

function saveCaptureIdentitas() {
    const canvas = document.getElementById("canvas");
    const inputField = document.getElementById("imgvisitorpathin");
    const ktpPreview = document.getElementById("ktpPreview");

    try {
        if (!canvas) {
            throw new Error("Canvas tidak ditemukan.");
        }

        const imgData = canvas.toDataURL("image/jpeg", 0.8);

        if (!imgData || imgData === "data:,") {
            throw new Error("Belum ada foto.");
        }

        // Update input hidden
        if (inputField) {
            inputField.value = imgData;
        }

        // Update preview
        if (ktpPreview) {
            ktpPreview.innerHTML = "";
            const imgElement = document.createElement("img");
            imgElement.src = imgData;
            imgElement.className = "captured-image";
            imgElement.alt = "KTP Image";
            imgElement.style.maxWidth = "100%";
            imgElement.style.borderRadius = "6px";
            ktpPreview.appendChild(imgElement);
            ktpPreview.style.display = "block"; // Pastikan ditampilkan
        }

        // 👇 VALIDASI WAJIB DIPANGGIL DI SINI
        checkAllRequiredElements();

        // Tutup modal
        const modalElement = document.querySelector("#myModal");
        if (window.bootstrap && modalElement) {
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
        } else if (typeof $ !== "undefined") {
            $("#myModal").modal("hide");
        }

        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Foto berhasil disimpan.",
            confirmButtonText: "Lanjutkan",
        });
    } catch (err) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: err.message || "Terjadi kesalahan saat menyimpan foto.",
            confirmButtonText: "OK",
        });

        // 👇 PASTIKAN VALIDASI JUGA DIPANGGIL MESKI ERROR
        checkAllRequiredElements();
    }
}

function resetPreviewImage() {
    $("#ktpPreview").hide();
    $("#ktpImage").attr("src", "");

    $("#selfiePreview").hide();
    $("#selfiePreview").html("");

    selfiePhotos = [];
    checkAllRequiredElements();
}

function formatDate(dateStr) {
    if (!dateStr) return "-";
    const parts = dateStr.split("-");
    return parts[2] + "/" + parts[1] + "/" + parts[0];
}

function formatTime(datetimeStr) {
    if (!datetimeStr) return "-";
    return datetimeStr.substr(11, 8);
}

document.addEventListener("DOMContentLoaded", function () {
    // Selfie Modal
    if (openSelfieModalBtn) {
        openSelfieModalBtn.addEventListener("click", () => {
            $("#selfieModal").modal("show");
        });
    }
    if (startSelfieBtn) {
        startSelfieBtn.addEventListener("click", startSelfieWebcam);
    }
    if (captureSelfieBtn) {
        captureSelfieBtn.addEventListener("click", captureSelfiePhoto);
    }
    if (retakeSelfieBtn) {
        retakeSelfieBtn.addEventListener("click", retakeSelfiePhoto);
    }
    if (saveSelfieBtn) {
        saveSelfieBtn.addEventListener("click", saveAllSelfies);
    }
    $("#selfieModal").on("hidden.bs.modal", stopSelfieStream);

    // Hapus KTP
    window.removeKtpImage = function () {
        const ktpImage = document.getElementById("ktpImage");
        const ktpPreview = document.getElementById("ktpPreview");
        const inputField = document.getElementById("imgvisitorpathin");
        if (ktpImage) ktpImage.src = "";
        if (ktpPreview) ktpPreview.style.display = "none";
        if (inputField) inputField.value = "";

        alert("Foto KTP berhasil dihapus.");
    };

    // Kamera KTP
    const modalElement = document.getElementById("myModal");
    if (modalElement) {
        modalElement.addEventListener("shown.bs.modal", () => startWebcam());
        modalElement.addEventListener("hidden.bs.modal", stopStream);
    }
    if (startCamera) {
        startCamera.addEventListener("click", () => startWebcam());
    }
    if (captureBtn) {
        captureBtn.addEventListener("click", captureImage);
    }
    if (retakeBtn) {
        retakeBtn.addEventListener("click", retakePhoto);
    }

    // Tombol simpan KTP
    const saveBtn = document.getElementById("saveKtpBtn");
    if (saveBtn) {
        saveBtn.addEventListener("click", saveCaptureIdentitas);
    }
});

$("#loadRealtimeVisitor").on("click", function () {
    $.ajax({
        // url: VAR_REALTIME_VISITOR,
        url: "/search-supplier",
        type: "GET",
        dataType: "json",
        beforeSend: function () {
            $("#loadRealtimeVisitor")
                .prop("disabled", true)
                .html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...');
        },
        success: function (data) {
            console.log("Realtime Visitor Data:", data);
            $("#namavisitor").val(data.NAMAVISITOR);
            $("#namacomp").val(data.NAMACOMP);
            $("#purpose").val(data.PURPOSE).trigger("change");
            $("#nopol").val(data.NOPOL);
            $("#sumpeople").val(data.SUMPEOPLE);
            $("#nohpdriver").val(data.NOHPDRIVER);
            $("#imgvisitorpathin").val(data.IMGVISITORPATHIN);
            $("#createdby").val(data.CREATEDBY || "");

            if (data.IMGVISITORPATHIN && data.IMGVISITORPATHIN !== "") {
                $("#ktpImage").attr("src", data.IMGVISITORPATHIN);
                $("#ktpPreview").show();
            } else {
                $("#ktpImage").attr("src", "");
                $("#ktpPreview").hide();
            }

            $('small:contains("Gate In:")').html(
                "<strong>Gate In:</strong> " + data.GATEIDIN
            );
            $('small:contains("Gate Line In:")').html(
                "<strong>Gate Line In:</strong> " + data.GATELINEIDIN
            );
            $('small:contains("Tanggal Masuk:")').html(
                "<strong>Tanggal Masuk:</strong> " + formatDate(data.DATEIN)
            );
            $('small:contains("Waktu Masuk:")').html(
                "<strong>Waktu Masuk:</strong> " + formatTime(data.TIMEIN)
            );

            $("#formAlert")
                .hide()
                .removeClass("alert-success alert-danger")
                .html("");

            $("#submitBtn")
                .prop("disabled", false)
                .html('<i class="fas fa-save me-2"></i>Simpan Data');
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
            alert("Gagal ambil data realtime.");
        },
        complete: function () {
            $("#loadRealtimeVisitor")
                .prop("disabled", false)
                .html(
                    '<i class="fas fa-sync-alt me-2"></i>Load Realtime Visitor'
                );
        },
    });
});

$('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
    stopStream();
    stopSelfieStream();

    $("#ktpPreview").hide();
    $("#ktpImage").attr("src", "");
    $("#imgvisitorpathin").val("");

    $("#selfiePreview").hide();
    $("#selfiePreview").html("");
    $("#selfiePhotos").val("");
    selfiePhotos = [];

    toggleElements([
        { el: document.getElementById("video"), show: false },
        { el: document.getElementById("startCamera"), show: true },
        { el: document.getElementById("captureBtn"), show: false },
        { el: document.getElementById("retakeBtn"), show: false },
    ]);
});
