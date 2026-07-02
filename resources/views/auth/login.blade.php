@extends('layouts.auth', ['title' => 'Login'])

@section('content')
  <style>
    /* =========================================
           ANIMATION DEFINITIONS & DESIGN SYSTEM
           ========================================= */
    :root {
      --ease-out-expo: cubic-bezier(0.16, 1, 0.3, 1);
      --ease-out-quint: cubic-bezier(0.22, 1, 0.36, 1);
      --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      
      /* Type scale (Modular Ramp) */
      --text-xs: 0.8125rem;  /* 13px */
      --text-sm: 0.875rem;   /* 14px */
      --text-base: 1rem;     /* 16px */
      --text-lg: 1.125rem;   /* 18px */
      --text-xl: 1.375rem;   /* 22px */

      /* Custom Brand Theme Colors (Extracted from Gennext Audit Logo) */
      --color-brand-primary: #5800ff;   /* Vibrant Purple */
      --color-brand-hover: #4700d6;     /* Darker Purple */
      --color-brand-accent: #00d7ff;    /* Vivid Cyan */
    }

    /* Keyframes for entrance animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes revealUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes revealLeft {
      from {
        opacity: 0;
        transform: translateX(-40px);
      }

      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes floatAnimation {
      0% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }

      100% {
        transform: translateY(0);
      }
    }

    @keyframes slideDownFade {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Entrance Animation Classes */
    .animate-reveal-up {
      opacity: 0;
      animation: revealUp 0.8s var(--ease-out-expo) forwards;
    }

    .animate-reveal-left {
      opacity: 0;
      animation: revealLeft 1s var(--ease-out-expo) forwards;
    }

    .animate-fade-in {
      opacity: 0;
      animation: fadeIn 0.8s var(--ease-out-expo) forwards;
    }

    /* Stagger Delays */
    .stagger-1 {
      animation-delay: 100ms;
    }

    .stagger-2 {
      animation-delay: 200ms;
    }

    .stagger-3 {
      animation-delay: 300ms;
    }

    .stagger-4 {
      animation-delay: 400ms;
    }

    .stagger-5 {
      animation-delay: 500ms;
    }

    .stagger-6 {
      animation-delay: 600ms;
    }

    .stagger-7 {
      animation-delay: 700ms;
    }

    /* Accessibility reduced motion support */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
    }

    /* =========================================
       CORE LAYOUT
       ========================================= */
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
      font-family: var(--font-sans); 
    }
    
    body { 
      height: 100vh; 
      overflow: hidden; 
      background-color: #fff; 
    }

    .split-screen { 
      display: flex; 
      width: 100vw; 
      height: 100vh; 
    }

    /* =========================================
           BAGIAN KIRI (Ilustrasi & Branding)
           ========================================= */
    .left-pane {
      width: 65%;
      flex: none;
      background: #f8faff url('/image/wvsw-gb.png') bottom center no-repeat;
      background-size: cover;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      border-right: 1px solid #eaeaea;
      overflow: hidden;
    }

    .logos-top {
      position: absolute;
      top: 30px;
      width: 90%;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logos-top .logo-danantara {
      height: 40px;
      object-fit: contain;
    }

    .logos-top .logo-pln {
      height: 53px;
      object-fit: contain;
    }

    .main-illustration-wrapper {
      width: 60%;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 10;
      margin-top: 5px;
    }

    .main-illustration {
      width: 100%;
      max-height: 86vh;
      object-fit: contain;
      animation: floatAnimation 6s ease-in-out infinite;
    }

    /* =========================================
           BAGIAN KANAN (Form Login)
           ========================================= */
    .right-pane {
      width: 35%;
      flex: none;
      background: #ffffff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .logo-pln-mobile {
      display: none;
    }

    .logo-aku-jago {
      position: absolute;
      top: 20px;
      right: 30px;
    }

    .logo-aku-jago img {
      height: 50px;
      object-fit: contain;
    }

    /* Styling Form Area */
    .login-container {
      text-align: center;
      width: 70%;
      max-width: 400px;
    }

    .audit-logo {
      display: block;
      margin: 0 auto 15px auto;
      width: 200px;
      height: auto;
    }

    .login-container h2 {
      font-size: var(--text-xl);
      font-weight: 700;
      color: var(--color-brand-primary);
      letter-spacing: 0.05em;
      line-height: 1.2;
      margin-bottom: 0.25rem;
    }

    .login-container h3 {
      font-size: var(--text-sm);
      font-weight: 500;
      color: #475569;
      letter-spacing: 0.075em;
      margin-bottom: 2.2rem;
      text-transform: uppercase;
    }

    .login-container p {
      font-size: var(--text-sm);
      font-weight: 400;
      color: #64748B;
      margin-bottom: 1.25rem;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .form-group {
      width: 78%;
      align-self: center;
      display: flex;
      flex-direction: column;
    }

    input {
      padding: 12px 20px;
      border: 3px solid #161616;
      border-radius: 25px;
      font-size: var(--text-sm);
      font-weight: 500;
      color: #1e293b;
      outline: none;
      width: 100%;
      transition: border-color 0.25s var(--ease-out-quint);
    }

    input:focus {
      border-color: var(--color-brand-primary);
    }

    input::placeholder {
      color: #94a3b8;
    }

    .password-wrapper {
      position: relative;
      width: 78%;
      align-self: center;
      display: flex;
    }

    .password-wrapper input {
      width: 100%;
      padding-right: 45px;
    }

    .toggle-password-btn {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #777;
      outline: none;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: color 0.2s var(--ease-out-quint), transform 0.2s var(--ease-out-quint);
    }

    .toggle-password-btn:hover {
      color: var(--color-brand-primary);
      transform: translateY(-50%) scale(1.15);
    }

    .toggle-password-btn:active {
      transform: translateY(-50%) scale(0.95);
    }

    .forgot-pwd {
      font-size: var(--text-xs);
      color: #64748B;
      font-weight: 500;
      text-decoration: none;
      align-self: center;
      margin-top: -5px;
      transition: color 0.2s var(--ease-out-quint), transform 0.2s var(--ease-out-quint);
    }

    .forgot-pwd:hover {
      color: var(--color-brand-primary);
      transform: translateY(-1px);
    }

    .btn-login {
      background-color: var(--color-brand-primary);
      color: white;
      padding: 12px;
      border: none;
      border-radius: 25px;
      font-size: var(--text-sm);
      font-weight: 700;
      cursor: pointer;
      width: 140px;
      align-self: center;
      margin-top: 10px;
      letter-spacing: 0.025em;
      transition: background-color 0.25s var(--ease-out-quint), transform 0.2s var(--ease-out-quint);
    }

    .btn-login:hover {
      background-color: var(--color-brand-hover);
      transform: translateY(-2px);
    }

    .btn-login:active {
      transform: translateY(0) scale(0.97);
    }

    /* Alerts styling */
    .alert-container {
      width: 78%;
      align-self: center;
      margin-bottom: 5px;
      text-align: left;
      animation: slideDownFade 0.4s var(--ease-out-quint) forwards;
    }

    .alert-danger {
      background-color: #fde8e8;
      border: 1px solid #f8b4b4;
      color: #9b1c1c;
      padding: 10px 15px;
      border-radius: 8px;
      font-size: var(--text-xs);
      font-weight: 500;
    }

    .alert-warning {
      background-color: #fef3c7;
      border: 1px solid #fde68a;
      color: #92400e;
      padding: 10px 15px;
      border-radius: 8px;
      font-size: var(--text-xs);
      font-weight: 500;
    }

    .alert-danger p,
    .alert-warning p {
      margin: 3px 0;
    }

    /* Responsive Support */
    @media (max-width: 768px) {
      .left-pane {
        display: none;
      }

      .right-pane {
        width: 100%;
      }

      .login-container {
        width: 85%;
      }

      .logo-pln-mobile {
        display: block;
        position: absolute;
        top: 20px;
        left: 30px;
      }

      .logo-pln-mobile img {
        height: 40px;
        object-fit: contain;
      }
    }
  </style>

  <div class="split-screen">
    <!-- LEFT PANEL -->
    <div class="left-pane">
      <div class="logos-top">
        <img src="/image/danantara-logo.png" alt="Danantara" class="logo-danantara">
        <img src="/image/pln putih-logo.png" alt="PLN Nusa Daya" class="logo-pln">
      </div>
      <div class="main-illustration-wrapper">
        <img src="/image/mn-logo.png" alt="Ilustrasi Audit" class="main-illustration">
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-pane">
      <div class="logo-pln-mobile">
        <img src="/image/pln-logo.png" alt="PLN Nusa Daya">
      </div>

      <div class="logo-aku-jago">
        <img src="/image/akujago-logo.png" alt="Aku Jago">
      </div>

      <div class="login-container">
        <img src="/image/gennext-logo.png" alt="Audit Logo" class="audit-logo">
        <h2>SATUAN PENGAWASAN INTERNAL</h2>
        <h3>NUSA DAYA EXCELLENCE TOOLS</h3>

        <p>Please Sign-in to your Account</p>

        <form autocomplete="off" method="POST" action="{{ route('login') }}" novalidate>
          @csrf

          <!-- Errors and Warning Messages -->
          @if (sizeof($errors) > 0)
            <div class="alert-container">
              <div class="alert-danger">
                @foreach ($errors->all() as $error)
                  <p>{{ $error }}</p>
                @endforeach
              </div>
            </div>
          @endif

          @if (session('warning'))
            <div class="alert-container">
              <div class="alert-warning">
                <p>
                  <i class="fas fa-exclamation-triangle"></i>
                  {{ session('warning') }}
                </p>
              </div>
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
          <div class="form-group animate-reveal-up stagger-1">
            <input type="text" name="nip" id="nip" placeholder="Username" value="{{ old('nip') }}" required autofocus>
          </div>

          <!-- Password Input with toggle button -->
          <div class="password-wrapper animate-reveal-up stagger-2">
            <input type="password" name="password" id="password" placeholder="Password" required
              autocomplete="current-password">
            <button type="button" id="toggle-password" class="toggle-password-btn" aria-label="Tampilkan password">
              <i class="fas fa-eye-slash" id="eye-icon"></i>
            </button>
          </div>

          <a href="#" class="forgot-pwd animate-reveal-up stagger-3">Forgot Password?</a>
          <button type="submit" class="btn-login animate-reveal-up stagger-3">Login</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // 1. Password Visibility Toggle
    const togglePasswordBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (togglePasswordBtn && passwordInput && eyeIcon) {
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
    }

    // 2. Auto-focus NIP field
    const nipInput = document.getElementById('nip');
    if (nipInput) {
      nipInput.focus();
    }
  </script>
@endsection