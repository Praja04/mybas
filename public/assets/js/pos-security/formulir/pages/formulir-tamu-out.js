let html5QrCode;
const qrRegionId = "qr-reader";
let isScannerRunning = false;
// Cek apakah browser mendukung getUserMedia
function isCameraSupported() {
    return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

function onScanError(errorMessage) {
    // Abaikan error umum karena QR belum ditemukan
    if (errorMessage.includes("No MultiFormat Readers were able to detect")) {
        return;
    }

    // Tampilkan error penting lainnya
    console.error("Scan error:", errorMessage);
    alert("Terjadi kesalahan saat memindai: " + errorMessage);
}

function searchVisitorData(keyword) {
    $("#returnCard").removeData("trnvisitorid");

    $.ajax({
        // url: API_FORM_SEARCH_TAMU,
        url: "/search-vendor-tamu",
        method: "POST",
        data: { keyword: keyword },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () {
            // Bisa pasang loading spinner di sini
        },
        success: function (response) {
            console.log(response.data);

            const qrcodeInput = document.getElementById("qrcode_input");

            if (response.success) {
                const data = response.data;

                console.log(
                    "trnvisitorid from search result: ",
                    data.trnvisitorid
                );

                qrcodeInput.value = ""; // 🔁 Reset
                qrcodeInput.focus(); // 👁️ Fokus ulang ke input

                // Isi data ke elemen
                $("#returnCard").attr("data-trnvisitorid", data.trnvisitorid);
                $("#visitorName").text(data.namavisitor || "-");
                $("#visitorCompany").text(data.namacomp || "-");
                $("#visitorCard").text(data.no_kartu || "-");
                $("#visitorKTP").text(data.no_ktp_sim || "-");
                $("#visitorNopol").text(data.nopol || "-");
                $("#visitorSumPeople").text(data.sumpeople || "1");
                $("#visitorDateIn").text(data.datein || "-");
                $("#visitorTimeIn").text(data.timein || "-");
                $("#visitorDateOut").text(data.dateout || "-");
                $("#visitorTimeOut").text(data.timeout || "");

                // Status kartu
                let statusKartu = "-";
                if (data.kartu_dikembalikan == 0) {
                    statusKartu = "Active";
                } else if (data.is_block == 1) {
                    statusKartu = "Blocked";
                } else if (data.kartu_dikembalikan == 1) {
                    statusKartu = "Sudah Dikembalikan";
                }
                $("#visitorCardStatus").text(statusKartu);

                // Info gate keluar
                $("#visitorGateIdOut").text(data.gateidout || "-");
                $("#visitorGateLineIdOut").text(data.gatelineidout || "-");

                // Foto KTP
                const ktpImage = $("#visitorKTPImage");
                ktpImage.attr("src", data.imgvisitorpathin || "");
                ktpImage.css("cursor", "pointer");
                ktpImage.off("click").on("click", function () {
                    $("#imagePreviewModalImg").attr("src", this.src);
                    const modal = new bootstrap.Modal(
                        document.getElementById("imagePreviewModal")
                    );
                    modal.show();
                });

                // Foto selfie
                const selfieContainer = $("#visitorSelfieImages");
                selfieContainer.empty();

                if (data.foto) {
                    let selfiePhotos = [];
                    try {
                        selfiePhotos = JSON.parse(data.foto);
                    } catch (err) {
                        console.error("Error parsing selfie foto JSON:", err);
                    }

                    selfiePhotos.forEach(function (photoUrl) {
                        const imgEl = $("<img>", {
                            src: photoUrl,
                            alt: "Selfie Photo",
                            class: "img-thumbnail",
                            css: {
                                maxWidth: "150px",
                                margin: "5px",
                                cursor: "pointer",
                            },
                        });

                        imgEl.on("click", function () {
                            $("#imagePreviewModalImg").attr("src", this.src);
                            const modal = new bootstrap.Modal(
                                document.getElementById("imagePreviewModal")
                            );
                            modal.show();
                        });

                        selfieContainer.append(imgEl);
                    });
                }

                // Show result section
                $("#visitorResult").show();
            } else {
                qrcodeInput.value = ""; // 🔁 Reset
                qrcodeInput.focus(); // 👁️ Fokus ulang ke input
                Swal.fire({
                    icon: "error",
                    title: "Oops!",
                    text: response.message || "Data tidak ditemukan.",
                    // timer: 2500,
                    showConfirmButton: true,
                });
                $("#visitorResult").hide();
            }
        },
        error: function () {
            alert("Gagal mencari data. Silakan coba lagi.");
            $("#visitorResult").hide();
        },
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Event ketika modal muncul
    document
        .getElementById("scanQrModal")
        .addEventListener("shown.bs.modal", function () {
            if (!isCameraSupported()) {
                alert(
                    "Kamera tidak didukung di browser ini. Gunakan browser modern dan pastikan menggunakan HTTPS."
                );
                return;
            }

            html5QrCode = new Html5Qrcode(qrRegionId);

            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
            };

            html5QrCode
                .start(
                    { facingMode: "environment" },
                    config,
                    onScanSuccess,
                    onScanError
                )
                .then(() => {
                    isScannerRunning = true;
                })
                .catch((err) => {
                    console.error(`Tidak dapat memulai scanner: ${err}`);
                    alert(
                        "Tidak dapat mengakses kamera. Pastikan izin kamera diizinkan dan perangkat memiliki kamera."
                    );
                });
        });

    function onScanSuccess(qrCodeMessage) {
        console.log("QR Code terdeteksi:", qrCodeMessage);
        document.getElementById("qrcode_input").value = qrCodeMessage;

        // Play beep sound
        const beepSound = document.getElementById("beepSound");
        if (beepSound) beepSound.play();

        if (html5QrCode && isScannerRunning) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                isScannerRunning = false;

                const modalEl = bootstrap.Modal.getInstance(
                    document.getElementById("scanQrModal")
                );
                if (modalEl) modalEl.hide();
            });
        }

        // Panggil pencarian data visitor otomatis setelah scan
        searchVisitorData(qrCodeMessage);
    }

    // Event ketika modal hilang
    document
        .getElementById("scanQrModal")
        .addEventListener("hidden.bs.modal", function () {
            if (html5QrCode && isScannerRunning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    isScannerRunning = false;
                });
            }
        });

    // Tombol Cari Data Visitor (bisa dari input manual atau hasil scan)
    document
        .getElementById("searchVisitorData")
        .addEventListener("click", function () {
            const qrValue = document
                .getElementById("qrcode_input")
                .value.trim();
            if (!qrValue) {
                alert("Silakan masukkan Visitor ID / No Kartu / KTP.");
                return;
            }

            searchVisitorData(qrValue);
        });
});
document.addEventListener("DOMContentLoaded", function () {
    const qrcodeInput = document.getElementById("qrcode_input");

    let isSearching = false;
    let typingTimer;
    const typingInterval = 500;

    function doSearch() {
        const keyword = qrcodeInput.value.trim();
        if (isSearching || keyword === "") return;

        isSearching = true;
        searchVisitorData(keyword); // panggil langsung function AJAX kamu

        setTimeout(() => {
            isSearching = false;
        }, 1000); // bisa kamu sesuaikan jika request kamu cepat/lambat
    }

    // Mode Enter (keyboard atau card reader)
    qrcodeInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            clearTimeout(typingTimer); // Batalkan trigger via input timeout
            doSearch();
        }
    });

    // Mode input timeout (card reader tanpa Enter)
    qrcodeInput.addEventListener("input", function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            doSearch();
        }, typingInterval);
    });
});

// Tombol Kartu Dikembalikan
$("#returnCard").on("click", function () {
    const trnvisitorid = $(this).data("trnvisitorid");
    console.log("returnCard trnvisitorid: ", trnvisitorid);

    if (!trnvisitorid) {
        Swal.fire("Error", "Visitor ID tidak ditemukan!", "error");
        return;
    }

    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mengembalikan kartu untuk visitor ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, kembalikan!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                // url: API_FORM_KEMBALIKAN_KARTU_TAMU,
                url: "/kembalikan-vendor-tamu",
                type: "POST",
                dataType: "json",
                data: {
                    trnvisitorid: trnvisitorid,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                    if (data.success) {
                        Swal.fire("Berhasil!", data.message, "success");
                        $("#visitorResult").hide();
                    } else {
                        Swal.fire("Gagal!", data.message, "error");
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    Swal.fire(
                        "Error",
                        "Terjadi kesalahan saat mengirim data.",
                        "error"
                    );
                },
            });
        }
    });
});
