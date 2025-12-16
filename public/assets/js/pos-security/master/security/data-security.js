window.openCreateSecurityModal = function () {
    $("#formCreateSecurity")[0].reset();
    $("#modalCreateSecurity").modal("show");
};

window.openEditSecurityModal = function (id) {
    $.get(API_FORM_EDIT_SECURITY.replace(":id", id), function (res) {
        if (res.success) {
            $("#edit_id").val(res.data.id);
            $("#edit_nik").val(res.data.nik);
            $("#edit_nama_security").val(res.data.nama_security);
            $("#edit_nomor_kartu").val(res.data.nomor_kartu);

            $("#modalEditSecurity").modal("show");
        } else {
            Swal.fire("Gagal", "Data tidak ditemukan", "error");
        }
    });
};

window.toggleSecurity = function (id) {
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mengubah status security ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            let url = API_FORM_TOGGLE_SECURITY.replace(":id", id);

            $.ajax({
                url,
                method: "POST",
                // data: {
                //     _token: CSRF_TOKEN,
                // },
                success: function (res) {
                    if (res.success) {
                        $(".ga-data-security-datatables")
                            .DataTable()
                            .ajax.reload(null, false);

                        Swal.fire("Berhasil", res.message, "success");
                    } else {
                        Swal.fire("Gagal", res.message, "error");
                    }
                },
                error: function (xhr) {
                    console.log(xhr.responseJSON?.message);

                    Swal.fire(
                        "Error",
                        "Terjadi kesalahan saat mengubah status security",
                        "error"
                    );
                },
            });
        }
    });
};

$("#formCreateSecurity").on("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.ajax({
        url: API_FORM_CREATE_SECURITY,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                $("#modalCreateSecurity").modal("hide");

                $(".ga-data-security-datatables")
                    .DataTable()
                    .ajax.reload(null, false);

                Swal.fire("Berhasil", res.message, "success");
            } else {
                Swal.fire("Gagal", res.message, "error");
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                const firstKey = Object.keys(errors)[0];
                const firstMessage = errors[firstKey][0];

                Swal.fire("Validasi Gagal", firstMessage, "warning");
            } else {
                Swal.fire(
                    "Error",
                    xhr.responseJSON?.message || "Terjadi kesalahan",
                    "error"
                );
            }
        },
    });
});

$("#formEditSecurity").on("submit", function (e) {
    e.preventDefault();

    let id = $("#edit_id").val();
    let formData = new FormData(this);

    $.ajax({
        url: API_FORM_UPDATE_SECURITY.replace(":id", id),
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if (res.success) {
                $("#modalEditSecurity").modal("hide");

                $(".ga-data-security-datatables")
                    .DataTable()
                    .ajax.reload(null, false);

                Swal.fire("Berhasil", res.message, "success");
            } else {
                Swal.fire("Gagal", res.message, "error");
            }
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                const firstKey = Object.keys(xhr.responseJSON.errors)[0];
                Swal.fire(
                    "Validasi Gagal",
                    xhr.responseJSON.errors[firstKey][0],
                    "warning"
                );
            } else {
                Swal.fire("Error", "Terjadi kesalahan sistem", "error");
            }
        },
    });
});

$(document).on("click", ".preview-image", function () {
    const imageUrl = $(this).data("preview");

    $("#modalImage").attr("src", imageUrl);
    $("#imageModal").modal("show");
});

$("#imageModal").on("hidden.bs.modal", function () {
    $("#modalImage").attr("src", "");
});
