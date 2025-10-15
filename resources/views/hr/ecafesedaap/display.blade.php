@extends('layouts.base-display')

@push('styles')
    <style>
        .not-visible {
            opacity: 0 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="card card-custom gutter-b w-100">
            <div class="card-body">
                <h1 class="text-center"> Ecafesedaap ({{ ucfirst($kategori) }})</h1>

                <input type="hidden" id="kategori" value="{{ $kategori }}">

                <div class="row">
                    <div class="col-md-3">
                        <img id="image" alt="Pic" class="rounded w-100"
                            src="{{ asset('assets/media/images/no-image.jpg') }}">
                    </div>
                    <div class="col-md-6">
                        <div style="border-top: 10px solid #ffffff; width: 100%;" class="indikator-normal indikator"></div>
                        <div style="border-top: 10px solid #1BC5BD; width: 120%; display:none"
                            class="indikator-berhasil indikator"></div>
                        <div style="border-top: 10px solid #F64E60; width: 120%; display:none"
                            class="indikator-gagal indikator"></div>
                        <div class="form-group">
                            <label for="nik" style="font-weight: bold; font-size: 29px">NIK</label>
                            <input id="nik" type="text" class="form-control text-white"
                                style="font-size: 29px; background-color: #525461; font-weight: bold" placeholder="NIK">
                        </div>
                        <div class="form-group">
                            <label for="nama" style="font-weight: bold; font-size: 29px">NAMA</label>
                            <input id="name" type="text" class="form-control text-white"
                                style="font-size: 29px; background-color: #525461; font-weight: bold" placeholder="Nama">
                        </div>
                        <div class="form-group">
                            <label for="department" style="font-weight: bold; font-size: 29px">DEPARTMENT</label>
                            <input id="department" type="text" class="form-control text-white"
                                style="font-size: 29px; background-color: #525461; font-weight: bold"
                                placeholder="Department">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="alert alert-success indikator-berhasil indikator"
                            style="font-size: 20px; display: none">
                            <strong>Scan Berhasil</strong><br /><span class="message">Selamat menikmati</span>
                        </div>
                        <div class="alert alert-danger indikator-gagal indikator" style="font-size: 20px; display: none">
                            <strong>Scan Gagal</strong><br /><span class="message">Tidak bisa</span>
                        </div>
                        <div class="alert alert-danger indikator-bug indikator" style="font-size: 20px; display: none">
                            <strong>Scan Gagal</strong><br /><span class="message"></span>
                        </div>
                    </div>
                </div>
                <div class="separator separator-solid my-7"></div>
                <div class="row">
                    <div class="col-3">
                        <input id="scanner" autofocus type="text" class="form-control text-white"
                            placeholder="Scan here.." style="background-color: #525461">
                        <input type="hidden" id="temp_rfid">
                    </div>
                    <div class="col-5">
                        <span id="loading" style="font-size: 26px; background-color: #eee; font-weight: bold"
                            class="rounded px-2">
                            <i class="fas fa-spinner fa-spin text-dark-75" style="font-size: 26px"></i>
                            LOADING.. MOHON TUNGGU
                        </span>
                    </div>
                    <div class="col-4">
                        <div class="col bg-light-success px-6 py-3 rounded-xl text-center">
                            <div class="fas text-dark-75" style="font-size: 30px">SISA PORSI = <span
                                    id="sisa-porsi">0</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        setTimeout(function() {
            $('#loading').hide()
        }, 2000);
        var number = 0;
        setInterval(() => {
            if (number % 2 == 0) {
                $('.indikator').addClass('not-visible')
            } else {
                $('.indikator').removeClass('not-visible')
            }
            number = number + 1
        }, 1000);

        var data = {
            // bug selalu berkurang -1
            'rfid': '989989',
            'kategori': $('#kategori').val(),
        }

        // console.log("inital render: ", data);
        // count sisa porsi
        doScan(data);

        setTimeout(function() {
            $('.indikator-gagal').hide()
        }, 1000)

        $('#scanner').keypress(function(e) {
            if (e.which == 13) {
                var scanner_value = $('#scanner').val();
                $('#scanner').val('');

                if (scanner_value != $('#temp_rfid').val()) {
                    var data = {
                        'rfid': scanner_value,
                        'kategori': $('#kategori').val(),
                    }

                    // console.log("send data to controller: ", data);
                    doScan(data);
                    $('#temp_rfid').val(scanner_value);
                }
            }
        });

        function playSound(sound) {
            var src = "{{ asset('assets/media/sounds') }}/" + sound;
            var sound = new Audio();
            sound.src = src;
            sound.play();
            sound.onended = function() {
                sound = null;
            }
        }

        var timeOut = '';

        function doScan(data) {
            $('#loading').show()
            // console.log( data );

            clearTimeout(timeOut);
            $.ajax({
                url: "{{ url('/ecafesedaap-scan/do-scan') }}",
                data: data,
                type: "POST",
                dataType: "JSON",
                success: function(response) {
                    if (response.success == 1) {
                        success(response.data, response.message)

                        timeOut = setTimeout(function() {
                            $('.indikator-berhasil').hide()

                            $('#image').attr('src', 'data:image/jpg;base64,..');
                            $('#name').val('');
                            $('#nik').val('');
                            $('#department').val('');

                            $('#temp_rfid').val('')
                        }, 10000);
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 1000);
                    } else {
                        failed(response.data, response.message)
                    }
                    $('#loading').hide()
                },
                error: function(error) {
                    // console.error(error.responseJSON.message);
                    // warning()
                    $('#temp_rfid').val('');
                    $('#loading').hide()
                    $('.indikator-bug').show()
                    $('.message').text('Ada kesalahan, silahkan coba lagi')
                }
            })
        }

        function success(data, message) {
            playSound('ecafesedaap-scan-success.mp3')
            $('.indikator-berhasil').show()
            $('.indikator-normal').hide()
            $('.indikator-gagal').hide()
            $('.message').html(message)

            $('#image').attr('src', 'data:image/jpg;base64,' + data.image);
            $('#name').val(data.name);
            $('#nik').val(data.nik);
            $('#department').val(data.department);

            $('#sisa-porsi').text(data.sisa_porsi)
        }

        function failed(data, message) {
            playSound('ecafesedaap-scan-failed.mp3')
            $('.indikator-berhasil').hide()
            $('.indikator-normal').hide()
            $('.indikator-gagal').show()
            $('.message').html(message)

            if (data == null) {
                $('#image').attr('src', "{{ asset('assets/media/images/no-image.jpg') }}");
                $('#name').val('');
                $('#nik').val('');
                $('#department').val('');
                $('#sisa-porsi').text('');
                return;
            }

            $('#image').attr('src', 'data:image/jpg;base64,' + data.image);
            $('#name').val(data.name);
            $('#nik').val(data.nik);
            $('#department').val(data.department);
            $('#sisa-porsi').text(data.sisa_porsi);
        }
    </script>
@endpush
