const qrRegionId = "qr-reader";

// Cek apakah browser mendukung getUserMedia
function isCameraSupported() {
    return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

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
    console.log("Keyword pencarian:", keyword);

    $.ajax({
        // url: API_FORM_SEARCH_SUPPLIER,
        url: "/search-supplier",
        method: "POST",
        data: { keyword: keyword },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () {
            // Tambahkan loading spinner jika mau
        },
        success: function (response) {
            // console.log(response.data);

            // console.log(
            //     "Elemennya ada?",
            //     document.getElementById("visitorKTP")
            // );

            // console.log("Isi no_ktp_sim:", response.data.no_ktp_sim);

            console.log(response.success);

            if (response.success) {
                // Isi data ke elemen HTML
                document
                    .getElementById("returnCard")
                    .setAttribute(
                        "data-trnvisitorid",
                        response.data.trnvisitorid
                    );
                document.getElementById("visitorName").innerText =
                    response.data.namavisitor || "-";
                document.getElementById("visitorCompany").innerText =
                    response.data.namacomp || "-";
                document.getElementById("visitorCard").innerText =
                    response.data.no_kartu || "-";
                document.getElementById("visitorKTP").innerText =
                    response.data.no_ktp_sim || "-";
                document.getElementById("visitorNopol").innerText =
                    response.data.nopol || "-";
                document.getElementById("visitorSumPeople").innerText =
                    response.data.sumpeople || "1";
                document.getElementById("visitorDateIn").innerText =
                    response.data.datein || "-";
                document.getElementById("visitorTimeIn").innerText =
                    response.data.timein || "-";

                // Tanggal keluar
                document.getElementById("visitorDateOut").innerText =
                    response.data.dateout || "-";

                // Waktu keluar
                document.getElementById("visitorTimeOut").innerText =
                    response.data.timeout || "-";

                // Status kartu
                let statusKartu = "-";
                if (response.data.kartu_dikembalikan == 0) {
                    statusKartu = "Active";
                } else if (response.data.is_block == 1) {
                    statusKartu = "Blocked";
                } else if (response.data.kartu_dikembalikan == 1) {
                    statusKartu = "Sudah Dikembalikan";
                }
                document.getElementById("visitorCardStatus").innerText =
                    statusKartu;

                // Gate info
                document.getElementById("visitorGateIdOut").innerText =
                    response.data.gateidout || "-";
                document.getElementById("visitorGateLineIdOut").innerText =
                    response.data.gatelineidout || "-";

                // Foto KTP
                document.getElementById("visitorKTPImage").src =
                    response.data.imgvisitorpathin || "";

                // Foto Selfie (array)
                const selfieContainer = document.getElementById(
                    "visitorSelfieImages"
                );
                selfieContainer.innerHTML = ""; // kosongin dulu

                if (response.data.foto) {
                    let selfiePhotos = [];
                    try {
                        selfiePhotos = JSON.parse(response.data.foto);
                    } catch (err) {
                        console.error("Error parsing selfie foto JSON:", err);
                    }

                    selfiePhotos.forEach(function (photoUrl) {
                        const imgEl = document.createElement("img");
                        imgEl.src = photoUrl;
                        imgEl.alt = "Selfie Photo";
                        imgEl.className = "img-thumbnail";
                        imgEl.style.maxWidth = "150px";
                        imgEl.style.margin = "5px";

                        // Tambahkan onclick untuk preview
                        imgEl.style.cursor = "pointer";
                        imgEl.onclick = function () {
                            document.getElementById(
                                "imagePreviewModalImg"
                            ).src = this.src;
                            const modal = new bootstrap.Modal(
                                document.getElementById("imagePreviewModal")
                            );
                            modal.show();
                        };

                        selfieContainer.appendChild(imgEl);
                    });
                }

                // Foto KTP click preview
                const ktpImage = document.getElementById("visitorKTPImage");
                ktpImage.src = response.data.imgvisitorpathin || "";
                ktpImage.style.cursor = "pointer";
                ktpImage.onclick = function () {
                    document.getElementById("imagePreviewModalImg").src =
                        this.src;
                    const modal = new bootstrap.Modal(
                        document.getElementById("imagePreviewModal")
                    );
                    modal.show();
                };

                // Set trnvisitorid ke tombol "Kartu Dikembalikan"
                document
                    .getElementById("returnCard")
                    .setAttribute(
                        "data-trnvisitorid",
                        response.data.trnvisitorid
                    );

                // Tampilkan alert box
                document.getElementById("visitorResult").style.display =
                    "block";
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops!",
                    text: response.message || "Data tidak ditemukan.",
                    timer: 2000,
                    showConfirmButton: false,
                });
                document.getElementById("visitorResult").style.display = "none";
            }
        },

        error: function () {
            alert("Gagal mencari data. Silakan coba lagi.");
            document.getElementById("visitorResult").style.display = "none";
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
                alert("Silakan scan QR atau masukkan Visitor ID / No Kartu.");
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

$("#returnCard").on("click", function () {
    const trnvisitorid = $(this).data("trnvisitorid");

    if (!trnvisitorid) {
        Swal.fire("Error", "Visitor ID tidak ditemukan!", "error");
        return;
    }

    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mengembalikan kartu untuk transporter ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, kembalikan!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                // url: API_FORM_KEMBALIKAN_KARTU_SUPPLIER,
                url: "/kembalikan-supplier",
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

$('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
    // Reset isi visitorResult
    $("#visitorResult").hide();
    $("#visitorName").text("-");
    $("#visitorCompany").text("-");
    $("#visitorCard").text("-");
    $("#visitorKTP").text("-");
    $("#visitorNopol").text("-");
    $("#visitorSumPeople").text("-");
    $("#visitorDateIn").text("-");
    $("#visitorTimeIn").text("-");
    $("#visitorDateOut").text("-");
    $("#visitorTimeOut").text("-");
    $("#visitorCardStatus")
        .text("-")
        .removeClass()
        .addClass("badge bg-secondary");
    $("#visitorGateIdOut").text("-");
    $("#visitorGateLineIdOut").text("-");
    $("#visitorKTPImage").attr("src", "");
    $("#visitorSelfieImages").empty(); // Kosongkan kontainer selfie
});
