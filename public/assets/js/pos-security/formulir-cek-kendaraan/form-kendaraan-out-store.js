// form ajax
$(document).ready(function () {
    $("#cekKendaraanFormOut").on("submit", function (e) {
        e.preventDefault();

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
            success: function (response) {
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
                        window.cekKendaraanOutTable.ajax.reload(null, false);
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

            error: function (xhr) {
                console.log(xhr.responseJSON?.message);

                let message = "Terjadi kesalahan. Silakan coba lagi.";

                // 409: conflict
                if (xhr.status === 409 && xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                // 404: not found
                else if (xhr.status === 404 && xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
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
