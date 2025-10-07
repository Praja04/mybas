<div id="sidebar">
    <div class="sidebar-wrapper text-white">
        <div class="sidebar-header position-relative">
            <div class="logo text-center" style="margin-bottom: 10px;">
                <a href="{{ url('/') }}">
                    <img style="width: 180px; height: 140px;" alt="Logo"
                        src="{{ url('/') }}/assets/media/logos/Logo-Kecap-Sedaap.png">
                </a>
            </div>
            <div class="justify-content-between align-items-center">
                <div>
                    <h4 class="menu-text"
                        style="font-size: 14px; justify-content:center; width: 100%; text-align:center;">Boiler PT Bumi
                        Alam Segar</h4>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                @if (in_array('boiler', $permissions))
                    <li class="sidebar-item {{ request()->is('sistem-plc/boiler/home-page') ? 'active' : '' }}">
                        <a href="{{ route('boiler.home-page') }}" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Data Trend</span>
                        </a>
                    </li>
                @endif

                {{-- <li class="sidebar-item  has-sub">
                    <a href="" class='sidebar-link'>
                        <i class="bi bi-stack"></i>
                        <span>Data Trend</span>
                    </a>

                    <ul class="submenu">
                        {{-- <li class="submenu-item  ">
                            <a href="" class="submenu-link">Pengisian tanggal Kedatangan</a>
                        </li> --}}

                        {{-- <li class="submenu-item">
                            <a href="#" class="submenu-link">Menu 2</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#" class="submenu-link">Menu 3</a>
                        </li>
                        <li class="submenu-item">
                            <a href="#" class="submenu-link">Menu 4</a>
                        </li>
                    </ul> --}}
                {{-- </li>  --}}
                {{-- <li class="sidebar-title">Menu Lainnya</li> --}}

            </ul>
        </div>
    </div>
</div>
