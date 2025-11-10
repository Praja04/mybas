// $("#vendorform").on("submit", function (e) {
//     e.preventDefault(); // stop default submit

//     // Validasi foto (seperti sebelumnya)
//     let ktpPath = $("#imgvisitorpathin").val().trim();
//     let selfiePath = $("#selfiePhotos").val().trim();

//     $(".invalid-feedback-foto").remove();
//     let isValid = true;

//     if (!ktpPath || ktpPath === "") {
//         $(
//             '<div class="invalid-feedback-foto text-danger mt-2">Foto KTP wajib diisi</div>'
//         ).insertAfter("#ktpPreview");
//         isValid = false;
//     }

//     // Validasi Foto Selfie
//     if (!selfiePath || selfiePath === "") {
//         $(
//             '<div class="invalid-feedback-foto text-danger mt-2">Foto selfie wajib diisi</div>'
//         ).insertAfter("#selfiePreview");
//         isValid = false;
//     }

//     if (
//         !selfiePath ||
//         selfiePath === "[]" ||
//         JSON.parse(selfiePath).length === 0
//     ) {
//         $("#selfieErrorContainer").append(
//             '<div class="invalid-feedback-foto text-danger">Foto selfie wajib diisi</div>'
//         );
//         isValid = false;
//     }

//     if (!isValid) {
//         Swal.fire({
//             icon: "warning",
//             title: "Lengkapi Foto!",
//             text: "Harap lengkapi semua foto sebelum submit.",
//             confirmButtonText: "OK",
//         });
//         return;
//     }

//     // Ambil data form
//     let formData = new FormData(this);

//     // Matikan tombol submit (biar ga double klik)
//     $("#submitBtn")
//         .attr("disabled", true)
//         .html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');

//     $.ajax({
//         url: $(this).attr("action"),
//         method: "POST",
//         data: formData,
//         processData: false,
//         contentType: false,
//         success: function (response) {
//             Swal.fire({
//                 icon: "success",
//                 title: "Berhasil!",
//                 text: response.message || "Data berhasil disimpan.",
//                 timer: 2000,
//                 showConfirmButton: false,
//             });

//             console.log(response);

//             $("#formAlert")
//                 .stop(true)
//                 .hide()
//                 .removeClass("alert-danger alert-success")
//                 .addClass("alert-success")
//                 .html(response.message)
//                 .fadeIn();

//             // Hide alert setelah 2 detik
//             setTimeout(function () {
//                 $("#formAlert")
//                     .fadeOut()
//                     .removeClass("alert-success alert-danger")
//                     .html("");
//             }, 2000);

//             // Reset form
//             $("#vendorform")[0].reset(); // reset semua input biasa

//             resetPreviewImage(); // Reset semua tampilan preview
//             resetModalKamera(); // Stop kamera & clear canvas
//             selfiePhotos = []; // Reset array selfie
//             updateSelfieHiddenInput(); // Sync ke input hidden
//             renderSelfiePreviews();

//             $("#submitBtn")
//                 .prop("disabled", false)
//                 .html('<i class="fas fa-save me-2"></i>Simpan Data');
//         },
//         error: function (xhr) {
//             console.log("ERROR RESPONSE:", xhr);

//             var message = "Terjadi kesalahan. Silakan coba lagi.";
//             if (xhr.responseJSON && xhr.responseJSON.message) {
//                 message = xhr.responseJSON.message;
//             }

//             // Notifikasi SweetAlert Error
//             Swal.fire({
//                 icon: "error",
//                 title: "Gagal!",
//                 text: message,
//                 timer: 2500,
//                 showConfirmButton: false,
//             });

//             // Tampilkan alert inline
//             $("#formAlert")
//                 .stop(true)
//                 .hide()
//                 .removeClass("alert-success alert-danger")
//                 .addClass("alert-danger")
//                 .html(message)
//                 .fadeIn();

//             // Hide alert setelah 3 detik
//             setTimeout(function () {
//                 $("#formAlert")
//                     .fadeOut()
//                     .removeClass("alert-success alert-danger")
//                     .html("");
//             }, 3000);

//             $("#submitBtn")
//                 .prop("disabled", false)
//                 .html('<i class="fas fa-save me-2"></i>Simpan Data');
//         },
//     });
// });

