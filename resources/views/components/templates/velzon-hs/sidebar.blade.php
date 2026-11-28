@props([
    'menus' => [],
    'longName' => '',
    'shortName' => '',
    'nameIcon' => 'heart',
    'activeMenu' => null,
])

<style>
    .nav-link.menu-link.active {
        background-color: rgba(0, 0, 0, 0.2);
    }

    [data-layout=vertical][data-sidebar-size=sm] .logo span.logo-lg {
        display: none !important;
    }
</style>

<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="javascript:;" data-tilt data-tilt-perspective="70" data-tilt-speed="400" data-tilt-max="25"
            class="logo logo-dark lh-1 py-4" style="transform-style: preserve-3d">
            <span class="logo-sm bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-5">
                <span style="transform: translateZ(20px)" class="mdi mdi-{{ $nameIcon }}">{{ $shortName }}</span>
            </span>
            <span class="logo-lg bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-3">
                <span style="transform: translateZ(20px)"
                    class="mdi mdi-{{ $nameIcon }}">{!! $longName !!}</span>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="javascript:;" data-tilt data-tilt-perspective="70" data-tilt-speed="400" data-tilt-max="25"
            class="logo logo-light lh-1 py-4" style="transform-style: preserve-3d">
            <span class="logo-sm bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-5">
                <span style="transform: translateZ(20px)" class="mdi mdi-{{ $nameIcon }}">{{ $shortName }}</span>
            </span>
            <span class="logo-lg bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-3 d-flex align-items-center">
                <span style="transform: translateZ(20px)" class="mdi mdi-{{ $nameIcon }}"></span>
                {!! $longName !!}
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" style="height: 96% !important;">
        <div class="container-fluid">

            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                @php
                    $bestMatchingPath = '';
                    $decodedMenus = json_decode($menus) ?: [];
                    foreach ($decodedMenus as $mGroup) {
                        if (!empty($mGroup->menu)) {
                            foreach ($mGroup->menu as $mItem) {
                                if (empty($mItem->submenu)) {
                                    if (request()->is($mItem->path) || request()->is($mItem->path . '/*')) {
                                        if (strlen($mItem->path) > strlen($bestMatchingPath)) {
                                            $bestMatchingPath = $mItem->path;
                                        }
                                    }
                                } else {
                                    foreach ($mItem->submenu as $sItem) {
                                        if (request()->is($sItem->path) || request()->is($sItem->path . '/*')) {
                                            if (strlen($sItem->path) > strlen($bestMatchingPath)) {
                                                $bestMatchingPath = $sItem->path;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                @endphp
                @foreach (json_decode($menus) as $menu)
                    @php
                        $visibleMenuItems = collect($menu->menu)->filter(function ($menuItem) use ($permissions) {
                            if (!property_exists($menuItem, 'permission')) {
                                return true;
                            }
                            if (is_array($menuItem->permission)) {
                                return count(array_intersect($menuItem->permission, $permissions)) > 0;
                            }
                            return in_array($menuItem->permission, $permissions);
                        });
                    @endphp

                    @if (property_exists($menu, 'permission'))
                        @if (is_array($menu->permission))
                            @php $isGranted = false; @endphp
                            @foreach ($menu->permission as $permission)
                                @if (in_array($permission, $permissions))
                                    @php $isGranted = true; @endphp
                                    @break
                                @endif
                            @endforeach
                            @if (!$isGranted)
                                @continue
                            @endif
                        @else
                            @if (!in_array($menu->permission, $permissions))
                                @continue
                            @endif
                        @endif
                    @endif

                    @if ($menu->label != '' && $visibleMenuItems->count() > 0)
                        <li class="menu-title">
                            <i class="ri-more-fill"></i>
                            <span>{{ $menu->label }}</span>
                        </li>
                    @endif

                    @foreach ($visibleMenuItems as $menuItem)
                        @if (property_exists($menuItem, 'permission'))
                            @if (is_array($menuItem->permission))
                                @php $isGranted = false; @endphp
                                @foreach ($menuItem->permission as $permission)
                                    @if (in_array($permission, $permissions))
                                        @php $isGranted = true; @endphp
                                        @break
                                    @endif
                                @endforeach
                                @if (!$isGranted)
                                    @continue
                                @endif
                            @else
                                @if (!in_array($menuItem->permission, $permissions))
                                    @continue
                                @endif
                            @endif
                        @endif

                        <li class="nav-item">
                            @if (count($menuItem->submenu) == 0)
                                <!-- MENU SINGLE (TANPA SUBMENU) -->
                                @php
                                    $isItemActive = ($bestMatchingPath !== '') ? ($menuItem->path === $bestMatchingPath) : request()->is($menuItem->path . '*');
                                @endphp
                                <a class="nav-link menu-link {{ $isItemActive ? 'active' : '' }}"
                                    data-identity="{{ str_replace('/', '-', $menuItem->path) }}"
                                    href="{{ url($menuItem->path) }}">
                                    <i class="mdi {{ $menuItem->icon }}"></i> <span>{{ $menuItem->label }}</span>
                                </a>
                            @else
                                <!-- MENU DENGAN SUBMENU (DROPDOWN) -->
                                @php
                                    // Bikin ID yang aman dari garis miring
                                    $safeId = str_replace('/', '-', $menuItem->path);
                                    // Cek apakah ada anak submenu yang lagi aktif
                                    $isChildActive = false;
                                    foreach ($menuItem->submenu as $sub) {
                                        $subActive = ($bestMatchingPath !== '') ? ($sub->path === $bestMatchingPath) : request()->is($sub->path . '*');
                                        if ($subActive) {
                                            $isChildActive = true;
                                            break;
                                        }
                                    }
                                @endphp

                                <a class="nav-link menu-link {{ $isChildActive ? 'active collapsed' : '' }}"
                                    href="#{{ $safeId }}" data-bs-toggle="collapse" role="button"
                                    aria-expanded="{{ $isChildActive ? 'true' : 'false' }}"
                                    aria-controls="{{ $safeId }}">
                                    <i class="mdi {{ $menuItem->icon }}"></i> <span
                                        data-key="t-base-ui">{{ $menuItem->label }}</span>
                                </a>

                                <div class="collapse menu-dropdown mega-dropdown-menu {{ $isChildActive ? 'show' : '' }}"
                                    id="{{ $safeId }}">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <ul class="nav nav-sm flex-column">
                                                @foreach ($menuItem->submenu as $submenu)
                                                    @if (property_exists($submenu, 'permission') && !in_array($submenu->permission, $permissions))
                                                        @continue
                                                    @endif

                                                    @php
                                                        $isSubActive = ($bestMatchingPath !== '') ? ($submenu->path === $bestMatchingPath) : request()->is($submenu->path);
                                                    @endphp
                                                    <li class="nav-item">
                                                        <a data-identity="{{ str_replace('/', '-', $submenu->path) }}"
                                                            href="{{ url($submenu->path) }}"
                                                            class="nav-link {{ $isSubActive ? 'active' : '' }}">{!! $submenu->label !!}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Sidebar footer -->
    <div class="navbar-footer">
        <div class="continer-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="ri-home-2-line"></i>
                        <span>My BAS Online Home</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
