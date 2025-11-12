@extends('layouts.auth', ['title' => 'Login'])

@section('content')
  <div class="absolute top-0 left-0 p-6">
    <img
      src="{{ asset('images/logo-pln.png') }}"
      alt="PLN Nusa Daya logo"
      class="w-72 h-52 object-contain"
    />
  </div>

  <div class="min-h-screen w-full flex flex-col md:flex-row">
    <div class="md:w-2/3 bg-[#f5f6f8] flex flex-col justify-center items-center p-6 md:p-12">
      <div class="w-full max-w-lg">
        <img
          src="{{ asset('images/jumbotron.png') }}"
          alt="Illustration of two workers in warehouse"
          class="w-full h-auto object-cover rounded-lg"
          width="450"
          height="200"
        />
      </div>
    </div>

    <div class="md:w-1/3 bg-white flex justify-center items-center p-6 md:p-10">
      <form class="bg-white w-full max-w-md" autocomplete="off" method="POST" action="{{ route('login') }}" novalidate >
        @csrf
        <div class="text-center mb-6">
          <div class="text-xs text-gray-600 font-normal mb-1 uppercase tracking-widest">
          Akurat, Unggul, inDenpenden, Integritas, dan kompeTen
          </div>
          <h1 class="text-gray-900 font-extrabold text-xl">
            E-AUDIT 
          </h1>
        </div>

        @if (sizeof($errors) > 0)
            @foreach ($errors->all() as $error)
                <p class="text-red-600 mb-3 text-sm">{{ $error }}</p>
            @endforeach
        @endif
        
        @if (session('warning'))
            <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 rounded-md">
                <p class="text-xs text-yellow-800">
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

        <label for="username" class="block text-xs font-normal text-gray-700 mb-1">Username</label>
        <input
          id="username"
          name="username"
          type="text"
          placeholder="Username"
          class="w-full rounded-md border border-gray-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 px-3 py-2 mb-5 text-gray-500 placeholder-gray-400 outline-none"
          required 
          value="{{ old('username') }}" 
          autofocus
        />

        <label for="password" class="block text-xs font-normal text-gray-700 mb-1">Password</label>
        <div class="relative mb-5">
          <input
            id="password"
            name="password"
            type="password"
            placeholder="..........."
            class="w-full rounded-md border border-gray-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 px-3 py-2 pr-10 text-gray-700 placeholder-gray-400 outline-none"
            required
            autocomplete="new-password"
          />
          <button
            type="button"
            id="togglePassword"
            aria-label="Toggle password visibility"
            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600"
            tabindex="-1"
          >
            <i class="fas fa-eye-slash"></i>
          </button>
        </div>
        
        <!-- Development Notice -->
        <div class="mb-4 p-3 bg-blue-100 border border-blue-400 rounded-md">
          <p class="text-xs text-blue-800">
            <strong>Info:</strong> Gunakan username dan password yang telah didaftarkan. Password tidak akan ditampilkan otomatis untuk keamanan.
          </p>
        </div>
        
        <button
          type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md py-3"
        >
         {{ __('Log in') }}
        </button>
      </form>
    </div>
  </div>
@endsection

@section('script')
<script>
  // Toggle password visibility
  document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    }
  });

  // Auto-focus username field
  document.getElementById('username').focus();
</script>
@endsection
