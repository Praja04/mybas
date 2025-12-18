(() => {
    const videoOut = document.getElementById("videoOut");
    const canvasOut = document.getElementById("canvasOut");
    const captureBtn = document.getElementById("captureBtnOut");
    const retakeBtn = document.getElementById("retakeBtnOut");
    const saveBtn = document.getElementById("saveBtnOut");
    const startCamera = document.getElementById("startCameraOut");
    const capturedImageOut = document.getElementById("capturedImageOut");
    const capturedImageContainerOut = document.getElementById(
        "capturedImageContainerOut"
    );

    let activePhotoKey = null;

    // window.setActivePhotoKey = function (value) {
    //     activePhotoKey = value;
    // };

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

            if (videoOut) {
                videoOut.srcObject = stream;
                videoOut.style.display = "block";
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
        if (!videoOut || !canvasOut) return;

        const context = canvasOut.getContext("2d");
        canvasOut.width = videoOut.videoWidth;
        canvasOut.height = videoOut.videoHeight;
        context.drawImage(videoOut, 0, 0);

        const dataURL = canvasOut.toDataURL("image/jpeg", 0.8);

        if (capturedImageOut) capturedImageOut.src = dataURL;
        if (capturedImageContainerOut)
            capturedImageContainerOut.style.display = "block";

        toggleElements([
            { el: videoOut, show: false },
            { el: captureBtn, show: false },
            { el: retakeBtn, show: true },
            { el: saveBtn, show: true },
        ]);

        stopStream();
    }

    function retakePhoto() {
        if (capturedImageContainerOut)
            capturedImageContainerOut.style.display = "none";

        toggleElements([
            { el: videoOut, show: true },
            { el: captureBtn, show: true },
            { el: retakeBtn, show: false },
            { el: saveBtn, show: false },
        ]);

        startCamera.click();
    }

    function stopStream() {
        const stream = videoOut?.srcObject;

        if (stream && typeof stream.getTracks === "function") {
            stream.getTracks().forEach((track) => track.stop());
            videoOut.srcObject = null;
        }
    }

    function saveCaptureOut() {
        if (!activePhotoKey) {
            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Slot foto tidak ditemukan",
            });
            return;
        }

        const imgData = canvasOut.toDataURL("image/jpeg", 0.8);

        const input = document.getElementById(`input-out-${activePhotoKey}`);
        input.value = imgData;

        const previewBox = document.getElementById(
            `preview-out-${activePhotoKey}`
        );

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
            document.getElementById("myModalOut")
        );
        if (modal) modal.hide();

        Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "Foto berhasil disimpan",
        });
    }

    window.resetCameraModal = function () {
        stopStream();

        if (capturedImageOut) capturedImageOut.src = "";
        if (capturedImageContainerOut)
            capturedImageContainerOut.style.display = "none";

        toggleElements([
            { el: startCamera, show: true },
            { el: captureBtn, show: false },
            { el: retakeBtn, show: false },
            { el: saveBtn, show: false },
        ]);

        if (videoOut) videoOut.style.display = "none";

        if (canvasOut) {
            const ctx = canvasOut.getContext("2d");
            ctx.clearRect(0, 0, canvasOut.width, canvasOut.height);
        }
    };

    function renderFotoSectionOut(truckType) {
        const $fotoSection = $("#fotoSectionOut");
        const sections = fotoConfig[truckType] || [];

        $fotoSection.html(
            sections
                .map((label) => {
                    const key = label.replace(/\s+/g, "_").toLowerCase();

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
                            id="preview-out-${key}"
                            style="width: 100%; min-height: 180px; background:#f8f9fa; padding:10px; border-radius:6px; border:1px solid #dee2e6;">
                        </div>

                        <button type="button"
                            class="btn btn-sm btn-primary w-100 open-camera"
                            data-key="${key}"
                            data-bs-toggle="modal"
                            data-bs-target="#myModalOut">
                            Ambil Foto ${label}
                        </button>

                        <input type="hidden"
                            name="photos[${key}]"
                            id="input-out-${key}"
                            ${requiredAttr}>
                    </div>
                </div>
            `;
                })
                .join("")
        );
    }
    document.addEventListener("DOMContentLoaded", function () {
        // autofocus ketika baru dibuka
        const nopolInput = document.getElementById("nopol-search-out");
        if (nopolInput) nopolInput.focus();

        const modalElement = document.getElementById("myModalOut");
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
        if (saveBtn) saveBtn.addEventListener("click", saveCaptureOut);
    });

    $(document).ready(function () {
        // label in modal
        $(document).on("click", '[data-bs-target="#myModalOut"]', function () {
            const $btn = $(this);
            const labelText =
                $btn
                    .closest(".d-flex.flex-column")
                    .find("label")
                    .text()
                    .trim() || "Foto";
            $("#myModalLabelOut").text(`Foto ${labelText}`);
        });

        function scrollToFotoSection() {
            const fotoSection = document.getElementById("fotoSectionOut");
            if (!fotoSection) return;

            fotoSection.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }
    });

    $("#nopol-search-out").on("keypress", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $("#searchVisitorDataOut").click();
        }
    });

    // search data
    $("#searchVisitorDataOut").on("click", function () {
        const keyword = $("#nopol-search-out").val().trim();

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
            url: "/search-kendaraan-out",
            type: "GET",
            data: {
                keyword: keyword,
            },
            beforeSend: function () {
                $("#searchVisitorDataOut")
                    .prop("disabled", true)
                    .html('<i class="mdi mdi-loading"></i>Mencari...');
            },
            success: function (res) {
                if (res.success) {
                    const data = res.data;

                    const checkedIn = new Date(
                        data.cek_kendaraan.checked_in_at
                    );

                    // show form view
                    $("#cekKendaraanFormOut").show();

                    const target = document.getElementById(
                        "section-pemeriksaan-out"
                    );

                    if (target) {
                        target.scrollIntoView({
                            behavior: "smooth",
                            block: "start",
                        });
                    }

                    const formattedDate = new Intl.DateTimeFormat("id-ID", {
                        day: "2-digit",
                        month: "long",
                        year: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                        hour12: false,
                    })
                        .format(checkedIn)
                        .replace(",", "");

                    // hitung durasi
                    const now = new Date();
                    const diffMs = now - checkedIn;

                    const totalMinutes = Math.floor(diffMs / 60000);
                    const hours = Math.floor(totalMinutes / 60);
                    const minutes = totalMinutes % 60;

                    let durationText;
                    if (hours > 0) {
                        durationText = `${hours} jam ${minutes} menit lalu`;
                    } else {
                        durationText = `${minutes} menit lalu`;
                    }

                    $("#trncekid").val(data.cek_kendaraan.trncekid);

                    // card
                    $("#card-nama-supir-out").text(data.visitor.namavisitor);
                    $("#card-perusahaan-out").text(data.visitor.namacomp);
                    $("#card-nopol-out").text(data.visitor.nopol);
                    $("#card-waktu-masuk").text(
                        `${formattedDate} (${durationText})`
                    );
                    $("#card-jenis-muatan").text(
                        data.cek_kendaraan.muatan_type
                    );
                    $("#card-jenis-truk").text(
                        data.cek_kendaraan.truck_type +
                            (data.cek_kendaraan.truck_type_other
                                ? ` (${data.cek_kendaraan.truck_type_other})`
                                : "")
                    );

                    renderFotoSectionOut(data.cek_kendaraan.truck_type);
                } else {
                    $("#cekKendaraanFormOut").hide();

                    Swal.fire({
                        icon: "warning",
                        title: "Gagal",
                        text: res.message,
                    });
                }
            },
            error: function (xhr) {
                let message = "Terjadi kesalahan. Silakan coba lagi.";

                if (xhr.responseJSON?.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: message,
                });
            },
            complete: function () {
                $("#searchVisitorDataOut")
                    .prop("disabled", false)
                    .html('<i class="mdi mdi-account-search"></i> Cari');
            },
        });
    });

    // slot handler
    $(document).on("click", ".open-camera", function () {
        activePhotoKey = $(this).data("key");
        // setActivePhotoKey($(this).data("key"));
    });

    $(document).on("click", ".remove-photo", function () {
        const key = $(this).data("key");

        $(`#preview-out-${key}`).html("");
        $(`#input-out-${key}`).val("");
    });
})();