document.addEventListener("DOMContentLoaded", function() {
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

    // ✅ Toggle visibility dan required-nya jika jenis kunjungan berubah
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
    }

    // 🧠 Trigger toggle saat DOM load & saat berubah
    toggleFields();
    jenisSelect.addEventListener("change", () => {
        toggleFields();

        // Re-evaluate validasi internal
        const isValid = validateSilent();
        $rfidInput.prop("disabled", !isValid);
    });

    // ✅ Fungsi Validasi Utama
    function checkFormValidity() {
        let isValid = true;
        let firstInvalid = null;

        // Reset error
        $(".invalid-feedback-foto").remove();
        $form.find(".is-invalid").removeClass("is-invalid");

        // Cek Foto KTP
        const ktpPath = $("#imgvisitorpathin")
            .val()
            .trim();
        if (!ktpPath) {
            $(
                '<div class="invalid-feedback-foto text-danger mt-2">Foto KTP wajib diisi</div>'
            ).insertAfter("#ktpPreview");
            if (!firstInvalid) firstInvalid = $("#imgvisitorpathin");
            isValid = false;
        }

        // Cek Foto Selfie
        const selfiePath = $("#selfiePhotos")
            .val()
            .trim();
        if (
            !selfiePath ||
            selfiePath === "[]" ||
            JSON.parse(selfiePath).length === 0
        ) {
            $(
                '<div class="invalid-feedback-foto text-danger mt-2">Foto selfie wajib diisi</div>'
            ).insertAfter("#selfiePreview");
            if (!firstInvalid) firstInvalid = $("#selfiePhotos");
            isValid = false;
        }

        // Validasi field [required]
        $form
            .find("input[required], select[required], textarea[required]")
            .each(function() {
                const $el = $(this);
                if (!$el.val().trim()) {
                    $el.addClass("is-invalid");
                    if (!firstInvalid) firstInvalid = $el;
                    isValid = false;
                }
            });

        // Kondisi khusus transporter
        const jenis = jenisSelect.value;
        if (jenis === "transporter") {
            if (!purposeSelect.value.trim()) {
                $(purposeSelect).addClass("is-invalid");
                if (!firstInvalid) firstInvalid = $(purposeSelect);
                isValid = false;
            }

            if (!nopolInput.value.trim()) {
                $(nopolInput).addClass("is-invalid");
                if (!firstInvalid) firstInvalid = $(nopolInput);
                isValid = false;
            }
        }

        // Fokus ke field pertama yang invalid
        if (!isValid && firstInvalid) {
            // firstInvalid.focus();
            Swal.fire({
                icon: "warning",
                title: "Form Belum Lengkap!",
                text: "Harap lengkapi semua isian wajib sebelum submit."
            });
        }

        // RFID hanya aktif jika form valid
        $rfidInput.prop("disabled", !isValid);

        return isValid;
    }

    function validateSilent() {
        let isValid = true;

        $(".invalid-feedback-foto").remove();
        $form.find(".is-invalid").removeClass("is-invalid");

        const namaVendor = $("input[name='namavisitor']")
            .val()
            .trim();
        const namaPerusahaan = $("input[name='namacomp']")
            .val()
            .trim();
        const tanggalLahir = $("#tglLahir")
            .val()
            .trim();
        const jenis = jenisSelect.value;
        const noKtpSim = $("input[name='nomorktp']")
            .val()
            .trim();
        const jumlahOrang = $("input[name='sumpeople']")
            .val()
            .trim();
        const fotoKtp = $("#imgvisitorpathin")
            .val()
            .trim();
        const fotoSelfie = $("#selfiePhotos")
            .val()
            .trim();

        // Validasi JSON untuk foto selfie
        let selfieData = [];
        try {
            selfieData = fotoSelfie ? JSON.parse(fotoSelfie) : [];
        } catch (e) {
            selfieData = [];
        }

        const isTransporter = jenis === "transporter";
        const tujuan = purposeSelect.value.trim();
        const nomorPolisi = nopolInput.value.trim();
        const deptId = $("select[name='hostdeptid']").val(); // trim() tidak diperlukan karena select

        // Validasi Umum untuk semua jenis
        if (
            !namaVendor ||
            !namaPerusahaan ||
            !tanggalLahir ||
            !noKtpSim ||
            !jumlahOrang ||
            !fotoKtp ||
            selfieData.length === 0 ||
            !deptId
        ) {
            isValid = false;
        }

        // Validasi tambahan jika jenis = transporter
        if (isTransporter && (!tujuan || !nomorPolisi)) {
            isValid = false;
        }

        return isValid;
    }

    function highlightInvalidFields() {
        let isValid = true;
        let firstInvalid = null;

        // Bersihkan error sebelumnya
        $(".invalid-feedback-foto").remove();
        $(".invalid-feedback").remove(); // ❗ tambahkan ini
        $form.find(".is-invalid").removeClass("is-invalid");

        const namaVendor = $("input[name='namavisitor']");
        const namaPerusahaan = $("input[name='namacomp']");
        const tanggalLahir = $("#tglLahir");
        const noKtpSim = $("input[name='nomorktp']");
        const jumlahOrang = $("input[name='sumpeople']");
        const fotoKtp = $("#imgvisitorpathin")
            .val()
            .trim();
        const fotoSelfie = $("#selfiePhotos")
            .val()
            .trim();
        const deptId = $("select[name='hostdeptid']");
        const keperluan = $("input[name='keperluan']");
        const host = $("input[name='host']");
        const tujuan = $(purposeSelect);
        const nomorPolisi = $(nopolInput);
        const jenis = jenisSelect.value;
        const isTransporter = jenis === "transporter";

        // Validasi JSON selfie
        let selfieData = [];
        try {
            selfieData = fotoSelfie ? JSON.parse(fotoSelfie) : [];
        } catch (e) {
            selfieData = [];
        }

        // Helper
        const showError = ($el, message) => {
            $el.addClass("is-invalid");
            if (!firstInvalid) firstInvalid = $el;
            if ($el.next(".invalid-feedback").length === 0) {
                $el.after(`<div class="invalid-feedback">${message}</div>`);
            }
        };

        // Validasi
        if (!namaVendor.val().trim()) showError(namaVendor, "Nama wajib diisi");
        if (!namaPerusahaan.val().trim())
            showError(namaPerusahaan, "Perusahaan wajib diisi");
        if (!tanggalLahir.val().trim())
            showError(tanggalLahir, "Tanggal lahir wajib diisi");
        if (!noKtpSim.val().trim())
            showError(noKtpSim, "Nomor KTP/SIM wajib diisi");
        if (!jumlahOrang.val().trim())
            showError(jumlahOrang, "Jumlah orang wajib diisi");
        if (!fotoKtp) {
            $("#ktpPreview").after(
                '<div class="invalid-feedback-foto text-danger mt-2">Foto KTP wajib diisi</div>'
            );
            if (!firstInvalid) firstInvalid = $("#imgvisitorpathin");
            isValid = false;
        }
        if (selfieData.length === 0) {
            $("#selfiePreview").after(
                '<div class="invalid-feedback-foto text-danger mt-2">Foto selfie wajib diisi</div>'
            );
            if (!firstInvalid) firstInvalid = $("#selfiePhotos");
            isValid = false;
        }
        if (!deptId.val()) showError(deptId, "Departemen wajib dipilih");
        if (!keperluan.val().trim())
            showError(keperluan, "Keperluan wajib diisi");
        if (!host.val().trim()) showError(host, "PIC wajib diisi");

        if (isTransporter) {
            if (!nomorPolisi.val().trim())
                showError(nomorPolisi, "Nomor polisi wajib diisi");
            if (!tujuan.val().trim()) showError(tujuan, "Tujuan wajib dipilih");
        }

        if (!jenis || jenis === "") {
            $(jenisSelect).addClass("is-invalid");
            if (!firstInvalid) firstInvalid = $(jenisSelect);
        }

        // Fokus pertama
        // if (firstInvalid) {
        //     isValid = false;
        //     setTimeout(() => firstInvalid.focus(), 100);
        // }

        return isValid;
    }

    // ✅ Submit handler
    $form.on("submit", function(e) {
        e.preventDefault();
        if (!checkFormValidity()) return;

        const formData = new FormData(this);

        $submitBtn
            .attr("disabled", true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');

        $.ajax({
            url: $form.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message || "Data berhasil disimpan.",
                    timer: 2000,
                    showConfirmButton: false
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
                selfiePhotos = [];
                updateSelfieHiddenInput();
                renderSelfiePreviews();

                $submitBtn
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');

                $rfidInput.prop("disabled", true).val("");
            },
            error: function(xhr) {
                console.log("ERROR RESPONSE:", xhr);
                let message = "Terjadi kesalahan. Silakan coba lagi.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: message,
                    // timer: 2500,
                    showConfirmButton: true
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
            }
        });
    });

    // ✅ MODAL FOTO
    const modalKtp = document.getElementById("myModal");
    if (modalKtp) {
        modalKtp.addEventListener("shown.bs.modal", () => startWebcam());
        modalKtp.addEventListener("hidden.bs.modal", () => stopStream());
    }

    if (typeof startCamera !== "undefined" && startCamera)
        startCamera.addEventListener("click", () => startWebcam());

    if (typeof captureBtn !== "undefined" && captureBtn)
        captureBtn.addEventListener("click", captureImage);

    if (typeof retakeBtn !== "undefined" && retakeBtn)
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

    if (typeof startSelfieCamera !== "undefined" && startSelfieCamera)
        startSelfieCamera.addEventListener("click", startSelfieWebcam);

    if (typeof captureSelfieBtn !== "undefined" && captureSelfieBtn)
        captureSelfieBtn.addEventListener("click", captureSelfiePhoto);

    if (typeof retakeSelfieBtn !== "undefined" && retakeSelfieBtn)
        retakeSelfieBtn.addEventListener("click", retakeSelfiePhoto);

    if (typeof saveSelfieBtn !== "undefined" && saveSelfieBtn)
        saveSelfieBtn.addEventListener("click", saveAllSelfies);

    // input, select, textarea event listener
    // $form.on("input change", "input, select, textarea", function() {
    //     const $el = $(this);

    //     if ($el.val().trim()) {
    //         $el.removeClass("is-invalid");
    //         if ($el.attr("id") === "imgvisitorpathin") {
    //             $("#ktpPreview")
    //                 .next(".invalid-feedback-foto")
    //                 .remove();
    //         }
    //         if ($el.attr("id") === "selfiePhotos") {
    //             $("#selfiePreview")
    //                 .next(".invalid-feedback-foto")
    //                 .remove();
    //         }
    //     }

    //     const isValid = validateSilent();
    //     highlightInvalidFields();
    //     console.log("Form Validasi Internal:", isValid);

    //     if (!$el.val().trim() && $el.prop("required")) {
    //         $el.addClass("is-invalid");
    //     }

    //     $rfidInput.prop("disabled", !isValid);
    // });

    // Jika user mengisi ulang/ubah foto, validasi ulang & aktifkan RFID
    $("#imgvisitorpathin, #selfiePhotos").on("change", function() {
        const isValid = validateSilent();
        console.log("[foto change triggered] isValid =", isValid);
        $rfidInput.prop("disabled", !isValid);
    });

    // Jika user ambil ulang foto KTP atau selfie → validasi ulang
    // $("#imgvisitorpathin").on("change", function () {
    //     checkFormValidity();
    //     const isValid = validateSilent();
    //     $rfidInput.prop("disabled", !isValid);
    // });
    // $("#selfiePhotos").on("change", function () {
    //     checkFormValidity();
    //     const isValid = validateSilent();
    //     $rfidInput.prop("disabled", !isValid);
    // });
});

