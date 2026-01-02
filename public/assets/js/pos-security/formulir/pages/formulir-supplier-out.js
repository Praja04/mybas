const videoOut = document.getElementById("videoOut");
const canvasOut = document.getElementById("canvasOut");
const captureBtnOut = document.getElementById("captureBtnOut");
const retakeBtnOut = document.getElementById("retakeBtnOut");
const saveBtnOut = document.getElementById("saveBtnOut");
const startCameraOut = document.getElementById("startCameraOut");
const capturedImageOut = document.getElementById("capturedImageOut");
const capturedImageContainerOut = document.getElementById(
    "capturedImageContainerOut"
);

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
    $("#returnCard").removeData("trnvisitorid");
    // $("#kondisiKacamataGroupOut").hide();
    // $("#kondisiKacamataOut").val("");
    // resetFotoOut();

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
            if (response.success) {
                console.log(
                    "trnvisitorid from search result: ",
                    response.data.trnvisitorid
                );

                document
                    .getElementById("returnCard")
                    .setAttribute(
                        "data-trnvisitorid",
                        response.data.trnvisitorid
                    );
                document.getElementById("visitorName").innerText =
                    response.data.namavisitor || "-";
                document.getElementById("visitorKeterangan").innerText =
                    response.data.keterangan || "-";
                document.getElementById("visitorCompany").innerText =
                    response.data.namacomp || "-";
                document.getElementById("visitorCard").innerText =
                    response.data.no_kartu || "-";
                document.getElementById("visitorKTP").innerText =
                    response.data.no_ktp_sim || "-";
                document.getElementById("visitorNopol").innerText =
                    response.data.nopol || "-";
                document.getElementById("visitorDateIn").innerText =
                    response.data.datein || "-";
                document.getElementById("visitorTimeIn").innerText =
                    response.data.timein || "-";

                // Tanggal keluar
                document.getElementById("visitorDateOut").innerText =
                    response.data.dateout || "-";

                // Waktu keluar
                document.getElementById("visitorTimeOut").innerText =
                    response.data.timeout || "";

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

                // Pakai kacamata
                // let pakaiKacamata = "-";
                // if (response.data.is_kacamata == 0) {
                //     pakaiKacamata = "Tidak";
                // } else if (response.data.is_kacamata == 1) {
                //     pakaiKacamata = "Ya";
                // }

                // document.getElementById("visitorIsKacamata").innerText =
                //     pakaiKacamata;
                // document.getElementById("visitorKondisiKacamata").innerText =
                //     response.data.kondisi_kacamata || "-";

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

                // Kondisi kacamata (OUT)
                // if (
                //     response.data.is_kacamata === 1 ||
                //     response.data.is_kacamata === "1"
                // ) {
                //     $("#kondisiKacamataGroupOut").show();
                // } else {
                //     $("#kondisiKacamataGroupOut").hide();
                //     $("#kondisiKacamataOut").val("");
                // }

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
    // const isKacamata = $(this).data("is_kacamata");
    // const fotoOut = $("#fotoOut").val();
    // const kondisiKacamata = $("#kondisiKacamataOut").val();

    console.log("returnCard trnvisitorid: ", trnvisitorid);

    if (!trnvisitorid) {
        Swal.fire("Error", "Visitor ID tidak ditemukan!", "error");
        return;
    }

    // if (!fotoOut) {
    //     Swal.fire(
    //         "Peringatan",
    //         "Silakan ambil foto keluar terlebih dahulu.",
    //         "warning"
    //     );
    //     return;
    // }

    // if (isKacamata == 1 && !kondisiKacamata) {
    //     Swal.fire(
    //         "Peringatan",
    //         "Silakan pilih kondisi kacamata terlebih dahulu.",
    //         "warning"
    //     );
    //     return;
    // }

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
                    // foto_out: fotoOut,
                    // kondisi_kacamata_out: kondisiKacamata,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                    if (data.success) {
                        Swal.fire("Berhasil!", data.message, "success");
                        // resetFotoOut();
                        // $("#kondisiKacamataGroupOut").hide();
                        // $("#kondisiKacamataOut").val("");
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

// Mulai kamera untuk foto diri
async function startWebcamOut() {
    if (!isCameraSupported()) {
        alert("Kamera tidak didukung di browser ini.");
        return;
    }

    try {
        const streamOut = await navigator.mediaDevices.getUserMedia({
            video: { width: 400, height: 300, facingMode: "environment" },
        });

        if (videoOut) {
            videoOut.srcObject = streamOut;
            videoOut.style.display = "block";
        }

        toggleElements([
            { el: startCameraOut, show: false },
            { el: captureBtnOut, show: true },
            { el: retakeBtnOut, show: false },
            { el: saveBtnOut, show: false },
        ]);
    } catch (err) {
        alert("Gagal mengakses kamera: " + err.message);
        console.error(err);
    }
}

function captureImageOut() {
    if (!videoOut || !canvasOut) return;

    const contextOut = canvasOut.getContext("2d");
    canvasOut.width = videoOut.videoWidth;
    canvasOut.height = videoOut.videoHeight;
    contextOut.drawImage(videoOut, 0, 0);
    const dataURLOut = canvasOut.toDataURL("image/jpeg", 0.8);

    capturedImageOut.src = dataURLOut;
    capturedImageContainerOut.style.display = "block";

    toggleElements([
        { el: videoOut, show: false },
        { el: captureBtnOut, show: false },
        { el: retakeBtnOut, show: true },
        { el: saveBtnOut, show: true },
    ]);

    stopStreamOut();
}

// Ambil ulang foto KTP
function retakePhotoOut() {
    capturedImageOut.src = "";
    capturedImageContainerOut.style.display = "none";
    toggleElements([
        { el: videoOut, show: true },
        { el: captureBtnOut, show: true },
        { el: retakeBtnOut, show: false },
        { el: saveBtnOut, show: false },
    ]);
    startWebcamOut();
}

// Simpan foto KTP ke input hidden
function saveCaptureOut() {
    const inputField = document.getElementById("fotoOut");
    const previewImg = document.getElementById("fotoDiriOut");

    if (!canvasOut) {
        Swal.fire("Error", "Canvas tidak ditemukan.", "error");
        return;
    }

    const imgData = canvasOut.toDataURL("image/jpeg", 0.8);
    if (!imgData || imgData === "data:,") {
        Swal.fire(
            "Peringatan",
            "Silakan ambil foto terlebih dahulu.",
            "warning"
        );
        return;
    }

    inputField.value = imgData;
    inputField.dispatchEvent(new Event("change"));

    previewImg.innerHTML = "";
    const imgElement = document.createElement("img");
    imgElement.src = imgData;
    imgElement.className = "img-fluid rounded shadow-sm";
    previewImg.appendChild(imgElement);
    previewImg.style.display = "block";

    const modal = bootstrap.Modal.getInstance(
        document.getElementById("myModalOut")
    );
    if (modal) modal.hide();

    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Foto berhasil disimpan.",
        timer: 1500,
        showConfirmButton: false,
    });
}

