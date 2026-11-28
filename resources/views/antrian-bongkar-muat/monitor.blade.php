<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Antrian Bongkar Muat</title>
    <!-- CSS -->
    <link href="{{ url('/') }}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('/') }}/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <style>
        body {
            background: radial-gradient(circle at center, #1e1e2d 0%, #161621 100%);
            height: 100vh;
            overflow: hidden;
            color: white;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: rgba(27, 27, 40, 0.95);
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 4px solid #3699ff;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 10;
        }
        .main-content {
            flex: 1;
            display: flex;
            padding: 30px 40px;
            gap: 30px;
        }
        .category-column {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .category-header {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 800;
            color: #fff;
            background: rgba(54, 153, 255, 0.15);
            border: 2px solid #3699ff;
            padding: 15px;
            border-radius: 20px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .current-queue-box {
            flex: 1;
            background: linear-gradient(145deg, #1b1b28 0%, #212130 100%);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
            padding: 20px;
            min-height: 250px;
        }
        .current-queue-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(54, 153, 255, 0.05) 0%, transparent 70%);
        }
        .next-queue-box {
            background: rgba(27, 27, 40, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
        }
        .current-title {
            font-size: 1.5rem;
            color: #3699ff;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 800;
            margin-bottom: 10px;
            z-index: 1;
        }
        .current-number {
            font-size: 7rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1.2;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
            z-index: 1;
            transition: all 0.5s ease;
        }
        .blink {
            animation: blinker 1s linear infinite;
        }
        @keyframes blinker {
            50% { opacity: 0.3; transform: scale(1.05); }
        }
        .list-group-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            color: #e1e1ef;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 15px !important;
            text-align: center;
            transition: all 0.3s ease;
        }
        .list-group-item:first-child {
            background: rgba(54, 153, 255, 0.1);
            border-color: rgba(54, 153, 255, 0.2);
            color: #3699ff;
        }
        .marquee-container {
            background: #1b1b28;
            color: #fff;
            padding: 20px 0;
            font-size: 1.8rem;
            font-weight: 500;
            border-top: 4px solid #3699ff;
            overflow: hidden;
            z-index: 10;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="d-flex align-items-center">
            <img src="{{ url('/') }}/assets/media/logos/bas_logo.jpg" alt="Logo" style="height: 60px; border-radius: 8px;" class="mr-5">
            <h1 class="mb-0 font-weight-bolder text-white" style="font-size: 2.2rem;">PT BUMI ALAM SEGAR</h1>
        </div>
        <div class="text-right">
            <div id="clockDisplay" style="font-size: 2.5rem; font-weight: 800; color: #3699ff; font-family: 'Courier New', Courier, monospace;">
                00:00:00
            </div>
            <div class="text-muted font-weight-bold" style="font-size: 1.2rem; letter-spacing: 2px;">{{ date('d F Y') }}</div>
        </div>
    </div>

    <div class="main-content">
        <!-- Bongkar Muat Column -->
        <div class="category-column" id="col-bongkar_muat">
            <div class="category-header">BONGKAR MUAT</div>
            <div class="current-queue-box">
                <div class="current-title">SEDANG DILAYANI</div>
                <div class="current-number" id="current-bongkar_muat">---</div>
            </div>
            <div class="next-queue-box">
                <h3 class="text-center mb-5" style="color: #3699ff; font-size: 1.2rem; font-weight: 800; letter-spacing: 2px;">SELANJUTNYA</h3>
                <div id="next-bongkar_muat">
                    <div class="list-group-item">---</div>
                </div>
            </div>
        </div>

        <!-- Tamu Column -->
        <div class="category-column" id="col-tamu">
            <div class="category-header">TAMU</div>
            <div class="current-queue-box">
                <div class="current-title">SEDANG DILAYANI</div>
                <div class="current-number" id="current-tamu">---</div>
            </div>
            <div class="next-queue-box">
                <h3 class="text-center mb-5" style="color: #3699ff; font-size: 1.2rem; font-weight: 800; letter-spacing: 2px;">SELANJUTNYA</h3>
                <div id="next-tamu">
                    <div class="list-group-item">---</div>
                </div>
            </div>
        </div>

        <!-- TKBM Column -->
        <div class="category-column" id="col-tkbm">
            <div class="category-header">TKBM</div>
            <div class="current-queue-box">
                <div class="current-title">SEDANG DILAYANI</div>
                <div class="current-number" id="current-tkbm">---</div>
            </div>
            <div class="next-queue-box">
                <h3 class="text-center mb-5" style="color: #3699ff; font-size: 1.2rem; font-weight: 800; letter-spacing: 2px;">SELANJUTNYA</h3>
                <div id="next-tkbm">
                    <div class="list-group-item">---</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Running Text -->
    <div class="marquee-container">
        <marquee behavior="scroll" direction="left" scrollamount="8">
            SELAMAT DATANG DI PT BUMI ALAM SEGAR. PASTIKAN DOKUMEN ANDA LENGKAP SEBELUM MENUJU LOKET. TETAP PATUHI PROTOKOL KESEHATAN DAN KESELAMATAN KERJA (K3) DI LINGKUNGAN PABRIK. TERIMA KASIH.
        </marquee>
    </div>

    <!-- Audio untuk Ting-nong -->
    <audio id="bellSound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

    <script src="{{ url('/') }}/assets/plugins/global/plugins.bundle.js"></script>
    <script src="{{ url('/') }}/assets/js/scripts.bundle.js"></script>
    <script>
        // Jam Berjalan
        function renderTime() {
            var mydate = new Date();
            var h = mydate.getHours();
            var m = mydate.getMinutes();
            var s = mydate.getSeconds();
            h = h < 10 ? "0" + h : h;
            m = m < 10 ? "0" + m : m;
            s = s < 10 ? "0" + s : s;
            document.getElementById("clockDisplay").innerText = h + ":" + m + ":" + s;
            setTimeout(renderTime, 1000);
        }
        renderTime();

        var lastCalledTime = 'initial';

        function fetchQueueData() {
            $.ajax({
                url: '{{ route("antrian-bongkar-muat.monitor.data") }}',
                type: 'GET',
                cache: false,
                success: function(response) {
                    
                    if (response.categories) {
                        const categories = ['bongkar_muat', 'tamu', 'tkbm'];
                        
                        categories.forEach(kategori => {
                            const data = response.categories[kategori];
                            
                            // Update Current Called per category
                            if (data.current) {
                                $('#current-' + kategori).text(data.current.nomor_antrian);
                            } else {
                                $('#current-' + kategori).text('---');
                            }

                            // Update Next Queues per category
                            var listHtml = '';
                            if (data.next && data.next.length > 0) {
                                for(var i=0; i < data.next.length; i++) {
                                    listHtml += '<div class="list-group-item">' + data.next[i].nomor_antrian + '</div>';
                                }
                            } else {
                                listHtml = '<div class="list-group-item">---</div>';
                            }
                            $('#next-' + kategori).html(listHtml);
                        });
                    }

                    // Audio & Blink Logic based on the absolute latest call
                    if (response.latest_call) {
                        if (lastCalledTime === 'initial') {
                            // Inisialisasi awal, jangan bunyikan suara
                            lastCalledTime = response.latest_call.waktu_dipanggil;
                            console.log('Monitor initialized with last call:', lastCalledTime);
                        } else if (lastCalledTime !== response.latest_call.waktu_dipanggil) {
                            console.log('New call detected:', response.latest_call.nomor_antrian);
                            lastCalledTime = response.latest_call.waktu_dipanggil;
                            
                            playCallNotification(response.latest_call.nomor_antrian);
                            
                            // Animasi blink pada kategori yang dipanggil
                            var activeCategory = response.latest_call.kategori;
                            var $currentNumber = $('#current-' + activeCategory);
                            var $box = $currentNumber.parent();
                            
                            $box.addClass('blink');
                            setTimeout(function(){
                                $box.removeClass('blink');
                            }, 8000);
                        }
                    } else {
                        // Jika tidak ada panggilan sama sekali
                        lastCalledTime = null;
                    }
                }
            });
        }


        // Panggil tiap 3 detik
        setInterval(fetchQueueData, 3000);
        fetchQueueData();

        function playCallNotification(queueNumber) {
            var bell = document.getElementById('bellSound');
            
            // Play bell sound with error handling
            var playPromise = bell.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.log("Audio playback failed. Please click on the page to enable audio.", error);
                });
            }
            
            setTimeout(function() {
                console.log('Synthesizing voice for:', queueNumber);
                // Parsing BAS-001 menjadi "Nomor Antrian B A S Kosong Kosong Satu"
                var parts = queueNumber.split('-');
                var prefix = parts[0].split('').join(' ');
                var number = parts[1];
                
                // Ubah angka 0 menjadi "Kosong" agar lebih natural
                var spokenNumber = number.split('').map(function(char) {
                    return char === '0' ? 'Kosong' : char;
                }).join(' ');

                var textToSpeak = "Nomor Antrian. " + prefix + ". " + spokenNumber + ". Silakan menuju loket security.";
                
                var msg = new SpeechSynthesisUtterance();
                msg.text = textToSpeak;
                msg.lang = 'id-ID';
                msg.rate = 0.85;
                msg.pitch = 1.1;
                window.speechSynthesis.speak(msg);
            }, 1800);
        }

        // Interaksi awal untuk audio
        document.body.addEventListener('click', function() {
            var bell = document.getElementById('bellSound');
            bell.load();
            console.log('Audio system initialized.');
        }, { once: true });
    </script>
</body>
</html>
