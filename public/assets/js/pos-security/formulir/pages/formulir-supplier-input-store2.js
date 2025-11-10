window.resetModalKamera = function() {
    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");
    const capturedImage = document.getElementById("capturedImage");
    const capturedImageContainer = document.getElementById(
        "capturedImageContainer"
    );

    // Hentikan stream kamera KTP
    if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }
    if (video) video.style.display = "none";
    if (canvas) canvas.style.display = "none";
    if (capturedImage) capturedImage.src = "";
    if (capturedImageContainer) capturedImageContainer.style.display = "none";

    $("#startCamera").show();
    $("#captureBtn, #retakeBtn").hide();

    // Kamera Selfie
    const selfieVideo = document.getElementById("selfieVideo");
    const selfieCanvas = document.getElementById("selfieCanvas");
    const capturedSelfieImage = document.getElementById("capturedSelfieImage");
    const capturedSelfieContainer = document.getElementById(
        "capturedSelfieContainer"
    );

    if (selfieVideo && selfieVideo.srcObject) {
        selfieVideo.srcObject.getTracks().forEach(track => track.stop());
        selfieVideo.srcObject = null;
    }
    if (selfieVideo) selfieVideo.style.display = "none";
    if (selfieCanvas) selfieCanvas.style.display = "none";
    if (capturedSelfieImage) capturedSelfieImage.src = "";
    if (capturedSelfieContainer) capturedSelfieContainer.style.display = "none";

    $("#startSelfieCamera").show();
    $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
};

// =============== GLOBAL VARIABLES ===============
window.selfiePhotos = []; // Array global untuk foto selfie

// =============== HELPER FUNCTIONS ===============
window.addSelfiePhoto = function(base64Image) {
    if (!window.selfiePhotos) window.selfiePhotos = [];
    window.selfiePhotos.push(base64Image);
    updateSelfieHiddenInput();
    renderSelfiePreviews();
    console.log(
        "📸 Foto selfie ditambahkan. Total:",
        window.selfiePhotos.length
    );
};

function updateSelfieHiddenInput() {
    $("#selfiePhotos").val(JSON.stringify(window.selfiePhotos));
    $("#selfiePhotos").trigger("change");
}

function finalizeCapture(base64Image) {
    if (base64Image === "," || !base64Image.includes("base64")) {
        Swal.fire("Error", "Gagal ambil foto. Coba lagi.", "error");
        return;
    }

    // ✅ WUZZZ — langsung tampil!
    document.getElementById("capturedImage").src = base64Image;
    document.getElementById("capturedImageContainer").style.display = "block";

    $("#captureBtn").hide();
    $("#retakeBtn").show();

    // Simpan ke form
    $("#imgvisitorpathin").val(base64Image);
    $("#ktpPreview")
        .empty()
        .append(
            $(
                `<img src="${base64Image}" class="img-thumbnail" width="100" style="height: 100px; object-fit: cover;">`
            )
        );

    if (typeof triggerFormValidation === "function") {
        triggerFormValidation();
    }
}

function renderSelfiePreviews() {
    const $preview = $("#selfiePreview");
    $preview.empty();

    if (!window.selfiePhotos || window.selfiePhotos.length === 0) {
        $preview.append(
            '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>'
        );
    } else {
        window.selfiePhotos.forEach((src, index) => {
            const $img = $(`
                <img src="${src}" 
                     class="img-thumbnail me-1 mb-2" 
                     width="100" 
                     style="height: 100px; object-fit: cover;" 
                     alt="Selfie ${index + 1}">
            `);
            $preview.append($img);
        });
    }
}

// function clearModalPreviews() {
//     // Reset KTP Modal
//     const video = document.getElementById("video");
//     const canvas = document.getElementById("canvas");
//     const capturedImage = document.getElementById("capturedImage");
//     const capturedImageContainer = document.getElementById(
//         "capturedImageContainer"
//     );

