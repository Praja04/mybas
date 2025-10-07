<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head><base href="{{ url('/') }}">
		<meta charset="utf-8" />
		<title>My BAS Online</title>
		<meta name="description" content="PT. Prakarsa Alam Segar Applications Base" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
		<link href="{{ url('/') }}/assets/plugins/global/plugins.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/style.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/header/base/light.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/brand/dark.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/aside/dark.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<!--end::Layout Themes-->
		<link rel="shortcut icon" href="{{ url('/bas_login') }}/assets/media/logos/bas_logo.jpg" />
        <style type="text/css">
            .hide {
                display: none;
            }
        </style>
		@stack('styles')
	</head>


	<body id="kt_body" class="header-fixed header-mobile-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
			<div class="d-flex align-items-center">
				<button class="btn p-0" id="kt_aside_mobile_toggle"><i class="fas fa-angle-double-right"></i>>
					<span></span>
				</button>
                	<button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle"><i class="fas fa-angle-down"></i>
				</button>
			</div>
			<!--end::Toolbar-->
		</div>
		<!--end::Header Mobile-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">
				<!--begin::Aside-->
				<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
					<!--begin::Brand-->
					<div class="brand flex-column-auto" id="kt_brand">
						<!--begin::Logo-->
                           <img alt="Logo" class="mt-4" src="{{ url('/') }}/assets/media/logos/bas_brand.png" style="width: 70%"/>
						<!--end::Logo-->
						<!--begin::Toggle-->
						<button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
							<span class="svg-icon svg-icon svg-icon-xl">
								<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Navigation/Angle-double-left.svg-->
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<polygon points="0 0 24 0 24 24 0 24" />
										<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
										<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
									</g>
								</svg>
								<!--end::Svg Icon-->
							</span>
						</button>
						<!--end::Toolbar-->
					</div>
					<!--end::Brand-->
					<!--begin::Aside Menu-->
					<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
						<!--begin::Menu Container-->
						<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
							<!--begin::Menu Nav-->
							<ul class="menu-nav">
								<li class="menu-item menu-item-active" aria-haspopup="true">
									<a class="menu-link">
								<i class="fas fa-layer-group menu-icon"></i>
										<span class="menu-text">Dashboard</span>
									</a>
								</li>
								<li class="menu-section">
									<h4 class="menu-text">BAS Logger</h4>
									<i class="menu-icon ki ki-bold-more-hor icon-md"></i>
								</li>
								<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
									<a href="javascript:;" class="menu-link menu-toggle">
									<i class="fas fa-marker menu-icon"></i>
										<span class="menu-text">Logger</span>
										<i class="menu-arrow"></i>
									</a>
									<div class="menu-submenu">
									  <i class="menu-arrow"></i>
										<ul class="menu-subnav">
											{{-- <li class="menu-item menu-item-parent" aria-haspopup="true">
												<span class="menu-link">
													<span class="menu-text">Logger Produksi</span>
												</span>
											</li> --}}
											@if (Auth::user()->auth_group_id == 57)
											<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
												<a href="/bas_logger/operator/batch_identity" class="menu-link menu-toggle">
													<i class="menu-bullet fas fa-clipboard mr-2">
														<span></span>
													</i>
													<span class="menu-text">Batch Identity</span>
												</a>
											<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
												<a href="/bas_logger/operator/batch_history" class="menu-link menu-toggle">
													<i class="menu-bullet fas fa-history mr-2">
														<span></span>
													</i>
													@php 
													$count = DB::table('bas_batch_identity')->where('nik', Auth::user()->username)->count(); 
													@endphp
													
													<span class="menu-text">History Identity Logger</span>
													<span class="menu-label">
														@if($count > 0 )
														<span class="label label-rounded label-info">{{$count}}</span>
														@endif
													</span>
												</a>

													<li class="menu-item" aria-haspopup="true">
													<a class="menu-link">
													<i class="fa fa-tv menu-icon" aria-hidden="true"></i>
														<span class="menu-text"> Monitoring</span>
													</a>
												</li>
											@endif
										
											@if (Auth::user()->auth_group_id == 60)
											<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
												<a href="javascript:;" class="menu-link menu-toggle">
													<i class="menu-bullet fas fa-user-check mr-2"></i>
														<span></span>
													</i>
													<span class="menu-text">Form Analisa Logger</span>
												</a>
											@endif
										
											@if (Auth::user()->auth_group_id == 59)
												<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
												<a href="javascript:;" class="menu-link menu-toggle">
													<i class="menu-bullet fas fa-database mr-2">
														<span></span>
													</i>
													<span class="menu-text">MASTER STANDAR</span>
													<i class="menu-arrow"></i>
												</a>
												<div class="menu-submenu">
													<i class="menu-arrow"></i>
													<ul class="menu-subnav">
														<li class="menu-item" aria-haspopup="true">
															<a href="/bas_logger/spv/sampel_varian" class="menu-link">
																<i class="menu-bullet menu-bullet-dot">
																	<span></span>
																</i>
																<span class="menu-text">Sampel Dan Varian</span>
															</a>
														</li>
														<li class="menu-item" aria-haspopup="true">
															<a href="/bas_logger/spv/parameter_pengecekan" class="menu-link">
																<i class="menu-bullet menu-bullet-dot">
																	<span></span>
																</i>
																<span class="menu-text">Parameter Pengecekan</span>
															</a>
														</li>
													</ul>
												</div>
											</li>
											@endif
											
										</ul>
									</div>
								</li>
									<li class="menu-section">
									<h4 class="menu-text">System</h4>
									<i class="menu-icon ki ki-bold-more-hor icon-md"></i>
								</li>
									<li class="menu-item" aria-haspopup="true">

									<a class="menu-link" href="#logout" data-toggle="modal">
										
									<i class="fa fa-power-off menu-icon" aria-hidden="true"></i>
										<span class="menu-text"> Logout</span>
									</a>
								</li>
							</ul>
							<!--end::Menu Nav-->
						</div>
						<!--end::Menu Container-->
					</div>
					<!--end::Aside Menu-->
				</div>
				<!--end::Aside-->
				<!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
					<div id="kt_header" class="header header-fixed">
                <!--begin::Container-->
                <div class="container-fluid d-flex align-items-stretch justify-content-between">
                    <!--begin::Header Menu Wrapper-->
                    <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                        <!--begin::Header Logo-->
                        <div class="header-logo" style="width: 150px">
                                <img alt="Logo" class="mt-4" src="{{ url('/') }}/assets/media/logos/bas_text.png" style="width: 100%"/>
                        </div>

                        <!--end::Header Menu-->
                    </div>
                    <div class="topbar">
                        <!--end::Languages-->
                        <!--begin::User-->
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
                        <!--end::User-->
                    </div>
                {{-- @endif --}}
                <!--end::Topbar-->
                </div>
            </div>
					<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
					@yield('content')
					</div>
					<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
                    <!--begin::Container-->
                    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <!--begin::Copyright-->
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-muted font-weight-bold mr-2">2021 © </span>
                        </div>
                        <!--end::Copyright-->
                        <!--begin::Nav-->
                        <div class="nav nav-dark">
                          -
                        </div>
                        <div class="nav nav-dark">
                            <span class="nav-link text-dark-75">PT. Bumi Alam Segar</span>
                        </div>
                        <!--end::Nav-->
                    </div>
                    <!--end::Container-->
                </div>
            </div>
        </div>
    </div>

