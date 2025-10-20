<div id="kt_header" class="header header-fixed">
    <!--begin::Container-->
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <!--begin::Header Menu Wrapper-->
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <!--begin::Header Logo-->
            <div class="header-logo">
                <a href="{{ url('/') }}">
                    <img style="width: 40px; height: 35px" alt="Logo"
                        src="{{ url('/') }}/assets/media/logos/bas_logo.jpg" />
                </a>
            </div>
            <!--end::Header Logo-->
            <!--begin::Header Menu-->
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                @if (Auth::check())
                    <!--begin::Header Nav-->
                    <ul class="menu-nav">
                        @if (in_array('hrd_pengambilan_id_card', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel">
                                <a href="{{ url('/display/pengambilan-id-card') }}" class="menu-link">
                                    <span class="menu-text"><i class="fa fa-id-card"></i> &MediumSpace; Pengambilan ID
                                        Card</span>
                                </a>
                            </li>
                        @endif

                        {{-- 5r system --}}
                        @if (in_array('5r_system', $permissions))
                            <li class="menu-item {{ request()->is('5r-system/*') ? 'menu-item-active' : '' }}">
                                <a href="{{ url('/5r-system') }}" class="menu-link">
                                    <span class="menu-text">5R System</span>
                                </a>
                            </li>
                        @endif

                        {{-- 5r system --}}
                        @if (in_array('boiler', $permissions))
                            <li class="menu-item {{ request()->is('sistem-plc/*') ? 'menu-item-active' : '' }}">
                                <a href="{{ url('/sistem-plc/boiler/home-page') }}" class="menu-link">
                                    <span class="menu-text">boiler</span>
                                </a>
                            </li>
                        @endif

                        @if (in_array('ite', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">ITE <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <li class="menu-item menu-item-submenu">
                                            <a href="{{ url('ite/orders') }}" class="menu-link">
                                                <span class="svg-icon menu-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path
                                                                d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z"
                                                                fill="#000000" opacity="0.3" />
                                                            <path
                                                                d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z"
                                                                fill="#000000" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="menu-text">ORDERS</span>
                                            </a>
                                        </li>
                                        <li class="menu-item menu-item-submenu" aria-haspopup="true">
                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                <span class="svg-icon menu-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path
                                                                d="M18.4246212,12.6464466 L21.2530483,9.81801948 C21.4483105,9.62275734 21.764893,9.62275734 21.9601551,9.81801948 L22.6672619,10.5251263 C22.862524,10.7203884 22.862524,11.0369709 22.6672619,11.232233 L19.8388348,14.0606602 C19.6435726,14.2559223 19.3269901,14.2559223 19.131728,14.0606602 L18.4246212,13.3535534 C18.2293591,13.1582912 18.2293591,12.8417088 18.4246212,12.6464466 Z M3.22182541,17.9497475 L13.1213203,8.05025253 C13.5118446,7.65972824 14.1450096,7.65972824 14.5355339,8.05025253 L15.9497475,9.46446609 C16.3402718,9.85499039 16.3402718,10.4881554 15.9497475,10.8786797 L6.05025253,20.7781746 C5.65972824,21.1686989 5.02656326,21.1686989 4.63603897,20.7781746 L3.22182541,19.363961 C2.83130112,18.9734367 2.83130112,18.3402718 3.22182541,17.9497475 Z"
                                                                fill="#000000" opacity="0.3" />
                                                            <path
                                                                d="M12.3890873,1.28248558 L12.3890873,1.28248558 C15.150511,1.28248558 17.3890873,3.52106183 17.3890873,6.28248558 L17.3890873,10.7824856 C17.3890873,11.058628 17.1652297,11.2824856 16.8890873,11.2824856 L12.8890873,11.2824856 C12.6129449,11.2824856 12.3890873,11.058628 12.3890873,10.7824856 L12.3890873,1.28248558 Z"
                                                                fill="#000000"
                                                                transform="translate(14.889087, 6.282486) rotate(-45.000000) translate(-14.889087, -6.282486) " />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="menu-text">PROJECT</span>
                                                <i class="menu-arrow"></i>
                                            </a>
                                            <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                <ul class="menu-subnav">
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="{{ url('/ite/projects') }}" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Project List</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Project Jobs</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Project Schedule</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="javascript:;" class="menu-link">
                                                <span class="svg-icon menu-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path
                                                                d="M19,16 L19,12 C19,8.13400675 15.8659932,5 12,5 C8.13400675,5 5,8.13400675 5,12 L5,16 L19,16 Z M21,16 L3,16 L3,12 C3,7.02943725 7.02943725,3 12,3 C16.9705627,3 21,7.02943725 21,12 L21,16 Z"
                                                                fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                                            <path
                                                                d="M5,14 L6,14 C7.1045695,14 8,14.8954305 8,16 L8,19 C8,20.1045695 7.1045695,21 6,21 L5,21 C3.8954305,21 3,20.1045695 3,19 L3,16 C3,14.8954305 3.8954305,14 5,14 Z M18,14 L19,14 C20.1045695,14 21,14.8954305 21,16 L21,19 C21,20.1045695 20.1045695,21 19,21 L18,21 C16.8954305,21 16,20.1045695 16,19 L16,16 C16,14.8954305 16.8954305,14 18,14 Z"
                                                                fill="#000000" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="menu-text">HELPDESK</span>
                                            </a>
                                        </li>
                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover"
                                            aria-haspopup="true">
                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                <span class="svg-icon menu-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path
                                                                d="M10.5,5 L19.5,5 C20.3284271,5 21,5.67157288 21,6.5 C21,7.32842712 20.3284271,8 19.5,8 L10.5,8 C9.67157288,8 9,7.32842712 9,6.5 C9,5.67157288 9.67157288,5 10.5,5 Z M10.5,10 L19.5,10 C20.3284271,10 21,10.6715729 21,11.5 C21,12.3284271 20.3284271,13 19.5,13 L10.5,13 C9.67157288,13 9,12.3284271 9,11.5 C9,10.6715729 9.67157288,10 10.5,10 Z M10.5,15 L19.5,15 C20.3284271,15 21,15.6715729 21,16.5 C21,17.3284271 20.3284271,18 19.5,18 L10.5,18 C9.67157288,18 9,17.3284271 9,16.5 C9,15.6715729 9.67157288,15 10.5,15 Z"
                                                                fill="#000000" />
                                                            <path
                                                                d="M5.5,8 C4.67157288,8 4,7.32842712 4,6.5 C4,5.67157288 4.67157288,5 5.5,5 C6.32842712,5 7,5.67157288 7,6.5 C7,7.32842712 6.32842712,8 5.5,8 Z M5.5,13 C4.67157288,13 4,12.3284271 4,11.5 C4,10.6715729 4.67157288,10 5.5,10 C6.32842712,10 7,10.6715729 7,11.5 C7,12.3284271 6.32842712,13 5.5,13 Z M5.5,18 C4.67157288,18 4,17.3284271 4,16.5 C4,15.6715729 4.67157288,15 5.5,15 C6.32842712,15 7,15.6715729 7,16.5 C7,17.3284271 6.32842712,18 5.5,18 Z"
                                                                fill="#000000" opacity="0.3" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="menu-text">MASTER</span>
                                                <i class="menu-arrow"></i>
                                            </a>
                                            <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                <ul class="menu-subnav">
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">User</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Material</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Department</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Jenis Asset</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Asset</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Jenis Project</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">Jenis Pekerjaan</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover"
                                            aria-haspopup="true">
                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                <span class="svg-icon menu-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <rect fill="#000000" opacity="0.3" x="12" y="4"
                                                                width="3" height="13" rx="1.5" />
                                                            <rect fill="#000000" opacity="0.3" x="7" y="9"
                                                                width="3" height="8" rx="1.5" />
                                                            <path
                                                                d="M5,19 L20,19 C20.5522847,19 21,19.4477153 21,20 C21,20.5522847 20.5522847,21 20,21 L4,21 C3.44771525,21 3,20.5522847 3,20 L3,4 C3,3.44771525 3.44771525,3 4,3 C4.55228475,3 5,3.44771525 5,4 L5,19 Z"
                                                                fill="#000000" fill-rule="nonzero" />
                                                            <rect fill="#000000" opacity="0.3" x="17" y="11"
                                                                width="3" height="6" rx="1.5" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="menu-text">LAPORAN</span>
                                                <i class="menu-arrow"></i>
                                            </a>
                                            <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                <ul class="menu-subnav">
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet menu-bullet-dot">
                                                                <span></span>
                                                            </i>
                                                            <span class="menu-text">DASHBOARD</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="menu-item menu-item-submenu" data-menu-toggle="hover"
                                            aria-haspopup="true">
                                            <a href="javascript:;" class="menu-link menu-toggle">
                                                <span class="svg-icon menu-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                        height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none"
                                                            fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <rect fill="#000000" opacity="0.3" x="4" y="4"
                                                                width="4" height="4" rx="1" />
                                                            <path
                                                                d="M5,10 L7,10 C7.55228475,10 8,10.4477153 8,11 L8,13 C8,13.5522847 7.55228475,14 7,14 L5,14 C4.44771525,14 4,13.5522847 4,13 L4,11 C4,10.4477153 4.44771525,10 5,10 Z M11,4 L13,4 C13.5522847,4 14,4.44771525 14,5 L14,7 C14,7.55228475 13.5522847,8 13,8 L11,8 C10.4477153,8 10,7.55228475 10,7 L10,5 C10,4.44771525 10.4477153,4 11,4 Z M11,10 L13,10 C13.5522847,10 14,10.4477153 14,11 L14,13 C14,13.5522847 13.5522847,14 13,14 L11,14 C10.4477153,14 10,13.5522847 10,13 L10,11 C10,10.4477153 10.4477153,10 11,10 Z M17,4 L19,4 C19.5522847,4 20,4.44771525 20,5 L20,7 C20,7.55228475 19.5522847,8 19,8 L17,8 C16.4477153,8 16,7.55228475 16,7 L16,5 C16,4.44771525 16.4477153,4 17,4 Z M17,10 L19,10 C19.5522847,10 20,10.4477153 20,11 L20,13 C20,13.5522847 19.5522847,14 19,14 L17,14 C16.4477153,14 16,13.5522847 16,13 L16,11 C16,10.4477153 16.4477153,10 17,10 Z M5,16 L7,16 C7.55228475,16 8,16.4477153 8,17 L8,19 C8,19.5522847 7.55228475,20 7,20 L5,20 C4.44771525,20 4,19.5522847 4,19 L4,17 C4,16.4477153 4.44771525,16 5,16 Z M11,16 L13,16 C13.5522847,16 14,16.4477153 14,17 L14,19 C14,19.5522847 13.5522847,20 13,20 L11,20 C10.4477153,20 10,19.5522847 10,19 L10,17 C10,16.4477153 10.4477153,16 11,16 Z M17,16 L19,16 C19.5522847,16 20,16.4477153 20,17 L20,19 C20,19.5522847 19.5522847,20 19,20 L17,20 C16.4477153,20 16,19.5522847 16,19 L16,17 C16,16.4477153 16.4477153,16 17,16 Z"
                                                                fill="#000000" />
                                                        </g>
                                                    </svg>
                                                </span>
                                                <span class="menu-text">MANAGE</span>
                                                <i class="menu-arrow"></i>
                                            </a>
                                            <div class="menu-submenu menu-submenu-classic menu-submenu-right">
                                                <ul class="menu-subnav">
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M11,20 L11,17 C11,16.4477153 11.4477153,16 12,16 C12.5522847,16 13,16.4477153 13,17 L13,20 L15.5,20 C15.7761424,20 16,20.2238576 16,20.5 C16,20.7761424 15.7761424,21 15.5,21 L8.5,21 C8.22385763,21 8,20.7761424 8,20.5 C8,20.2238576 8.22385763,20 8.5,20 L11,20 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M3,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,16 C22,16.5522847 21.5522847,17 21,17 L3,17 C2.44771525,17 2,16.5522847 2,16 L2,6 C2,5.44771525 2.44771525,5 3,5 Z M4.5,8 C4.22385763,8 4,8.22385763 4,8.5 C4,8.77614237 4.22385763,9 4.5,9 L13.5,9 C13.7761424,9 14,8.77614237 14,8.5 C14,8.22385763 13.7761424,8 13.5,8 L4.5,8 Z M4.5,10 C4.22385763,10 4,10.2238576 4,10.5 C4,10.7761424 4.22385763,11 4.5,11 L7.5,11 C7.77614237,11 8,10.7761424 8,10.5 C8,10.2238576 7.77614237,10 7.5,10 L4.5,10 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </i> &nbsp;
                                                            <span class="menu-text">KOMPUTER</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M16,17 L16,21 C16,21.5522847 15.5522847,22 15,22 L9,22 C8.44771525,22 8,21.5522847 8,21 L8,17 L5,17 C3.8954305,17 3,16.1045695 3,15 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,15 C21,16.1045695 20.1045695,17 19,17 L16,17 Z M17.5,11 C18.3284271,11 19,10.3284271 19,9.5 C19,8.67157288 18.3284271,8 17.5,8 C16.6715729,8 16,8.67157288 16,9.5 C16,10.3284271 16.6715729,11 17.5,11 Z M10,14 L10,20 L14,20 L14,14 L10,14 Z"
                                                                            fill="#000000" />
                                                                        <rect fill="#000000" opacity="0.3" x="8"
                                                                            y="2" width="8" height="2"
                                                                            rx="1" />
                                                                    </g>
                                                                </svg>
                                                            </i> &nbsp;
                                                            <span class="menu-text">PRINTER</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M15,15 L15,10 C15,9.44771525 15.4477153,9 16,9 C16.5522847,9 17,9.44771525 17,10 L17,15 L20,15 C21.1045695,15 22,15.8954305 22,17 L22,19 C22,20.1045695 21.1045695,21 20,21 L4,21 C2.8954305,21 2,20.1045695 2,19 L2,17 C2,15.8954305 2.8954305,15 4,15 L15,15 Z M5,17 C4.44771525,17 4,17.4477153 4,18 C4,18.5522847 4.44771525,19 5,19 L10,19 C10.5522847,19 11,18.5522847 11,18 C11,17.4477153 10.5522847,17 10,17 L5,17 Z"
                                                                            fill="#000000" />
                                                                        <path
                                                                            d="M20.5,7.7155722 L19.2133304,8.85714286 C18.425346,7.82897283 17.2569914,7.22292937 15.9947545,7.22292937 C14.7366498,7.22292937 13.571742,7.82497398 12.7836854,8.84737587 L11.5,7.70192243 C12.6016042,6.27273291 14.2349886,5.42857143 15.9947545,5.42857143 C17.7603123,5.42857143 19.3985009,6.27832502 20.5,7.7155722 Z M23.5,5.46053062 L22.1362873,6.57142857 C20.629466,4.78945909 18.4012066,3.73944576 15.9963045,3.73944576 C13.5947271,3.73944576 11.3692392,4.78653417 9.8623752,6.56427829 L8.5,5.45180053 C10.340077,3.28094376 13.0626024,2 15.9963045,2 C18.934073,2 21.6599771,3.28451636 23.5,5.46053062 Z"
                                                                            fill="#000000" fill-rule="nonzero"
                                                                            opacity="0.3" />
                                                                    </g>
                                                                </svg>
                                                            </i> &nbsp;
                                                            <span class="menu-text">WIFI</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M15,9 L13,9 L13,5 L15,5 L15,9 Z M15,15 L15,20 L13,20 L13,15 L15,15 Z M5,9 L2,9 L2,6 C2,5.44771525 2.44771525,5 3,5 L5,5 L5,9 Z M5,15 L5,20 L3,20 C2.44771525,20 2,19.5522847 2,19 L2,15 L5,15 Z M18,9 L16,9 L16,5 L18,5 L18,9 Z M18,15 L18,20 L16,20 L16,15 L18,15 Z M22,9 L20,9 L20,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,9 Z M22,15 L22,19 C22,19.5522847 21.5522847,20 21,20 L20,20 L20,15 L22,15 Z"
                                                                            fill="#000000" />
                                                                        <path
                                                                            d="M9,9 L7,9 L7,5 L9,5 L9,9 Z M9,15 L9,20 L7,20 L7,15 L9,15 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <rect fill="#000000" opacity="0.3" x="0"
                                                                            y="11" width="24" height="2"
                                                                            rx="1" />
                                                                    </g>
                                                                </svg>
                                                            </i> &nbsp;
                                                            <span class="menu-text">SCANNER</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="{{ url('/ite/manage/cameras') }}" class="menu-link">
                                                            <i class="menu-bullet">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <rect fill="#000000" x="2" y="6"
                                                                            width="13" height="12"
                                                                            rx="2" />
                                                                        <path
                                                                            d="M22,8.4142119 L22,15.5857848 C22,16.1380695 21.5522847,16.5857848 21,16.5857848 C20.7347833,16.5857848 20.4804293,16.4804278 20.2928929,16.2928912 L16.7071064,12.7071013 C16.3165823,12.3165768 16.3165826,11.6834118 16.7071071,11.2928877 L20.2928936,7.70710477 C20.683418,7.31658067 21.316583,7.31658098 21.7071071,7.70710546 C21.8946433,7.89464181 22,8.14899558 22,8.4142119 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                    </g>
                                                                </svg>
                                                            </i> &nbsp;
                                                            <span class="menu-text">KAMERA</span>
                                                        </a>
                                                    </li>
                                                    <li class="menu-item" aria-haspopup="true">
                                                        <a href="javascript:;" class="menu-link">
                                                            <i class="menu-bullet">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                                                    width="24px" height="24px" viewBox="0 0 24 24"
                                                                    version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24"
                                                                            height="24" />
                                                                        <path
                                                                            d="M5,2 L19,2 C20.1045695,2 21,2.8954305 21,4 L21,6 C21,7.1045695 20.1045695,8 19,8 L5,8 C3.8954305,8 3,7.1045695 3,6 L3,4 C3,2.8954305 3.8954305,2 5,2 Z M11,4 C10.4477153,4 10,4.44771525 10,5 C10,5.55228475 10.4477153,6 11,6 L16,6 C16.5522847,6 17,5.55228475 17,5 C17,4.44771525 16.5522847,4 16,4 L11,4 Z M7,6 C7.55228475,6 8,5.55228475 8,5 C8,4.44771525 7.55228475,4 7,4 C6.44771525,4 6,4.44771525 6,5 C6,5.55228475 6.44771525,6 7,6 Z"
                                                                            fill="#000000" opacity="0.3" />
                                                                        <path
                                                                            d="M5,9 L19,9 C20.1045695,9 21,9.8954305 21,11 L21,13 C21,14.1045695 20.1045695,15 19,15 L5,15 C3.8954305,15 3,14.1045695 3,13 L3,11 C3,9.8954305 3.8954305,9 5,9 Z M11,11 C10.4477153,11 10,11.4477153 10,12 C10,12.5522847 10.4477153,13 11,13 L16,13 C16.5522847,13 17,12.5522847 17,12 C17,11.4477153 16.5522847,11 16,11 L11,11 Z M7,13 C7.55228475,13 8,12.5522847 8,12 C8,11.4477153 7.55228475,11 7,11 C6.44771525,11 6,11.4477153 6,12 C6,12.5522847 6.44771525,13 7,13 Z"
                                                                            fill="#000000" />
                                                                        <path
                                                                            d="M5,16 L19,16 C20.1045695,16 21,16.8954305 21,18 L21,20 C21,21.1045695 20.1045695,22 19,22 L5,22 C3.8954305,22 3,21.1045695 3,20 L3,18 C3,16.8954305 3.8954305,16 5,16 Z M11,18 C10.4477153,18 10,18.4477153 10,19 C10,19.5522847 10.4477153,20 11,20 L16,20 C16.5522847,20 17,19.5522847 17,19 C17,18.4477153 16.5522847,18 16,18 L11,18 Z M7,20 C7.55228475,20 8,19.5522847 8,19 C8,18.4477153 7.55228475,18 7,18 C6.44771525,18 6,18.4477153 6,19 C6,19.5522847 6.44771525,20 7,20 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                            </i> &nbsp;
                                                            <span class="menu-text">DEVICES</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('master', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('master/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">Master <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('master_variant', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/variant') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Variant</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_user', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/user') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">User</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_jenis_asset', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/jenis-asset') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Jenis Asset</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_asset', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/asset') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Asset</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_periode_checklist', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/checklist/periode') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Periode Checklis</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_checklist_schedule', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/checklist/schedule') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Schedule Checklis</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_department', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/department') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Department</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_jenis_material', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/jenis-material') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Jenis Material</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_material', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/material') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Material</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('master_approval', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/master/approval') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Manage Approval</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('report', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('report/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">Report <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('report_batch', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/mixing/report') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Report Batch</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('report_uco', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/uco/report') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">UCO Report</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('report_uco_totalizer', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ URL::to('/uco/totalizer') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Uco Totalizer</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('report_checklist_kaca', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="index.html" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Checklist Kaca</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('report_checklist_fly_catcher', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('checklist/fly_catcher/report') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Checklist Fly Catcher</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('report_pasteurisation', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="index.html" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Pasteurisation</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('report_cek_suhu', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ route('cek-suhu.report') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Cek Suhu</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('permission', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('permission/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">Permission <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('permission', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('permission/auth-permission') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Auth Permission</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('permission_auth_group', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('permission/auth-group') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Auth Group</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('upload', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('upload/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">Upload</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('upload_co_product', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/upload/co-product') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Co Product</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('checklist', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('checklist/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:;" class="menu-link menu-toggle">
                                    <span class="menu-text">Upload <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('/checklist/kaca') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-line">
                                                    <span></span>
                                                </i>
                                                <span class="menu-text">Checklist Kaca</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('hr', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('hr/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">HR <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('hr_absensi', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr/absensi') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Absensi</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_ecafesedaap', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr/ecafesedaap') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">EcafeSedaap</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('klinik', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/klinik/dokter') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Klinik Online</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_connect', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr-connect/dashboard') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">HR_connect</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_overtime', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="index.html" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Upload Masuk Hari Libur</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_overtime_report', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="index.html" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">IM Masuk Hari Libur</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_kbbm', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr/kbbm') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">KBBM</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_data_tamu', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr/data-tamu') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Data Tamu</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_pembagian', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr/pembagian') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Pembagian</span>
                                                </a>``
                                            </li>
                                        @endif
                                        @if (in_array('hr_karyawan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr/karyawan') }}" class="menu-link">
                                                    <i class="flaticon flaticon-users mr-3"
                                                        style="margin-left: -5px"></i>
                                                    <span class="menu-text"> Karyawan</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('sigra', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('sigra/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">SIGRA <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        {{-- @if (in_array('sigra_master_vendor', $permissions))
                                    <li class="menu-item" aria-haspopup="true">
                                        <a href="{{ url('/sigra/master-vendor') }}" class="menu-link">
                                <i class="menu-bullet menu-bullet-line">
                                    <span></span>
                                </i>
                                <span class="menu-text">Master Vendor</span>
                                </a>
                    </li>
                    @endif --}}
                                        @if (in_array('sigra_kontrak_vendor', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/kontrak-vendor') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Kontrak Vendor</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('sigra_legalitas', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/legalitas') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Legalitas</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('sigra_operasional', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/operasional') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Operasional</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('sigra_sio', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/sio') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">SIO</span>
                                                </a>
                                            </li>
                                        @endif
                                        {{-- @if (in_array('sigra_sni_mi_instan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/sni-mi-instan') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">SNI Mi Instan</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('sigra_bdkt_mi_instan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/bdkt-mi-instan') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">BDKT Mi Instan</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('sigra_md_mi_instan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/md-mi-instan') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">MD Mi Instan</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('sigra_sh_bahan_baku', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/sigra/sh-bahan-baku') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">SH Bahan Baku</span>
                                                </a>
                                            </li>
                                        @endif --}}
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('halo-security-dashboard', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('hr/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">GA <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('halo-security', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/halo-security') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Halo Security</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('homepage', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('homepage/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">Homepage <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('homepage_banner', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/homepage/banner') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Banner</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('homepage_shortcut', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/homepage/shortcut') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Shortcut</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('self_service_wsp', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('self_service_wsp/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">Self Service WSP <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('self_service_wsp_report', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/self-service-wsp/report') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Report</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('bas', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('bas/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">BAS <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('bas_pasteurisation', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pasteurisation/upload') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Upload Pasteurisation</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('hrd_pkw', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('pkw/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">PAS PKW <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('hrd_pkw_calon_karyawan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pkw/karyawan/calon') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Calon Karyawan</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hrd_pkw_karyawan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pkw/karyawan') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Karyawan</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hrd_pkw_pkwt', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pkw/pkwt') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">PKWT</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hrd_pkw_pkwtt', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pkw/pkwtt') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">PKWTT</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hrd_pkw_form_pa', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pkw/form-pa') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Form PA</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hrd_pkw_approve_form_pa', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pkw/form-pa/approve-page') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Approve Form PA</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('pme', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('pme/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">PME <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('pme_monthly_bill', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pme/monthly-bill') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Monthly Bill</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('pme_transfer_energy', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pme/transfer-energy') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Transfer Energy</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('pme_data_log', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pme/data-log') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Data Log</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('pme_data_log_2', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/pme/data-log-2') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Data Log 2</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('logging_machine', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('logging-machine/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">Logging Machine <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('logging_machine_operator', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/logging_machine/maintenance/operator') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Operator</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('logging_machine_engineering', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/logging_machine/maintenance/' . Auth::user()->username) }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Engineering</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('logging_machine_report', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/logging_machine/adm_prod/report') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Report</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('logging_machine_adm_prod', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/logging_machine/adm_prod') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Admin Produksi Menu</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('logging_machine_spv', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/logging_machine/spv_prod') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Spv. Produksi Menu</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif

                        @if (in_array('noodle_1', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">PPIC <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('ppic_noodle_1', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/noodle_1/ppic/index') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Menu PPIC Noodle 1</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif

                        {{-- <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click" aria-haspopup="true">
                        <a href="javascript:" class="menu-link menu-toggle">
                            <span class="menu-text">Internal Memo <i class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                        </a>
                        <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                            <ul class="menu-subnav">
                                <li class="menu-item" aria-haspopup="true">
                                    <a href="{{ url('/internal_memo/menu/index') }}" class="menu-link">
            <i class="menu-bullet menu-bullet-line">
                <span></span>
            </i>
            <span class="menu-text">Internal Memo Menu </span>
            </a>
            </li>
            </ul>
        </div>
        </li> --}}

                        @if (in_array('men', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('men/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">MEN <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('men_master_material_type', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/men/master-material-type') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Master Material Type</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('men_master_material', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/men/master-material') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Master Material</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('men_master_group', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/men/master-group') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Master Group</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('men_master_notif', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/men/master-notif') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Master Notif</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('loker_online', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">Loker Online BAS <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('/loker') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-line">
                                                    <span></span>
                                                </i>
                                                <span class="menu-text">Loker Online BAS Menu</span>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (!in_array('eksternal', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">e-Doc <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('security', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('edoc') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text"><b>Main Menu</b></span>
                                                </a>
                                            </li>
                                        @endif
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('edoc/pengiriman') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-line">
                                                    <span></span>
                                                </i>
                                                <span class="menu-text"><b>Pengiriman Barang/Dokumen</b></span>
                                            </a>
                                        </li>
                                        @if (in_array('edoc_master_pic', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('edoc/masterpic') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text"><b>MASTER PIC</b></span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('hr_mhl', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">Kerja Hari Libur <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('hr_mhl_user', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/masukharilibur/upload-data-karyawan') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Upload Data karyawan</span>
                                                </a>
                                            </li>
                                        @endif

                                        @if (in_array('hr_mhl_approver', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/masukharilibur/approver') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Approver</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_mhl_reporting', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/masukharilibur/reporting') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Reporting</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('hr_mhl_reporting_hrd', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/masukharilibur/reporting') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Reporting</span>
                                                </a>
                                            </li>
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/masukharilibur/master_approval') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Master Approval</span>
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </li>

                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">HR Connect <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('hr_mhl_user', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr-connect/dashboard') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">HR Connect</span>
                                                </a>
                                            </li>
                                        @elseif (in_array('hr_mhl_approver', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr-connect/dashboard') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">HR Connect</span>
                                                </a>
                                            </li>
                                        @elseif (in_array('hr_connect', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/hr-connect/dashboard') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">HR Connect</span>
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </li>
                        @endif

                        @if (in_array('klinik', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel" data-menu-toggle="click"
                                aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">KLINIK<i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        @if (in_array('klinik_dokter', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/klinik/dokter') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Dokter</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('klinik_master_data_obat', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/klinik/master-data-obat') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Master Data Obat</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('klinik_laporan_obat', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/klinik/laporan-obat') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Laporan Obat</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('klinik_laporan_pemeriksaan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/klinik/laporan-pemeriksaan') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Laporan pemeriksaan</span>
                                                </a>
                                            </li>
                                        @endif
                                        @if (in_array('klinik_rekam_medis', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/klinik/rekam-medis') }}" class="menu-link"
                                                    style="padding-left: 25px">
                                                    <i class="menu-icon la la-user mr-2 ml-0" style="flex: 0"></i>
                                                    <span class="menu-text">Rekam Medis</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endif
                        @if (in_array('kedatangan_beras', $permissions))
                            <li
                                class="menu-item {{ request()->is('dashboard/master-kedatangan-beras*') ? 'menu-item-active' : '' }}">
                                <a href="{{ url('/dashboard/master-kedatangan-beras') }}" class="menu-link">
                                    <span class="menu-text">Kedatangan Beras</span>
                                </a>
                            </li>
                        @endif
                        @if (in_array('hr_ecafesedaap', $permissions))
                            <li class="menu-item menu-item-submenu menu-item-rel {{ request()->is('ecafesedaap/*') ? 'menu-item-active' : '' }}"
                                data-menu-toggle="click" aria-haspopup="true">
                                <a href="javascript:" class="menu-link menu-toggle">
                                    <span class="menu-text">Ecafesedaap <i
                                            class="ml-1 ki ki-bold-triangle-bottom icon-xs text-dark-50"></i></span>
                                </a>
                                <div class="menu-submenu menu-submenu-classic menu-submenu-left">
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('/ecafesedaap-scan/staff') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-line">
                                                    <span></span>
                                                </i>
                                                <span class="menu-text">Scan Makan (Staff)</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="menu-subnav">
                                        <li class="menu-item" aria-haspopup="true">
                                            <a href="{{ url('/ecafesedaap-scan/non-staff') }}" class="menu-link">
                                                <i class="menu-bullet menu-bullet-line">
                                                    <span></span>
                                                </i>
                                                <span class="menu-text">Scan Makan (Non-staff)</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <ul class="menu-subnav">
                                        @if (in_array('ga_upload_pesanan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/ecafesedaap/upload-jumlah-pesanan') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Upload Jumlah Pesanan</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('ga_update_jumlah_pesanan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/ecafesedaap/update-jumlah-pesanan') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Update Jumlah Pesanan</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('ga_view_jumlah_pesanan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/ecafesedaap/view-jumlah-pesanan') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">View Jumlah Update Pesanan</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('ga_upload_overtime', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/ecafesedaap/upload-overtime') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Upload Overtime</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('ga_report_ecafesedaap', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/ecafesedaap/reporting') }}" class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Report</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('pengecekan_kendaraan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/cateringbas/pengecekaan-kendaraan') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Pengecekan Kendaraan</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('kedatangan_lauk', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/cateringbas/kedatangan-lauk') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Cek Kedatangan Lauk</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('pengambilan_sampel', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/cateringbas/pengambilan-sampel') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Pengambilan Sampel</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('reporting_catering', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/cateringbas/reporting-GA') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Catering Approval</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('reporting_catering', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/cateringbas/reporting-GA-detail') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Reporting Catering Detail</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                    <ul class="menu-subnav">
                                        @if (in_array('reporting_mingguan', $permissions))
                                            <li class="menu-item" aria-haspopup="true">
                                                <a href="{{ url('/cateringbas/export-report-mingguan') }}"
                                                    class="menu-link">
                                                    <i class="menu-bullet menu-bullet-line">
                                                        <span></span>
                                                    </i>
                                                    <span class="menu-text">Cetak Report Catering</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                    </ul>
                @endif

                <!--end::Header Nav-->
            @else
                <ul class="menu-nav">
                    <li class="menu-item menu-item-submenu menu-item-rel menu-item-active" data-menu-toggle="click"
                        aria-haspopup="true">
                        <a href="{{ url('login') }}" class="menu-link menu-toggle">
                            <span class="menu-text">Login</span>
                            <i class="menu-arrow"></i>
                        </a>
                    </li>
                </ul>
                @endif
            </div>
            <!--end::Header Menu-->
        </div>
        <!--end::Header Menu Wrapper-->
        <!--begin::Topbar-->
        {{-- @if (Auth::check()) --}}
        <div class="topbar">
            <!--end::Languages-->
            <!--begin::User-->
            <div class="topbar-item">
                <div class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2"
                    id="kt_quick_user_toggle">
                    <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                    <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">
                        @if (Auth::check())
                            {{ explode(' ', Auth::user()->name)[0] }}
                        @endif
                    </span>
                    <span class="symbol symbol-35 symbol-light-danger">
                        <span class="symbol-label font-size-h5 font-weight-bold">
                            @if (Auth::check())
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @else
                                <i class="far fa-smile text-dark-50"></i>
                            @endif
                        </span>
                    </span>
                </div>
            </div>
            <!--end::User-->
        </div>
        {{-- @endif --}}
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>
