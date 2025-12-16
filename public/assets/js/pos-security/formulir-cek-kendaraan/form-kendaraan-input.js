const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const captureBtn = document.getElementById("captureBtn");
const retakeBtn = document.getElementById("retakeBtn");
const saveBtn = document.getElementById("saveBtn");
const startCamera = document.getElementById("startCamera");
const capturedImage = document.getElementById("capturedImage");
const capturedImageContainer = document.getElementById(
    "capturedImageContainer"
);

let activePhotoKey = null;

function toggleElements(elements = []) {
    elements.forEach(({ el, show }) => {
        if (el) el.style.display = show ? "inline-block" : "none";
    });
}

async function startWebcam(options = {}) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 400 },
                height: { ideal: 300 },
                facingMode: "environment",
                ...options,
            },
        });

        if (video) {
            video.srcObject = stream;
            video.style.display = "block";
        }

        toggleElements([
            { el: startCamera, show: false },
            { el: captureBtn, show: true },
            { el: retakeBtn, show: false },
            { el: saveBtn, show: false },
        ]);
    } catch (err) {
        alert("Gagal mengakses kamera: " + err.message);
        console.error(err);
    }
}

function captureImage() {
    if (!video || !canvas) return;

    const context = canvas.getContext("2d");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);

    const dataURL = canvas.toDataURL("image/jpeg", 0.8);

    if (capturedImage) capturedImage.src = dataURL;
    if (capturedImageContainer) capturedImageContainer.style.display = "block";

    toggleElements([
        { el: video, show: false },
        { el: captureBtn, show: false },
        { el: retakeBtn, show: true },
        { el: saveBtn, show: true },
    ]);

    stopStream();
}

function retakePhoto() {
    if (capturedImageContainer) capturedImageContainer.style.display = "none";

    toggleElements([
        { el: video, show: true },
        { el: captureBtn, show: true },
        { el: retakeBtn, show: false },
        { el: saveBtn, show: false },
    ]);

    startCamera.click();
}

function stopStream() {
    const stream = video?.srcObject;

    if (stream && typeof stream.getTracks === "function") {
        stream.getTracks().forEach((track) => track.stop());
        video.srcObject = null;
    }
}

function saveCapture() {
    if (!activePhotoKey) {
        Swal.fire({
            icon: "error",
            title: "Error!",
            text: "Slot foto tidak ditemukan",
        });
        return;
    }

    const imgData = canvas.toDataURL("image/jpeg", 0.8);

    const input = document.getElementById(`input-${activePhotoKey}`);
    input.value = imgData;

    const previewBox = document.getElementById(`preview-${activePhotoKey}`);

    previewBox.innerHTML = `
        <div class="position-relative">
            <img src="${imgData}" class="img-fluid rounded shadow" style="max-height:160px">
            <button type="button"
                class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-photo"
                data-key="${activePhotoKey}">
                <i class="mdi mdi-close"></i>
            </button>
        </div>
    `;

    const modal = bootstrap.Modal.getInstance(
        document.getElementById("myModal")
    );
    modal.hide();

    Swal.fire({
        icon: "success",
        title: "Berhasil!",
        text: "Foto berhasil disimpan",
    });
}

function resetCameraModal() {
    stopStream();

    if (capturedImage) capturedImage.src = "";
    if (capturedImageContainer) capturedImageContainer.style.display = "none";

    toggleElements([
        { el: startCamera, show: true },
        { el: captureBtn, show: false },
        { el: retakeBtn, show: false },
        { el: saveBtn, show: false },
    ]);

    if (video) video.style.display = "none";

    if (canvas) {
        const ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // autofocus ketika baru dibuka
    const nopolInput = document.getElementById("nopol-search");
    if (nopolInput) nopolInput.focus();

    const modalElement = document.getElementById("myModal");
    if (modalElement) {
        modalElement.addEventListener("shown.bs.modal", () => {
            resetCameraModal();
            startWebcam();
        });

        modalElement.addEventListener("hidden.bs.modal", () => {
            resetCameraModal();
        });
    }

    if (startCamera) startCamera.addEventListener("click", startWebcam);
    if (captureBtn) captureBtn.addEventListener("click", captureImage);
    if (retakeBtn) retakeBtn.addEventListener("click", retakePhoto);
    if (saveBtn) saveBtn.addEventListener("click", saveCapture);
});

