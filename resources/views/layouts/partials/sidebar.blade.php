<!-- Left Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <div class="logo-box" style="margin-bottom: 20px;">
                <a href="{{ route('root') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="/images/logo-pln.png" alt="" height="50">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-pln.png" alt="" height="50">
                    </span>
                </a>
                <a href="{{ route('root') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="/images/logo-pln.png" alt="" height="50">
                    </span>
                    <span class="logo-lg">
                        <img src="/images/logo-pln.png" alt="" height="50">
                    </span>
                </a>
            </div>

            <ul id="side-menu">

                <li class="menu-title">Menu</li>

                @php
                    $userAkses = auth()->user()->akses->nama_akses ?? '';
                    $isAuditee = strtoupper(trim($userAkses)) === 'AUDITEE';
                @endphp

                @if(!$isAuditee)
                <li>
                    <a href="#sidebarDashboards" data-bs-toggle="collapse">
                        <i data-feather="home"></i>
                        <span> Dashboard </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarDashboards">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ route('audit.dashboard') }}" class="tp-link">Analytical</a>
                            </li>
                            {{--
                            <li>
                                <a href="{{ route('audit.dashboard-pelaksanaan-audit.index') }}"
                                    class="tp-link">Dashboard Pelaksanaan Audit</a>
                            </li>
                            <li>
                                <a href="{{ route('audit.realisasi-audit.index') }}" class="tp-link">Realisasi Audit</a>
                            </li>
                            <li>
                                <a href="{{ route('audit.progress-tindak-lanjut.index') }}" class="tp-link">Progress
                                    Tindak Lanjut </a>
                            </li>
                            --}}
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="{{ route('audit.persetujuan.index') }}" class="tp-link">
                        <i data-feather="check-circle" class="text-warning"></i>
                        <span> Persetujuan Dokumen </span>
                    </a>
                </li>
                @endif
                {{--
                <li class="menu-title">Pages</li> --}}

                {{-- <li>
                    <a href="#sidebarAuth" data-bs-toggle="collapse">
                        <i data-feather="users"></i>
                        <span> Authentication </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarAuth">
                        <ul class="nav-second-level">
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Log In</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Register</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Recover
                                    Password</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Lock Screen</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Confirm
                                    Mail</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Email
                                    Verification</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Logout</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                {{-- <li>
                    <a href="#sidebarError" data-bs-toggle="collapse">
                        <i data-feather="alert-octagon"></i>
                        <span> Error Pages </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarError">
                        <ul class="nav-second-level">
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Error 404</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Error 500</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Error 503</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Error 429</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Offline Page</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

                {{-- <li>
                    <a href="#sidebarExpages" data-bs-toggle="collapse">
                        <i data-feather="file-text"></i>
                        <span> Utility </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarExpages">
                        <ul class="nav-second-level">
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Starter</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Profile</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Pricing</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Timeline</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Invoice</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">FAQs</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Gallery</a>
                            </li>
                            <li>
                                <a class="tp-link"
                                    href="{{ '#'}}">Maintenance</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}">Coming
                                    Soon</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

                {{-- <li>
                    <a href="{{ '#' }}" class="tp-link">
                        <i data-feather="calendar"></i>
                        <span> Calendar </span>
                    </a>
                </li> --}}

                @if(!$isAuditee)
                    <li class="menu-title mt-2">General</li>

                    {{-- <li>
                        <a href="#sidebarBaseui" data-bs-toggle="collapse">
                            <i data-feather="package"></i>
                            <span> Components </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarBaseui">
                            <ul class="nav-second-level">
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Accordions</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Alerts</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Badges</a>
                                </li>
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Breadcrumb</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Buttons</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Cards</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Collapse</a>
                                </li>
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Dropdowns</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Embed
                                        Video</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Grid</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Images</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">List
                                        Group</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Modals</a>
                                </li>
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Placeholders</a>
                                </li>
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Pagination</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Popovers</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Progress</a>
                                </li>
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Scrollspy</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Spinners</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Tabs</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Tooltips</a>
                                </li>
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Typography</a>
                                </li>
                            </ul>
                        </div>
                    </li> --}}

                    {{-- <li>
                        <a href="{{ '#' }}" class="tp-link">
                            <i data-feather="aperture"></i>
                            <span> Widgets </span>
                        </a>
                    </li> --}}

                    {{-- <li>
                        <a href="#sidebarAdvancedUI" data-bs-toggle="collapse">
                            <i data-feather="cpu"></i>
                            <span> Extended UI </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarAdvancedUI">
                            <ul class="nav-second-level">
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Carousel</a>
                                </li>
                                <li>
                                    <a class="tp-link"
                                        href="{{ '#'}}">Notifications</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Offcanvas</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Range
                                        Slider</a>
                                </li>
                            </ul>
                        </div>
                    </li> --}}

                    {{-- <li>
                        <a href="#sidebarIcons" data-bs-toggle="collapse">
                            <i data-feather="award"></i>
                            <span> Icons </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarIcons">
                            <ul class="nav-second-level">
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Feather Icons</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ '#'}}">Material Design
                                        Icons</a>
                                </li>
                            </ul>
                        </div>
                    </li> --}}

                    <li>
                        <a href="#sidebarMasterData" data-bs-toggle="collapse">
                            <i data-feather="database"></i>
                            <span> Master Data </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarMasterData">
                            <ul class="nav-second-level">
                                <li><a class="tp-link" href="{{ url('tables/master_kode_aoi') }}">Master
                                        Kode AOI</a></li>
                                <li><a class="tp-link" href="{{ url('tables/master_kode_risk') }}">Master
                                        Kode Risk</a></li>
                                <li><a class="tp-link" href="{{ url('tables/master_auditee') }}">Master
                                        Auditee</a></li>
                                <li><a class="tp-link" href="{{ url('tables/master_user') }}">Master
                                        User</a></li>
                                <li><a class="tp-link" href="{{ route('master.jenis-audit.index') }}">Master Jenis Audit</a>
                                </li>
                                <li><a class="tp-link" href="{{ route('master.area.index') }}">Master Area</a></li>
                                {{-- <li><a class="tp-link" href="{{ '#' }}">Master Akses User</a></li> --}}
                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#sidebarForms" data-bs-toggle="collapse">
                            <i data-feather="briefcase"></i>
                            <span> Perencanaan Audit </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarForms">
                            <ul class="nav-second-level">
                                <li>
                                    <a class="tp-link" href="{{ route('audit.pkpt.index') }}">Jadwal PKPT Audit</a>
                                </li>
                                <!-- <li>
                                    <a class="tp-link" href="{{ route('audit.dashboard-pkpt.index') }}">Dashboard PKPT</a>
                                </li> -->
                                <li>
                                    <a class="tp-link"
                                        href="{{ url('forms/tabel_perencanaan_audit') }}">Surat Tugas
                                        Audit</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ route('audit.pka.index') }}">Program Kerja Audit</a>
                                </li>

                            </ul>
                        </div>
                    </li>

                    <li>
                        <a href="#sidebarPelaksanaanAudit" data-bs-toggle="collapse">
                            <i data-feather="check-square"></i>
                            <span> Pelaksanaan Audit </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarPelaksanaanAudit">
                            <ul class="nav-second-level">
                                <li>
                                    <a class="tp-link" href="{{ route('audit.entry-meeting.index') }}">Entry Meeting</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ route('audit.walkthrough.index') }}">Hasil Walkthrough
                                        Audit</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ route('audit.tod-bpm.index') }}">Hasil TOD Audit</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ route('audit.toe.index') }}">Hasil TOE Audit</a>
                                </li>
                                <li>
                                    <a class="tp-link" href="{{ route('audit.exit-meeting.index') }}">Exit Meeting</a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif

                <li>
                    <a href="#sidebarPelaporanHasilAudit" data-bs-toggle="collapse">
                        <i data-feather="file-text"></i>
                        <span> Pelaporan Hasil Audit </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarPelaporanHasilAudit">
                        <ul class="nav-second-level">
                            <li>
                                <a class="tp-link" href="{{ route('audit.pelaporan-hasil-audit.index') }}"> Judul
                                    LHA/LHK </a>
                            </li>
                            <!-- <li>
                                <a class="tp-link" href="{{ route('audit.pelaporan-hasil-audit.index') }}">Tabel Temuan Audit</a>
                            </li> -->

                            <li>
                                <a class="tp-link" href="{{ route('audit.penutup-lha-rekomendasi.index') }}">Penutup
                                    LHA/LHK</a>
                            </li>
                            {{--
                            <li>
                                <a class="tp-link" href="{{ route('audit.unggah-dokumen.index') }}">Daftar Upload Dokumen</a>
                            </li>
                            --}}
                        </ul>
                    </div>
                </li>

                <!--  -->


                <!-- <li>
                    <a href="#sidebarCharts" data-bs-toggle="collapse">
                        <i data-feather="pie-chart"></i>
                        <span> Apex Charts </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarCharts">
                        <ul class="nav-second-level">
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Line</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Area</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Column</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Bar</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Mixed</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Timeline</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Range Area</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Funnel</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Candlestick</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Boxplot</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Bubble</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Scatter</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Heatmap</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Treemap</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Pie</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Radialbar</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Radar</a>
                            </li>
                            <li>
                                <a class="tp-link" class="tp-link" href="{{ '#'}}">Polar</a>
                            </li>
                        </ul>
                    </div>
                </li>  -->

                {{-- <li>
                    <a href="#sidebarMaps" data-bs-toggle="collapse">
                        <i data-feather="map"></i>
                        <span> Maps </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarMaps">
                        <ul class="nav-second-level">
                            <li>
                                <a class="tp-link" href="{{ '#'}}"
                                    class="tp-link">Google Maps</a>
                            </li>
                            <li>
                                <a class="tp-link" href="{{ '#'}}"
                                    class="tp-link">Vector Maps</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}

                <li>
                    <a href="{{ route('audit.pemantauan.index') }}">
                        <i data-feather="monitor"></i>
                        <span> Pemantauan Hasil Audit </span>
                    </a>
                </li>

                @if(!$isAuditee)
                    <li>
                        <a href="{{ route('audit.monitoring-tindak-lanjut.index') }}">
                            <i data-feather="trending-up"></i>
                            <span> Monitoring Tindak Lanjut </span>
                        </a>
                    </li>
                @endif


            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
</div>
<!-- Left Sidebar End -->
