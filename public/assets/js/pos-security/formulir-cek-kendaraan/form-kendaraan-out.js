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
    const uploadGalleryBtnOut = document.getElementById("uploadGalleryBtnOut");
    const fileInputOut = document.getElementById("fileInputOut");

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
                { el: uploadGalleryBtnOut, show: true },
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
            { el: uploadGalleryBtnOut, show: false },
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
            { el: uploadGalleryBtnOut, show: true },
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

        if (fileInputOut) fileInputOut.value = "";

        toggleElements([
            { el: startCamera, show: true },
            { el: captureBtn, show: false },
            { el: retakeBtn, show: false },
            { el: uploadGalleryBtnOut, show: true },
            { el: saveBtn, show: false },
        ]);

        if (videoOut) videoOut.style.display = "none";

        if (canvasOut) {
            const ctx = canvasOut.getContext("2d");
            ctx.clearRect(0, 0, canvasOut.width, canvasOut.height);
        }
    };

    function handleGalleryUploadOut(e) {
        const files = e.target.files;
        if (!files || files.length === 0) return;

        stopStream();
        if (videoOut) videoOut.style.display = "none";

        let loadedCount = 0;
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const dataURL = event.target.result;
                tempPhotos.push(dataURL);

                if (capturedImageOut) capturedImageOut.src = dataURL;
                if (capturedImageContainerOut)
                    capturedImageContainerOut.style.display = "block";

                loadedCount++;
                if (loadedCount === files.length) {
                    toggleElements([
                        { el: videoOut, show: false },
                        { el: captureBtn, show: false },
                        { el: retakeBtn, show: true },
                        { el: uploadGalleryBtnOut, show: false },
                        { el: saveBtn, show: true },
                    ]);
                }
            };
            reader.readAsDataURL(files[i]);
        }
    }

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
        if (uploadGalleryBtnOut && fileInputOut) {
            uploadGalleryBtnOut.addEventListener("click", () => fileInputOut.click());
            fileInputOut.addEventListener("change", handleGalleryUploadOut);
        }
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
    // =====================================================
    // MANAJEMEN SLOT PARKIR DI CEK KENDARAAN KELUAR (OUT)
    // =====================================================
    function updateParkingCardDisplayOut(lokasi_parkir, slot_id, assignment_id) {
        $("#parking_slot_id-out").val(slot_id || "");
        $("#parking_assignment_id-out").val(assignment_id || "");

        const isParked = lokasi_parkir && lokasi_parkir.trim() !== "" && lokasi_parkir.trim() !== "-";

        if (isParked) {
            $("#card-lokasi-parkir-out").text(lokasi_parkir);
            $("#badge-parking-status-out")
                .removeClass("bg-soft-secondary text-muted")
                .addClass("bg-success text-white")
                .html('<i class="mdi mdi-check-circle me-1"></i>Sudah Parkir');

            $("#btnParkirkanTextOut").text("Ganti Slot");
            $("#btnParkirkanKendaraanOut")
                .removeClass("btn-primary")
                .addClass("btn-outline-primary");
            $("#btnReleaseParkirOut").show();
        } else {
            $("#card-lokasi-parkir-out").text("Belum Parkir / -");
            $("#badge-parking-status-out")
                .removeClass("bg-success text-white")
                .addClass("bg-soft-secondary text-muted")
                .html('<i class="mdi mdi-close-circle-outline me-1"></i>Belum Parkir');

            $("#btnParkirkanTextOut").text("Pilih Slot Parkir");
            $("#btnParkirkanKendaraanOut")
                .removeClass("btn-outline-primary")
                .addClass("btn-primary");
            $("#btnReleaseParkirOut").hide();
        }
    }

    window.openParkingSlotModalForCekKendaraanOut = function () {
        const nopol = $("#nomor-polisi-out").val();
        if (!nopol) {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    icon: "warning",
                    title: "Perhatian",
                    text: "Silakan pilih kendaraan terlebih dahulu.",
                });
            } else {
                alert("Silakan pilih kendaraan terlebih dahulu.");
            }
            return;
        }

        // Set hook callback saat tombol "Gunakan Slot Ini" ditekan di modal
        window.onParkingSlotConfirmed = function (selectedSlot) {
            assignParkingSlotToVehicleOut(selectedSlot);
        };

        if (typeof openParkingSlotModal === "function") {
            openParkingSlotModal();
            if (typeof refreshParkingSlotsData === "function") {
                refreshParkingSlotsData();
            }
        }
    };

    function assignParkingSlotToVehicleOut(selectedSlot) {
        const nopol = $("#nomor-polisi-out").val();
        const trnvisitorid = $("#trnvisitorid-out").val();
        const nama_driver = $("#nama-supir-out").val();
        const jenis_kendaraan = "SUPIR";

        if (!selectedSlot || !selectedSlot.id) return;

        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Menempatkan Kendaraan...",
                text: `Menugaskan ${nopol} ke Slot ${selectedSlot.code}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });
        }

        const targetAssignUrl = window.API_ASSIGN_PARKING || "/pos-security/master/kantong-parkir/assignment/assign";

        $.ajax({
            url: targetAssignUrl,
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content") || $('input[name="_token"]').val(),
                parking_slot_id: selectedSlot.id,
                no_polisi: nopol,
                trnvisitorid: trnvisitorid,
                nama_driver: nama_driver,
                jenis_kendaraan: jenis_kendaraan,
                catatan: `Penugasan dari Form Cek Kendaraan Keluar: ${trnvisitorid}`
            },
            success: function (res) {
                const modalEl = document.getElementById("modalParkingSlotPicker");
                if (modalEl) {
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                }

                const displayLokasi = `${selectedSlot.zoneName || "Zona"} - ${selectedSlot.code}`;
                updateParkingCardDisplayOut(displayLokasi, selectedSlot.id, res.data ? res.data.id : null);

                // Reload datatable di background jika ada
                if (window.cekKendaraanOutTable && typeof window.cekKendaraanOutTable.reload === "function") {
                    window.cekKendaraanOutTable.reload(null, false);
                } else if (window.cekKendaraanOutTable && window.cekKendaraanOutTable.ajax) {
                    window.cekKendaraanOutTable.ajax.reload(null, false);
                }
                if (window.cekKendaraanInTable && typeof window.cekKendaraanInTable.reload === "function") {
                    window.cekKendaraanInTable.reload(null, false);
                }

                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil Diparkirkan!",
                        text: `Kendaraan ${nopol} berhasil ditempatkan di Slot ${selectedSlot.code}.`,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || "Gagal menempatkan kendaraan ke slot parkir.";
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal Menempatkan Kendaraan",
                        text: msg,
                    });
                } else {
                    alert(msg);
                }
            }
        });
    }

    window.releaseParkingForCekKendaraanOut = function () {
        const nopol = $("#nomor-polisi-out").val();
        const trnvisitorid = $("#trnvisitorid-out").val();
        const assignmentId = $("#parking_assignment_id-out").val();

        if (!nopol) return;

        const executeRelease = () => {
            if (typeof Swal !== "undefined") {
                Swal.fire({
                    title: "Memproses...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            }

            const targetId = (assignmentId && assignmentId !== "") ? assignmentId : "by-vehicle";
            const targetUrl = window.API_RELEASE_PARKING
                ? window.API_RELEASE_PARKING.replace(':id', targetId)
                : `/pos-security/master/kantong-parkir/assignment/release/${targetId}`;

            $.ajax({
                url: targetUrl,
                method: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content") || $('input[name="_token"]').val(),
                    no_polisi: nopol,
                    trnvisitorid: trnvisitorid,
                },
                success: function (res) {
                    updateParkingCardDisplayOut("-", null, null);

                    if (window.cekKendaraanOutTable && typeof window.cekKendaraanOutTable.reload === "function") {
                        window.cekKendaraanOutTable.reload(null, false);
                    } else if (window.cekKendaraanOutTable && window.cekKendaraanOutTable.ajax) {
                        window.cekKendaraanOutTable.ajax.reload(null, false);
                    }
                    if (window.cekKendaraanInTable && typeof window.cekKendaraanInTable.reload === "function") {
                        window.cekKendaraanInTable.reload(null, false);
                    }

                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "success",
                            title: "Slot Parkir Dilepas!",
                            text: `Kendaraan ${nopol} telah dilepas dan slot parkir kembali kosong.`,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || "Gagal melepaskan slot parkir.";
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: msg,
                        });
                    } else {
                        alert(msg);
                    }
                }
            });
        };

        if (typeof Swal !== "undefined") {
            Swal.fire({
                title: "Lepas Slot Parkir?",
                text: `Kendaraan ${nopol} akan dilepas dari slot parkir saat ini.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Lepas Parkir",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    executeRelease();
                }
            });
        } else {
            if (confirm(`Lepas slot parkir untuk kendaraan ${nopol}?`)) {
                executeRelease();
            }
        }
    };

    window.openFormOut = function (
        trncekid,
        trnvisitorid,
        nomor_polisi,
        nama_supir,
        company,
        muatan_type,
        truck_type,
        truck_type_other,
        checked_in_at,
        lokasi_parkir = "-",
        area_tujuan = null,
        no_antrian = null,
        unloading_status = null,
        warehouse_status = null,
        finish_loading_time = null,
        parking_slot_id = null,
        parking_assignment_id = null
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
            $("#trnvisitorid-out").val(trnvisitorid);
            $("#nomor-polisi-out").val(nomor_polisi);
            $("#nama-supir-out").val(nama_supir);
            $("#company-out").val(company);

            $("#card-nopol-out").text(nomor_polisi);
            $("#card-nama-supir-out").text(nama_supir);
            $("#card-perusahaan-out").text(company);

            $("#card-waktu-masuk").text(`${formattedDate} (${durationText})`);
            $("#card-jenis-muatan").text(muatan_type);
            $("#card-jenis-truk").text(
                truck_type + (truck_type_other ? ` (${truck_type_other})` : "")
            );

            // Update display card lokasi parkir & tombol aksi parkir
            updateParkingCardDisplayOut(lokasi_parkir, parking_slot_id, parking_assignment_id);

            // Render data warehouse awal dari data attribute
            renderWarehouseInfoCardsOut(
                area_tujuan,
                no_antrian,
                unloading_status,
                null,
                null,
                warehouse_status,
                finish_loading_time
            );

            // Fetch status live terbaru dari API warehouse
            fetchLiveWarehouseStatusOut(nomor_polisi);

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
        photoSessionId = null;

        updateParkingCardDisplayOut("-", null, null);
        $("#card-area-warehouse-out").text("Memuat info...");
        $("#badge-target-area-code-out").hide();
        $("#card-antrian-warehouse-out").html('<span class="text-muted fs-13">Memuat antrian...</span>');
        $("#badge-unloading-status-out").hide();

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

    function renderWarehouseInfoCardsOut(areaTujuan, noAntrian, unloadingStatus, areaCode = null, queueTime = null, warehouseStatus = null, finishTime = null) {
        // Area Tujuan
        const areaText = areaTujuan && areaTujuan !== '-' ? areaTujuan : 'Belum Ditentukan';
        $("#card-area-warehouse-out").text(areaText);
        if (areaCode && areaCode !== '-') {
            $("#badge-target-area-code-out").text(areaCode).show();
        } else {
            $("#badge-target-area-code-out").hide();
        }

        const isCompleted = (unloadingStatus === 'completed' || warehouseStatus === 'timbangan_out');
        const isProcess = (unloadingStatus === 'process' || warehouseStatus === 'loading' || warehouseStatus === 'unloading');

        // Antrian
        let antrianHtml = '';
        if (isCompleted) {
            antrianHtml = `
                <span class="badge bg-success fs-13 px-2 py-1 me-1">
                    <i class="mdi mdi-check-circle-outline me-1"></i>Selesai Bongkar / Muat
                </span>
                <span class="badge bg-info text-white fs-11">
                    <i class="mdi mdi-scale-balance me-1"></i>Ke Timbangan Out
                </span>
                ${finishTime ? `<small class="text-muted ms-1 fs-11" title="Waktu selesai bongkar/muat">${finishTime}</small>` : ''}
            `;
            $("#badge-unloading-status-out").text("SELESAI").removeClass().addClass("badge bg-soft-success text-success fs-11").show();
        } else if (isProcess) {
            antrianHtml = `
                <span class="badge bg-primary fs-13 px-2 py-1 me-1">
                    <i class="mdi mdi-progress-clock me-1"></i>Sedang Bongkar / Muat
                </span>
                ${noAntrian ? `<span class="badge bg-soft-primary text-primary fs-11">No. ${noAntrian}</span>` : ''}
                ${queueTime ? `<small class="text-muted ms-1 fs-11" title="Waktu antri">${queueTime}</small>` : ''}
            `;
            $("#badge-unloading-status-out").text("PROSES").removeClass().addClass("badge bg-soft-primary text-primary fs-11").show();
        } else if (noAntrian) {
            let statusText = unloadingStatus ? unloadingStatus.toUpperCase() : 'ANTRI';
            antrianHtml = `
                <span class="badge bg-primary fs-13 px-2 py-1 me-1">
                    <i class="mdi mdi-ticket me-1"></i>No. ${noAntrian}
                </span>
                <span class="badge bg-soft-info text-info fs-11">${statusText}</span>
                ${queueTime ? `<small class="text-muted ms-1 fs-11" title="Waktu ambil antrian">${queueTime}</small>` : ''}
            `;
            $("#badge-unloading-status-out").text(statusText).removeClass().addClass("badge bg-soft-secondary text-dark fs-11").show();
        } else {
            antrianHtml = `
                <span class="badge bg-soft-warning text-warning border border-warning-subtle fs-12 px-2 py-1">
                    <i class="mdi mdi-clock-outline me-1"></i>Belum Antri
                </span>
            `;
            $("#badge-unloading-status-out").hide();
        }
        $("#card-antrian-warehouse-out").html(antrianHtml);
    }

    function fetchLiveWarehouseStatusOut(nopol) {
        if (!nopol) return;

        $.ajax({
            url: `/kendaraan/warehouse-status/${encodeURIComponent(nopol)}`,
            method: 'GET',
            success: function(res) {
                if (res.status === 'success' && res.data) {
                    const d = res.data;
                    const areaTujuan = (d.target_location && d.target_location.name)
                        ? d.target_location.name
                        : (d.target_area || '-');
                    const areaCode = (d.target_location && d.target_location.s_loc)
                        ? d.target_location.s_loc
                        : (d.target_area_code || '-');

                    renderWarehouseInfoCardsOut(
                        areaTujuan,
                        d.no_antrian,
                        d.unloading_status,
                        areaCode,
                        d.queue_taken_human,
                        d.status,
                        d.finish_loading_time
                    );
                } else if (res.status === 'success' && !res.found) {
                    $("#card-area-warehouse-out").text("Belum Terdaftar di Warehouse");
                    $("#badge-target-area-code-out").hide();
                    $("#card-antrian-warehouse-out").html(`
                        <span class="badge bg-soft-secondary text-muted fs-12">
                            <i class="mdi mdi-minus-circle-outline me-1"></i>Tidak Ada Antrian
                        </span>
                    `);
                    $("#badge-unloading-status-out").hide();
                }
            },
            error: function(err) {
                console.warn("Gagal mengambil live status warehouse out:", err);
            }
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
            $btn.data("checked-in-at"),
            $btn.data("lokasiParkir"),
            $btn.data("areaTujuan"),
            $btn.data("noAntrian"),
            $btn.data("unloadingStatus"),
            $btn.data("warehouseStatus"),
            $btn.data("finishLoadingTime"),
            $btn.data("parkingSlotId"),
            $btn.data("parkingAssignmentId")
        );
    });
})();
