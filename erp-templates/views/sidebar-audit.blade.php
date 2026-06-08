{{-- 
    Tambahkan snippet ini ke resources/views/layouts/sidebar.blade.php di repo ERP PLN.
    Sesuaikan icon dan urutan menu sesuai kebutuhan.
--}}

<!-- ── Audit PCN ──────────────────────────────────────────── -->
<li class="nav-item has-treeview {{ request()->is('audit*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->is('audit*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-clipboard-check"></i>
        <p>
            Audit SPI
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('audit.dashboard') }}" class="nav-link {{ request()->routeIs('audit.dashboard') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Dashboard</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.perencanaan') }}" class="nav-link {{ request()->routeIs('audit.perencanaan') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Perencanaan Audit</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.pkpt') }}" class="nav-link {{ request()->routeIs('audit.pkpt') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Jadwal PKPT</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.pka') }}" class="nav-link {{ request()->routeIs('audit.pka') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Program Kerja Audit</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.walkthrough') }}" class="nav-link {{ request()->routeIs('audit.walkthrough') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Walkthrough</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.tod-bpm') }}" class="nav-link {{ request()->routeIs('audit.tod-bpm') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>TOD BPM</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.toe') }}" class="nav-link {{ request()->routeIs('audit.toe') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>TOE</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.entry-meeting') }}" class="nav-link {{ request()->routeIs('audit.entry-meeting') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Entry Meeting</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.exit-meeting') }}" class="nav-link {{ request()->routeIs('audit.exit-meeting') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Exit Meeting</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.pelaporan') }}" class="nav-link {{ request()->routeIs('audit.pelaporan') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Pelaporan Hasil Audit</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.penutup-lha') }}" class="nav-link {{ request()->routeIs('audit.penutup-lha') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Penutup LHA</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.pemantauan') }}" class="nav-link {{ request()->routeIs('audit.pemantauan') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Pemantauan</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.monitoring') }}" class="nav-link {{ request()->routeIs('audit.monitoring') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Monitoring TL</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('audit.persetujuan') }}" class="nav-link {{ request()->routeIs('audit.persetujuan') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Persetujuan</p>
            </a>
        </li>
    </ul>
</li>
