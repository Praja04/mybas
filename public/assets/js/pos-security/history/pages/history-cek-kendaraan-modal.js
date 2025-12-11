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
        $("#detailNamaPetugas").text(data.nama_petugas);
        $("#detailJenisMuatan").text(data.muatan_type);
        $("#detailJenisTruk").text(data.truck_type);
        $("#detailJenisTrukLainnya").text(data.truck_type_other ?? "-");
        $("#detailWaktuPemeriksaan").text(
            `${data.tgl_periksa} ${data.jam_periksa}`
        );

        let fotoContainer = $("#detailFotoContainer");
        fotoContainer.html("");

        try {
            const fotoList = JSON.parse(data.foto_in || "{}");

            if (Object.keys(fotoList).length === 0) {
                fotoContainer.append(
                    "<p class='text-muted'>Tidak ada foto.</p>"
                );
            } else {
                console.log({ fotoList });
                Object.values(fotoList).forEach((path) => {
                    const label = getLabelFromFilename(path);
                    const imageUrl = `/storage/${path}`;
                    console.log({ path });

                    fotoContainer.append(`
                <div class="col-md-6 col-lg-3 mb-3 text-center">
                    <small class="text-muted d-block mb-1">${label}</small>
                    <img src="${imageUrl}" class="img-fluid rounded border" style="max-height: 180px;" alt="${label}">
                </div>
            `);
                });
            }
        } catch (e) {
            console.warn("Foto kendaraan gagal diparsing", e);
            fotoContainer.append(
                "<p class='text-danger'>Gagal memuat foto kendaraan.</p>"
            );
        }

        // Buka modalnya
        const modalEl = document.getElementById("modalCekKendaraanDetail");
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        } else {
            console.error("Element modalCekKendaraanDetail tidak ditemukan.");
        }
    },
};

function getLabelFromFilename(filePath) {
    try {
        const filename = filePath.split("/").pop(); // ambil nama file
        const nameOnly = filename.split(".")[0]; // remove extension

        const parts = nameOnly.split("_");

        // buang: nopol (0), status (1), dan 2 bagian timestamp terakhir
        if (parts.length > 4) {
            const labelParts = parts.slice(2, parts.length - 2);
            return labelParts
                .join(" ")
                .replace(/\b\w/g, (c) => c.toUpperCase());
        }

        return "Foto Kendaraan";
    } catch (e) {
        return "Foto Kendaraan";
    }
}
