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
    window.photoStore = {};
    let tempPhotos = [];
    window.photoSessionId = null; 

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

    async function saveCaptureOut() {
        if (!activePhotoKey || tempPhotos.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "Error!",
                text: "Gagal menyimpan foto.",
            });
            return;
        }

        photoStore[activePhotoKey].push(...tempPhotos);
        await window.IDBDraft.saveDraft(collectDraftDataOut());

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

        renderAlertFoto(sections);

        $fotoSection.html(
            sections
                .map((label) => {
                    const baseKey = label.replace(/\s+/g, "_").toLowerCase();
                    const key = `${photoSessionId}__${baseKey}`;

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
                                    class="btn btn-sm btn-primary w-100 open-camera-out"
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
                tempPhotos = [];
                activePhotoKey = null;
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
    $(document).on("click", ".open-camera-out", function () {
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
        trnvisitorid,
        nomor_polisi,
        nama_supir,
        company,
        muatan_type,
        truck_type,
        truck_type_other,
        checked_in_at
    ) {
        photoStore = {};
        tempPhotos = [];
        activePhotoKey = null;
        photoSessionId = trnvisitorid;
        
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

            setStepOut("form");
            renderFotoSectionOut(truck_type);

            (async () => {
                const draft = await window.IDBDraft.getDraft(trnvisitorid);
                if (!draft) return;

                // restore nama petugas
                $("#nama_petugas-out").val(draft.nama_petugas_out);

                // restore foto
                photoStore = draft.photos || {};
                
                // renderFotoSectionOut(truck_type);

                Object.keys(photoStore).forEach((key) => {
                    renderPhotoPreviewOut(key); 
                    updateHiddenInputOut(key);
                });

                const lastSaved = draft.updatedAt
                    ? formatTime(draft.updatedAt)
                    : "waktu tidak diketahui";

                Swal.fire({
                    icon: "info",
                    title: "Draft ditemukan",
                    html: `
                        <div>
                            Data pengecekan sebelumnya dipulihkan<br>
                            <small class="text-muted">
                                Terakhir disimpan: <b>${lastSaved}</b>
                            </small>
                        </div>
                    `,
                    timer: 2500,
                    showConfirmButton: false,
                });
            })();
        }
    };

    window.backToTableOut = function () {
        photoStore = {};
        tempPhotos = [];
        activePhotoKey = null;
        photoSessionId = trnvisitorid;

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

    window.removePhotoOut = async function (key, index) {
        photoStore[key].splice(index, 1);
        renderPhotoPreviewOut(key);
        updateHiddenInputOut(key);

        if (photoSessionId) {
            await window.IDBDraft.saveDraft(collectDraftDataOut());
        }

    };

    function updateHiddenInputOut(key) {
        const input = document.getElementById(`input-out-${key}`);
        if (input) {
            input.value = JSON.stringify(photoStore[key]);
            input.dispatchEvent(new Event("change"));
        }
    }

     function renderAlertFoto(sections) {
        const alertBox = document.getElementById("alertFotoWajibOut");
        const ul = alertBox.querySelector("ul");

        ul.innerHTML = "";

        sections.forEach((label) => {
            const li = document.createElement("li");
            
            if (/temuan barang mencurigakan/i.test(label)) {
                li.innerHTML = `${label} <em class="text-muted">(jika ada)</em>`;
            } else {
                li.textContent = label;
            }

            ul.appendChild(li);
        });

        if (ul.children.length > 0) {
            alertBox.classList.remove("d-none");
        } else {
            alertBox.classList.add("d-none");
        }
    }

    function collectDraftDataOut() {
        return {
            sessionId: photoSessionId,
            nama_petugas_out: $("#nama_petugas-out").val(),
            photos: structuredClone(photoStore),
            updatedAt: Date.now(),
        };
    }

    let draftTimer;
    $("#cekKendaraanFormOut").on("input change", function () {
        if (!photoSessionId) return;

        clearTimeout(draftTimer);
        draftTimer = setTimeout(() => {
            window.IDBDraft.saveDraft(collectDraftDataOut());
        }, 500);
    });

    function formatTime(ts) {
        const d = new Date(ts);
        return d.toLocaleString("id-ID", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    $(document).on("click", ".open-form-out", function () {
        const $btn = $(this);

        openFormOut(
            $btn.data("trncekid"),
            $btn.data("trnvisitorid"),
            $btn.data("nomor-polisi"),
            $btn.data("nama-supir"),
            $btn.data("company"),
            $btn.data("muatan-type"),
            $btn.data("truck-type"),
            $btn.data("truck-type-other"),
            $btn.data("checked-in-at")
        );
    });
})();
