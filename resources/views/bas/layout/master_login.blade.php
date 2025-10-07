<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head><base href="{{ url('/') }}">
		<meta charset="utf-8" />
		<title>My BAS Online</title>
		<meta name="description" content="PT. Prakarsa Alam Segar Applications Base" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<!--begin::Fonts-->
		<link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
		<link href="{{ url('/') }}/assets/css/style.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/header/base/light.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/header/menu/light.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="{{ url('/') }}/assets/media/logos/bas_logo.png" />
        <style type="text/css">
            .hide {
                display: none;
            }
        </style>
		@stack('styles')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="header-fixed header-mobile-fixed page-loading">
		<!--begin::Main-->
		<!--begin::Header Mobile-->
		<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed" style="right: 50px">
			<!--begin::Logo-->
			<a href="{{ url('/bas_login') }}">
				<img alt="Logo" src="{{ url('/') }}/assets/media/logos/bas_logo.png" style="width: 50%" class="mb-2" />
			</a>
			<!--end::Logo-->
			<!--begin::Toolbar-->
			<div class="d-flex align-items-center">
				<!--begin::Header Menu Mobile Toggle-->
				
			</div>
			<!--end::Toolbar-->
		</div>
		<!--end::Header Mobile-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">
				<!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
					<!--begin::Header-->
					<div id="kt_header" class="header header-fixed ">
                <!--begin::Container-->
                <div class="container-fluid d-flex align-items-stretch justify-content-between">
                    <!--begin::Header Menu Wrapper-->
                    <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                        <!--begin::Header Logo-->
                        <div class="header-logo" style="width: 150px">
                            <a href="{{ url('/bas_login') }}">
                                <img alt="Logo" class="mt-4" src="{{ url('/') }}/assets/media/logos/bas_text.png" style="width: 100%"/>
                            </a>
                        </div>

                    </div>
                    <div class="topbar">
                       <div class="topbar-item">
                            <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
                                <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3 text-right">
                                    <h6 class="logo-title mb-0"><strong>PT. BAS</strong></h6>
                                    <div>
                                        <strong class="date font-weight-normal"></strong>
                                    </div>
                                </span>
                                <span class="symbol symbol-35 symbol-light-danger">
                                    <span class="symbol-label font-size-h5 font-weight-bold" style="width: 100px">
                                        <span class="time"></span>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
						<div class="d-flex flex-column-fluid">

							@yield('content')
                            
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>var HOST_URL = "{{ url('/') }}";</script>
		<script src="{{ url('/') }}/assets/plugins/global/plugins.bundle.js?v=7.0.5"></script>
		<script src="{{ url('/') }}/assets/js/particles.js"></script>

<script>
          function kasihNol(data) {
              if(data < 10)
              {
                  return '0'+data;
              }else{
                  return data;
              }
          }

          function formatTanggalIndonesia(tanggal)
          {
              const today = new Date(tanggal);
              return kasihNol(today.getDate()) + '/' + kasihNol((today.getMonth() + 1)) + '/' + kasihNol(today.getFullYear());
		  }
		  
		  function formatTanggalIndonesia2(tanggal)
          {
			  var formated;
			  const today = new Date(tanggal);
			  const bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
			  formated = kasihNol(today.getDate()) + ' ' + bulan[today.getMonth()] + ' ' + kasihNol(today.getFullYear());

			  if(tanggal == null || tanggal == '') {
				  formated = '';
			  }

			  return formated;
          }

          $(".datepicker-year").datepicker({
              format: "yyyy",
              viewMode: "years",
              minViewMode: "years",
              autoclose: true
          });

              function get_time()
    {
        const today = new Date();
        const time = kasihNol(today.getHours()) + ":" + kasihNol(today.getMinutes()) + ":" + kasihNol(today.getSeconds());
        const date = kasihNol(today.getDate())+'/'+kasihNol((today.getMonth()+1))+'/'+kasihNol(today.getFullYear());
        $('.date').text(date);
        $('.time').text(time);
    }

    get_time();

    setInterval(function () {
        get_time();
    }, 1000);
    

		</script>
		@stack('scripts')
	</body>
	<!--end::Body-->
</html>
