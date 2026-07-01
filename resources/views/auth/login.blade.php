@extends('layouts.auth', ['title' => 'Login'])

@section('content')
  <style>
    /* Floating keyframes for main illustration */
    @keyframes float {

      0%,
      100% {
        transform: translateY(0) scale(1.05);
      }

      50% {
        transform: translateY(-8px) scale(1.05);
      }
    }

    .animate-float {
      animation: float 6s ease-in-out infinite;
    }

    /* Transition classes for page entrance */
    .animate-on-load {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 700ms cubic-bezier(0.16, 1, 0.3, 1), transform 700ms cubic-bezier(0.16, 1, 0.3, 1);
      will-change: opacity, transform;
    }

    .animate-on-load.is-visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Respect accessibility prefers-reduced-motion setting */
    @media (prefers-reduced-motion: reduce) {
      .animate-on-load {
        transition: none !important;
        opacity: 1 !important;
        transform: none !important;
      }

      .transition-all {
        transition: none !important;
      }
    }
  </style>

  <!-- Main Grid/Flex Container for Split Screen Layout -->
  <div class="w-full h-screen flex flex-col md:flex-row bg-white overflow-hidden">

    <!-- LEFT PANEL: Branding & Illustration (70% width on desktop, hidden on mobile) -->
    <div
      class="relative hidden md:flex md:w-[70%] bg-[#F4F7FA] items-center justify-center p-8 lg:p-12 overflow-hidden h-screen">

      <!-- 1. Top Wave Asset -->
      <div class="absolute top-[-33rem] left-0 right-0 w-full z-0 pointer-events-none">
        <img src="{{ asset('images/asset-audit/18.png') }}" alt="Wave Atas"
          class="w-full object-cover object-top opacity-90">
      </div>

      <!-- 2. Bottom Wave Asset -->
      <div class="absolute bottom-[-420px] left-0 right-0 w-full z-0 pointer-events-none">
        <img src="{{ asset('images/asset-audit/17.png') }}" alt="Wave Bawah" class="w-full object-cover object-bottom">
      </div>

      <!-- 3. Header Logos (Floating at the top) -->
      <div class="absolute top-1 left-8 right-8 lg:top-2 lg:left-12 lg:right-12 z-10 flex justify-between items-center">
        <!-- Danantara Indonesia Logo -->
        <img src="{{ asset('images/asset-audit/danantara.png') }}" alt="Danantara Indonesia Logo"
          class="h-10 w-auto object-contain">
        <!-- PLN Nusa Daya Logo -->
        <img src="{{ asset('images/asset-audit/logo.png') }}" alt="PLN Nusa Daya Logo"
          class="h-20 lg:h-24 w-auto object-contain">
      </div>

      <!-- 4. Center Illustration (Centered with float animation) -->
      <div class="relative z-10 flex justify-center items-center w-full h-full max-h-[98vh]">
        <img src="{{ asset('images/asset-audit/19.png') }}" alt="Main Illustration"
          class="max-w-[130%] lg:max-w-[125%] max-h-[98vh] object-contain scale-105 animate-float transition-all">
      </div>

    </div>

    <!-- RIGHT PANEL: Login Form (30% width on desktop, 100% on mobile) -->
    <div
      class="w-full md:w-[30%] h-screen bg-white flex flex-col justify-start items-center px-6 py-12 lg:px-8 relative z-10">

      <!-- 5. Aku Jago Logo (Mascot) (Desktop only) -->
      <div class="hidden md:block absolute top-6 right-6 z-10">
        <img src="{{ asset('images/asset-audit/aku-jago.png') }}" alt="Aku Jago Mascot Logo"
          class="h-12 lg:h-14 w-auto object-contain">
      </div>

      <!-- Header Logos for Mobile View (Visible only on mobile/tablet when left panel is hidden) -->
      <div class="w-full flex justify-between items-center px-6 py-4 md:hidden absolute top-0 left-0 right-0 z-20">
        <img src="{{ asset('images/asset-audit/danantara.png') }}" alt="Danantara Indonesia Logo"
          class="h-8 w-auto object-contain">
        <img src="{{ asset('images/asset-audit/logo.png') }}" alt="PLN Nusa Daya Logo" class="h-14 w-auto object-contain">
      </div>

      <!-- 6. Login Form Container -->
      <div class="w-full max-w-[420px] flex flex-col justify-center mt-[-4vh] relative z-10">

        <!-- App Identity Logo (Floating directly above the login form) -->
        <div class="flex flex-col items-center mb-[-85px] relative z-10 pointer-events-none animate-on-load">
          <img src="{{ asset('images/asset-audit/21.png') }}" alt="E-Audit Logo"
            class="w-[400px] h-auto object-contain relative z-20">
        </div>

        <!-- Sign-in Subtitle -->
        <div class="text-center mb-2 relative z-30 animate-on-load">
          <p class="text-[#64748B] text-[15px] font-semibold tracking-wide">Please Sign-in to your Account</p>
        </div>

        <!-- Login Form Action -->
        <form class="space-y-4 relative z-30" autocomplete="off" method="POST" action="{{ route('login') }}" novalidate>
          @csrf

          <!-- Errors and Warning Messages -->
          @if (sizeof($errors) > 0)
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg animate-on-load">
              @foreach ($errors->all() as $error)
                <p class="text-red-600 text-xs font-medium mb-1 last:mb-0">{{ $error }}</p>
              @endforeach
            </div>
          @endif

          @if (session('warning'))
            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg animate-on-load">
              <p class="text-xs text-yellow-800 font-medium">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
              </p>
            </div>
          @endif

          @if (session('gagal'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
              Swal.fire({
                icon: 'error',
                title: '{{ session('gagal') }}',
                text: '{{ session('infogagal') }}',
              })
            </script>
          @endif

          <!-- Username (NIP) Input -->
          <div class="animate-on-load">
            <label for="nip" class="sr-only">NIP</label>
            <input type="text" name="nip" id="nip" placeholder="NIP" value="{{ old('nip') }}"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6e58f1] focus:border-transparent text-gray-700 placeholder-gray-400 text-sm transition-all duration-200 focus:shadow-[0_0_12px_rgba(110,88,241,0.15)]"
              required autofocus>
          </div>

          <!-- Password Input -->
          <div class="relative animate-on-load">
            <label for="password" class="sr-only">Password</label>
            <input type="password" name="password" id="password" placeholder="Password"
              class="w-full px-4 py-3 rounded-lg border border-gray-300 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-[#6e58f1] focus:border-transparent text-gray-700 placeholder-gray-400 text-sm transition-all duration-200 pr-12 focus:shadow-[0_0_12px_rgba(110,88,241,0.15)]"
              required autocomplete="new-password">
            <!-- Password toggle button -->
            <button type="button" id="toggle-password"
              class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-[#6e58f1] transition-all duration-200 hover:scale-110 active:scale-95 transform focus:outline-none"
              aria-label="Tampilkan password">
              <i class="fas fa-eye-slash text-base" id="eye-icon"></i>
            </button>
          </div>

          <!-- Development Notice -->
          <!-- <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg animate-on-load">
              <p class="text-xs text-blue-800 leading-normal">
                <strong>Info:</strong> Gunakan NIP dan password yang telah didaftarkan. Password tidak akan ditampilkan otomatis untuk keamanan.
              </p>
            </div> -->

          <!-- Submit Button -->
          <div class="animate-on-load mt-6">
            <button type="submit"
              class="w-full bg-[#6e58f1] hover:bg-[#5a44dc] text-white font-semibold py-3 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] text-sm focus:outline-none focus:ring-2 focus:ring-[#6e58f1] focus:ring-offset-2">
              Log in
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <script>
    // 1. Password Visibility Toggle
    const togglePasswordBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    togglePasswordBtn.addEventListener('click', function () {
      const isPassword = passwordInput.getAttribute('type') === 'password';
      const newType = isPassword ? 'text' : 'password';
      passwordInput.setAttribute('type', newType);

      if (isPassword) {
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      } else {
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      }
    });

    // 2. Auto-focus NIP field
    const nipInput = document.getElementById('nip');
    if (nipInput) {
      nipInput.focus();
    }

    // 3. Staggered Entrance Animation on Load (Safe Check for DOMContentLoaded)
    function startEntranceAnimation() {
      const items = document.querySelectorAll('.animate-on-load');
      items.forEach((item, index) => {
        setTimeout(() => {
          item.classList.add('is-visible');
        }, 80 + (index * 100)); // Stagger delay: 80ms delay, 100ms interval
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', startEntranceAnimation);
    } else {
      startEntranceAnimation(); // Jalankan langsung jika DOM sudah selesai dimuat
    }
  </script>
@endsection