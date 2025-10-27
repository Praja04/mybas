@extends('layouts.base')

@push('styles')
    <style type="text/css">
        .hide {
            display: none;
        }

        .shortcut-item:hover span.link {
            color: #bf0000 !important
        }

        .shortcut-item {
            display: inline-block;
            cursor: pointer;
            width: 50px;
            text-align: left;
            overflow: hidden;
            white-space: nowrap;
        }

        .shortcut-item-hover {
            width: auto;
        }

        .btn-show-all {
            transition-duration: 5s;
        }

        .btn-show-all:hover i {
            transform: scale(0.8)
        }

        #shortcuts-bank {
            height: 500px;
            width: 80%;
            right: 12px;
            bottom: 70px;
            background-color: rgba(255, 255, 255, 0.7);
            display: none;
        }

        .shortcuts-bank-container {
            width: 100%;
            max-height: 400px;
            overflow: auto;
            padding-top: 10px;
        }

        .shortcuts-bank-item img {
            transition-duration: 0.2s;
            margin: 0 auto
        }

        .shortcuts-bank-item span {
            color: #666 !important
        }

        .shortcuts-bank-item:hover img {
            transform: scale(0.8);
        }

        .shortcuts-bank-item:hover span {
            color: #bf0000 !important;
            text-decoration: underline
        }

        /* width */
        ::-webkit-scrollbar {
            width: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex flex-column flex-root">
        <div style="background-image: url('{{ asset('/') }}assets/media/bg/bg-9.jpg'); margin-top: -50px; margin-bottom: -30px"
            class="login login-4 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid" id="kt_login">
            <!--begin::Aside-->
            <div class="col-md-12 login-aside d-flex flex-column flex-row-auto">
                <!--begin::Aside Top-->
                <div class="login-form text-center p-7 position-relative overflow-hidden bgi-no-repeat"
                    style="background-image: url('{{ asset('/') }}assets/media/bg/bg-3.jpg'); height: 400px; margin: 70px auto;border-radius: 10px">
                    <!--begin::Login Sign in form-->
                    <div class="login-signin">
                        <div class="mb-20">
                            <h3>My BAS Online</h3>
                            <div class="text-muted font-weight-bold">Silahkan login untuk menggunakan aplikasi ini</div>
                        </div>
                        <div id="error" class="alert alert-danger hide"><strong>Gagal! </strong>Tidak dikenali</div>
                        <form action="{{ URL::to('/') }}" class="form" id="login-form">
                            @csrf
                            <div class="form-group mb-5 nik">
                                <input class="form-control h-auto form-control-solid py-4 px-8" type="text"
                                    placeholder="NIK bagian belakang" name="nik" autocomplete="off" autofocus />
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block"></div>
                                </div>
                            </div>
                            <div class="form-group mb-5 password">
                                <input class="form-control h-auto form-control-solid py-4 px-8" type="password"
                                    placeholder="Password" name="password" />
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block"></div>
                                </div>
                            </div>
                            <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                                <div class="checkbox-inline">
                                    <label class="checkbox m-0 text-muted">
                                        <input type="checkbox" name="remember" />
                                        <span></span>Remember me</label>
                                </div>
                                <a href="javascript:" id="kt_login_forgot" class="text-muted text-hover-primary">Forget
                                    Password ?</a>
                            </div>
                            <button type="submit" id="submitButton"
                                class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Sign In</button>
                        </form>
                    </div>
                    <!--end::Login Sign in form-->
                </div>
                <!--end::Aside Top-->
                <!--begin::Aside Bottom-->
                <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        jQuery.fn.animateAuto = function(prop, speed, callback) {
            var elem, height, width;
            return this.each(function(i, el) {
                el = jQuery(el), elem = el.clone().css({
                    "height": "auto",
                    "width": "auto"
                }).appendTo("#shortcut-container");
                height = elem.css("height"),
                    width = elem.css("width"),
                    elem.remove();

                if (prop === "height")
                    el.animate({
                        "height": height
                    }, speed, callback);
                else if (prop === "width")
                    el.animate({
                        "width": width
                    }, speed, callback);
                else if (prop === "both")
                    el.animate({
                        "width": width,
                        "height": height
                    }, speed, callback);
            });
        }

        $('.shortcut-item').hover(function() {
            $('.shortcut-item').stop().animate({
                width: '50px'
            }, {
                duration: 300
            })
            $(this).stop().animateAuto('width', 300)
            // $('.shortcut-item').removeClass('shortcut-item-hover');
            // $(this).addClass('shortcut-item-hover');
            return false;
        })

        function toggleShortcutsBank() {
            $('#shortcuts-bank').fadeToggle();
            $('#shortcuts-search').focus();
        }

        function showError() {
            $('#error').slideDown();
            setTimeout(function() {
                $('#error').slideUp();
            }, 2000);
        }

        $("#login-form").submit(function(e) {

            e.preventDefault();
            $('.form-group').removeClass('has-danger');
            $('.fv-help-block').text('');
            $('input').removeClass('is-invalid');

            $('#submitButton').attr('disabled', 'true');
            $('#submitButton').addClass('spinner spinner-left pl-15')

            $.ajax({
                url: "{{ URL::to('/') }}/login",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success == 1) {
                        location.reload();
                    }
                },
                error: function(error) {
                    if (error.status == 200) {
                        location.reload();
                    } else if (error.status == 401) {
                        showError();
                        $('#submitButton').removeAttr('disabled');
                        $('#submitButton').removeClass('spinner spinner-left pl-15');
                    } else {
                        $('#submitButton').removeAttr('disabled');
                        $('#submitButton').removeClass('spinner spinner-left pl-15');
                        // console.log(error);
                        $.each(error.responseJSON.errors, function(key, val) {

                            $('.' + key).addClass('has-danger');
                            $('.' + key + ' input').addClass('is-invalid');

                            $.each(val, function(_key, error) {
                                $('.' + key + ' .fv-help-block').text(error + '\n');
                            })

                        });
                    }
                }
            });
        });
    </script>
@endpush
