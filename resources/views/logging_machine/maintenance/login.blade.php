@extends('layouts.base-display')

@section('title', 'FORM LOGIN ENGINEERING SEASSSONING 2 ')

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
                            <span class="card-label font-weight-bolder text-dark">FORM LOGIN ENGINEERING</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Silahkan Scan ID CARD Anda</span>
                        </h3>
                        <div class="col-5 message hide">
                            <div class="alert alert-success"><i class="fa fa-check-circle text-white"></i> <span
                                    class="message-content">Message</span></div>
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
                            <div class="flex-grow-1">

                                <div class="d-flex align-items-center flex-wrap justify-content-between">
                                    <div class="d-flex flex-wrap align-items-center py-2">
                                        <div class="d-flex align-items-center mr-10">
                                            <div class="scan-kartu">
                                                <div class="row">
                                                    <div class="col-sm-12 mt-4">
                                                        <input type="number" class="form-control" id="scanner"
                                                            placeholder="Silahkan Scan" autofocus>

                                                        <input type="hidden" id="temp_rfid" name="temp_rfid">

                                                        <input style="width: 50px" type="text" class="nik" hidden>
                                                        <span id="nik" class="label label-inline label-light-info"
                                                            hidden></span>
                                                    </div>

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
                    <!--end::Advance Table Widget 4-->
                </div>
            </div>
            <!--end::Row-->
            <!--end::Dashboard-->
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ url('/assets/js/soundmanager2-nodebug-jsmin.js') }}"></script>
    <script src="{{ url('/assets/js/pages/features/miscellaneous/sweetalert2.js?v=7.2.8') }}"></script>

    
    <script type="text/javascript">

    if(sessionStorage.length != 0) {
        location.href = "{{ url('/logging_machine/maintenance') }}/" + sessionStorage.getItem('nik')
    }

        $(document).ready(function() {
            $('#picture').attr('src', '{{ asset('assets/media/users/blank.png') }}');
            $('#name').html('');
            $('#nik').html('');
            $('.nama').val('');
            $('.nik').val('');
            $('#scanner').keypress(function(e) {
                if (e.which == 13) {
                    var scanner_value = $('#scanner').val();
                    $('#scanner').val('');

                    if (scanner_value != $('#temp_rfid').val()) {
                        var data = {
                            'rfid': scanner_value
                        }
                        doScan(data);
                        $('#temp_rfid').val(scanner_value);
                    }
                }
            });
        });

        function failed(data) {
            $('#status').text(data.message);
            $('#tanggal-boleh-masuk').text(data.tanggal_boleh_masuk);
            $('#name').text(data.name);
            $('#nik').text(data.nik);
            $('#dept').text(data.dept);
            $('.progress-bar').removeClass('bg-success')
            $('.progress-bar').removeClass('bg-secondary')
            $('.progress-bar').addClass('bg-denger')
            if (data.image == 'default') {
                $('#picture').attr('src', '{{ asset('/assets/media/users/blank.png') }}');
            } else {
                $('#picture').attr('src', 'data:image/jpg;base64,' + data.image);
            }
        }

        function success(data) {
            $('#status').text(data.message);
            $('#name').text(data.name);
            $('#nik').text(data.nik);
            $('#temp_rfid').text(data.rfid);
            $('.nama').val(data.name);
            $('.nik').val(data.nik);
            $('.nik').val(data.nik);
            $('#picture').attr('src', 'data:image/jpg;base64,' + data.image);
            $('#submit-button').removeAttr('disabled');
        }

        function doScan(data) {
            // console.log( data );
            $.ajax({
                url: "{{ url('/engineering/scan_login') }}",
                data: data,
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success == 1) {
                        success(response.data);
                        for (var i = 0; i < response.data.auth_engineering.length; i++) {
                            var username = response.data.auth_engineering[i];

                            if (username.username == response.data.nik) 
                                {
                                        sessionStorage.setItem('nik', username.username);
                                        location.href = "{{ url('/logging_machine/maintenance') }}/" +
                                        username.username
                                        break;
                                }
                              }
                            if(username.username != response.data.nik)
                            {
                                 Swal.fire({
                                title: "GAGAL LOGIN",
                                text: "ID CARD TIDAK DI KENAL, KAMU BUKAN ENGINEERING!",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Oke",
                                customClass: {
                                confirmButton: "btn btn-primary"
                                }
                              });
                            }
                        }
                    },
            error: function(error) {
                console.log(error);
            }
        });
    };

        $('#scanner').focus();

    </script>
@endpush
