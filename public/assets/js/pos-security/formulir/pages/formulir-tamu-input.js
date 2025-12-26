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
const startSelfieCamera = document.getElementById("startSelfieCamera");
const capturedSelfieContainer = document.getElementById(
    "capturedSelfieContainer"
);
const capturedSelfieImage = document.getElementById("capturedSelfieImage");

let selfieStream = null;
let selfiePhotos = [];
let currentSelfieIndex = -1;

// Fungsi untuk menghentikan stream selfie
function stopSelfieStream() {
    if (selfieStream) {
        selfieStream.getTracks().forEach((track) => track.stop());
        selfieStream = null;
    }
}

// Cek apakah browser mendukung kamera
function isCameraSupported() {
    return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

// Mulai kamera untuk foto selfie
async function startSelfieWebcam() {
    try {
        selfieStream = await navigator.mediaDevices.getUserMedia({
            video: { width: 320, height: 240, facingMode: "user" },
        });
        selfieVideo.srcObject = selfieStream;
        selfieVideo.style.display = "block";

        toggleElements([
            { el: startSelfieCamera, show: false },
            { el: captureSelfieBtn, show: true },
            { el: retakeSelfieBtn, show: false },
        ]);
    } catch (err) {
        alert("Gagal mengakses kamera: " + err.message);
        console.error(err);
    }
}

// Ambil foto selfie
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

// Ambil ulang foto selfie
function retakeSelfiePhoto() {
    capturedSelfieImage.src = "";
    capturedSelfieContainer.style.display = "none";
    toggleElements([
        { el: selfieVideo, show: true },
        { el: captureSelfieBtn, show: true },
        { el: retakeSelfieBtn, show: false },
        { el: saveSelfieBtn, show: false },
    ]);
    startSelfieWebcam();
}

// Simpan semua foto selfie
function saveAllSelfies() {
    const modal = bootstrap.Modal.getInstance(
        document.getElementById("selfieModal")
    );
    if (modal) modal.hide();

    updateSelfieHiddenInput();

    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Foto diri berhasil disimpan.",
        timer: 2000,
        showConfirmButton: false,
    });
}

// Tampilkan preview foto selfie
function renderSelfiePreviews() {
    const container = document.getElementById("selfiePreview");
    if (!container) return;

    container.innerHTML = "";

    if (selfiePhotos.length === 0) {
        const emptyMsg = document.createElement("div");
        emptyMsg.textContent = "Tidak ada foto yang diambil.";
        emptyMsg.className = "text-muted fst-italic mt-2";
        container.appendChild(emptyMsg);
        return;
    }

    selfiePhotos.forEach((photo, index) => {
        const wrapper = document.createElement("div");
        wrapper.className = "position-relative d-inline-block me-2";

        const img = document.createElement("img");
        img.src = photo;
        img.alt = "Foto Diri";
        img.className = "rounded shadow-sm captured-selfie";
        img.style.height = "70px";
        img.style.width = "auto";

        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.className =
            "btn btn-danger btn-sm position-absolute top-0 end-0 remove-selfie-btn";
        removeBtn.innerHTML = '<i class="mdi mdi-close"></i>';
        removeBtn.onclick = () => removeSelfiePhoto(index);

        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        container.appendChild(wrapper);
    });
}

// Hapus foto selfie tertentu
function removeSelfiePhoto(index) {
    Swal.fire({
        title: "Hapus foto?",
        icon: "warning",
        showCancelButton: true,
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

// Update nilai input hidden untuk foto diri
function updateSelfieHiddenInput() {
    const inputField = document.getElementById("selfiePhotos");
    if (inputField) {
        inputField.value = JSON.stringify(selfiePhotos);
        inputField.dispatchEvent(new Event("change"));
    }
}

// Mulai kamera untuk foto KTP
async function startWebcam() {
    if (!isCameraSupported()) {
        alert("Kamera tidak didukung di browser ini.");
        return;
    }

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { width: 400, height: 300, facingMode: "environment" },
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

// Ambil foto KTP
function captureImage() {
    if (!video || !canvas) return;

    const context = canvas.getContext("2d");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);
    const dataURL = canvas.toDataURL("image/jpeg", 0.8);

    capturedImage.src = dataURL;
    capturedImageContainer.style.display = "block";

    toggleElements([
        { el: video, show: false },
        { el: captureBtn, show: false },
        { el: retakeBtn, show: true },
        { el: saveBtn, show: true },
    ]);

    stopStream();
}

// Ambil ulang foto KTP
function retakePhoto() {
    capturedImage.src = "";
    capturedImageContainer.style.display = "none";
    toggleElements([
        { el: video, show: true },
        { el: captureBtn, show: true },
        { el: retakeBtn, show: false },
        { el: saveBtn, show: false },
    ]);
    startWebcam();
}

// Simpan foto KTP ke input hidden
function saveCaptureIdentitas() {
    const inputField = document.getElementById("imgvisitorpathin");
    const ktpPreview = document.getElementById("ktpPreview");

    if (!canvas) {
        Swal.fire("Error", "Canvas tidak ditemukan.", "error");
        return;
    }

    const imgData = canvas.toDataURL("image/jpeg", 0.8);
    if (!imgData || imgData === "data:,") {
        Swal.fire(
            "Peringatan",
            "Silakan ambil foto terlebih dahulu.",
            "warning"
        );
        return;
    }

    inputField.value = imgData;
    inputField.dispatchEvent(new Event("change"));

    ktpPreview.innerHTML = "";
    const imgElement = document.createElement("img");
    imgElement.src = imgData;
    imgElement.className = "img-fluid rounded shadow-sm";
    ktpPreview.appendChild(imgElement);
    ktpPreview.style.display = "block";

    const modal = bootstrap.Modal.getInstance(
        document.getElementById("myModal")
    );
    if (modal) modal.hide();

    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Foto KTP berhasil disimpan.",
        timer: 1500,
        showConfirmButton: false,
    });
}

// Reset preview foto
function resetPreviewImage() {
    $("#ktpPreview").hide().html("");
    $("#selfiePreview").hide().html("");
    $("#imgvisitorpathin").val("").trigger("change");
    $("#selfiePhotos").val("[]").trigger("change");
    selfiePhotos = [];
}

// Hapus foto KTP
window.removeKtpImage = function () {
    const ktpPreview = document.getElementById("ktpPreview");
    const inputField = document.getElementById("imgvisitorpathin");

    ktpPreview.innerHTML = "";
    ktpPreview.style.display = "none";
    inputField.value = "";
    alert("Foto KTP berhasil dihapus.");
};

// Toggle tampilan elemen
function toggleElements(elements = []) {
    elements.forEach(({ el, show }) => {
        if (el) el.style.display = show ? "inline-block" : "none";
    });
}

// Berhenti stream kamera
function stopStream() {
    const stream = video?.srcObject;
    if (stream && typeof stream.getTracks === "function") {
        stream.getTracks().forEach((track) => track.stop());
        video.srcObject = null;
    }
}