// Reset preview foto
function resetPreviewImage() {
    $("#previewImg").hide().html("");
    $("#fotoOut").val("").trigger("change");
}

// Hapus foto KTP
window.removeKtpImage = function () {
    const previewImg = document.getElementById("fotoDiriOut");
    const inputField = document.getElementById("fotoOut");

    previewImg.innerHTML = "";
    previewImg.style.display = "none";
    inputField.value = "";
    alert("Foto KTP berhasil dihapus.");
};

// Toggle tampilan elemen
function toggleElements(elements = []) {
    elements.forEach(({ el, show }) => {
        if (el) el.style.display = show ? "inline-block" : "none";
    });
}

// Berhenti stream kamera
function stopStreamOut() {
    const stream = videoOut?.srcObject;
    if (stream && typeof stream.getTracks === "function") {
        stream.getTracks().forEach((track) => track.stop());
        videoOut.srcObject = null;
    }
}

function resetFotoOut() {
    $("#fotoOut").val("");

    $("#fotoDiriOut").html("").hide();

    $("#capturedImageOut").attr("src", "");
    $("#capturedImageContainerOut").hide();

    toggleElements([
        { el: startCameraOut, show: true },
        { el: captureBtnOut, show: false },
        { el: retakeBtnOut, show: false },
        { el: saveBtnOut, show: true },
        { el: videoOut, show: false },
    ]);

    stopStreamOut();
}

startCameraOut.addEventListener("click", startWebcamOut);
captureBtnOut.addEventListener("click", captureImageOut);
retakeBtnOut.addEventListener("click", retakePhotoOut);

document
    .getElementById("myModalOut")
    .addEventListener("shown.bs.modal", function () {
        startWebcamOut();
    });
