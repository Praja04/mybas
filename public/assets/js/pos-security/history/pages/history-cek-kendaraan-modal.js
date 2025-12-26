function openCekKendaraanActionModal(trncekid) {
    $.get(API_CEK_KENDARAAN_SHOW + "?id=" + trncekid, function (response) {
        if (!response.success) {
            Swal.fire("Gagal", response.message, "error");
            return;
        }
        const data = response.data;

        VisitorDetailModal.show(data);
    });
}

const VisitorDetailModal = {
    show(data) {
        $("#detailNomorPolisi").text(data.nomor_polisi);
        $("#detailNamaSupir").text(data.nama_supir);
        $("#detailNamaPerusahaan").text(data.company);

        $("#detailStatus").html(renderStatusBadge(data));

        $("#detailJenisMuatan").text(data.muatan_type ?? "-");
        $("#detailJenisTruk").text(data.truck_type ?? "-");
        $("#detailJenisTrukLainnya").text(data.truck_type_other ?? "-");

        $("#detailNamaPetugasMasuk").text(data.nama_petugas_masuk ?? "-");
        $("#detailNamaPetugasKeluar").text(data.nama_petugas_keluar ?? "-");

        $("#detailWaktuMasuk").text(
            data.checked_in_at ? formatTanggal(data.checked_in_at) : "-"
        );
        $("#detailWaktuKeluar").text(
            data.checked_out_at ? formatTanggal(data.checked_out_at) : "-"
        );
        $("#detailDurasi").text(
            data.checked_out_at
                ? hitungDurasi(data.checked_in_at, data.checked_out_at)
                : // : hitungDurasi(data.checked_in_at, new Date()
                  "-"
        );

        let fotoContainer = $("#detailFotoContainer");
        fotoContainer.html("");

        renderFoto($("#detailFotoContainer"), data.foto_in);
        renderFoto($("#detailFotoKeluarContainer"), data.foto_out);

        const modalEl = document.getElementById("modalCekKendaraanDetail");
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        } else {
            console.error("Element modalCekKendaraanDetail tidak ditemukan.");
        }
    },
};

function getLabelFromPath(filePath) {
    try {
        if (!filePath) return "Foto Kendaraan";

        const parts = filePath.split("/");

        if (parts.length < 2) return "Foto Kendaraan";

        const labelRaw = parts[parts.length - 2];

        return labelRaw
            .replace(/_/g, " ")
            .replace(/\b\w/g, (c) => c.toUpperCase());
    } catch (e) {
        return "Foto Kendaraan";
    }
}

function formatTanggal(dateString) {
    if (!dateString) return "-";

    const date = new Date(dateString);
    if (isNaN(date)) return "-";

    return new Intl.DateTimeFormat("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).format(date);
}

function hitungDurasi(inTime, outTime) {
    if (!inTime || !outTime) return "-";

    const masuk = new Date(inTime);
    const keluar = new Date(outTime);

    if (isNaN(masuk) || isNaN(keluar)) return "-";
    if (keluar < masuk) return "-";

    const diffMs = keluar - masuk;
    const totalMenit = Math.floor(diffMs / 1000 / 60);

    const jam = Math.floor(totalMenit / 60);
    const menit = totalMenit % 60;

    let hasil = [];
    if (jam > 0) hasil.push(`${jam} jam`);
    if (menit > 0) hasil.push(`${menit} menit`);

    return hasil.length ? hasil.join(" ") : "0 menit";
}

function renderFoto(container, fotoJson) {
    container.html("");

    try {
        const fotoObj = JSON.parse(fotoJson || "{}");

        if (Object.keys(fotoObj).length === 0) {
            container.append("<p class='text-muted'>Tidak ada foto.</p>");
            return;
        }

        // flatten semua path
        Object.values(fotoObj)
            .flat()
            .forEach((path) => {
                const label = getLabelFromPath(path);
                const imageUrl = `/${path}`;

                container.append(`
                    <div class="col-md-6 col-lg-3 mb-3 text-center">
                        <small class="text-muted d-block mb-1">${label}</small>
                        <img
                            src="${imageUrl}"
                            alt="${label}"
                            class="img-fluid rounded border zoomable-image"
                            style="max-height:180px;cursor:pointer;transition:transform .2s"
                            onmouseover="this.style.transform='scale(1.03)'"
                            onmouseout="this.style.transform='scale(1)'"
                        >
                    </div>
                `);
            });
    } catch (e) {
        console.warn("Gagal render foto", e);
        container.append(
            "<p class='text-danger'>Gagal memuat foto kendaraan.</p>"
        );
    }
}

function renderStatusBadge(data) {
    // 1. Belum cek kendaraan
    if (!data.checked_in_at) {
        return '<span class="badge bg-danger">Belum Cek</span>';
    }

    // 2. Sudah cek tapi belum keluar
    if (data.checked_in_at && !data.checked_out_at) {
        return '<span class="badge bg-warning text-dark">Belum Keluar</span>';
    }

    // 3. Sudah keluar
    return '<span class="badge bg-success">Sudah Keluar</span>';
}

$(document).on("click", ".zoomable-image", function () {
    const src = this.src;

    $("#overlayImage").attr("src", src);
    $("#imageOverlay").css("display", "flex");
});

// close overlay saat diklik
$("#imageOverlay").on("click", function () {
    $(this).css("display", "none");
    $("#overlayImage").attr("src", "");
});

// close esc
$(document).on("keydown", function (e) {
    if (e.key === "Escape") {
        $("#imageOverlay").css("display", "none");
        $("#overlayImage").attr("src", "");
    }
});
