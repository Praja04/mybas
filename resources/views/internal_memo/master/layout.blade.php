<!DOCTYPE html>
<html lang="en">
	<head><base href="{{ url('/') }}">
		<meta charset="utf-8" />
		<title>My PAS Online</title>
		<meta name="description" content="PT. Prakarsa Alam Segar Applications Base" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}" />
		<link href="{{ url('/') }}/assets/plugins/global/plugins.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/style.bundle.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/header/base/light.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/brand/dark.css?v=7.0.5" rel="stylesheet" type="text/css" />
		<link href="{{ url('/') }}/assets/css/themes/layout/aside/dark.css?v=7.0.5" rel="stylesheet" type="text/css" />

		
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
				<button class="btn p-0" id="kt_aside_mobile_toggle"><i class="fas fa-angle-double-right"></i>
					<span></span>
				</button>
                	<button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle"><i class="fas fa-angle-down"></i>
				</button>
				<div class="float-right">
						<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
									<div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-primary">
										<span class="svg-icon svg-icon-xl svg-icon-primary">
										<i class="fas fa-bell text-white"> </i>
										</span>
										<span class="pulse-ring"></span>
									</div>
								</div>
								<div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg pre-scrollable">
									<form>
										<div class="d-flex flex-column pt-12 bgi-size-cover bgi-no-repeat rounded-top" style="background-image: url(/assets/media/misc/bg-1.jpg)">
											<h4 class="d-flex flex-center rounded-top">
												<span class="text-white">Notifikasi</span>
												<span class="btn btn-text btn-primary btn-sm font-weight-bold btn-font-md ml-2">23 new</span>
											</h4>
										</div>
										<div class="d-flex flex-center pt-7">
										<a href="#" class="btn btn-light-primary font-weight-bold text-center">Tandai Sudah Di Baca Semua</a>
									</div>
											<div class="tab-pane active show p-8" id="topbar_notifications_notifications" role="tabpanel">
												<div class="scroll pr-7 mr-n7" data-scroll="true" data-height="300" data-mobile-height="200">
													<div class="d-flex align-items-center mb-6">
														<div class="symbol symbol-40 symbol-light-warning mr-5">
															<span class="symbol-label">
																<span class="svg-icon svg-icon-lg svg-icon-warning">
																<i class="fas fa-list"></i>
																</span>
															</span>
														</div>
														<div class="d-flex flex-column font-weight-bold">
															<a href="#" class="text-dark-75 text-hover-primary mb-1 font-size-lg">Judul</a>
															<span class="text-muted">ISINYA </span>
														</div>
													</div>
													<div class="d-flex align-items-center mb-6">
														<div class="symbol symbol-40 symbol-light-warning mr-5">
															<span class="symbol-label">
																<span class="svg-icon svg-icon-lg svg-icon-warning">
																<i class="fas fa-list"></i>
																</span>
															</span>
														</div>
														<div class="d-flex flex-column font-weight-bold">
															<a href="#" class="text-dark-75 text-hover-primary mb-1 font-size-lg">Judul</a>
															<span class="text-muted">ISINYA </span>
														</div>
													</div>
													</div>
												
											</div>
										</form>
									</div>
						</div>
					</div>
				</div>
				<div class="d-flex flex-column flex-root">
					<div class="d-flex flex-row flex-column-fluid page">
						<div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
							<div class="brand flex-column-auto" id="kt_brand">
								<img alt="Logo" src="{{ url('/') }}/assets/media/logos/pas_brand.png" style="width: 70%"/>
								<button class="brand-toggle btn btn-sm px-0" id="kt_aside_toggle">
									<span class="svg-icon svg-icon svg-icon-xl">
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<polygon points="0 0 24 0 24 24 0 24" />
												<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999)" />
												<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999)" />
											</g>
										</svg>
									</span>
								</button>
							</div>
							<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
								<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
									<ul class="menu-nav">
										<li class="menu-item menu-item-active" aria-haspopup="true">
											<a class="menu-link" href="/internal_memo/menu/index">
										<i class="fas fa-layer-group menu-icon"></i>
												<span class="menu-text">Dashboard</span>
											</a>
										</li>
										<li class="menu-section">
											<h4 class="menu-text">INTERNAL MEMO {{Auth::user()->name}}</h4>
											<i class="menu-icon ki ki-bold-more-hor icon-md"></i>
										</li>
										<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
											<a href="javascript:;" class="menu-link menu-toggle">
											<i class="fas fa-signature menu-icon"></i>
												<span class="menu-text">INTERNAL MEMO </span>
												<i class="menu-arrow"></i>
											</a>
											<div class="menu-submenu">
											<i class="menu-arrow"></i>
												<ul class="menu-subnav">
													<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
														<a href="/internal_memo/menu/buat_dokumen/" class="menu-link menu-toggle">
															<i class="menu-bullet fas fa-plus mr-2">
																<span></span>
															</i>
															<span class="menu-text">BUAT MEMO</span>
														</a>
													</ul>
												<ul class="menu-subnav">
													<li class="menu-item menu-item-submenu" aria-haspopup="true" data-menu-toggle="hover">
														<a href="/internal_memo/menu/history_dokumen/{{Crypt::encrypt(Auth::user()->username)}}" class="menu-link menu-toggle">
															<i class="menu-bullet fas fa-history mr-2">
																<span></span>
															</i>
															<span class="menu-text">HISTORY MEMO</span>
														</a>
													</ul>
												</div>
												@php
													$approver = DB::table('internal_memo_ttd')
																	->where('nik_tujuan', Auth::user()->username)
																	->where('status', 1)
																	->get();
													$cek_user = DB::table('internal_memo_ttd')
																->where('nik_tujuan', Auth::user()->username)
																->where('status', 1)
																->first();

													if($cek_user)
													{
														$validasi = DB::table('internal_memo_ttd')
														->join('internal_memo', 'internal_memo.id', '=', 'internal_memo_ttd.id_im')
														->orderBy('internal_memo_ttd.id', 'ASC')
														->where('internal_memo_ttd.status', 1)
														->where('internal_memo_ttd.id_im', $cek_user->id_im)
														->first();
													}
													else 
													{
														$validasi = "";
													}
													
																$notif_approver = count($approver);

													$sub_kategori = "";
													foreach ($approver as $list)
													{
														$sub_kategori = $list->sub_kategori;
													}
													@endphp

									{{-- LOGIC NOTIFIKASI APPROVER --}}
											@if($validasi)
												@if($validasi->nik_tujuan == Auth::user()->username)
												<li class="menu-item menu-item bg-primary Outstanding" aria-haspopup="true">
													<a class="menu-link" href="/internal_memo/menu/outstanding/{{Crypt::encrypt(Auth::user()->username)}}">
													 	<i class="fas fa-user-clock menu-icon text-white"></i>
														<span class="menu-text text-white">OUTSTANDING</span>

															@if ($notif_approver)
														<span class="label pulse mr-10">
														<span class="position-relative">{{$notif_approver}}</span>
														<span class="pulse-ring"></span>
														</span>
															@else
														@endif
													</a>
												</li>
											</li>
											@endif
											@else
										@endif
												@php
													
													$revisi = DB::table('internal_memo')
													->where('nik_pembuat', Auth::user()->username)
													->where('status', 2)
													->get();

													$validasi_rev = DB::table('internal_memo_ttd')
													->join('internal_memo', 'internal_memo.id', '=', 'internal_memo_ttd.id_im')
													->orderBy('internal_memo.id', 'ASC')
													->where('internal_memo.status', 2)
													->first();

														$notif_revisi = count($revisi);
														$status = 0;
														foreach($revisi as $val)
														{
															$status = $val->status;
														}
													@endphp

									{{-- LOGIC NOTIFIKASI REVISI --}}
											@if($validasi_rev)
												@if($validasi_rev->nik_pembuat == Auth::user()->username)
												<li class="menu-item menu-item bg-primary Outstanding" aria-haspopup="true">
													<a class="menu-link" href="/internal_memo/menu/list_revisi/{{$status}}/{{Crypt::encrypt(Auth::user()->username)}}">
													 	<i class="fas fa-edit menu-icon text-white"></i>
														<span class="menu-text text-white">REVISI</span>

															@if ($notif_revisi)
														<span class="label pulse mr-10">
														<span class="position-relative">{{$notif_revisi}}</span>
														<span class="pulse-ring"></span>
														</span>
															@else
														@endif
													</a>
												</li>
											</li>
											@endif
											@else
										@endif
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
								</div>
							</div>
						</div>
						{{-- {{dd(Auth::user()->username)}} --}}
						<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
							<div id="kt_header" class="header header-fixed">
						<div class="container-fluid d-flex align-items-stretch justify-content-between">
							<div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
								<div class="header-logo" style="width: 150px">
										<img alt="Logo" class="mt-4" src="{{ url('/') }}/assets/media/logos/logo-pas-with-text.png" style="width: 100%"/>
								</div>
							</div>
							<div class="topbar">

								<div class="dropdown">
								
								@php
								$isi_notif = DB::table('internal_memo_notifikasi')
											->select(
												'internal_memo_notifikasi.id',
												'internal_memo_notifikasi.id_im',
												'internal_memo_notifikasi.notif_from_nik',
												'internal_memo_notifikasi.notif_to_nik',
												'internal_memo_notifikasi.no_notif',
												'internal_memo_notifikasi.isi',
												'internal_memo_notifikasi.no_dokumen',
												'internal_memo_notifikasi.status',
												'internal_memo_notifikasi.jenis_notif',
												'internal_memo.nik_pembuat',
												)
											->join('internal_memo', 'internal_memo.id', '=', 'internal_memo_notifikasi.id_im' )
											->where('internal_memo_notifikasi.notif_to_nik', Auth::user()->username)
											->orderBy('internal_memo_notifikasi.id', 'DESC')
											->get();

								$count_notif = DB::table('internal_memo_notifikasi')
											->where('notif_to_nik', Auth::user()->username)
											->where('status',1)
											->count();

								@endphp

								<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
									<div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1 pulse pulse-primary">
										<span class="svg-icon svg-icon-xl svg-icon-primary">
										<i class="fas fa-bell text-dark"> </i>
										</span>
										<span class="pulse-ring"></span>
										<h5 class="position-relative ml-2 text-primary pulse pulse-danger">
											@if($count_notif != 0 ) 
														{{$count_notif}}
													@else 
													
													@endif
										</h5>
										<span class="pulse-ring "></span>
										<span class="pulse-ring"></span>
										<span class="pulse-ring"></span>
										<span class="pulse-ring"></span>
										<span class="pulse-ring"></span>
									</span>
									</div>
								</div>
								<div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg pre-scrollable">
										<div class="d-flex flex-column pt-12 bgi-size-cover bgi-no-repeat rounded-top" style="background-image: url(/assets/media/misc/bg-1.jpg)">
											<h4 class="d-flex flex-center rounded-top">
												<span class="text-white">Notifikasi</span>
												@if($count_notif != 0 ) 
												<span class="btn btn-text btn-primary btn-sm font-weight-bold btn-font-md ml-2">
														{{$count_notif}} New
													@else 
													@endif
												</span>
											</h4>
										</div>
										<div class="d-flex flex-center pt-7">
										<a href="/internal_memo/menu/read_all_notif/{{Auth::user()->username}}" class="btn btn-light-primary font-weight-bold text-center btn-sm">Tandai Sudah Di Baca Semua</a>
									</div>
											<div class="tab-pane active show p-8" id="topbar_notifications_notifications" role="tabpanel">
												@foreach ($isi_notif as $isi)
												<div class="scroll pr-7 mr-n7" data-scroll="true" data-height="300" data-mobile-height="200">
													<div class="d-flex align-items-center mb-6">
														<div class="symbol symbol-40 symbol-light-warning mr-5">
															<span class="symbol-label">
																<span class="svg-icon svg-icon-lg svg-icon-warning">
																	<i class="fas fa-list"></i>
																</span>
															</span>
														</div>
														<div class="d-flex flex-column font-weight-bold">
															<a class="text-dark-75 text-hover-primary mb-1 font-size-sm">
																<form action="/internal_memo/menu/read_notif/{{$isi->id}}" method="POST">
																	@method('PATCH')
																	@csrf
																	<input type="hidden" name="nik" value="{{$isi->nik_pembuat}}">

																	<input type="hidden" name="id_im" value="{{$isi->id_im}}">
																	<button type="submit" class="btn btn-block">
																		@if ($isi->jenis_notif == 1)
																		<span class="badge badge-pill badge-light"><i class="fas fa-check text-dark mr-2"></i> Selesai Di Approve</span>
																		@elseif ($isi->jenis_notif == 2)
																		<span class="badge badge-pill badge-danger"><i class="fas fa-times mr-2 text-white"></i> Permintaan Revisi </span>
																		@elseif ($isi->jenis_notif == 3)
																		<span class="badge badge-pill badge-info text-white"><i class="fas fa-check text-white mr-2"></i> Selesai Di Revisi</span>
																		@endif
																<p class="mt-2">
																	<span class="text-dark">{{$isi->isi}} </span>
																	@if ($isi->status ==1)
																	<span class="text-primary ml-2">
																			New
																		</span>
																	@endif
																</p>
															</button>
														</form>
															</a>
														</div>
													</div>
												</div>
												@endforeach
												</div>
											</div>
										</div>
									<div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2">
										<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3 text-right">
											<h6 class="logo-title mb-0 mt-4"><strong>PT. PAS</strong></h6>
											<div>
												<strong class="date font-weight-normal"></strong>
											</div>
										</span>
										<span class="symbol symbol-35 symbol-light-danger mt-4">
											<span class="symbol-label font-size-h5 font-weight-bold" style="width: 100px">
												<span class="time"></span>
											</span>
										</span>
									</div>
								</div>
							</div>
						</div>
							<div class="content d-flex flex-column flex-colusssmn-fluid" id="kt_content">
							@yield('content')
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
            <form action=" {{ url('/internal_memo/logout') }} " method="post">
				@csrf
				<div class="modal-footer">
					<button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary font-weight-bold logout"> <i class="fas fa-power-off"></i> Logout</button>
				</div>
			</form>
        </div>
    </div>
