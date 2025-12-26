// Inisialisasi elemen DOM
const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const captureBtn = document.getElementById("captureBtn");
const retakeBtn = document.getElementById("retakeBtn");
const startCamera = document.getElementById("startCamera");
const capturedImage = document.getElementById("capturedImage");
const capturedImageContainer = document.getElementById(
    "capturedImageContainer"
);
const selfieVideo = document.getElementById("selfieVideo");
const selfieCanvas = document.getElementById("selfieCanvas");
const selfieModal = document.getElementById("selfieModal");
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

function startQrScanner() {
    qrResult.textContent = "";
    qrResultInput.value = "";

    if (!isCameraSupported()) {
        alert("Kamera tidak didukung di browser ini.");
        return;
    }

    const scannerRegionId = "qr-reader";
    html5QrCode = new Html5Qrcode(scannerRegionId);

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        supportedFormats: [Html5QrcodeSupportedFormats.QR_CODE],
    };

    html5QrCode
        .start(
            { facingMode: "environment" },
            config,
            onScanSuccess,
            onScanError
        )
        .then(() => {
            isScannerRunning = true;
        })
        .catch((err) => {
            console.error("Tidak dapat mengakses kamera:", err);
            alert(
                "Gagal akses kamera. Pastikan izin diberikan dan perangkat memiliki kamera."
            );
        });
}

function onScanSuccess(decodedText) {
    qrResult.textContent = "Berhasil membaca QR Code: " + decodedText;
    qrResultInput.value = decodedText;

    $("#qrScannerModal").modal("hide");

    if (html5QrCode && isScannerRunning) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            isScannerRunning = false;
        });
    }
}

function onScanError(errorMessage) {
    if (errorMessage.includes("No MultiFormat Readers were able to detect"))
        return;
    console.warn("Scan error:", errorMessage);
}

function stopQrStream() {
    if (html5QrCode && isScannerRunning) {
        html5QrCode
            .stop()
            .then(() => {
                html5QrCode.clear();
                isScannerRunning = false;
            })
            .catch((err) => {
                console.error("Gagal menghentikan scanner:", err);
            });
    }
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

    // Kosongkan kontainer, tapi jangan hilangkan/hapus elemennya
    previewSelfieContainer.innerHTML = "";

    if (selfiePhotos.length === 0) {
        // Tampilkan pesan bahwa belum ada foto
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

    // console.log("📸 Jumlah selfie photos:", selfiePhotos.length);
    // console.log("🧾 Data selfiePhotos:", selfiePhotos);
}

function saveAllSelfies() {
    const modalElement = document.querySelector("#selfieModal");

    // Tutup modal dengan Bootstrap native atau fallback ke jQuery
    if (window.bootstrap && modalElement) {
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
            modalInstance.hide();
        }
    } else if (typeof $ !== "undefined") {
        $("#selfieModal").modal("hide");
    }

    // Update input hidden
    updateSelfieHiddenInput();

    // Notifikasi sukses dengan SweetAlert2
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
    ]);
    stopStream();
}

function retakePhoto() {
    if (capturedImageContainer) capturedImageContainer.style.display = "none";
    toggleElements([
        { el: video, show: true },
        { el: captureBtn, show: true },
        { el: retakeBtn, show: false },
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

    // Validasi: Pastikan canvas tersedia
    if (!canvas) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Canvas tidak ditemukan.",
            confirmButtonText: "OK",
        });
        return;
    }

    const imgData = canvas.toDataURL("image/jpeg", 0.8);

    // Validasi: Pastikan gambar ada
    if (!imgData || imgData === "data:,") {
        Swal.fire({
            icon: "warning",
            title: "Belum Ada Foto",
            text: "Silakan ambil foto terlebih dahulu.",
            confirmButtonText: "OK",
        });
        return;
    }

    // Update input hidden
    if (inputField) {
        inputField.value = imgData;
    }

    // Hapus semua konten lama di preview dan tambahkan gambar baru
    if (ktpPreview) {
        ktpPreview.innerHTML = "";

        const imgElement = document.createElement("img");
        imgElement.src = imgData;
        imgElement.className = "captured-image";
        imgElement.alt = "KTP Image";
        imgElement.style.maxWidth = "100%";
        imgElement.style.borderRadius = "6px";

        ktpPreview.appendChild(imgElement);
    }

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

    // Notifikasi sukses dengan SweetAlert
    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Foto berhasil disimpan.",
        confirmButtonText: "Lanjutkan",
    });
}

function resetPreviewImage() {
    $("#ktpPreview").hide();
    $("#ktpImage").attr("src", "");

    $("#selfiePreview").hide();
    $("#selfiePreview").html("");
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

// Tambahkan handler ke tombol Save Changes jika ada
document.addEventListener("DOMContentLoaded", function () {
    const saveBtn = document.querySelector(
        '[onclick="saveCaptureIdentitas()"]'
    );
    if (saveBtn) {
        saveBtn.addEventListener("click", saveCaptureIdentitas);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    $("#qrScannerModal").on("shown.bs.modal", startQrScanner);
    $("#qrScannerModal").on("hidden.bs.modal", stopQrStream);

    if (openSelfieModalBtn) {
        openSelfieModalBtn.addEventListener("click", () => {
            $("#selfieModal").modal("show");
        });
    }
    if (startSelfieBtn) {
        startSelfieBtn.addEventListener("click", startSelfieWebcam);
    }

    if (selfieModal) {
        selfieModal.addEventListener("shown.bs.modal", startSelfieWebcam);
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

    window.removeKtpImage = function () {
        const ktpImage = document.getElementById("ktpImage");
        const ktpPreview = document.getElementById("ktpPreview");
        const inputField = document.getElementById("imgvisitorpathin");
        if (ktpImage) ktpImage.src = "";
        if (ktpPreview) ktpPreview.style.display = "none";
        if (inputField) inputField.value = "";
        alert("Foto KTP berhasil dihapus.");
    };

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
    const saveBtn = document.querySelector(
        '[onclick="saveCaptureIdentitas()"]'
    );
    if (saveBtn) {
        saveBtn.addEventListener("click", saveCaptureIdentitas);
    }
});

$("#loadRealtimeVisitor").on("click", function () {
    $.ajax({
        url: VAR_REALTIME_VISITOR,
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
            $("#createdby").val(data.CREATEDBY || ""); // <--- tambahkan ini

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

            // Reset alert
            $("#formAlert")
                .hide() // ini sembunyikan kotaknya
                .removeClass("alert-success alert-danger")
                .html("");

            // Optional: Reset form atau reload data
            // $("#visitorForm")[0].reset();
            // $("#imgvisitorpathin").val("");
            // $("#selfiePhotos").val("");

            // resetPreviewImage();

            $("#submitBtn")
                .prop("disabled", false)
                .html('<i class="fas fa-save me-2"></i>Simpan Data');

            // alert("Data berhasil dimuat!");
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
    // Reset video stream
    stopStream();
    stopSelfieStream();

    // Sembunyikan dan reset preview KTP
    $("#ktpPreview").hide();
    $("#ktpImage").attr("src", "");
    $("#imgvisitorpathin").val("");

    // Reset preview selfie
    $("#selfiePreview").hide();
    $("#selfiePreview").html("");
    $("#selfiePhotos").val("");

    // Optional: Reset tombol kamera
    toggleElements([
        { el: document.getElementById("video"), show: false },
        { el: document.getElementById("startCamera"), show: true },
        { el: document.getElementById("captureBtn"), show: false },
        { el: document.getElementById("retakeBtn"), show: false },
    ]);
});
