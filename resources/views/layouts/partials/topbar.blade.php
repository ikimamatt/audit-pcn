<!-- Topbar Start -->
<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link" type="button" aria-label="Buka atau tutup navigasi menu">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
                <li class="d-none d-lg-block">
                    <div class="position-relative topbar-search">
                        <label for="topbar-search-input" class="visually-hidden">Cari menu atau data</label>
                        <input type="text" id="topbar-search-input" class="form-control bg-light bg-opacity-75 border-light ps-4"
                               placeholder="Cari..." aria-label="Cari menu atau data">
                        <i class="mdi mdi-magnify fs-16 position-absolute text-muted top-50 translate-middle-y ms-2"></i>
                    </div>
                </li>
            </ul>
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" 
                       data-bs-toggle="dropdown" 
                       href="#" 
                       role="button"
                       aria-haspopup="true" 
                       aria-expanded="false">
                        <span class="pro-user-name ms-1">
                            Profile <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                        @php
                            $user = Auth::user();
                            $userNama = $user ? $user->nama : 'User';
                            $userJabatan = $user && $user->jabatan ? $user->jabatan : '-';
                        @endphp
                        
                        <!-- item - User Info -->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0 mb-1" style="font-weight: 600;">{{ $userNama }}</h6>
                            @if($userJabatan != '-')
                                <small class="text-muted d-block">{{ $userJabatan }}</small>
                            @endif
                        </div>

                        <!-- item-->
                        <!-- <a href="/utility/profile" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle text-info"></i>
                            <span>Profil Saya</span>
                        </a> -->

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item" id="reset-password-nav-link">
                            <i class="mdi mdi-key-change fs-16 align-middle text-primary"></i>
                            <span>Reset Password</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item notify-item" id="logout-link">
                            <i class="mdi mdi-location-exit fs-16 align-middle text-danger"></i>
                            <span class="text-danger">Logout</span>
                        </a>

                    </div>
                </li>

            </ul>
        </div>

    </div>

</div>
<!-- end Topbar -->
