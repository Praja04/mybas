document.addEventListener("DOMContentLoaded", function () {
    const $form = $("#vendorform");
    const $submitBtn = $("#submitBtn");
    const $rfidInput = $("input[name='rfid']");
    const jenisSelect = document.getElementById("jenisSelect");
    const purposeGroup = document.getElementById("purposeGroup");
    const nopolGroup = document.getElementById("nopolGroup");
    const purposeSelect = document.getElementById("purposeSelect");
    const nopolInput = document.getElementById("nopolInput");

    // Default: RFID disable
    $rfidInput.prop("disabled", true);

    // Toggle visibility & required attribute untuk transporter
    function toggleFields() {
        const isTransporter = jenisSelect.value === "transporter";
        purposeGroup.style.display = isTransporter ? "block" : "none";
        nopolGroup.style.display = isTransporter ? "block" : "none";

        if (isTransporter) {
            purposeSelect.setAttribute("required", "required");
            nopolInput.setAttribute("required", "required");
        } else {
            purposeSelect.removeAttribute("required");
            purposeSelect.value = "";
            nopolInput.removeAttribute("required");
            nopolInput.value = "";
        }

        // Update status RFID setelah toggle
        updateRfidState();
    }

    // Trigger saat load & saat ganti jenis kunjungan
    toggleFields();
    jenisSelect.addEventListener("change", toggleFields);

    // ✅ Cek kelengkapan form (untuk enable/disable RFID)
    function isFormComplete() {
        const namaVendor = $("input[name='namavisitor']").val().trim();
        const namaPerusahaan = $("input[name='namacomp']").val().trim();
        const tanggalLahir = $("#tglLahir").val().trim();
        const noKtpSim = $("input[name='nomorktp']").val().trim();
        const jumlahOrang = $("input[name='sumpeople']").val().trim();
        const fotoKtp = $("#imgvisitorpathin").val().trim();

        let selfieData = [];
        try {
            const selfieVal = $("#selfiePhotos").val().trim();
            selfieData = selfieVal ? JSON.parse(selfieVal) : [];
        } catch (e) {
            console.error("Invalid JSON in selfiePhotos:", e);
            return false;
        }

        const deptId = $("select[name='hostdeptid']").val();
        const keperluan = $("input[name='keperluan']").val().trim();
        const host = $("input[name='host']").val().trim();
        const jenis = jenisSelect.value;
        const isTransporter = jenis === "transporter";
        const tujuan = purposeSelect.value?.trim() || "";
        const nomorPolisi = nopolInput.value?.trim() || "";

        if (
            !namaVendor ||
            !namaPerusahaan ||
            !tanggalLahir ||
            !noKtpSim ||
            !jumlahOrang ||
            !fotoKtp ||
            selfieData.length === 0 ||
            !deptId ||
            !keperluan ||
            !host ||
            !jenis
        ) {
            return false;
        }

        if (isTransporter && (!tujuan || !nomorPolisi)) {
            return false;
        }

        return true;
    }

    // Update status RFID
    function updateRfidState() {
        const isValid = isFormComplete();
        $rfidInput.prop("disabled", !isValid);
    }

    // Event listener untuk trigger validasi ulang
    $form.on(
        "input change",
        "input:not([type='file']), select, textarea",
        updateRfidState
    );
    $("#imgvisitorpathin, #selfiePhotos").on("change", updateRfidState);

    // Validasi visual (dengan feedback error)
    function checkFormValidity() {
        let isValid = true;
        let firstInvalid = null;

        // Bersihkan error sebelumnya
        $(".invalid-feedback, .invalid-feedback-foto").remove();
        $form.find(".is-invalid").removeClass("is-invalid");

        // Helper: tampilkan error
        const showError = ($el, message, isFoto = false) => {
            $el.addClass("is-invalid");
            if (!firstInvalid) firstInvalid = $el;
            const cls = isFoto
                ? "invalid-feedback-foto text-danger mt-2"
                : "invalid-feedback";
            if ($el.next("." + cls.replace(/ /g, ".")).length === 0) {
                $el.after(`<div class="${cls}">${message}</div>`);
            }
        };

        // Cek Foto KTP
        const fotoKtp = $("#imgvisitorpathin").val().trim();
        if (!fotoKtp) {
            showError($("#ktpPreview"), "Foto KTP wajib diisi", true);
            isValid = false;
        }

        // Cek Foto Selfie
        let selfieData = [];
        try {
            const selfieVal = $("#selfiePhotos").val().trim();
            selfieData = selfieVal ? JSON.parse(selfieVal) : [];
        } catch (e) {
            showError(
                $("#selfiePreview"),
                "Format foto selfie tidak valid",
                true
            );
            isValid = false;
        }
        if (selfieData.length === 0) {
            showError($("#selfiePreview"), "Foto selfie wajib diisi", true);
            isValid = false;
        }

        // Validasi field [required]
        $form
            .find("input[required], select[required], textarea[required]")
            .each(function () {
                const $el = $(this);
                if (!$el.val()?.trim()) {
                    showError($el, "Wajib diisi");
                    isValid = false;
                }
            });

        // Validasi khusus transporter
        const isTransporter = jenisSelect.value === "transporter";
        if (isTransporter) {
            if (!purposeSelect.value?.trim()) {
                showError($(purposeSelect), "Tujuan wajib dipilih");
                isValid = false;
            }
            if (!nopolInput.value?.trim()) {
                showError($(nopolInput), "Nomor polisi wajib diisi");
                isValid = false;
            }
        }

        // Validasi jenis kunjungan
        if (!jenisSelect.value) {
            $(jenisSelect).addClass("is-invalid");
            if (!firstInvalid) firstInvalid = $(jenisSelect);
            isValid = false;
        }

        // Tampilkan alert jika error
        if (!isValid && firstInvalid) {
            Swal.fire({
                icon: "warning",
                title: "Form Belum Lengkap!",
                text: "Harap lengkapi semua isian wajib sebelum submit.",
            });
        }

        // Sinkronkan status RFID
        $rfidInput.prop("disabled", !isValid);

        return isValid;
    }

    // ✅ Submit handler
    $form.on("submit", function (e) {
        e.preventDefault();
        if (!checkFormValidity()) return;

        const formData = new FormData(this);

        // ✅ AMAN: Ambil token dari meta tag dan tambahkan ke formData
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken && csrfToken.content) {
            formData.append("_token", csrfToken.content);
        } else {
            Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: "CSRF token tidak ditemukan. Silakan refresh halaman.",
            });
            return;
        }

        $submitBtn
            .attr("disabled", true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');

        $.ajax({
            url: $form.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "X-CSRF-TOKEN": csrfToken.content, // ✅ Kirim juga sebagai header (opsional tapi aman)
            },
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message || "Data berhasil disimpan.",
                    timer: 2000,
                    showConfirmButton: false,
                });

                $("#formAlert")
                    .stop(true)
                    .hide()
                    .removeClass("alert-danger alert-success")
                    .addClass("alert-success")
                    .html(response.message)
                    .fadeIn();

                setTimeout(() => {
                    $("#formAlert")
                        .fadeOut()
                        .removeClass("alert-success alert-danger")
                        .html("");
                }, 2000);

                $form[0].reset();
                resetPreviewImage();
                resetModalKamera();
                if (typeof selfiePhotos !== "undefined") {
                    selfiePhotos = [];
                    typeof updateSelfieHiddenInput === "function" &&
                        updateSelfieHiddenInput();
                    typeof renderSelfiePreviews === "function" &&
                        renderSelfiePreviews();
                }

                $submitBtn
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');
                $rfidInput.prop("disabled", true).val("");
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr);

                let message = "Terjadi kesalahan. Silakan coba lagi.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.status === 419) {
                    message =
                        "AntiForgeryToken tidak valid. Halaman mungkin sudah kedaluwarsa. Silakan refresh.";
                }

                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: message,
                    showConfirmButton: true,
                });

                $("#formAlert")
                    .stop(true)
                    .hide()
                    .removeClass("alert-success alert-danger")
                    .addClass("alert-danger")
                    .html(message)
                    .fadeIn();

                setTimeout(() => {
                    $("#formAlert")
                        .fadeOut()
                        .removeClass("alert-success alert-danger")
                        .html("");
                }, 3000);

                $rfidInput.val("").trigger("change");
                $submitBtn
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');
            },
        });
    });

    // MODAL KAMERA: Webcam handlers
    const modalKtp = document.getElementById("myModal");
    if (modalKtp) {
        modalKtp.addEventListener("shown.bs.modal", () => startWebcam());
        modalKtp.addEventListener("hidden.bs.modal", () => stopStream());
    }

    if (typeof startCamera !== "undefined")
        startCamera.addEventListener("click", () => startWebcam());
    if (typeof captureBtn !== "undefined")
        captureBtn.addEventListener("click", captureImage);
    if (typeof retakeBtn !== "undefined")
        retakeBtn.addEventListener("click", retakePhoto);

    const modalSelfie = document.getElementById("selfieModal");
    if (modalSelfie) {
        modalSelfie.addEventListener("shown.bs.modal", () =>
            startSelfieWebcam()
        );
        modalSelfie.addEventListener("hidden.bs.modal", () =>
            stopSelfieStream()
        );
    }

    if (typeof startSelfieCamera !== "undefined")
        startSelfieCamera.addEventListener("click", startSelfieWebcam);
    if (typeof captureSelfieBtn !== "undefined")
        captureSelfieBtn.addEventListener("click", captureSelfiePhoto);
    if (typeof retakeSelfieBtn !== "undefined")
        retakeSelfieBtn.addEventListener("click", retakeSelfiePhoto);
    if (typeof saveSelfieBtn !== "undefined")
        saveSelfieBtn.addEventListener("click", saveAllSelfies);

    // ✅ RESET FUNCTIONS - DIPINDAHKAN KE DALAM DOMContentLoaded
    window.resetPreviewImage = function () {
        $("#ktpPreview")
            .empty()
            .append(
                '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>'
            );
        $("#selfiePreview")
            .empty()
            .append(
                '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>'
            );

        if (typeof selfiePhotos !== "undefined") {
            selfiePhotos.length = 0;
        }

        $("#imgvisitorpathin").val("").trigger("change");
        $("#selfiePhotos").val("").trigger("change");
        $("#capturedSelfieImage").val("").trigger("change");
        $('select[name="hostdeptid"]').val("").trigger("change");
    };

    window.resetModalKamera = function () {
        const video = document.getElementById("video");
        const canvas = document.getElementById("canvas");
        const capturedImage = document.getElementById("capturedImage");
        const capturedImageContainer = document.getElementById(
            "capturedImageContainer"
        );

        if (video && video.srcObject) {
            video.srcObject.getTracks().forEach((track) => track.stop());
            video.srcObject = null;
        }
        if (video) video.style.display = "none";
        if (canvas) canvas.style.display = "none";
        if (capturedImage) capturedImage.src = "";
        if (capturedImageContainer)
            capturedImageContainer.style.display = "none";

        $("#startCamera").show();
        $("#captureBtn, #retakeBtn").hide();

        const selfieVideo = document.getElementById("selfieVideo");
        const selfieCanvas = document.getElementById("selfieCanvas");
        const capturedSelfieImage = document.getElementById(
            "capturedSelfieImage"
        );
        const capturedSelfieContainer = document.getElementById(
            "capturedSelfieContainer"
        );

        if (selfieVideo && selfieVideo.srcObject) {
            selfieVideo.srcObject.getTracks().forEach((track) => track.stop());
            selfieVideo.srcObject = null;
        }
        if (selfieVideo) selfieVideo.style.display = "none";
        if (selfieCanvas) selfieCanvas.style.display = "none";
        if (capturedSelfieImage) capturedSelfieImage.src = "";
        if (capturedSelfieContainer)
            capturedSelfieContainer.style.display = "none";

        $("#startSelfieCamera").show();
        $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
    };

    window.resetForm = function () {
        $form[0].reset();
        resetPreviewImage();
        resetModalKamera();
        if (typeof selfiePhotos !== "undefined") {
            selfiePhotos = [];
            typeof updateSelfieHiddenInput === "function" &&
                updateSelfieHiddenInput();
            typeof renderSelfiePreviews === "function" &&
                renderSelfiePreviews();
        }
        Swal.fire({
            icon: "success",
            title: "Form berhasil direset",
            text: "Semua data dan foto sudah dibersihkan.",
            timer: 2000,
            showConfirmButton: false,
        });
        $("#formAlert")
            .stop(true)
            .hide()
            .removeClass("alert-success alert-danger")
            .html("");
    };
});
