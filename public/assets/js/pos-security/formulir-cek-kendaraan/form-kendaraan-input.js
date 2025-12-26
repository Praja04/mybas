(() => {
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

    window.setActivePhotoKey = function (value) {
        activePhotoKey = value;
    };

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
        if (capturedImageContainer)
            capturedImageContainer.style.display = "block";

        toggleElements([
            { el: video, show: false },
            { el: captureBtn, show: false },
            { el: retakeBtn, show: true },
            { el: saveBtn, show: true },
        ]);

        stopStream();
    }

    function retakePhoto() {
        if (capturedImageContainer)
            capturedImageContainer.style.display = "none";

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

    window.resetCameraModal = function () {
        stopStream();

        if (capturedImage) capturedImage.src = "";
        if (capturedImageContainer)
            capturedImageContainer.style.display = "none";

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
    };

    document.addEventListener("DOMContentLoaded", function () {
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

            $("#myModalLabel").text(`Foto ${labelText || "Foto"}`);
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
                    value: "TRUK MUAT GULA CAIR",
                    text: "Truk Muat Gula Cair",
                },
                {
                    value: "LAINNYA (LIQUID)",
                    text: "Lainnya",
                },
            ],
            NONLIQUID: [
                {
                    value: "TRUK BONGKAR MATERIAL",
                    text: "Truck Bongkar Material",
                },
                {
                    value: "TRUK MUAT FINISH GOOD",
                    text: "Truck Muat Finish Good (WFG)",
                },
                {
                    value: "MOBIL SPAREPART",
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
                    value: "LAINNYA (NONLIQUID)",
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
            $otherTruckInput.val(null);
            $otherTruckInput.removeAttr("name");

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

            // auto-scroll
            // setTimeout(() => {
            //     scrollToFotoSection();
            // }, 150);

            // render other truck type field
            if (
                value === "LAINNYA (LIQUID)" ||
                value === "LAINNYA (NONLIQUID)"
            ) {
                $otherTruckContainer.slideDown();
                $otherTruckInput.prop("required", true);
                $otherTruckInput.attr("name", "otherTruckType");
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

    // slot handler
    $(document).on("click", ".open-camera", function () {
        // activePhotoKey = $(this).data("key");
        setActivePhotoKey($(this).data("key"));
    });

    $(document).on("click", ".remove-photo", function () {
        const key = $(this).data("key");

        $(`#preview-${key}`).html("");
        $(`#input-${key}`).val("");
    });

    window.setStep = function (step) {
        $("#step-table, #step-form").removeClass("active done");

        if (step === "table") {
            $("#step-table").addClass("active");
        }

        if (step === "form") {
            $("#step-table").addClass("done");
            $("#step-form").addClass("active");
        }
    };

    // default
    setStep("table");

    window.openMainForm = function (
        trnvisitorid,
        nomor_polisi,
        nama_supir,
        company
    ) {
        $("#tableWrapper").hide();
        $("#headerTable").hide();

        $("#formWrapper").fadeIn();
        $("#headerForm").fadeIn();

        $("#cekKendaraanForm")[0].reset();
        $("#fotoSection").html("");
        $("#truckTypeContainer").hide();
        $("#otherTruckContainer").hide();

        $("#trnvisitorid").val(trnvisitorid);
        $("#nomor-polisi").val(nomor_polisi);
        $("#nama-supir").val(nama_supir);
        $("#company").val(company);

        $("#card-nopol").text(nomor_polisi);
        $("#card-nama-supir").text(nama_supir);
        $("#card-perusahaan").text(company);

        const target = document.getElementById("section-pemeriksaan");
        if (target) {
            target.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }

        setStep("form");
    };

    window.backToTable = function () {
        $("#formWrapper").hide();
        $("#headerForm").hide();

        $("#tableWrapper").fadeIn();
        $("#headerTable").fadeIn();

        setStep("table");
    };
})();
