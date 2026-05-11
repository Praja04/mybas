// form ajax
$(document).ready(function () {
    $("#visitorForm").on("submit", function (e) {
        e.preventDefault(); // Jangan submit default

        // Disable button biar user gak double klik
        $("#submitBtn")
            .prop("disabled", true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

        // Clear alert
        $("#formAlert")
            .hide()
            .removeClass("alert-success alert-danger")
            .html("");

        // Prepare form data
        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message || "Data berhasil disimpan.",
                    timer: 2000,
                    showConfirmButton: false,
                });

                console.log(response);

                $("#formAlert")
                    .stop(true)
                    .hide()
                    .removeClass("alert-danger alert-success")
                    .addClass("alert-success")
                    .html(response.message)
                    .fadeIn();

                // Hide alert setelah 2 detik
                setTimeout(function () {
                    $("#formAlert")
                        .fadeOut()
                        .removeClass("alert-success alert-danger")
                        .html("");
                }, 2000);

                // Reset form
                $("#visitorForm")[0].reset(); // reset semua input biasa

                resetKtpData();
                resetPreviewImage(); // Reset semua tampilan preview
                resetModalKamera(); // Stop kamera & clear canvas
                selfiePhotos = []; // Reset array selfie
                if (typeof updateSelfieHiddenInput === "function") {
                    updateSelfieHiddenInput(); // Sync ke input hidden
                }
                if (typeof renderSelfiePreviews === "function") {
                    renderSelfiePreviews();
                }

                if (typeof resetKacamata === "function") {
                    resetKacamata();
                }

                // TAMBAHAN: Reset QR Code result
                if (document.getElementById("qrResultInput")) {
                    document.getElementById("qrResultInput").value = "";
                }
                if (document.getElementById("qrResult")) {
                    document.getElementById("qrResult").textContent = "";
                }

                // TAMBAHAN: Cek validasi RFID setelah reset (harus disable lagi)
                if (typeof checkAllRequiredElements === "function") {
                    checkAllRequiredElements();
                }

                // Reset tombol dan status
                $("#submitBtn")
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');

                console.log("Selfie photos:", selfiePhotos);
                console.log(
                    "Hidden input value:",
                    document.getElementById("selfiePhotos")?.value,
                );

                location.reload();
            },
            error: function (xhr) {
                // Ambil pesan dari response jika tersedia
                let message = "Terjadi kesalahan. Silakan coba lagi.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                // Notifikasi SweetAlert Error
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: message,
                    timer: 2500,
                    showConfirmButton: false,
                });

                // Tampilkan alert inline
                $("#formAlert")
                    .stop(true)
                    .hide()
                    .removeClass("alert-success alert-danger")
                    .addClass("alert-danger")
                    .html(message)
                    .fadeIn();

                // Hide alert setelah 3 detik
                setTimeout(function () {
                    $("#formAlert")
                        .fadeOut()
                        .removeClass("alert-success alert-danger")
                        .html("");
                }, 3000);

                // Aktifkan kembali tombol submit
                $("#submitBtn")
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');
            },
        });
    });
});

// reset preview kamera
function resetPreviewImage() {
    // Reset KTP Preview
    $("#ktpPreview")
        .empty()
        .append(
            '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>',
        );

    // Reset Selfie Preview
    $("#selfiePreview")
        .empty()
        .append(
            '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>',
        );

    // Reset variabel selfiePhotos jika ada
    if (typeof selfiePhotos !== "undefined") {
        selfiePhotos = [];
    }

    if (typeof selfiePhotos !== "undefined") {
        selfiePhotos.length = 0; // cara aman untuk benar-benar kosongkan array
    }

    // Sync ke hidden input jika ada
    if (typeof updateSelfieHiddenInput === "function") {
        updateSelfieHiddenInput();
    }

    // Reset input hidden - PASTIKAN BENAR-BENAR KOSONG
    $("#imgvisitorpathin").val("");
    $("#selfiePhotos").val("");
    $("#capturedSelfieImage").val("");

    // Tambahan: Reset juga dengan vanilla JS untuk memastikan
    const imgInput = document.getElementById("imgvisitorpathin");
    const selfieInput = document.getElementById("selfiePhotos");
    if (imgInput) imgInput.value = "";
    if (selfieInput) selfieInput.value = "";

    console.log(
        "Reset completed - imgvisitorpathin value:",
        $("#imgvisitorpathin").val(),
    );
    console.log(
        "Reset completed - selfiePhotos value:",
        $("#selfiePhotos").val(),
    );
}

