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
</style>

<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="javascript:;" data-tilt data-tilt-perspective="70" data-tilt-speed="400" data-tilt-max="25"
            class="logo logo-dark lh-1 py-4" style="transform-style: preserve-3d">
            <span class="logo-sm bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-5">
                {{-- <img src="{{ asset('assets/velzon/images/logo-sm.png') }}" alt="" height="22"> --}}
                <span style="transform: translateZ(20px)" class="mdi mdi-{{ $nameIcon }}">{{ $shortName }}</span>
            </span>
            <span class="logo-lg bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-3">
                {{-- <img src="{{ asset('assets/velzon/images/logo-dark.png') }}" alt="" height="17"> --}}
                <span style="transform: translateZ(20px)" class="mdi mdi-{{ $nameIcon }}">{{ $longName }}</span>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="javascript:;" data-tilt data-tilt-perspective="70" data-tilt-speed="400" data-tilt-max="25"
            class="logo logo-light lh-1 py-4" style="transform-style: preserve-3d">
            <span class="logo-sm bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-5">
                {{-- <img src="{{ asset('assets/velzon/images/logo-sm.png') }}" alt="" height="22"> --}}
                <span style="transform: translateZ(20px)" class="mdi mdi-{{ $nameIcon }}">{{ $shortName }}</span>
            </span>
            <span class="logo-lg bg-light px-2 py-1 rounded-3 text-dark fw-semibold fs-3">
                {{-- <img src="{{ asset('assets/velzon/images/logo-light.png') }}" alt="" height="17"> --}}
                <span style="transform: translateZ(20px)" class="mdi mdi-{{ $nameIcon }}">{{ $longName }}</span>
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
                @foreach (json_decode($menus) as $menuGroup)
                @php
                $hasMenuWithPermissions = false;
                foreach ($menuGroup->menu as $menuItem) {
                    if (!property_exists($menuItem, 'permission') || in_array($menuItem->permission, $permissions)) {
                        $hasMenuWithPermissions = true;
                        break;
                    }
                }
                @endphp
                    @if ($hasMenuWithPermissions)
        
                    @endif
                    @foreach ($menuGroup->menu as $menuItem)
                        @if (property_exists($menuItem, 'permission') && !in_array($menuItem->permission, $permissions))
                            @continue
                        @endif
                        @php
                            $isActive = request()->is($menuItem->path);
                        @endphp
                        <li class="nav-item {{ $isActive ? 'active' : '' }}">
                            @if (count($menuItem->submenu) == 0)
                                <a class="nav-link menu-link {{ $isActive ? 'active' : '' }}"
                                href="{{ url($menuItem->path) }}">
                                <i class="mdi {{ $menuItem->icon }}"></i> <span>{{ $menuItem->label }}</span>
                                </a>
                            @else
                                <a class="nav-link menu-link" href="#{{ $menuItem->path }}" data-bs-toggle="collapse"
                                role="button" aria-expanded="false" aria-controls="{{ $menuItem->path }}">
                                    <i class="mdi {{ $menuItem->icon }}"></i> <span>{{ $menuItem->label }}</span>
                                </a>
                                <div class="collapse menu-dropdown mega-dropdown-menu" id="{{ $menuItem->path }}">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <ul class="nav nav-sm flex-column">
                                                @foreach ($menuItem->submenu as $submenu)
                                                    @if (property_exists($submenu, 'permission') && !in_array($submenu->permission, $permissions))
                                                        @continue
                                                    @endif
                                                    <li class="nav-item">
                                                        <a href="{{ url($submenu->path) }}" class="nav-link">{{ $submenu->label }}</a>
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
        <!-- Sidebar footer -->
        <div class="navbar-footer pt-4">
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
    </div>
    <!-- Sidebar -->
</div>


<div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
