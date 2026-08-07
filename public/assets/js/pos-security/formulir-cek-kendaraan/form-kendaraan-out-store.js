// form ajax
$(document).ready(function () {
    $("#cekKendaraanFormOut").on("submit", function (e) {
        e.preventDefault();

        if (!navigator.onLine) {
            Swal.fire({
                icon: "error",
                title: "Koneksi Internet Terputus",
                text: "Periksa koneksi internet Anda lalu coba kembali.",
                confirmButtonText: "OK",
            });
            return false;
        }

        let valid = true;
        let firstEmptyLabel = "";

        $("#fotoSectionOut input[required]").each(function () {
            const rawValue = $(this).val();
            let photos = [];

            try {
                photos = JSON.parse(rawValue || "[]");
            } catch (e) {
                photos = [];
            }

            // validasi foto kosong
            if (!Array.isArray(photos) || photos.length === 0) {
                valid = false;

                const label = $(this)
                    .closest(".foto-slot")
                    .find("label")
                    .clone()
                    .children()
                    .remove()
                    .end()
                    .text()
                    .trim();

                firstEmptyLabel = label || "Foto";

                return false;
            }
        });

        if (!valid) {
            Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: `Foto ${firstEmptyLabel} wajib diisi.`,
            });
            e.preventDefault();
            return false;
        }

        $("#submitBtnOut")
            .prop("disabled", true)
            .html('<i class="mdi mdi-spin me-2"></i>Menyimpan...');

        $("#formAlertOut")
            .hide()
            .removeClass("alert-success alert-danger")
            .html("");

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: async function (response) {
                if (photoSessionId && window.IDBDraft) {
                    await window.IDBDraft.deleteDraft(photoSessionId);
                }

                if (response.success) {
                    photoStore = {};
                    tempPhotos = [];
                    activePhotoKey = null;
                    photoSessionId = null;

                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: response.message || "Data berhasil disimpan.",
                        timer: 2000,
                        showConfirmButton: false,
                    });

                    $("#formAlertOut")
                        .stop(true)
                        .hide()
                        .removeClass("alert-danger alert-success")
                        .addClass("alert-success")
                        .html(response.message)
                        .fadeIn();

                    setTimeout(function () {
                        $("#formAlertOut")
                            .fadeOut()
                            .removeClass("alert-success alert-danger")
                            .html("");
                    }, 2000);

                    document
                        .querySelectorAll('[id^="preview-out-"]')
                        .forEach((el) => {
                            el.innerHTML = "";
                        });

                    $("#cekKendaraanFormOut")
                        .find('input[name^="photos"]')
                        .val("");

                    // reset kamera
                    if (typeof resetCameraModal === "function") {
                        resetCameraModal();
                    }

                    // reset state kamera
                    if (typeof setActivePhotoKey === "function") {
                        setActivePhotoKey(null);
                    }

                    $("#fotoSectionOut").html("");
                    $("#cekKendaraanFormOut")[0].reset();

                    $("#submitBtnOut")
                        .prop("disabled", false)
                        .html(
                            '<i class="mdi mdi-content-save me-2"></i>Simpan Data'
                        );

                    $("#formWrapperOut").hide();
                    $("#headerFormOut").hide();

                    $("#tableWrapperOut").fadeIn();
                    $("#headerTableOut").fadeIn();

                    setStepOut("table");

                    if (window.cekKendaraanOutTable) {
                        window.cekKendaraanOutTable.reload(null, false);
                    }

                    return;
                }

                // res.success === false
                Swal.fire({
                    icon: "warning",
                    title: "Gagal!",
                    text: response.message || "Terjadi kesalahan.",
                });

                $("#formAlertOut")
                    .stop(true)
                    .hide()
                    .removeClass("alert-success alert-danger")
                    .addClass("alert-danger")
                    .html(response.message || "Terjadi kesalahan.")
                    .fadeIn();

                $("#submitBtnOut")
                    .prop("disabled", false)
                    .html(
                        '<i class="mdi mdi-content-save me-2"></i>Simpan Data'
                    );
            },

            error: function (xhr, status) {
                console.log(xhr.responseJSON?.message);

                let message = "Terjadi kesalahan. Silakan coba lagi.";

                if (xhr.status === 0) {
                    message = "Koneksi internet terputus. Silakan coba lagi";
                }
                else if (status === "timeout") {
                    message = "Koneksi terlalu lambat. Silakan coba lagi.";
                }
                else if (
                    [422, 409, 404].includes(xhr.status) &&
                    xhr.responseJSON?.message
                ) {
                    message = xhr.responseJSON.message;
                }
                else if (xhr.status >= 500) {
                    message = "Terjadi kesalahan pada server. Silakan coba lagi.";
                }

                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: message,
                });

                $("#formAlertOut")
                    .stop(true)
                    .hide()
                    .removeClass("alert-success alert-danger")
                    .addClass("alert-danger")
                    .html(message)
                    .fadeIn();

                $("#submitBtnOut")
                    .prop("disabled", false)
                    .html(
                        '<i class="mdi mdi-content-save me-2"></i>Simpan Data'
                    );
            },
        });
    });
});
