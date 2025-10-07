@extends('layouts.base-display')

@section('title', 'DISPLAY KARYAWAN BELUM BOLEH MASUK')

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }
        .message {
            transition-duration: 0.7ms;
        }
    </style>
@endpush

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">

            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">PENGECEKAN SUHU</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Silahkan scan dan isi suhu</span>
                        </h3>
                        <div class="col-5 message hide">
                            <div class="alert alert-success"><i class="fa fa-check-circle text-white"></i> <span class="message-content">Message</span></div>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="d-flex ml-10">
                            <!--begin: Pic-->
                            <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                <div class="symbol symbol-120 symbol-lg-120">
                                    <img alt="Pic" id="picture" src="{{ asset('assets/media/users/blank.png') }}">
                                </div>
                            </div>
                            <!--end: Pic-->
                            <!--begin: Info-->
                            <div class="flex-grow-1">
                                <!--begin: Title-->
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="mt-5">
                                        <!--begin::Name-->
                                        <a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3">
                                            Nama :
                                            <input style="width: 50px" type="hidden" class="nama">
                                            <span id="name"></span>
                                            -
                                            <input style="width: 50px" type="hidden" class="nik">
                                            <span id="nik" class="label label-inline label-light-info"></span>
                                        </a>
                                        <!--end::Contacts-->
                                    </div>
                                </div>
                                <!--end: Title-->
                                <!--begin: Content-->
                                <div class="d-flex align-items-center flex-wrap justify-content-between">
                                    <div class="d-flex flex-wrap align-items-center py-2">
                                        <div class="d-flex align-items-center mr-10">
                                            <div class="scan-kartu">
                                                <i style="font-size: 70px" class="far fa-address-card"></i>
                                                <br>
                                                <span class="text-light-black">Silahkan scan kartu</span>
                                            </div>
                                            <div class="scan-suhu text-center hide">
                                                <i style="font-size: 70px" class="fas fa-temperature-low"></i>
                                                <br>
                                                <span class="text-light-black">Silahkan pilih suhu</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end: Content-->
                            </div>
                            <!--end: Info-->
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <input type="number" class="form-control" id="scanner" placeholder="Silahkan Scan">
                                <input type="hidden" id="temp_rfid">
                            </div>
                            <div class="col-4 form-inline">
                                <label>Suhu : </label>
                                <select style="width: 60px; border: none" name="suhu" id="suhu" class="form-control">
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                </select>
                                <span class="ml-1"></span>
                                <span style="margin-bottom: 10px; font-size: 25px; line-height: 0.5">,</span>
                                <span class="mr-2"></span>
                                <select style="width: 60px; border: none" name="suhu-koma" id="suhu-belakang-koma" class="form-control">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <button id="submit-button" onClick="submitSuhu()" class="btn btn-success"><i class="submit-button-icon fa fa-paper-plane"></i> Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Advance Table Widget 4-->
            </div>
        </div>
        <!--end::Row-->
        <!--end::Dashboard-->
    </div>

@endsection

