function openCekKendaraanActionModal(trncekid) {
    $.get(API_CEK_KENDARAAN_SHOW + "?id=" + trncekid, function (response) {
        if (!response.success) {
            console.log(response.message);
            Swal.fire("Gagal", "Terjadi kesalahan", "error");
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

    let fotoObj = {};
    try {
        fotoObj = JSON.parse(fotoJson || "{}");
    } catch {
        fotoObj = {};
    }

    if (!fotoObj || Object.keys(fotoObj).length === 0) {
        container.append(
            "<p class='text-muted fst-italic'>Tidak ada foto.</p>"
        );
        return;
    }

    const accordionId = `accordion-${Math.random().toString(36).substr(2, 6)}`;

    const accordion = $(`<div class="accordion" id="${accordionId}"></div>`);

    let index = 0;

    Object.entries(fotoObj).forEach(([key, images]) => {
        if (!Array.isArray(images) || images.length === 0) return;

        const label = key
            .replace(/_/g, " ")
            .replace(/\b\w/g, (c) => c.toUpperCase());

        const itemId = `${accordionId}-item-${index}`;
        const collapseId = `${accordionId}-collapse-${index}`;

        const item = $(`
            <div class="accordion-item">
                <h2 class="accordion-header" id="${itemId}">
                    <button class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#${collapseId}">
                        ${label}
                        <span class="badge bg-secondary ms-2">
                            ${images.length} foto
                        </span>
                    </button>
                </h2>
                <div id="${collapseId}"
                    class="accordion-collapse collapse"
                    data-bs-parent="#${accordionId}">
                    <div class="accordion-body">
                        <div class="row g-2"></div>
                    </div>
                </div>
            </div>
        `);

        const row = item.find(".row");

        images.forEach((path) => {
            row.append(`
                <div class="col-6 col-md-4 col-lg-3">
                    <img src="/${path}"
                        class="img-fluid rounded border zoomable-image"
                        style="height:120px;object-fit:cover;cursor:pointer">
                </div>
            `);
        });

        accordion.append(item);
        index++;
    });

    container.append(accordion);
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

document.addEventListener("DOMContentLoaded", function () {
    const modalDetail = document.getElementById("modalCekKendaraanDetail");
    if (!modalDetail) return;

    modalDetail.addEventListener("hidden.bs.modal", function () {
        // reset ke tab Informasi
        const infoTabBtn = document.querySelector(
            '#detailTabs button[data-bs-target="#tabInfo"]'
        );

        if (infoTabBtn) {
            const tab = bootstrap.Tab.getOrCreateInstance(infoTabBtn);
            tab.show();
        }

        // reset scroll tab content
        const tabContent = modalDetail.querySelector(".tab-content");
        if (tabContent) {
            tabContent.scrollTop = 0;
        }
    });
});
