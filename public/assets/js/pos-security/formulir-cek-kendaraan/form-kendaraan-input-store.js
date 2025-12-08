// form ajax
$(document).ready(function () {
    $("#cekKendaraanForm").on("submit", function (e) {
        e.preventDefault();

        $("#submitBtn")
            .prop("disabled", true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...');

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

                setTimeout(function () {
                    $("#formAlert")
                        .fadeOut()
                        .removeClass("alert-success alert-danger")
                        .html("");
                }, 2000);

                $("#cekKendaraanForm")[0].reset();
                $("#cekKendaraanForm").hide();
                resetForm();

                $("#submitBtn")
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');
            },
            error: function (xhr) {
                let message = "Terjadi kesalahan. Silakan coba lagi.";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
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
                    .html('<i class="fas fa-save me-2"></i>Simpan Data');
            },
        });
    });
});

function resetForm() {
    document.querySelectorAll('[id^="preview-"]').forEach((el) => {
        el.innerHTML = "";
    });

    document.querySelectorAll('input[name^="photos"]').forEach((input) => {
        input.value = "";
    });

    resetCameraModal();

    $("#muatanType").val("").trigger("change");
    $("#truckType")
        .empty()
        .append(
            '<option value="" disabled selected>-- Pilih Jenis Truk --</option>'
        );
    $("#truckTypeContainer").hide();
    $("#fotoSection").html("");
    $("#nopol-search").val("");

    activePhotoKey = null;
}

function resetFormButton() {
    // save autofilled
    const namaSupir = $("#nama-supir").val();
    const company = $("#company").val();
    const nomorPolisi = $("#nomor-polisi").val();
    const createdby = $("#createdby").val();

    $("#cekKendaraanForm")[0].reset();

    $("#nama-supir").val(namaSupir);
    $("#company").val(company);
    $("#nomor-polisi").val(nomorPolisi);
    $("#createdby").val(createdby);

    resetForm();

    Swal.fire({
        icon: "success",
        title: "Form berhasil direset",
        text: "Semua data dan foto sudah dibersihkan.",
    });

    $("#formAlert")
        .stop(true)
        .hide()
        .removeClass("alert-success alert-danger")
        .html("");
}
