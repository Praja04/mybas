@extends('layouts.base-display')

@section('title', 'DISPLAY KARYAWAN BELUM BOLEH MASUK')

@push('styles')
    <style type="text/css">
        .hide {
            visibility: hidden;
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
                            <span class="card-label font-weight-bolder text-dark">DISPLAY KBBM</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Scan untuk memastikan karywan sudah boleh masuk</span>
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="d-flex ml-10">
                            <!--begin: Pic-->
                            <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                <div class="symbol symbol-50 symbol-lg-120">
                                    <img alt="Pic" id="picture" src="{{ asset('assets/media/users/blank.png') }}">
                                </div>
                            </div>
                            <!--end: Pic-->
                            <!--begin: Info-->
                            <div class="flex-grow-1">
                                <!--begin: Title-->
                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="mr-3">
                                        <!--begin::Name-->
                                        <a href="#" class="d-flex align-items-center text-dark text-hover-primary font-size-h5 font-weight-bold mr-3"><span id="name"></span> - <span id="nik" class="label label-inline label-light-info"></span>
                                            <i class="icon-md ml-2"></i></a>
                                        <!--end::Name-->
                                        <!--begin::Contacts-->
                                        <div class="d-flex flex-wrap my-2">
                                            <a href="#" class="text-muted text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                                                <span class="svg-icon svg-icon-md svg-icon-gray-500 mr-1">
                                                    <!--begin::Svg Icon | path:assets/media/svg/icons/General/Lock.svg-->
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <mask fill="white">
                                                                <use xlink:href="#path-1"></use>
                                                            </mask>
                                                            <g></g>
                                                            <path d="M7,10 L7,8 C7,5.23857625 9.23857625,3 12,3 C14.7614237,3 17,5.23857625 17,8 L17,10 L18,10 C19.1045695,10 20,10.8954305 20,12 L20,18 C20,19.1045695 19.1045695,20 18,20 L6,20 C4.8954305,20 4,19.1045695 4,18 L4,12 C4,10.8954305 4.8954305,10 6,10 L7,10 Z M12,5 C10.3431458,5 9,6.34314575 9,8 L9,10 L15,10 L15,8 C15,6.34314575 13.6568542,5 12,5 Z" fill="#000000"></path>
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>
                                                <span id="dept"></span>
                                            </a>
                                        </div>
                                        <!--end::Contacts-->
                                    </div>
                                </div>
                                <!--end: Title-->
                                <!--begin: Content-->
                                <div class="d-flex align-items-center flex-wrap justify-content-between">
                                    <div class="d-flex flex-wrap align-items-center py-2">
                                        <div class="d-flex align-items-center mr-10">
                                            <div class="">
                                                <div class="font-weight-bold mb-2">Tanggal Boleh Masuk</div>
                                                <span class="btn btn-sm btn-text btn-light-danger text-uppercase font-weight-bold" id="tanggal-boleh-masuk"></span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 flex-shrink-0 w-150px w-xl-300px mt-4 mt-sm-0">
                                            <span id="status"></span>
                                            <div class="progress progress-xs mt-2 mb-2 hide">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end: Content-->
                            </div>
                            <!--end: Info-->
                        </div>
                        <img src="{{ asset('/assets/media/cartoon/security.jpg') }}" alt="Security" style="position: absolute;right: 20px;top:20px">
                    </div>
                    <!--end::Body-->
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="col-2">
                            <input type="text" class="form-control" id="scanner" placeholder="Silahkan Scan">
                            <input type="hidden" id="temp_rfid">
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

        $(document).ready(function () {
            $('#picture').attr('src', '{{ asset('assets/media/users/blank.png') }}');
            $('#name').html('');
            $('#nik').html('');
            $('#dept').html('');
            $('#tanggal-boleh-masuk').html('')
            $('.progress-bar').removeClass('bg-success')
            $('.progress-bar').removeClass('bg-denger')
            $('.progress-bar').addClass('bg-secondary')
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
            $('#tanggal-boleh-masuk').text(data.tanggal_boleh_masuk);
            $('#name').text(data.name);
            $('#nik').text(data.nik);
            $('#dept').text(data.dept);
            $('.progress-bar').removeClass('bg-denger')
            $('.progress-bar').removeClass('bg-secondary')
            $('.progress-bar').addClass('bg-success')
            $('#picture').attr('src', 'data:image/jpg;base64,'+data.image);
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
                url: "{{ URL::to('/display/kbbm/scan') }}",
                data: data,
                type: "POST",
                dataType: "json",
                success : function ( response ) {
                    if (response.success == 1) {
                        success(response.data);
                    }else{
                        failed(response.data);
                    }
                    // else if(response[10] == 'do'){
                    // 	// Lakukan scan
                    // 	// doPost(data);
                    // }
                    // console.log( response[1] );
                },
                error : function ( error ) {
                    console.log( error );
                }
            })
        }

        function kedipProgressBar()
        {
            $('.progress').toggleClass('hide');
        }

        $('#scanner').focus();

        setInterval(function() {
            $('#scanner').focus();
        }, 1000);

        setInterval(function() {
            kedipProgressBar();
        }, 500);
    </script>
@endpush
