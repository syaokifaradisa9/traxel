<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('home') }}">Traxel</a>
        </div>
        <ul class="sidebar-menu">
            <li class="@if(str_contains(Request::url(), "home")) active @endif">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-tachometer-alt"></i> <span>Home</span>
                </a>
            </li>
        </ul>
        <ul class="sidebar-menu">
            <li class="@if(str_contains(Request::url(), "tutorial")) active @endif">
                <a class="nav-link" href="{{ route('tutorial') }}">
                    <i class="fas fa-book"></i> <span>Penggunaan Aplikasi</span>
                </a>
            </li>
        </ul>
    </aside>
</div>