$(document).ready(function () {
    // label in modal
    $(document).on("click", '[data-bs-target="#myModal"]', function () {
        const $btn = $(this);
        const labelText =
            $btn.closest(".d-flex.flex-column").find("label").text().trim() ||
            "Foto";
        $("#myModalLabel").text(`Foto ${labelText}`);
    });

    const $muatan = $("#muatanType");
    const $truckContainer = $("#truckTypeContainer");
    const $truck = $("#truckType");
    const $fotoSection = $("#fotoSection");
    const $otherTruckContainer = $("#otherTruckContainer");
    const $otherTruckInput = $("#otherTruckType");

    const options = {
        LIQUID: [
            {
                value: "MUAT GULA CAIR",
                text: "Truk Muat Gula Cair",
            },
            {
                value: "LAINNYA LIQUID",
                text: "Lainnya",
            },
        ],
        NONLIQUID: [
            {
                value: "BONGKAR MATERIAL",
                text: "Truck Bongkar Material",
            },
            {
                value: "MUAT FINISH GOOD",
                text: "Truck Muat Finish Good (WFG)",
            },
            {
                value: "SPAREPART",
                text: "Mobil Sparepart/Bahan Bangunan",
            },
            {
                value: "MOBIL VENDOR",
                text: "Mobil Pribadi Vendor/Perusahaan",
            },
            {
                value: "MOBIL PENGANGKUT SAMPAH",
                text: "Mobil Pengangkut Sampah",
            },
            {
                value: "LAINNYA NONLIQUID",
                text: "Lainnya",
            },
        ],
    };

    $truckContainer.hide();
    $otherTruckContainer.hide();

    // render jenis truk setelah pilih jenis muatan
    $muatan.on("change", function () {
        const selected = $(this).val();

        // reset jenis truck
        $truck
            .empty()
            .append(
                '<option value="" disabled selected>-- Pilih Jenis Truk --</option>'
            );

        // reset foto
        $fotoSection.empty();

        $otherTruckContainer.slideUp();
        $otherTruckInput.prop("required", false);
        $otherTruckInput.val("");

        if (selected && options[selected]) {
            $truckContainer.slideDown();

            options[selected].forEach((opt) => {
                $truck.append(
                    `<option value="${opt.value}">${opt.text}</option>`
                );
            });
        } else {
            $truckContainer.slideUp();
        }
    });

    // render foto section berdasarkan jenis truk
    $truck.on("change", function () {
        const value = $(this).val();
        const sections = fotoConfig[value] || [];

        $fotoSection.html(
            sections
                .map((label) => {
                    const key = label.replace(/\s+/g, "_").toLowerCase();

                    // kecuali "temuan barang mencurigakan"
                    const isOptional = key.includes(
                        "temuan_barang_mencurigakan"
                    );
                    const requiredAttr = isOptional ? "" : "required";
                    const requiredMark = isOptional
                        ? ""
                        : '<span class="text-danger"> *</span>';
                    const badge = isOptional
                        ? '<span class="badge bg-secondary ms-1">Opsional</span>'
                        : '<span class="badge bg-danger ms-1">Wajib</span>';

                    return `
                        <div class="col-12 col-lg-4 mb-4">
                            <div class="foto-slot" data-key="${key}">
                                
                                <label class="form-label fw-semibold mb-2 text-center">
                                    ${label} ${requiredMark} ${badge}
                                </label>

                                <div class="preview-container d-flex flex-wrap gap-2 justify-content-center mb-3"
                                    id="preview-${key}"
                                    style="width: 100%; min-height: 180px; background:#f8f9fa; padding:10px; border-radius:6px; border:1px solid #dee2e6;">
                                </div>

                                <button type="button"
                                    class="btn btn-sm btn-primary w-100 open-camera"
                                    data-key="${key}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#myModal">
                                    Ambil Foto ${label}
                                </button>

                                <input type="hidden"
                                    name="photos[${key}]"
                                    id="input-${key}"
                                    ${requiredAttr}>
                            </div>
                        </div>
                    `;
                })
                .join("")
        );

        setTimeout(() => {
            scrollToFotoSection();
        }, 150);

        // render other truck type field
        if (value === "LAINNYA LIQUID" || value === "LAINNYA NONLIQUID") {
            $otherTruckContainer.slideDown();
            $otherTruckInput.prop("required", true);
        } else {
            $otherTruckContainer.slideUp();
            $otherTruckInput.prop("required", false);
            $otherTruckInput.val("");
        }
    });

    function scrollToFotoSection() {
        const fotoSection = document.getElementById("fotoSection");
        if (!fotoSection) return;

        fotoSection.scrollIntoView({
            behavior: "smooth",
            block: "start",
        });
    }
});

