function openVendorActionModal(trnvisitorid) {
    $.get(
        API_GET_VISITOR_DETAIL_VENDOR + "?id=" + trnvisitorid,
        function (response) {
            if (!response.success) {
                Swal.fire("Gagal", response.message, "error");
                return;
            }

            const data = response.data;

            // Kirim data ke masing-masing modal handler
            VisitorDetailModal.show(data); // Modal info utama
            BlacklistManager.prepare(data); // Modal blacklist
        }
    );
}

const VisitorDetailModal = {
    show(data) {
        $("#detailNamaVisitor").text(data.namavisitor);
        $("#detailNoKartu").text(data.no_kartu);
        $("#detailNoIdentitas").text(data.no_ktp_sim);
        $("#detailPerusahaan").text(data.namacomp);
        $("#detailPurpose").text(data.purpose);
        $("#detailNopol").text(data.nopol);
        $("#detailNoHpDriver").text(data.nohpdriver);
        $("#detailTglLahir").text(data.tgl_lahir);
        $("#detailWaktuMasuk").text(`${data.datein} ${data.timein}`);
        $("#detailWaktuKeluar").text(
            `${data.dateout || "-"} ${data.timeout || "-"}`
        );

        // Foto KTP
        $("#detailKtpFoto").attr("src", data.imgvisitorpathin || "");
        $("#detailSelfieOutContainer").attr("src", data.foto_out || "");

        // Foto Selfie
        let selfieContainer = $("#detailSelfieContainer");
        selfieContainer.html(""); // clear
        try {
            const fotoList = JSON.parse(data.foto);
            fotoList.forEach((url) => {
                selfieContainer.append(
                    `<img src="${url}" class="img-thumbnail" style="max-width: 120px;">`
                );
            });
        } catch (e) {
            console.warn("Foto selfie tidak bisa dibaca", e);
        }

        // Buka modalnya
        const modalEl = document.getElementById("modalVisitorDetail");
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        } else {
            console.error("Element modalVisitorDetail tidak ditemukan.");
        }
    },
};

function triggerReportLostCard() {
    Swal.fire("Informasi", "Fitur Lapor Kartu Hilang hanya tersedia untuk Supplier.", "info");
}

function triggerBlacklistVisitor(visitorId) {
    if (!visitorId) {
        BlacklistManager.open();
        return;
    }
    $.get(
        API_GET_VISITOR_DETAIL_VENDOR + "?id=" + visitorId,
        function (response) {
            if (!response.success) {
                Swal.fire("Gagal", response.message, "error");
                return;
            }

            BlacklistManager.prepare(response.data);
            BlacklistManager.open();
        }
    );
}

const BlacklistManager = {
    _data: {},

    prepare(data) {
        this._data = data;

        // Prefill form field
        $("#bl_no_identitas").val(data.no_ktp_sim || "");
        $("#bl_nama").val(data.namavisitor || "");
        $("#tanggal_lahir").val(data.tgl_lahir || "");
        $("#jenis_identitas").val(data.jenis_identitas || "KTP");

        // Informasi tambahan
        $("#bl_trnvisitorid").val(data.trnvisitorid || data.id || "");
        $("#bl_info_nama").text(data.namavisitor || "-");
        $("#bl_info_no_identitas").text(data.no_ktp_sim || "-");
        $("#bl_info_no_kartu").text(data.no_kartu || "-");
        $("#bl_info_perusahaan").text(data.namacomp || "-");
        $("#bl_info_waktu_masuk").text(`${data.datein} ${data.timein}` || "-");
        $("#bl_info_tujuan").text(data.purpose || "-");
        $("#bl_info_nopol").text(data.nopol || "-");
        $("#bl_info_nohp").text(data.nohpdriver || "-");
        $("#bl_info_kernet").text("-");
        $("#bl_info_plant").text(data.plant || "-");

        $("#blKtpFoto").attr("src", data.imgvisitorpathin || "");
        let selfieContainer = $("#blSelfieContainer");
        selfieContainer.html("");
        try {
            const fotoList = JSON.parse(data.foto);
            fotoList.forEach((url) => {
                selfieContainer.append(
                    `<img src="${url}" class="img-thumbnail" style="max-width: 120px;">`
                );
            });
        } catch (e) {
            console.warn("Selfie hilang", e);
        }
    },

    open() {
        const modalEl = document.getElementById("modalBlacklistVisitor");
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        } else {
            console.error("Element modalBlacklistVisitor tidak ditemukan.");
        }
    },

    submit() {
        const payload = {
            trnvisitorid: $("#bl_trnvisitorid").val(),
            no_identitas: $("#bl_no_identitas").val(),
            nama: $("#bl_nama").val(),
            tanggal_lahir: $("#tanggal_lahir").val(),
            jenis_identitas: $("#jenis_identitas").val(),
            alasan_blacklist: $("#alasan_blacklist").val(),
            diblacklist_oleh: $("#diblacklist_oleh").val(),
        };

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $.post(API_BLOCK_VENDOR, payload, function (res) {
            if (res.success) {
                Swal.fire("Sukses", "Visitor diblacklist.", "success");
                $("#modalBlacklistVisitor").modal("hide");
            } else {
                if (res.data) {
                    const detail = res.data;

                    console.log("Blacklist detail:", detail);

                    const statusBadge = detail.aktif
                        ? '<span class="badge bg-success">Aktif</span>'
                        : '<span class="badge bg-danger">Nonaktif</span>';

                    const html = `
                        <div class="text-start mb-2">Identitas ini sudah diblacklist sebelumnya:</div>
                        <table class="table table-sm table-bordered text-start">
                            <tr><th>Nama</th><td>${detail.nama}</td></tr>
                            <tr><th>No Identitas</th><td>${detail.no_identitas}</td></tr>
                            <tr><th>Tanggal Lahir</th><td>${detail.tanggal_lahir}</td></tr>
                            <tr><th>Alasan</th><td>${detail.alasan_blacklist}</td></tr>
                            <tr><th>Tanggal Blacklist</th><td>${detail.tanggal_blacklist}</td></tr>
                            <tr><th>Diblacklist Oleh</th><td>${detail.diblacklist_oleh}</td></tr>
                            <tr><th>Status</th><td>${statusBadge}</td></tr>
                        </table>
                    `;

                    Swal.fire({
                        title: "Sudah diblacklist",
                        html: html,
                        icon: "warning",
                        width: 600,
                    });
                } else {
                    Swal.fire({
                        title: "Gagal",
                        html: res.message.replace(/\n/g, "<br>"),
                        icon: "error",
                    });
                }
            }
        });
    },
};

// Submit handler
$("#formBlacklistVisitor").on("submit", function (e) {
    e.preventDefault();
    BlacklistManager.submit();
});