//     if (video && video.srcObject) {
//         video.srcObject.getTracks().forEach(track => track.stop());
//         video.srcObject = null;
//     }
//     if (video) video.style.display = "none";
//     if (canvas) canvas.style.display = "none";
//     if (capturedImage) capturedImage.src = "";
//     if (capturedImageContainer) capturedImageContainer.style.display = "none";
//     $("#startCamera").show();
//     $("#captureBtn, #retakeBtn").hide();
//     $("#imgvisitorpathin").val(""); // 🆕 Clear hidden input

//     // Reset Selfie Modal
//     const selfieVideo = document.getElementById("selfieVideo");
//     const selfieCanvas = document.getElementById("selfieCanvas");
//     const capturedSelfieImage = document.getElementById("capturedSelfieImage");
//     const capturedSelfieContainer = document.getElementById(
//         "capturedSelfieContainer"
//     );

//     if (selfieVideo && selfieVideo.srcObject) {
//         selfieVideo.srcObject.getTracks().forEach(track => track.stop());
//         selfieVideo.srcObject = null;
//     }
//     if (selfieVideo) selfieVideo.style.display = "none";
//     if (selfieCanvas) selfieCanvas.style.display = "none";
//     if (capturedSelfieImage) capturedSelfieImage.src = "";
//     if (capturedSelfieContainer) capturedSelfieContainer.style.display = "none";
//     $("#startSelfieCamera").show();
//     $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
//     $("#selfiePhotos").val("[]"); // 🆕 Clear hidden input selfie
//     window.selfiePhotos = [];
// }

// // =============== RESET FUNCTIONS ===============
// window.resetPreviewImage = function() {
//     $("#ktpPreview")
//         .empty()
//         .append(
//             '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>'
//         );
//     $("#selfiePreview")
//         .empty()
//         .append(
//             '<div class="text-muted fst-italic mt-2">Tidak ada foto yang diunggah.</div>'
//         );
//     $("#imgvisitorpathin").val("");
//     $("#selfiePhotos").val("[]");
//     if (window.selfiePhotos) {
//         window.selfiePhotos = [];
//         updateSelfieHiddenInput();
//         renderSelfiePreviews();
//     }
// };

// window.resetModalKamera = function() {
//     const video = document.getElementById("video");
//     const canvas = document.getElementById("canvas");
//     const capturedImage = document.getElementById("capturedImage");
//     const capturedImageContainer = document.getElementById(
//         "capturedImageContainer"
//     );

//     if (video && video.srcObject) {
//         video.srcObject.getTracks().forEach(track => track.stop());
//         video.srcObject = null;
//     }
//     if (video) video.style.display = "none";
//     if (canvas) canvas.style.display = "none";
//     if (capturedImage) capturedImage.src = "";
//     if (capturedImageContainer) capturedImageContainer.style.display = "none";

//     $("#startCamera").show();
//     $("#captureBtn, #retakeBtn").hide();

//     // Selfie
//     const selfieVideo = document.getElementById("selfieVideo");
//     const selfieCanvas = document.getElementById("selfieCanvas");
//     const capturedSelfieImage = document.getElementById("capturedSelfieImage");
//     const capturedSelfieContainer = document.getElementById(
//         "capturedSelfieContainer"
//     );

//     if (selfieVideo && selfieVideo.srcObject) {
//         selfieVideo.srcObject.getTracks().forEach(track => track.stop());
//         selfieVideo.srcObject = null;
//     }
//     if (selfieVideo) selfieVideo.style.display = "none";
//     if (selfieCanvas) selfieCanvas.style.display = "none";
//     if (capturedSelfieImage) capturedSelfieImage.src = "";
//     if (capturedSelfieContainer) capturedSelfieContainer.style.display = "none";

//     $("#startSelfieCamera").show();
//     $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
// };
// window.resetForm = function() {
//     $("#visitorForm")[0].reset();
//     resetPreviewImage();
//     resetModalKamera();
//     Swal.fire({
//         icon: "success",
//         title: "Form berhasil direset",
//         text: "Semua data dan foto sudah dibersihkan.",
//         timer: 2000,
//         showConfirmButton: false
//     });
//     $("#formAlert")
//         .stop(true)
//         .hide()
//         .html("");
// };