function resetModalKamera() {
    // 🔴 RESET MODAL KTP
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const capturedImage = document.getElementById("capturedImage");
    const capturedImageContainer = document.getElementById(
        "capturedImageContainer",
    );

    if (video && video.srcObject) {
        const stream = video.srcObject;
        stream.getTracks().forEach((track) => track.stop());
        video.srcObject = null;
    }
    if (video) video.style.display = "none";
    if (canvas) canvas.style.display = "none";
    if (capturedImage) capturedImage.src = "";
    if (capturedImageContainer) capturedImageContainer.style.display = "none";
    $("#startCamera").show();
    $("#captureBtn, #retakeBtn").hide();

    // 🔵 RESET MODAL SELFIE
    const selfieVideo = document.getElementById("selfieVideo");
    const selfieCanvas = document.getElementById("selfieCanvas");
    const capturedSelfieImage = document.getElementById("capturedSelfieImage");
    const capturedSelfieContainer = document.getElementById(
        "capturedSelfieContainer",
    );

    if (selfieVideo && selfieVideo.srcObject) {
        const selfieStream = selfieVideo.srcObject;
        selfieStream.getTracks().forEach((track) => track.stop());
        selfieVideo.srcObject = null;
    }
    if (selfieVideo) selfieVideo.style.display = "none";
    if (selfieCanvas) selfieCanvas.style.display = "none";
    if (capturedSelfieImage) capturedSelfieImage.src = "";
    if (capturedSelfieContainer) capturedSelfieContainer.style.display = "none";
    $("#startSelfieCamera").show();
    $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
}

function resetForm() {
    // Reset elemen form
    $("#visitorForm")[0].reset(); // Reset semua input biasa

    // Reset preview image & kamera
    resetPreviewImage();
    resetModalKamera();

    // Reset variabel selfie jika ada
    if (typeof selfiePhotos !== "undefined") {
        selfiePhotos = [];
        updateSelfieHiddenInput?.();
        renderSelfiePreviews?.();
    }

    if (typeof resetKacamata === "function") {
        resetKacamata();
    }

    // TAMBAHAN: Reset QR Code result
    if (document.getElementById("qrResultInput")) {
        document.getElementById("qrResultInput").value = "";
    }
    if (document.getElementById("qrResult")) {
        document.getElementById("qrResult").textContent = "";
    }

    // TAMBAHAN: Disable RFID field setelah reset manual
    const rfidField = document.querySelector('input[name="rfid"]');
    if (rfidField) {
        rfidField.disabled = true;
        console.log("✅ RFID field berhasil di-disable setelah reset manual");
    }

    // Hapus pesan RFID jika ada
    const existingMessage = document.getElementById("rfidFieldMessage");
    if (existingMessage) {
        existingMessage.remove();
    }

    // TAMBAHAN: Cek validasi RFID setelah reset manual
    if (typeof checkAllRequiredElements === "function") {
        checkAllRequiredElements();
    }

    // Notifikasi sukses via SweetAlert
    Swal.fire({
        icon: "success",
        title: "Form berhasil direset",
        text: "Semua data dan foto sudah dibersihkan.",
        timer: 2000,
        showConfirmButton: false,
    });

    // Reset alert form jika ada
    $("#formAlert")
        .stop(true)
        .hide()
        .removeClass("alert-success alert-danger")
        .html("");
}

function resetKtpData() {
    // Reset hidden input KTP
    $("#imgvisitorpathin").val("");

    // Reset preview KTP
    $("#ktpPreview")
        .empty()
        .append(
            '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>',
        );

    // Reset captured image (modal)
    $("#capturedImage").attr("src", "");
    $("#capturedImageContainer").hide();

    console.log("🧹 KTP data direset");
}
