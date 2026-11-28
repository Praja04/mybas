<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Nomor Antrian - PT Bumi Alam Segar</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link href="{{ url('/') }}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        :root {
            --bg-color: #f0f2f5;
            --card-bg: #ffffff;
            --text-dark: #1e1e2d;
            --text-muted: #7e8299;
            --color-red: #8B0000;
            --color-grey: #6c757d;
            --color-green: #1B4D3E;
            --primary-shadow: 0 15px 50px rgba(0,0,0,0.06);
            --hover-shadow: 0 25px 60px rgba(0,0,0,0.1);
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Header Styles */
        .kiosk-header {
            background: white;
            padding: 1.5rem 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            margin-bottom: 3rem;
            width: 100%;
        }

        .header-logos {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .header-logos img {
            height: 60px;
            object-fit: contain;
        }

        .header-clock-section {
            text-align: right;
        }

        .header-clock {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin: 0;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .header-date {
            font-size: 1.1rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        /* Container & Cards */
        .kiosk-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 40px 60px;
        }

        .kiosk-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3rem;
            width: 100%;
            max-width: 1500px;
        }

        .kiosk-card {
            background: var(--card-bg);
            padding: 50px 40px;
            border-radius: 32px;
            box-shadow: var(--primary-shadow);
            text-align: center;
            border: 1px solid rgba(255,255,255,0.8);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .kiosk-card:hover {
            transform: translateY(-12px);
            box-shadow: var(--hover-shadow);
        }

        .card-icon-wrapper {
            width: 120px;
            height: 120px;
            background: #f8f9fa;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .kiosk-card:hover .card-icon-wrapper {
            background: white;
            transform: scale(1.1);
        }

        .card-icon-wrapper i {
            font-size: 4rem;
        }

        .category-title {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 2.5rem;
            line-height: 1.2;
        }

        .btn-ambil {
            width: 100%;
            padding: 24px;
            font-size: 1.4rem;
            font-weight: 800;
            border-radius: 20px;
            border: none;
            color: white;
            text-transform: uppercase;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            cursor: pointer;
        }

        .btn-ambil i {
            font-size: 1.8rem;
        }

        /* Color Variants */
        .card-bm .card-icon-wrapper i { color: var(--color-red); }
        .card-bm .btn-ambil { 
            background: linear-gradient(135deg, var(--color-red), #b22222);
            box-shadow: 0 10px 25px rgba(139, 0, 0, 0.3);
        }

        .card-tamu .card-icon-wrapper i { color: var(--color-grey); }
        .card-tamu .btn-ambil { 
            background: linear-gradient(135deg, var(--color-grey), #495057);
            box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
        }

        .card-tkbm .card-icon-wrapper i { color: var(--color-green); }
        .card-tkbm .btn-ambil { 
            background: linear-gradient(135deg, var(--color-green), #143a2f);
            box-shadow: 0 10px 25px rgba(27, 77, 62, 0.3);
        }

        .btn-ambil:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .btn-ambil:active {
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .kiosk-header { padding: 1.5rem 2rem; }
            .kiosk-container { gap: 2rem; }
        }

        @media (max-width: 992px) {
            .kiosk-container { grid-template-columns: 1fr; max-width: 500px; }
            .kiosk-header { flex-direction: column; gap: 1rem; text-align: center; }
            .header-clock-section { text-align: center; }
        }

        /* Print Configuration */
        @media screen {
            #print-area { display: none !important; }
        }

        @media print {
            @page { size: 58mm auto; margin: 0; }
            html, body { width: 58mm; margin: 0; padding: 0; background: #fff !important; }
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; color: #000 !important; }
            #print-area {
                position: absolute; left: 0; top: 0; width: 100%; text-align: center;
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 5mm 0;
            }
            #print-area h1 { font-size: 48px; margin: 10px 0; font-weight: 900; line-height: 1; }
            #print-area h2 { font-size: 20px; font-weight: 900; margin-bottom: 5px; }
            #print-area p { font-size: 14px; font-weight: 800; line-height: 1.4; }
            .print-divider { border-top: 2px dashed #000 !important; margin: 10px 0; }
        }
    </style>
</head>
<body>
    <header class="kiosk-header">
        <div class="header-logos">
            <img src="{{ url('/') }}/assets/media/logos/logo_bas_compress.png" alt="BAS Logo">
            <img src="{{ url('/') }}/assets/media/logos/Logo-Kecap-Sedaap.png" alt="Sedaap Logo">
        </div>
        <div class="header-clock-section">
            <div class="header-clock clock-display">00:00:00</div>
            <div class="header-date" id="header-date">Selasa, 05 Mei 2026</div>
        </div>
    </header>

    <main class="kiosk-main">
        <div class="kiosk-container">
            <!-- Card 1: Bongkar Muat -->
            <div class="kiosk-card card-bm">
                <div class="card-icon-wrapper">
                    <i class="flaticon-truck"></i>
                </div>
                <div class="category-title">Bongkar Muat</div>

                <button class="btn-ambil btn-generate" data-kategori="bongkar_muat">
                    <i class="flaticon2-print"></i>
                    <span>Ambil Antrian</span>
                </button>
            </div>

            <!-- Card 2: Tamu -->
            <div class="kiosk-card card-tamu">
                <div class="card-icon-wrapper">
                    <i class="flaticon-user"></i>
                </div>
                <div class="category-title">Tamu</div>

                <button class="btn-ambil btn-generate" data-kategori="tamu">
                    <i class="flaticon2-print"></i>
                    <span>Ambil Antrian</span>
                </button>
            </div>

            <!-- Card 3: TKBM -->
            <div class="kiosk-card card-tkbm">
                <div class="card-icon-wrapper">
                    <i class="flaticon-users"></i>
                </div>
                <div class="category-title">TKBM</div>

                <button class="btn-ambil btn-generate" data-kategori="tkbm">
                    <i class="flaticon2-print"></i>
                    <span>Ambil Antrian</span>
                </button>
            </div>
        </div>
    </main>

    <!-- Modal Kamera -->
    <div class="modal fade" id="cameraModal" tabindex="-1" role="dialog" aria-labelledby="cameraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: var(--card-bg); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px 30px;">
                    <h5 class="modal-title" id="cameraModalLabel" style="font-weight: 800; font-size: 1.5rem; color: var(--text-dark);">Ambil Foto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="stopCamera()">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body text-center" style="padding: 30px;">
                    <div style="border-radius: 15px; overflow: hidden; background: #000; position: relative; margin-bottom: 20px; box-shadow: inset 0 0 20px rgba(0,0,0,0.5);">
                        <video id="webcam" autoplay playsinline style="width: 100%; max-height: 480px; object-fit: cover;"></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                    </div>
                    <div class="alert alert-custom alert-light-primary mb-0" role="alert" style="border-radius: 12px;">
                        <div class="alert-icon"><i class="flaticon-information"></i></div>
                        <div class="alert-text font-weight-bold">Pastikan wajah Anda terlihat di layar, kemudian klik 'Capture Foto'.</div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: none; padding: 20px 30px 30px; justify-content: center;">
                    <button type="button" class="btn btn-light-danger font-weight-bold btn-lg" data-dismiss="modal" onclick="stopCamera()" style="border-radius: 12px; padding: 15px 30px;">Batal</button>
                    <button type="button" class="btn btn-primary font-weight-bold btn-lg" id="btn-capture" style="border-radius: 12px; padding: 15px 40px; font-size: 1.2rem; box-shadow: 0 10px 20px rgba(54, 153, 255, 0.3);">
                        <i class="flaticon2-camera mr-2"></i> Capture Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Print Preview -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: var(--card-bg); border-bottom: 1px solid rgba(0,0,0,0.05); padding: 20px;">
                    <h5 class="modal-title" id="previewModalLabel" style="font-weight: 800; font-size: 1.2rem; color: var(--text-dark); width: 100%; text-align: center;">Preview Tiket</h5>
                </div>
                <div class="modal-body text-center" style="padding: 30px; background: #fff;">
                    <!-- Simulasi Kertas Struk -->
                    <div style="border: 1px dashed #ccc; padding: 20px; font-family: monospace; color: #000;">
                        <h4 style="font-weight: bold; margin-bottom: 5px;">PT BUMI ALAM SEGAR</h4>
                        <p style="margin-bottom: 15px; font-size: 12px;" id="preview-category">Antrian ...</p>
                        <hr style="border-top: 1px dashed #000; margin: 10px 0;">
                        <h1 style="font-size: 2.5rem; font-weight: bold; margin: 10px 0;">***</h1>
                        <hr style="border-top: 1px dashed #000; margin: 10px 0;">
                        <p style="font-size: 10px; margin-top: 10px;">Silakan tunggu<br>nomor Anda dipanggil<br>oleh petugas Security</p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: none; padding: 20px; justify-content: space-between;">
                    <button type="button" class="btn btn-light-danger font-weight-bold" data-dismiss="modal" style="border-radius: 12px; padding: 12px 20px;">Batal</button>
                    <button type="button" class="btn btn-primary font-weight-bold" id="btn-print-preview" style="border-radius: 12px; padding: 12px 20px; box-shadow: 0 5px 15px rgba(54, 153, 255, 0.3);">
                        <i class="flaticon2-print mr-2"></i> Print Tiket
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ url('/') }}/assets/plugins/global/plugins.bundle.js"></script>
    <script src="{{ url('/') }}/assets/js/scripts.bundle.js"></script>
    <script>
        /**
         * Real-time Clock Function
         */
        function renderTime() {
            var mydate = new Date();
            var year = mydate.getFullYear();
            var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            var dayNames = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
            
            var day = mydate.getDate();
            var month = monthNames[mydate.getMonth()];
            var dayName = dayNames[mydate.getDay()];
            
            var h = mydate.getHours();
            var m = mydate.getMinutes();
            var s = mydate.getSeconds();
            
            h = h < 10 ? "0" + h : h;
            m = m < 10 ? "0" + m : m;
            s = s < 10 ? "0" + s : s;
            
            var timeStr = h + ":" + m + ":" + s;
            $('.clock-display').text(timeStr);
            $('#header-date').text(dayName + ", " + (day < 10 ? '0' + day : day) + " " + month + " " + year);
            
            setTimeout(renderTime, 1000);
        }
        renderTime();

        let currentKategori = '';
        let currentKategoriText = '';
        let stream = null;

        function startCamera() {
            const video = document.getElementById('webcam');
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: true }).then(function(s) {
                    stream = s;
                    video.srcObject = stream;
                    video.play();
                }).catch(function(err) {
                    console.log("Error accessing camera: " + err);
                    Swal.fire("Error", "Tidak dapat mengakses kamera. Pastikan kamera terhubung dan diizinkan pada browser.", "error");
                });
            } else {
                Swal.fire("Error", "Browser ini tidak mendukung akses kamera.", "error");
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        // Prevent closing modal by clicking outside
        $('#cameraModal').on('hide.bs.modal', function () {
            stopCamera();
        });

        $('.btn-generate').on('click', function() {
            var btn = $(this);
            currentKategori = btn.data('kategori');
            currentKategoriText = btn.siblings('.category-title').text();
            
            // Buka Modal
            $('#cameraModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            startCamera();
        });

        let currentImageData = '';

        $('#btn-capture').on('click', function() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');
            
            // Set canvas size sama dengan video
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            currentImageData = canvas.toDataURL('image/jpeg', 0.8);
            
            // Tutup kamera & tampilkan modal preview
            $('#cameraModal').modal('hide');
            stopCamera();
            
            $('#preview-category').text('Antrian ' + currentKategoriText);
            $('#previewModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $('#btn-print-preview').on('click', function() {
            var btnPrint = $(this);
            btnPrint.attr('disabled', true).addClass('spinner spinner-white spinner-right');

            $.ajax({
                url: '{{ route("antrian-bongkar-muat.kiosk.generate") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    kategori: currentKategori,
                    foto: currentImageData
                },
                success: function(response) {
                    if(response.success) {
                        $('#previewModal').modal('hide');
                        btnPrint.attr('disabled', false).removeClass('spinner spinner-white spinner-right');
                        
                        if(response.print_warning) {
                            Swal.fire({
                                title: "Peringatan Printer",
                                html: "Nomor antrian <b>" + response.data.nomor_antrian + "</b> berhasil disimpan, tetapi gagal mencetak struk.<br><small>" + response.print_warning + "</small>",
                                icon: "warning",
                                confirmButtonText: "Ok, mengerti",
                                customClass: {
                                    confirmButton: "btn font-weight-bold btn-primary"
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Berhasil!",
                                html: "Nomor antrian <b>" + response.data.nomor_antrian + "</b> berhasil dicetak.<br>Silakan ambil struk Anda.",
                                icon: "success",
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    }
                },
                error: function() {
                    Swal.fire({
                        text: "Gagal mencetak nomor antrian. Silakan coba lagi.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, mengerti",
                        customClass: {
                            confirmButton: "btn font-weight-bold btn-light"
                        }
                    });
                    btnPrint.attr('disabled', false).removeClass('spinner spinner-white spinner-right');
                }
            });
        });
    </script>
</body>
</html>