// function resetAllCameraAndPhotos() {
//     // ==== Reset KTP ====
//     const video = document.getElementById("video");
//     if (video && video.srcObject) {
//         video.srcObject.getTracks().forEach(track => track.stop());
//         video.srcObject = null;
//     }
//     $("#video, #canvas, #capturedImageContainer").hide();
//     $("#capturedImage").attr("src", "");
//     $("#startCamera").show();
//     $("#captureBtn, #retakeBtn").hide();
//     $("#imgvisitorpathin").val("");

//     // ==== Reset Selfie ====
//     const selfieVideo = document.getElementById("selfieVideo");
//     if (selfieVideo && selfieVideo.srcObject) {
//         selfieVideo.srcObject.getTracks().forEach(track => track.stop());
//         selfieVideo.srcObject = null;
//     }
//     $("#selfieVideo, #selfieCanvas, #capturedSelfieContainer").hide();
//     $("#capturedSelfieImage").attr("src", "");
//     $("#startSelfieCamera").show();
//     $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
//     $("#selfiePhotos").val("[]");
//     window.selfiePhotos = [];

//     console.log("reset semua data form");
// }

// function resetAllCameraAndPhotos() {
//     // ==== Reset KTP ====
//     const video = document.getElementById("video");
//     if (video && video.srcObject) {
//         video.srcObject.getTracks().forEach(track => track.stop());
//         video.srcObject = null;
//     }
//     $("#video").hide();
//     $("#canvas, #capturedImageContainer").hide();
//     $("#capturedImage").attr("src", "");
//     $("#startCamera").show();
//     $("#captureBtn, #retakeBtn").hide();
//     $("#imgvisitorpathin").val("");

//     // ==== Reset Selfie ====
//     const selfieVideo = document.getElementById("selfieVideo");
//     if (selfieVideo && selfieVideo.srcObject) {
//         selfieVideo.srcObject.getTracks().forEach(track => track.stop());
//         selfieVideo.srcObject = null;
//     }
//     $("#selfieVideo").hide();
//     $("#selfieCanvas, #capturedSelfieContainer").hide();
//     $("#capturedSelfieImage").attr("src", "");
//     $("#startSelfieCamera").show();
//     $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
//     $("#selfiePhotos").val("[]");
//     window.selfiePhotos = [];

//     console.log("reset semua data form");
// }

function resetAllCameraAndPhotos() {
    // ==== Reset KTP ====
    const video = document.getElementById("video");
    if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
        video.srcObject = null;
    }
    $("#video").hide();
    $("#canvas").hide();

    // Reset foto hasil capture
    $("#capturedImage").attr("src", "");
    $("#capturedImageContainer").hide();

    // Balikin tombol ke mode awal
    $("#startCamera").show();
    $("#captureBtn").show();
    $("#retakeBtn").hide();

    // Reset hidden input
    $("#imgvisitorpathin").val("");

    // ==== Reset Selfie ====
    const selfieVideo = document.getElementById("selfieVideo");
    if (selfieVideo && selfieVideo.srcObject) {
        selfieVideo.srcObject.getTracks().forEach(track => track.stop());
        selfieVideo.srcObject = null;
    }
    $("#selfieVideo").hide();
    $("#selfieCanvas").hide();

    $("#capturedSelfieImage").attr("src", "");
    $("#capturedSelfieContainer").hide();

    $("#startSelfieCamera").show();
    $("#captureSelfieBtn").show();
    $("#retakeSelfieBtn, #saveSelfieBtn").hide();

    $("#selfiePhotos").val("[]");
    window.selfiePhotos = [];
}

function resetForm() {
    const $form = $("#visitorForm");

    // Reset semua input biasa
    $form[0].reset();

    // Reset field khusus (yang tidak kena reset())
    $form
        .find("input[name='rfid']")
        .val("")
        .prop("disabled", true);
    $form.find("input[name='sumpeople']").val("1"); // karena readonly

    // Reset flatpickr (kalau pakai datepicker)
    if (window.flatpickr && $("#tglLahir").data("flatpickr")) {
        $("#tglLahir")
            .flatpickr()
            .clear();
    }

    // Reset preview foto
    $("#ktpPreview").empty();
    $("#selfiePreview").empty();

    // Hapus pesan error validasi
    $(".invalid-feedback, .invalid-feedback-foto").remove();
    $form.find(".is-invalid").removeClass("is-invalid");
}

