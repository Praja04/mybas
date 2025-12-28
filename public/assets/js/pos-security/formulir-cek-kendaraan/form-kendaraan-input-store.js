// form ajax
$(document).ready(function () {
    $("#cekKendaraanForm").on("submit", function (e) {
        e.preventDefault();

        let valid = true;
        let firstEmptyLabel = "";

        $("#fotoSection input[required]").each(function () {
            if (!$(this).val()) {
                valid = false;
                const key = $(this).attr("id").replace("input-", "");
                firstEmptyLabel = key.replace(/_/g, " ");
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
            success: function (response) {
                if (response.success) {
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

            error: function (xhr) {
                console.log(
                    "error store cek kendaraan: ",
                    xhr.responseJSON?.message
                );

                let message = "Terjadi kesalahan. Silakan coba lagi.";

                // 409: conflict
                if (xhr.status === 409 && xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: message,
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
