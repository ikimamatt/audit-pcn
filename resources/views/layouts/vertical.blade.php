<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials/title-meta', ['title' => $title])
    @yield('css')
    @include('layouts.partials/head-css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body data-menu-color="light" data-sidebar="default" @yield('body') >

<div id="app-layout">

    @include('layouts.partials/topbar')
    @include('layouts.partials/sidebar')

    <div class="content-page">
        <div class="content">
            <div class="container-xxl">
                @if(session('success'))
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ session('success') }}',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    </script>
                @endif
                @yield('content')
            </div>
        </div>

        @include("layouts.partials/footer")

    </div>

</div>

@vite(['resources/js/app.js'])
@include("layouts.partials/vendor")
@yield('script')

<!-- Session Timeout Handler -->
<script src="{{ asset('js/session-timeout.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logoutLink = document.getElementById('logout-link');
        if (logoutLink) {
            logoutLink.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Logout',
                    text: 'Apakah Anda yakin ingin keluar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, submit the logout form
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('logout') }}';
                        form.style.display = 'none';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const csrfInput = document.createElement('input');
                        csrfInput.setAttribute('type', 'hidden');
                        csrfInput.setAttribute('name', '_token');
                        csrfInput.setAttribute('value', csrfToken);
                        form.appendChild(csrfInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }
    });
</script>

@stack('scripts')

</body>
</html>