// =============== FULL SCRIPT IN ONE READY ===============
$(document).ready(function() {
    // Reset tampilan modal saat dibuka

    // =============== SETUP ELEMEN KTP ===============
    const videoEl = document.getElementById("video");
    const canvasEl = document.getElementById("canvas");
    const capturedImageEl = document.getElementById("capturedImage");
    const capturedImageContainerEl = document.getElementById(
        "capturedImageContainer"
    );

    // =============== SETUP ELEMEN SELFIE ===============
    const selfieVideoEl = document.getElementById("selfieVideo");
    const selfieCanvasEl = document.getElementById("selfieCanvas");
    const capturedSelfieImageEl = document.getElementById(
        "capturedSelfieImage"
    );
    const capturedSelfieContainerEl = document.getElementById(
        "capturedSelfieContainer"
    );

    // =============== KAMERA KTP ===============
    if (videoEl && canvasEl) {
        $("#startCamera").on("click", function() {
            navigator.mediaDevices
                .getUserMedia({ video: true })
                .then(stream => {
                    videoEl.srcObject = stream;
                    videoEl.style.display = "block";
                    $("#startCamera").hide();
                    $("#captureBtn").show();
                })
                .catch(err => {
                    Swal.fire(
                        "Error",
                        "Gagal akses kamera: " + err.message,
                        "error"
                    );
                });
        });

        $("#captureBtn").on("click", function() {
            const video = document.getElementById("video");
            const canvas = document.getElementById("canvas");
            const ctx = canvas.getContext("2d");

            // 🚀 Cek instan: apakah video siap?
            if (video.readyState >= 2 && video.videoWidth > 0) {
                // Langsung ambil!
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                const base64Image = canvas.toDataURL("image/jpeg", 0.8);
                if (base64Image === "," || base64Image.length < 100) {
                    forceCaptureFallback(video, canvas, ctx); // fallback
                    return;
                }

                finalizeCapture(base64Image);
                return;
            }

            // 🔁 Kalau belum siap, polling 1x pakai requestAnimationFrame (super cepat)
            const waitForVideo = () => {
                if (video.readyState >= 2 && video.videoWidth > 0) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const base64Image = canvas.toDataURL("image/jpeg", 0.8);
                    finalizeCapture(base64Image);
                } else {
                    requestAnimationFrame(waitForVideo); // ~16ms, tapi cuma 1x
                }
            };

            waitForVideo();
        });

        $("#retakeBtn").on("click", function() {
            if (capturedImageContainerEl)
                capturedImageContainerEl.style.display = "none";
            capturedImageEl.src = "";
            $("#retakeBtn").hide();
            $("#captureBtn").show();
        });

        // $("#myModal").on("hidden.bs.modal", function() {
        //     if (videoEl && videoEl.srcObject) {
        //         videoEl.srcObject.getTracks().forEach(track => track.stop());
        //         videoEl.srcObject = null;
        //     }
        //     videoEl.style.display = "none";
        //     $("#startCamera").show();
        //     $("#captureBtn, #retakeBtn").hide();
        //     if (capturedImageContainerEl)
        //         capturedImageContainerEl.style.display = "none";
        // });
        $("#myModal").on("hidden.bs.modal", function() {
            if (videoEl && videoEl.srcObject) {
                videoEl.srcObject.getTracks().forEach(track => track.stop());
                videoEl.srcObject = null;
            }
            videoEl.style.display = "none";
            $("#startCamera").show();
            $("#captureBtn, #retakeBtn").hide();
            $("#capturedImageContainer").hide(); // tambahkan ini
            $("#capturedImage").attr("src", ""); // tambahkan ini
        });
    }

    // =============== KAMERA SELFIE ===============
    if (selfieVideoEl && selfieCanvasEl) {
        $("#startSelfieCamera").on("click", function() {
            navigator.mediaDevices
                .getUserMedia({
                    video: {
                        facingMode: "user"
                    }
                })
                .then(stream => {
                    selfieVideoEl.srcObject = stream;
                    selfieVideoEl.style.display = "block";
                    $("#startSelfieCamera").hide();
                    $("#captureSelfieBtn").show();
                })
                .catch(err => {
                    Swal.fire(
                        "Error",
                        "Gagal akses kamera: " + err.message,
                        "error"
                    );
                });
        });

        $("#captureSelfieBtn").on("click", function() {
            selfieCanvasEl.width = selfieVideoEl.videoWidth;
            selfieCanvasEl.height = selfieVideoEl.videoHeight;
            const ctx = selfieCanvasEl.getContext("2d");
            ctx.translate(selfieCanvasEl.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(
                selfieVideoEl,
                0,
                0,
                selfieCanvasEl.width,
                selfieCanvasEl.height
            );

            const base64Image = selfieCanvasEl.toDataURL("image/jpeg", 0.8);
            capturedSelfieImageEl.src = base64Image;
            if (capturedSelfieContainerEl)
                capturedSelfieContainerEl.style.display = "block";

            $("#captureSelfieBtn").hide();
            $("#retakeSelfieBtn, #saveSelfieBtn").show();

            window.addSelfiePhoto(base64Image);
            if (typeof triggerFormValidation === "function")
                triggerFormValidation();
        });

        $("#retakeSelfieBtn").on("click", function() {
            if (capturedSelfieContainerEl)
                capturedSelfieContainerEl.style.display = "none";
            capturedSelfieImageEl.src = "";
            $("#retakeSelfieBtn, #saveSelfieBtn").hide();
            $("#captureSelfieBtn").show();
        });

        $("#saveSelfieBtn").on("click", function() {
            Swal.fire({
                icon: "success",
                title: "Foto berhasil disimpan!",
                timer: 1000,
                showConfirmButton: false
            });
            updateSelfieHiddenInput();
            renderSelfiePreviews();
            $("#selfieModal").modal("hide");
        });

        // $("#selfieModal").on("hidden.bs.modal", function() {
        //     if (selfieVideoEl && selfieVideoEl.srcObject) {
        //         selfieVideoEl.srcObject
        //             .getTracks()
        //             .forEach(track => track.stop());
        //         selfieVideoEl.srcObject = null;
        //     }
        //     selfieVideoEl.style.display = "none";
        //     $("#startSelfieCamera").show();
        //     $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
        //     if (capturedSelfieContainerEl)
        //         capturedSelfieContainerEl.style.display = "none";
        // });
        $("#selfieModal").on("hidden.bs.modal", function() {
            if (selfieVideoEl && selfieVideoEl.srcObject) {
                selfieVideoEl.srcObject
                    .getTracks()
                    .forEach(track => track.stop());
                selfieVideoEl.srcObject = null;
            }
            selfieVideoEl.style.display = "none";
            $("#startSelfieCamera").show();
            $("#captureSelfieBtn, #retakeSelfieBtn, #saveSelfieBtn").hide();
            $("#capturedSelfieContainer").hide();
            $("#capturedSelfieImage").attr("src", "");
        });
    }

    // =============== VALIDASI FORM & RFID ===============
    const $form = $("#visitorForm");
    const $submitBtn = $("#submitBtn");
    const $rfidInput = $form.find("input[name='rfid']");
    $rfidInput.prop("disabled", true);

    function isFormComplete() {
        const fields = [
            $form
                .find("input[name='namavisitor']")
                .val()
                ?.trim(),
            $form
                .find("input[name='namacomp']")
                .val()
                ?.trim(),
            $form
                .find("input[name='nomorktp']")
                .val()
                ?.trim(),
            $form
                .find("#tglLahir")
                .val()
                ?.trim(),
            $form
                .find("select[name='purpose']")
                .val()
                ?.trim(),
            $form
                .find("input[name='nopol']")
                .val()
                ?.trim(),
            $form
                .find("input[name='sumpeople']")
                .val()
                ?.trim(),
            $form
                .find("input[name='nohpdriver']")
                .val()
                ?.trim(),
            $form
                .find("#imgvisitorpathin")
                .val()
                ?.trim()
        ];

        let selfieValid = false;
        try {
            const val = $form
                .find("#selfiePhotos")
                .val()
                ?.trim();
            if (val) {
                const parsed = JSON.parse(val);
                selfieValid = Array.isArray(parsed) && parsed.length > 0;
            }
        } catch (e) {}

        return fields.every(Boolean) && selfieValid;
    }

    function updateRfidState() {
        $rfidInput.prop("disabled", !isFormComplete());
    }

    let updateTimeout;
    function triggerUpdate() {
        clearTimeout(updateTimeout);
        updateTimeout = setTimeout(updateRfidState, 100);
    }

    $form.on("input change", "input, select, textarea", triggerUpdate);
    $form.on("change", "#imgvisitorpathin, #selfiePhotos", updateRfidState);

    function checkFormValidity() {
        $(".invalid-feedback, .invalid-feedback-foto").remove();
        $form.find(".is-invalid").removeClass("is-invalid");
        let isValid = true;

        const showError = ($el, msg, isFoto = false) => {
            $el.addClass("is-invalid");
            const cls = isFoto
                ? "invalid-feedback-foto text-danger mt-2"
                : "invalid-feedback";
            if ($el.next("." + cls.replace(/ /g, ".")).length === 0) {
                $el.after(`<div class="${cls}">${msg}</div>`);
            }
            isValid = false;
        };

        [
            {
                sel: "input[name='namavisitor']",
                msg: "Nama supir wajib diisi"
            },
            {
                sel: "input[name='namacomp']",
                msg: "Nama perusahaan wajib diisi"
            },
            {
                sel: "input[name='nomorktp']",
                msg: "Nomor KTP/SIM wajib diisi"
            },
            {
                sel: "#tglLahir",
                msg: "Tanggal lahir wajib diisi"
            },
            {
                sel: "select[name='purpose']",
                msg: "Tujuan wajib dipilih"
            },
            {
                sel: "input[name='nopol']",
                msg: "Nomor polisi wajib diisi"
            },
            {
                sel: "input[name='sumpeople']",
                msg: "Jumlah orang wajib diisi"
            },
            {
                sel: "input[name='nohpdriver']",
                msg: "No HP driver wajib diisi"
            }
        ].forEach(({ sel, msg }) => {
            if (
                !$form
                    .find(sel)
                    .val()
                    ?.trim()
            )
                showError($form.find(sel), msg);
        });

        if (
            !$form
                .find("#imgvisitorpathin")
                .val()
                ?.trim()
        ) {
            showError($("#ktpPreview"), "Foto KTP wajib diunggah", true);
        }

        let selfieValid = false;
        try {
            const val = $form
                .find("#selfiePhotos")
                .val()
                ?.trim();
            if (val) {
                const parsed = JSON.parse(val);
                selfieValid = Array.isArray(parsed) && parsed.length > 0;
            }
        } catch (e) {}
        if (!selfieValid) {
            showError(
                $("#selfiePreview"),
                "Minimal 1 foto selfie wajib diunggah",
                true
            );
        }

        $rfidInput.prop("disabled", !isValid);
        if (!isValid) {
            Swal.fire({
                icon: "warning",
                title: "Form Belum Lengkap!",
                text: "Harap lengkapi semua isian wajib dan unggah foto.",
                showConfirmButton: true
            });
        }
        return isValid;
    }

    $form.on("submit", function(e) {
        e.preventDefault();
        if (!checkFormValidity()) return;

        $submitBtn
            .prop("disabled", true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Memproses...');
        $("#formAlert")
            .hide()
            .removeClass("alert-success alert-danger")
            .html();

        const formData = new FormData(this);
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        if (csrfToken) {
            formData.append("_token", csrfToken);
        } else {
            Swal.fire("Error", "Token CSRF tidak ditemukan.");
            $submitBtn
                .prop("disabled", false)
                .html('<i class="fas fa-save me-2"></i>Simpan Data');
            return;
        }

        $.ajax({
            url: $form.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": csrfToken },
            // success: function(response) {
            //     Swal.fire({
            //         icon: "success",
            //         title: "Berhasil!",
            //         text: response.message || "Data berhasil disimpan.",
            //         timer: 2000,
            //         showConfirmButton: false
            //     });
            //     resetForm();
            //     $submitBtn
            //         .prop("disabled", false)
            //         .html('<i class="fas fa-save me-2"></i>Simpan Data');
            //     $rfidInput.prop("disabled", true).val("");
            // },
            // success: function(response) {
            //     Swal.fire({
            //         icon: "success",
            //         title: "Berhasil!",
            //         text: response.message || "Data berhasil disimpan.",
            //         timer: 2000,
            //         showConfirmButton: false
            //     });

            //     // resetForm(); // reset form
            //     // clearModalPreviews(); // 🆕 reset modal & hidden input
            //     resetForm();
            //     resetAllCameraAndPhotos();

            //     $("#myModal").modal("hide");
            //     $("#selfieModal").modal("hide");

            //     $submitBtn
            //         .prop("disabled", false)
            //         .html('<i class="fas fa-save me-2"></i>Simpan Data');
            //     $rfidInput.prop("disabled", true).val("");

            //     // window.href.reload();
            // },
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: response.message || "Data berhasil disimpan.",
                    timer: 2000,
                    showConfirmButton: false
                });

                // Reset form & kamera
                resetForm();
                resetAllCameraAndPhotos();

                // ==== EXTRA: Reset Capture Identitas ====
                const img = document.getElementById("capturedImage");
                if (img) {
                    img.removeAttribute("src"); // hapus attribut
                    img.src = ""; // clear src
                }

                const container = document.getElementById(
                    "capturedImageContainer"
                );
                if (container) {
                    container.style.display = "none"; // hide inline
                    container.classList.add("d-none"); // hide via bootstrap
                }

                // Matikan kamera KTP kalau masih nyala
                const video = document.getElementById("video");
                if (video && video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                }

                // Matikan kamera Selfie kalau masih nyala
                const selfieVideo = document.getElementById("selfieVideo");
                if (selfieVideo && selfieVideo.srcObject) {
                    selfieVideo.srcObject
                        .getTracks()
                        .forEach(track => track.stop());
                    selfieVideo.srcObject = null;
                }

                // Tutup modal
                $("#myModal").modal("hide");
                $("#selfieModal").modal("hide");

                // Reset tombol & input RFID
                $submitBtn
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');
                $rfidInput.prop("disabled", true).val("");
            },
            // success: function(response) {
            //     Swal.fire({
            //         icon: "success",
            //         title: "Berhasil!",
            //         text: response.message || "Data berhasil disimpan.",
            //         timer: 2000,
            //         showConfirmButton: false
            //     });

            //     resetForm();
            //     resetAllCameraAndPhotos(); // 🔹 reset ke state awal (tombol Capture, kamera mati, foto kosong)

            //     $("#myModal").modal("hide");
            //     $("#selfieModal").modal("hide");

            //     $submitBtn
            //         .prop("disabled", false)
            //         .html('<i class="fas fa-save me-2"></i>Simpan Data');
            //     $rfidInput.prop("disabled", true).val("");
            // },
            error: function(xhr) {
                let message =
                    xhr.responseJSON?.message ||
                    "Terjadi kesalahan saat menyimpan data.";
                if (xhr.status === 419)
                    message =
                        "Sesi telah kedaluwarsa. Silakan refresh halaman.";

                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: message,
                    showConfirmButton: true
                });
                $("#formAlert")
                    .addClass("alert-danger")
                    .html(message)
                    .fadeIn()
                    .delay(3000)
                    .fadeOut(() => {
                        $("#formAlert")
                            .removeClass("alert-danger")
                            .html("");
                    });
                $submitBtn
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');
            }
        });
    });

    setTimeout(updateRfidState, 500);
    window.triggerFormValidation = updateRfidState;

    console.log("✅ Visitor Form Script Loaded");
});
