@extends('bas.layout.master_login')

@push('styles')

<style type="text/css">

    /* ---- reset ---- */

body {
  margin: 0;
  font:normal 75% Arial, Helvetica, sans-serif;
}

canvas {
  display: block;
  vertical-align: bottom;
}

/* ---- particles.js container ---- */

#particles-js {
  position: absolute;
  width: 100%;
  height: 100%;
background-color: #a55c1b;
background-image: linear-gradient(315deg, #a55c1b 0%, #000000 74%);
  background-repeat: no-repeat;
  background-size: cover;
  background-position: 50% 50%;
}

/* ---- stats.js ---- */

.count-particles{
  background: #000022;
  position: absolute;
  top: 48px;
  left: 0;
  width: 80px;
  color: #13E8E9;
  font-size: .8em;
  text-align: left;
  text-indent: 4px;
  line-height: 14px;
  padding-bottom: 2px;
  font-family: Helvetica, Arial, sans-serif;
  font-weight: bold;
}

.js-count-particles{
  font-size: 1.1em;
}

.count-particles{
  border-radius: 0 0 3px 3px;
}

</style>

@endpush

@section('content')

    <div class="d-flex flex-column flex-root">
        <div style="margin-top: -50px; margin-bottom: -30px" class="login login-4 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid" id="kt_login">
            <div id="particles-js"></div>
            <div class="col-md-12 login-aside d-flex flex-column flex-row-auto">
                <div class="login-form text-center p-7 position-relative overflow-hidden bgi-no-repeat" style="background-image: url('{{ asset('/') }}assets/media/bg/bg-3.jpg'); height: 400px; margin: 70px auto;border-radius: 10px">
                    <div class="login-signin">
                        <div class="mb-20">
                            <h3>My BAS Online</h3>
                            <div class="text-muted font-weight-bold">Silahkan login untuk menggunakan aplikasi ini</div>
                        </div>
                        <div id="error" class="alert alert-danger hide"><strong>Gagal! </strong>Tidak dikenali</div>
                        <form action="{{ URL::to('/bas_login') }}" class="form" id="login-form">
                            <div class="form-group mb-5 nik">
                                <input class="form-control h-auto form-control-solid py-4 px-8" type="text" placeholder="Masukan NIK Bagian Belakang" name="nik" autocomplete="off" autofocus />
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block"></div>
                                </div>
                            </div>
                            <div class="form-group mb-5 password">
                                <input class="form-control h-auto form-control-solid py-4 px-8" type="password" placeholder="Password" name="password" />
                                <div class="fv-plugins-message-container">
                                    <div class="fv-help-block"></div>
                                </div>
                            </div>
                          
                            <button type="submit" id="submitButton" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">Sign In</button>
                        </form>
                    </div>
                </div>
                <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center"></div>
            </div>
        </div>
    </div>
    
@endsection

@push('scripts')

    <script type="text/javascript">

      $.ajaxSetup({
		    headers: {
		      'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
		    }
		  });

        function showError() {
            $('#error').slideDown();
            setTimeout(function () {
                $('#error').slideUp();
            }, 2000);
        }

        $("#login-form").submit( function (e) {

            e.preventDefault();
            $('.form-group').removeClass('has-danger');
            $('.fv-help-block').text('');
            $('input').removeClass('is-invalid');

            $('#submitButton').attr('disabled', 'true');
            $('#submitButton').addClass('spinner spinner-left pl-15')

            $.ajax({
                url: "{{ URL::to('/bas_login') }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success == 1)
                     {
                        //  console.log(reponse.data.auth_group_id);
                        if(response.parsing.auth_group_id == 57 )
                        {
                            window.location.href = "bas_logger/operator/index";
                        }
                        else if(response.parsing.auth_group_id == 60 )
                        {
                            window.location.href = "bas_logger/analis/index";
                        }
                        else if(response.parsing.auth_group_id == 58 )
                        {
                            window.location.href = "bas_logger/qc/index";
                        }
                        else if(response.parsing.auth_group_id == 59 )
                        {
                            window.location.href = "bas_logger/spv/index";
                        }
                    }
                },
                error: function(error) {
                    if(error.status == 200) {
                        location.reload();
                    }else if(error.status == 401) {
                        showError();
                        $('#submitButton').removeAttr('disabled');
                        $('#submitButton').removeClass('spinner spinner-left pl-15');
                    }else{
                        $('#submitButton').removeAttr('disabled');
                        $('#submitButton').removeClass('spinner spinner-left pl-15');
                        // console.log(error);
                        $.each(error.responseJSON.errors, function (key, val) {

                            $('.' + key).addClass('has-danger');
                            $('.' + key + ' input').addClass('is-invalid');

                            $.each(val, function (_key,error) {
                                $('.' + key + ' .fv-help-block').text(error +'\n');
                            })

                        });
                    }
                }
            });
        });


        /* ---- particles.js config ---- */

particlesJS("particles-js", {
  "particles": {
    "number": {
      "value": 110,
      "density": {
        "enable": true,
        "value_area": 800
      }
    },
    "color": {
      "value": "#ffffff"
    },
    "shape": {
      "type": "circle",
      "stroke": {
        "width": 0,
        "color": "#000000"
      },
      "polygon": {
        "nb_sides": 8
      },
      "image": {
        "src": "img/github.svg",
        "width": 100,
        "height": 100
      }
    },
    "opacity": {
      "value": 0.5,
      "random": false,
      "anim": {
        "enable": false,
        "speed": 1,
        "opacity_min": 0.1,
        "sync": false
      }
    },
    "size": {
      "value": 5,
      "random": true,
      "anim": {
        "enable": false,
        "speed": 80,
        "size_min": 0.1,
        "sync": false
      }
    },
    "line_linked": {
      "enable": true,
      "distance": 150,
      "color": "#ffffff",
      "opacity": 0.4,
      "width": 1
    },
    "move": {
      "enable": true,
      "speed": 10,
      "direction": "none",
      "random": false,
      "straight": false,
      "out_mode": "out",
      "bounce": false,
      "attract": {
        "enable": false,
        "rotateX": 600,
        "rotateY": 1200
      }
    }
  },
  "interactivity": {
    "detect_on": "canvas",
    "events": {
      "onhover": {
        "enable": true,
        "mode": "grab"
      },
      "onclick": {
        "enable": true,
        "mode": "push"
      },
      "resize": true
    },
    "modes": {
      "grab": {
        "distance": 140,
        "line_linked": {
          "opacity": 1
        }
      },
      "bubble": {
        "distance": 400,
        "size": 40,
        "duration": 2,
        "opacity": 8,
        "speed": 3
      },
      "repulse": {
        "distance": 200,
        "duration": 0.4
      },
      "push": {
        "particles_nb": 4
      },
      "remove": {
        "particles_nb": 2
      }
    }
  },
  "retina_detect": true
});

    </script>

@endpush