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
    let photoStore = {};
    let tempPhotos = [];

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

        tempPhotos.push(dataURL);

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
        if (!activePhotoKey || tempPhotos.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "Error!",
                text: "Gagal menyimpan foto.",
            });
            return;
        }

        photoStore[activePhotoKey].push(...tempPhotos);

        renderPhotoPreviewOut(activePhotoKey);
        updateHiddenInputOut(activePhotoKey);

        tempPhotos = [];

        const modal = bootstrap.Modal.getInstance(
            document.getElementById("myModalOut")
        );
        if (modal) modal.hide();

        Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "Foto berhasil disimpan",
            timer: 1200,
            showConfirmButton: false,
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
                                    style="width: 100%; min-height: 180px; background-color: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #dee2e6;">
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
                                    value="[]"
                                    ${requiredAttr}>
                            </div>
                        </div>
                    `;
                })
                .join("")
        );
    }

    document.addEventListener("DOMContentLoaded", function () {
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
            const rawLabel = $btn
                .closest(".foto-slot")
                .find("label")
                .first()
                .text()
                .trim();

            const labelText = rawLabel
                .replace(/\*/g, "")
                .replace(/Wajib/i, "")
                .replace(/Opsional/i, "")
                .trim();

            $("#myModalLabelOut").text(`Foto ${labelText || "Foto"}`);
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

    // slot handler
    $(document).on("click", ".open-camera", function () {
        activePhotoKey = $(this).data("key");
        // setActivePhotoKey($(this).data("key"));
        if (!photoStore[activePhotoKey]) {
            photoStore[activePhotoKey] = [];
        }

        tempPhotos = [];
    });

    $(document).on("click", ".remove-photo", function () {
        const key = $(this).data("key");

        $(`#preview-out-${key}`).html("");
        $(`#input-out-${key}`).val("");
    });

    window.setStepOut = function (step) {
        $("#step-table-out, #step-form-out").removeClass("active done");

        if (step === "table") {
            $("#step-table-out").addClass("active");
        }

        if (step === "form") {
            $("#step-table-out").addClass("done");
            $("#step-form-out").addClass("active");
        }
    };

    // default
    setStepOut("table");

    window.openFormOut = function (
        trncekid,
        nomor_polisi,
        nama_supir,
        company,
        muatan_type,
        truck_type,
        truck_type_other,
        checked_in_at
    ) {
        const checkedIn = new Date(checked_in_at);

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

        const target = document.getElementById("section-pemeriksaan-out");
        if (target) {
            target.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });

            $("#tableWrapperOut").hide();
            $("#headerTableOut").hide();

            $("#formWrapperOut").fadeIn();
            $("#headerFormOut").fadeIn();

            setStepOut("form");

            $("#cekKendaraanFormOut")[0].reset();
            $("#fotoSectionOut").html("");

            $("#trncekid").val(trncekid);

            $("#card-nopol-out").text(nomor_polisi);
            $("#card-nama-supir-out").text(nama_supir);
            $("#card-perusahaan-out").text(company);

            $("#card-waktu-masuk").text(`${formattedDate} (${durationText})`);
            $("#card-jenis-muatan").text(muatan_type);
            $("#card-jenis-truk").text(
                truck_type + (truck_type_other ? ` (${truck_type_other})` : "")
            );

            renderFotoSectionOut(truck_type);
        }
    };

    window.backToTableOut = function () {
        $("#formWrapperOut").hide();
        $("#headerFormOut").hide();

        $("#tableWrapperOut").fadeIn();
        $("#headerTableOut").fadeIn();

        setStepOut("table");
    };

    function renderPhotoPreviewOut(key) {
        const container = document.getElementById(`preview-out-${key}`);
        if (!container) return;

        container.innerHTML = "";

        photoStore[key].forEach((photo, index) => {
            const wrapper = document.createElement("div");
            wrapper.className = "position-relative";

            wrapper.innerHTML = `
                    <img src="${photo}" class="rounded shadow-sm" style="height:80px">
                    <button type="button"
                        class="btn btn-danger btn-sm position-absolute top-0 end-0"
                        onclick="removePhotoOut('${key}', ${index})">
                        <i class="mdi mdi-close"></i>
                    </button>
                `;

            container.appendChild(wrapper);
        });
    }

    window.removePhotoOut = function (key, index) {
        photoStore[key].splice(index, 1);
        renderPhotoPreviewOut(key);
        updateHiddenInputOut(key);
    };

    function updateHiddenInputOut(key) {
        const input = document.getElementById(`input-out-${key}`);
        if (input) {
            input.value = JSON.stringify(photoStore[key]);
            input.dispatchEvent(new Event("change"));
        }
    }
})();
