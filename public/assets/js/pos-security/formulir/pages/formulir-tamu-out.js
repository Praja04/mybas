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

let html5QrCode;
const qrRegionId = "qr-reader";
let isScannerRunning = false;
let visitorIsKacamata = "0"; 

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
        $("#kondisiKacamataGroupOut").hide();
        $("#kondisiKacamataOut").val("");
        resetFotoOut();

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
                if (!response.success || !response.data) {
                    console.error("Search visitor failed:", response);

                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: response.message || "Data visitor tidak valid.",
                    });

                    $("#visitorResult").hide();
                    return;
                }
                
                console.log(response.data);

                visitorIsKacamata = response.data.is_kacamata === 1 || response.data.is_kacamata === "1" ? "1" : "0";
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

                    // Pakai kacamata
                    let pakaiKacamata = "-";
                    if (data.is_kacamata == 0) {
                        pakaiKacamata = "Tidak";
                    } else if (data.is_kacamata == 1) {
                        pakaiKacamata = "Ya";
                    }

                    $("#visitorIsKacamata").text(pakaiKacamata);
                    $("#visitorKondisiKacamata").text(data.kondisi_kacamata || "-");

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

                    // Kondisi kacamata (OUT)
                    if (data.is_kacamata === 1 || data.is_kacamata === "1") {
                        $("#kondisiKacamataGroupOut").show();
                    } else {
                        $("#kondisiKacamataGroupOut").hide();
                        $("#kondisiKacamataOut").val("");
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
    const fotoOut = $("#fotoOut").val();

    const kondisiKacamata = $("#kondisiKacamataOut").val();
    
    console.log("returnCard trnvisitorid: ", trnvisitorid);

    if (!trnvisitorid) {
        Swal.fire("Error", "Visitor ID tidak ditemukan!", "error");
        return;
    }

    if (!fotoOut) {
        Swal.fire(
            "Peringatan",
            "Silakan ambil foto keluar terlebih dahulu.",
            "warning"
        );
        return;
    }

    if (visitorIsKacamata === "1" && !kondisiKacamata) {
        Swal.fire(
            "Peringatan",
            "Silakan pilih kondisi kacamata terlebih dahulu.",
            "warning"
        );
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
                    foto_out: fotoOut,
                    kondisi_kacamata_out: kondisiKacamata,
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                    if (data.success) {
                        Swal.fire("Berhasil!", data.message, "success");
                        resetFotoOut();
                        $("#kondisiKacamataGroupOut").hide();
                        $("#kondisiKacamataOut").val("");
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