</div>

</body>
	<script>var HOST_URL = "{{ url('/') }}";</script>
		<script src="{{ url('/') }}/assets/plugins/global/plugins.bundle.js?v=7.0.5"></script>
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

		function read_notif(id)
			{
				location.href = "{{ url('/internal_memo/menu/read_notif') }}/" + id;
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
	 Swal.fire({
 	title: 'KONFIRMASI EMAIL',
				text: "Untuk Menggunakan Aplikasi Ini, Kamu Diwajibkan Mengisi Email.",
				icon: 'info',
  buttonsStyling: false,
  confirmButtonText: "<i class='fas fa-plus'></i> Masukan E-mail",
  customClass: {
   confirmButton: "btn btn-danger",
  }
	}).then(
		function (isConfirm) {
			if (isConfirm) 
			{
			location.href = "{{ url('/internal_memo/menu/add_email') }}";
			}
		}
	);

	
		//   if({{Auth::user()->email == NULL}})
		//   {
		// 	Swal.fire({
		// 		title: 'KONFIRMASI EMAIL',
		// 		text: "Untuk Menggunakan Aplikasi Ini, Kamu Diwajibkan Mengisi Email.",
		// 		icon: 'info',
		// 		showCancelButton: true,
		// 		confirmButtonColor: '#3085d6',
		// 		cancelButtonColor: '#d33',
		// 		confirmButtonText: 'Masukan Email',
		// 		cancelButtonText: "Nanti",
		// 		}).then((result) => {
		// 		if (result.isConfirmed) 
		// 		{
		// 			location.href = "{{ url('/internal_memo/menu/add_email') }}";
		// 		}
		// 		else
		// 		{
		// 			location.href = "{{ url('/') }}";
		// 		}
		// 		});
		//   }

    setInterval(function () {
        get_time();
    }, 1000);

	function alert_outstanding() {
            $('.Outstanding').fadeOut(700);
            $('.Outstanding').fadeIn(700);
        }
        setInterval(alert_outstanding, 2000);
		  $(".logout").click( function () {
		    $.ajax({
		      url: "{{ URL::to('/') }}/logout",
		      type: "POST",
		      dataType: "JSON",
		      success: function ( response )
		      {
		        if(response.success == 1)
		        {
		        //   location.reload();
					location.href = "{{ url('/') }}";
		        }
		      },
		      error: function ( error )
		      {
		      	location.reload();
		        console.log( error );
		      }
		    });
		  });


</script>

@stack('scripts')

		<!--end::Page Scripts-->
	</body>
	<!--end::Body-->
</html>