<!-- Modal-->
<div class="modal fade" id="logout" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <center><h4>KONFIRMASI LOGOUT</h4></center>
          </div>
            <form action="/bas_logout" method="post">
				@csrf
				<div class="modal-footer">
					<button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary font-weight-bold"> <i class="fas fa-power-off"></i> Logout</button>
				</div>
			</form>
        </div>
    </div>
</div>
	<script>var HOST_URL = "{{ url('/bas_login') }}";</script>
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="{{ url('/') }}/assets/plugins/global/plugins.bundle.js?v=7.0.5"></script>
		{{-- <script src="{{ url('/') }}/assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.5"></script> --}}
		<script src="{{ url('/') }}/assets/js/scripts.bundle.js?v=7.0.5"></script>


        <script type="text/javascript">
              $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        }
    });
	     @if(Session::has('success'))
  		toastr.success("{{ Session::get('success') }}");
		@endif


		@if(Session::has('info'))
				toastr.info("{{ Session::get('info') }}");
		@endif


		@if(Session::has('warning'))
				toastr.warning("{{ Session::get('warning') }}");
		@endif


		@if(Session::has('error'))
				toastr.error("{{ Session::get('error') }}");
		@endif

    function kasihNol($data) {
        if($data < 10)
        {
            return '0'+$data;
        }else{
            return $data;
        }
    }

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

		<!--end::Page Scripts-->
	</body>
	<!--end::Body-->
</html>