$("#nopol-search").on("keypress", function (e) {
    if (e.which === 13) {
        e.preventDefault();
        $("#searchVisitorData").click();
    }
});

// search data
$("#searchVisitorData").on("click", function () {
    const keyword = $("#nopol-search").val().trim();

    if (!keyword) {
        Swal.fire({
            icon: "error",
            title: "Oops!",
            text: "Nomor polisi wajib diisi",
        });
        return;
    }

    $.ajax({
        // todo: change to var
        // url: "{{ route('ajax.pos-security.cek-kendaraan.search') }}",
        url: "/search-kendaraan",
        type: "GET",
        data: {
            keyword: keyword,
        },
        beforeSend: function () {
            $("#searchVisitorData")
                .prop("disabled", true)
                .html('<i class="fas fa-spinner fa-spin me-2"></i>Mencari...');
        },
        success: function (res) {
            if (res.success) {
                const data = res.data;

                // set tanggal & jam otomatis saat data ditemukan
                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, "0");
                const dd = String(now.getDate()).padStart(2, "0");
                const hh = String(now.getHours()).padStart(2, "0");
                const min = String(now.getMinutes()).padStart(2, "0");

                // show form view
                $("#cekKendaraanForm").show();
                const target = document.getElementById("section-pemeriksaan");
                if (target) {
                    target.scrollIntoView({
                        behavior: "smooth",
                        block: "start",
                    });
                }

                // autofill
                $("#nama-supir").val(data.namavisitor).prop("disabled", false);
                $("#company").val(data.namacomp).prop("disabled", false);
                $("#nomor-polisi").val(data.nopol).prop("disabled", false);
                $("#createdby").val("system");
                $("#tgl_periksa").val(`${yyyy}-${mm}-${dd}`);
                $("#jam_periksa").val(`${hh}:${min}`);
                $("#trnvisitorid").val(data.trnvisitorid);
            } else {
                $("#cekKendaraanForm").hide();

                Swal.fire({
                    icon: "warning",
                    title: "Gagal",
                    text: res.message,
                });
            }
        },
        error: function (xhr) {
            let msg = "Terjadi kesalahan saat mencari data";

            if (xhr.status === 422 || xhr.status === 409) {
                msg = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: "error",
                title: "Oops!",
                text: msg,
            });

            $("#searchResult").hide();
        },
        complete: function () {
            $("#searchVisitorData")
                .prop("disabled", false)
                .html('<i class="mdi mdi-account-search"></i> Cari');
        },
    });
});

// slot handler
$(document).on("click", ".open-camera", function () {
    activePhotoKey = $(this).data("key");
});

$(document).on("click", ".remove-photo", function () {
    const key = $(this).data("key");

    $(`#preview-${key}`).html("");
    $(`#input-${key}`).val("");
});
