@extends('layouts.base-display')


@section('title', 'DASHBOARD MAINTENANCE')

@section('content')

    <div class="container-fluid">

        <!--begin::Row-->
        <div class="row">


            
            <ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4">
                <!--begin::Item-->
                <li class="nav-item mb-2" id="kt_demo_panel_toggle" data-toggle="tooltip" title="Check out more demos"
                    data-placement="right">
                    <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="/">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </li>
                </li>
            </ul>
            

            <div class="col-lg-12">
                <!--begin::Advance Table Widget 4-->
                <div class="card card-custom card-stretch gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                        </h3>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-0 pb-3">


                        <div class="tab-content">
                            <div class="col-md-8">
                                
                                <div class="card mb-3">

                                    <div class="card-toolbar">
                                        <div class="float-right">
                                            <button onclick="logout()"
                                                class="btn btn-danger font-weight-bolder font-size-sm logout mt-2 mr-4" style="border-radius: 19px"><i
                                                    class="fa fa-sign-out-alt"> </i> Log Out
                                                </button>
                                            </div>
                                        </div>

                                    <div id="maintenance">

                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <!--begin::Tiles Widget 11-->
                                                    <div class="card card-custom bg-info gutter-b"
                                                        style="height: 100;  border-radius: 15px">
                                                        <div class="card-body">
                                                            <i class="fas fa-hard-hat fa-icon-white"
                                                                style="color: white; zoom: 180%">
                                                            </i>
                                                            @if ($notif_out_eng != 0)
                                                                <span class="badge badge-danger">{{ $notif_out_eng }}
                                                                </span>
                                                            @endif
                                                            <div
                                                                class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                            </div>
                                                            <a href="/logging_machine/all_list/{{ $nik }}"
                                                                class="text-inverse-primary font-weight-bold font-size-lg mt-1">LIST
                                                                OUTSTANDING DOWNTIME</a>
                                                        </div>
                                                    </div>
                                                    <!--end::Tiles Widget 11-->
                                                </div>
                                                <div class="col-sm-6">
                                                    <!--begin::Tiles Widget 11-->
                                                    <div class="card card-custom bg-dark gutter-b"
                                                        style="height: 100;  border-radius: 15px">
                                                        <div class="card-body">
                                                            <i class="fas fa-user-clock" style="color: white; zoom: 180%">
                                                            </i>
                                                            @if ($notif_eng != 0)
                                                                <span class="badge badge-danger">New <span
                                                                        class="count-new">{{ $notif_eng }}</span>
                                                                </span>
                                                            @endif
                                                            <div
                                                                class="text-inverse-primary font-weight-bolder font-size-h2 mt-3">
                                                            </div>
                                                            <a href="/logging_machine/permintaan_baru/{{ $nik }}"
                                                                class="text-inverse-primary font-weight-bold font-size-lg mt-1">PERMINTAAN
                                                                BARU DOWNTIME </a>
                                                        </div>
                                                    </div>
                                                    <!--end::Tiles Widget 11-->
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <!--end::Body-->
        </div>
        <!--end::Advance Table Widget 4-->
    </div>

    <div class="modal fade" id="alert_downtime" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">

            <!-- Modal content-->
            <div class="modal-content" style="background-color: rgb(255, 255, 255);">
                <div class="modal-header" style="background-color: red">
                    <h4 class="modal-title text-white" id="">PERMINTAAN BARU DOWNTIME </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="background-color: red">
                    <h2 class="text-white" id="blink">MESIN DOWNTIME</h2>
                </div>
                <div class="modal-footer">
                    <button type=" button" class="btn btn-primary btn-block" id="Lihat"><i class="fas fa-check"></i>
                        Lihat</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Row-->
    <!--end::Dashboard-->

@endsection
@push('scripts')
    <script src="{{ url('/assets/js/soundmanager2-nodebug-jsmin.js') }}"></script>


    <script type="text/javascript">

      function logout() {
          sessionStorage.clear();
          location.href = "{{ url('/engineering/scan') }}";
        }
        
          if(sessionStorage.length == 0 )
            {
                location.href = "{{ url('/engineering/scan') }}";
            }

        var kedip;

        document.getElementById("Lihat").onclick = function() {
            location.href = "/logging_machine/permintaan_baru/{{ $nik }}";
        };

        $(document).ready(function() {
            get_data_monitoring();
            setInterval(function() {
                get_data_monitoring()
            }, 60000);
        });

        $('#alert_downtime').each(function() {
            var elem = $(this);
            setInterval(function() {
                if (elem.css('visibility') == 'hidden') {
                    elem.css('visibility', 'visible');
                } else {
                    elem.css('visibility', 'hidden');
                }
            }, 500);
        });

        function get_data_monitoring() {
            // console.log( data );
            $.ajax({
                url: "{{ URL::to('/maintenance/get_data_monitoring/') }}",
                type: "get",
                dataType: "json",
                success: function(response) {
                    console.log(response.data);
                    if (response.status == 1) {
                        if (response.data.length != localStorage.getItem('downtime_count')) {
                            localStorage.setItem('downtime_count', response.data.length);
                            $(".count-new").text(response.data.length)
                        }
                        if (response.data.length != 0) {
                            $('#alert_downtime').modal('show');
                            play_sound("downtime");
                        }
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        };

        function play_sound(sound) {
            soundManager.onready(function() {
                soundManager.createSound({
                    // id: 'sk4Audio',
                    url: "{{ url('/assets/media/sounds') }}/" + sound + ".mp3",
                    autoLoad: true,
                    autoPlay: true,
                    volume: 100,
                })
            });
        }

    </script>
@endpush
