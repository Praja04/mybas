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
    window.photoStore = {};
    let tempPhotos = [];
    window.photoSessionId = null; 

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

        tempPhotos.push(dataURL);

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

    async function saveCapture() {
        if (!activePhotoKey || tempPhotos.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "Error!",
                text: "Gagal menyimpan foto.",
            });
            return;
        }

        photoStore[activePhotoKey].push(...tempPhotos);
        await window.IDBDraft.saveDraft(collectDraftData());

        renderPhotoPreview(activePhotoKey);
        updateHiddenInput(activePhotoKey);

        tempPhotos = [];

        const modal = bootstrap.Modal.getInstance(
            document.getElementById("myModal")
        );
        modal.hide();

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
                tempPhotos = [];
                activePhotoKey = null;
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

        // ==============================
        // KONFIGURASI DROPDOWN JENIS TRUK
        // ==============================
        // ⚠️ PENTING:
        // - Object ini HANYA UNTUK DROPDOWN UI
        // - Label foto dan kewajiban foto TIDAK berasal dari sini
        // - Label foto berasal dari window.fotoConfig (foto-config.js)
        //
        // 🚫 JANGAN:
        // - Mengubah value (string) yang sudah ada
        //   karena value ini dipakai sebagai KEY ke fotoConfig
        //
        // ✅ BOLEH:
        // - Mengubah text (label tampilan dropdown)
        // - Menambah opsi BARU (tambahkan juga label fotonya di fotoConfig)        
        
        const options = {
            LIQUID: [
                {
                    value: "TRUK MUAT GULA CAIR",
                    text: "Truk Glukosa",
                },
                {
                    value: "FRUKTOSA",
                    text: "Truk Fruktosa",
                },
                {
                    value: "LAINNYA (LIQUID)",
                    text: "Lainnya",
                },
            ],
            NONLIQUID: [
                {
                    value: "TRUK RAW MATERIAL",
                    text: "Truk Raw Material (Bahan Mentah)",
                },
                {
                    value: "TRUK BONGKAR FINISH GOOD",
                    text: "Truk Bongkar Finish Good (WFG)",
                },
                {
                    value: "TRUK MUAT FINISH GOOD",
                    text: "Truk Bongkar Finish Good (WFG)",
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
            resetAlertFoto();
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
            resetAlertFoto();
            const value = $(this).val();
            const sections = fotoConfig[value] || [];

            renderAlertFoto(sections);

            $fotoSection.html(
                sections
                    .map((label) => {
                        const baseKey = label.replace(/\s+/g, "_").toLowerCase();
                        const key = `${photoSessionId}__${baseKey}`;

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

                                <div class="preview-container d-flex flex-wrap gap-2 justify-content-center mb-3" id="preview-${key}"
                                    style="width: 100%; min-height: 180px; background-color: #f8f9fa; padding: 10px; border-radius: 6px; border: 1px solid #dee2e6;">
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
                                    value="[]"
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

        if (!photoStore[activePhotoKey]) {
            photoStore[activePhotoKey] = [];
        }

        if (capturedImage) capturedImage.src = "";
        if (capturedImageContainer)
            capturedImageContainer.style.display = "none";

        if (canvas) {
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        tempPhotos = [];
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
        photoStore = {};
        tempPhotos = [];
        activePhotoKey = null;
        photoSessionId = trnvisitorid;
        
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

        setStep("form");
        resetAlertFoto();

        (async () => {
            const draft = await window.IDBDraft.getDraft(trnvisitorid);
            if (!draft) return;

            // restore input
            $("#nama_petugas").val(draft.nama_petugas);
            $("#muatanType").val(draft.muatan_type).trigger("change");

            setTimeout(() => {
                $("#truckType").val(draft.truck_type).trigger("change");

                photoStore = draft.photos || {};

                setTimeout(() => {
                    Object.keys(photoStore).forEach((key) => {
                        renderPhotoPreview(key);
                        updateHiddenInput(key);
                    });
                }, 100);

                $("#otherTruckType").val(draft.other_truck_type);
            }, 300);

            const lastSaved = draft.updatedAt
                ? formatTime(draft.updatedAt)
                : "waktu tidak diketahui";

            Swal.fire({
                icon: "info",
                title: "Draft ditemukan",
                html: `
                    <div>
                        Data pengecekan sebelumnya dipulihkan.<br>
                        <small class="text-muted">
                            Terakhir disimpan: <b>${lastSaved}</b>
                        </small>
                    </div>
                `,
                timer: 2500,
                showConfirmButton: false,
            });
        })();

        const target = document.getElementById("section-pemeriksaan");
        if (target) {
            target.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }
    };

    window.backToTable = function () {
        photoStore = {};
        tempPhotos = [];
        activePhotoKey = null;
        photoSessionId = null;

        $("#formWrapper").hide();
        $("#headerForm").hide();

        $("#tableWrapper").fadeIn();
        $("#headerTable").fadeIn();

        setStep("table");
    };

    function renderPhotoPreview(key) {
        const container = document.getElementById(`preview-${key}`);
        if (!container) return;

        container.innerHTML = "";

        photoStore[key].forEach((photo, index) => {
            const wrapper = document.createElement("div");
            wrapper.className = "position-relative";

            wrapper.innerHTML = `
            <img src="${photo}" class="rounded shadow-sm" style="height:80px">
            <button type="button"
                class="btn btn-danger btn-sm position-absolute top-0 end-0"
                onclick="removePhoto('${key}', ${index})">
                <i class="mdi mdi-close"></i>
            </button>
        `;

            container.appendChild(wrapper);
        });
    }

    window.removePhoto = async function (key, index) {
        photoStore[key].splice(index, 1);
        renderPhotoPreview(key);
        updateHiddenInput(key);

        if (photoSessionId) {
            await window.IDBDraft.saveDraft(collectDraftData());
        }
    };

    function updateHiddenInput(key) {
        const input = document.getElementById(`input-${key}`);
        if (input) {
            input.value = JSON.stringify(photoStore[key] || []);
            input.dispatchEvent(new Event("change"));
        }
    }

    function renderAlertFoto(sections) {
        const alertBox = document.getElementById("alertFotoWajib");
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

    function resetAlertFoto() {
        const alertBox = document.getElementById("alertFotoWajib");
        if (!alertBox) return;

        const ul = alertBox.querySelector("ul");
        ul.innerHTML = "";
        alertBox.classList.add("d-none");
    }

    function collectDraftData() {
        return {
            sessionId: photoSessionId, // trnvisitorid
            nomor_polisi: $("#nomor-polisi").val(),
            nama_supir: $("#nama-supir").val(),
            company: $("#company").val(),

            nama_petugas: $("#nama_petugas").val(),
            muatan_type: $("#muatanType").val(),
            truck_type: $("#truckType").val(),
            other_truck_type: $("#otherTruckType").val() || null,

            photos: structuredClone(photoStore),

            updatedAt: Date.now(), 
        };
    }

    let draftTimer;
    $("#cekKendaraanForm").on("input change", function () {
        if (!photoSessionId) return;

        clearTimeout(draftTimer);
        draftTimer = setTimeout(() => {
            window.IDBDraft.saveDraft(collectDraftData());
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
})();