@push('scripts')
    <script src="{{ url('/assets/js/soundmanager2-nodebug-jsmin.js') }}"></script>
    <script type="text/javascript">

        function submitSuhu()
        {
            $('#submit-button').attr('disabled', true);
            $('.submit-button-icon').removeClass('fa-paper-plane');
            $('.submit-button-icon').addClass('fa-spinner');
            $('.submit-button-icon').addClass('fa-spin');

            let nik = $('.nik').val();
            let nama = $('.nama').val();
            let suhu = $('#suhu').val();
            let suhu_belakang_koma = $('#suhu-belakang-koma').val();

            if(nik === '' || nama === '' || suhu === '' || suhu_belakang_koma === '')
            {
                // Kalo ada yang ga beres
                $('#scanner').focus();
            }
            // Submit cek suhu
            $.ajax({
                url: "{{ url('display/cek-suhu/submit') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    nik : nik,
                    nama : nama,
                    suhu : suhu,
                    suhu_belakang_koma : suhu_belakang_koma
                },
                success: function ( response ) {
                    if(response.success == 1) {
                        $('#picture').attr('src', '{{ asset('assets/media/users/blank.png') }}');
                        $('#name').html('');
                        $('#nik').html('');
                        $('.nama').val('');
                        $('.nik').val('');

                        // Kalu berhasil
                        $('.message-content').text(response.message);
                        $('.message').slideDown('hide');
                        setTimeout(function () {
                            $('.message').slideUp('hide');
                        }, 2000);
                        $('#scanner').focus();

                        $('.submit-button-icon').addClass('fa-paper-plane');
                        $('.submit-button-icon').removeClass('fa-spinner');
                        $('.submit-button-icon').removeClass('fa-spin');
                    }else{
                        // Kalau gagal
                        alert('Gagal, submit ulang');
                        $('#submit-button').removeAttr('disabled');
                        $('.submit-button-icon').addClass('fa-paper-plane');
                        $('.submit-button-icon').removeClass('fa-spinner');
                        $('.submit-button-icon').removeClass('fa-spin');
                    }
                }
            })

        }

        $(document).ready(function () {
            $('#picture').attr('src', '{{ asset('assets/media/users/blank.png') }}');
            $('#name').html('');
            $('#nik').html('');
            $('.nama').val('');
            $('.nik').val('');
            $('#scanner').keypress(function(e) {
                if(e.which == 13)
                {
                    var scanner_value = $('#scanner').val();
                    $('#scanner').val('');

                    if(scanner_value != $('#temp_rfid').val())
                    {
                        var data = {
                            'rfid' : scanner_value
                        }
                        doScan(data);
                        $('#temp_rfid').val(scanner_value);
                    }
                }
            });
        });

        function failed(data)
        {
            $('#status').text(data.message);
            $('#tanggal-boleh-masuk').text(data.tanggal_boleh_masuk);
            $('#name').text(data.name);
            $('#nik').text(data.nik);
            $('#dept').text(data.dept);
            $('.progress-bar').removeClass('bg-success')
            $('.progress-bar').removeClass('bg-secondary')
            $('.progress-bar').addClass('bg-denger')
            if(data.image == 'default') {
                $('#picture').attr('src', '{{ asset('/assets/media/users/blank.png') }}');
            }else{
                $('#picture').attr('src', 'data:image/jpg;base64,'+data.image);
            }
            play_sound('reject');
        }

        function success(data)
        {
            $('#status').text(data.message);
            $('#name').text(data.name);
            $('#nik').text(data.nik);
            $('.nama').val(data.name);
            $('.nik').val(data.nik);
            $('#picture').attr('src', 'data:image/jpg;base64,'+data.image);
            $('#submit-button').removeAttr('disabled');
            play_sound('welcome');
        }

        function play_sound(sound)
        {
            soundManager.onready(function() {
                soundManager.createSound({
                    // id: 'sk4Audio',
                    url: "{{ url('/assets/media/sounds') }}/"+sound+".mp3",
                    autoLoad: true,
                    autoPlay: true,
                    volume: 100,
                })
            });
        }

        function doScan(data)
        {
            // console.log( data );
            $.ajax({
                url: "{{ URL::to('/display/cek-suhu/scan') }}",
                data: data,
                type: "POST",
                dataType: "json",
                success : function ( response ) {
                    if (response.success == 1) {
                        $('.scan-kartu')
                            .css('display', 'block')
                            .slideUp('fast')
                            .animate(
                                { display: 'none' },
                                { queue: false, duration: 'slow' }
                            )
                        $('.scan-suhu')
                            .css('display', 'none')
                            .slideDown('slow')
                            .animate(
                                { display: 'block' },
                                { queue: false, duration: 'slow' }
                            )
                        success(response.data);
                    }else{
                        failed(response.data);
                    }
                },
                error : function ( error ) {
                    console.log( error );
                }
            })
        }

        $('#scanner').focus();
    </script>
@endpush
