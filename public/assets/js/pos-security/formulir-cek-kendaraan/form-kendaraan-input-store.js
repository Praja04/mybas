// form ajax
$(document).ready(function () {
    $("#cekKendaraanForm").on("submit", function (e) {
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

        $("#fotoSection .foto-slot").each(function () {
            const key = $(this).data("key");
            const isRequired = $(this).find("input[required]").length > 0;

            if (!isRequired) return;

            const photos = photoStore[key] || [];

            if (!Array.isArray(photos) || photos.length === 0) {
                valid = false;

                const label = $(this)
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

        $("#submitBtn")
            .prop("disabled", true)
            .html('<i class="mdi mdi-loading"></i>Menyimpan...');

        $("#formAlert")
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
                if (response.success) {
                    if (photoSessionId && window.IDBDraft) {
                        await window.IDBDraft.deleteDraft(photoSessionId);
                    }
                    
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

                    $("#formAlert")
                        .stop(true)
                        .hide()
                        .removeClass("alert-danger alert-success")
                        .addClass("alert-success")
                        .html(response.message)
                        .fadeIn();

                    setTimeout(function () {
                        $("#formAlert")
                            .fadeOut()
                            .removeClass("alert-success alert-danger")
                            .html("");
                    }, 2000);

                    document
                        .querySelectorAll('[id^="preview-"]')
                        .forEach((el) => {
                            el.innerHTML = "";
                        });

                    document
                        .querySelectorAll('input[name^="photos"]')
                        .forEach((input) => {
                            input.value = "";
                        });

                    // reset kamera
                    if (typeof resetCameraModal === "function") {
                        resetCameraModal();
                    }

                    // reset state kamera
                    if (typeof setActivePhotoKey === "function") {
                        setActivePhotoKey(null);
                    }

                    // reset form
                    $("#cekKendaraanForm")[0].reset();
                    $("#fotoSection").html("");
                    $("#muatanType").val("").trigger("change");
                    $("#truckTypeContainer").hide();

                    $("#submitBtn")
                        .prop("disabled", false)
                        .html(
                            '<i class="mdi mdi-content-save"></i>Simpan Data'
                        );

                    $("#formWrapper").hide();
                    $("#headerForm").hide();

                    $("#tableWrapper").fadeIn();
                    $("#headerTable").fadeIn();

                    setStep("table");

                    if (window.cekKendaraanInTable) {
                        window.cekKendaraanInTable.ajax.reload(null, false);
                    }

                    return;
                }

                // if res.success === false
                Swal.fire({
                    icon: "warning",
                    title: "Gagal!",
                    text: response.message || "Terjadi kesalahan.",
                });

                $("#formAlert")
                    .stop(true)
                    .hide()
                    .removeClass("alert-success alert-danger")
                    .addClass("alert-danger")
                    .html(response.message || "Terjadi kesalahan.")
                    .fadeIn();

                $("#submitBtn")
                    .prop("disabled", false)
                    .html('<i class="mdi mdi-content-save"></i>Simpan Data');
            },

            error: function (xhr, status) {
                console.log(
                    "error store cek kendaraan: ",
                    xhr.responseJSON?.message
                );

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
                    text: xhr.responseJSON?.message || message,
                });

                $("#formAlert")
                    .stop(true)
                    .hide()
                    .removeClass("alert-success alert-danger")
                    .addClass("alert-danger")
                    .html(message)
                    .fadeIn();

                $("#submitBtn")
                    .prop("disabled", false)
                    .html('<i class="mdi mdi-content-save"></i>Simpan Data');
            },
        });
    });
});