// reset preview kamera
function resetPreviewImage() {
    // Reset KTP Preview
    $("#ktpPreview")
        .empty()
        .append(
            '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>'
        );

    // Reset Selfie Preview
    $("#selfiePreview")
        .empty()
        .append(
            '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>'
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

    // Reset input hidden
    $("#imgvisitorpathin")
        .val("")
        .trigger("change");
    $("#selfiePhotos")
        .val("")
        .trigger("change");
    $("#capturedSelfieImage")
        .val("")
        .trigger("change");
    $('select[name="hostdeptid"]')
        .val("")
        .trigger("change");
}

function resetModalKamera() {
    // 🔴 RESET MODAL KTP
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const capturedImage = document.getElementById("capturedImage");
    const capturedImageContainer = document.getElementById(
        "capturedImageContainer"
    );

    if (video && video.srcObject) {
        const stream = video.srcObject;
        stream.getTracks().forEach(track => track.stop());
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
        "capturedSelfieContainer"
    );

    if (selfieVideo && selfieVideo.srcObject) {
        const selfieStream = selfieVideo.srcObject;
        selfieStream.getTracks().forEach(track => track.stop());
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
    $("#vendorform")[0].reset(); // Reset semua input biasa

    // Reset preview image & kamera
    resetPreviewImage();
    resetModalKamera();

    // Reset variabel selfie jika ada
    if (typeof selfiePhotos !== "undefined") {
        selfiePhotos = [];
        updateSelfieHiddenInput?.();
        renderSelfiePreviews?.();
    }

    // Notifikasi sukses via SweetAlert
    Swal.fire({
        icon: "success",
        title: "Form berhasil direset",
        text: "Semua data dan foto sudah dibersihkan.",
        timer: 2000,
        showConfirmButton: false
    });

    // Reset alert form jika ada
    $("#formAlert")
        .stop(true)
        .hide()
        .removeClass("alert-success alert-danger")
        .html("");
}
