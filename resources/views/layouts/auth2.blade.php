<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PLN Nusa Daya - Asset Tracking Platform Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet"
  />
  <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap"
    rel="stylesheet"
  />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      overflow: hidden;
    }
  </style>
</head>
<body>

 @yield('login')
    <script>
    // Pilih elemen tombol dan input password
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const icon = togglePassword.querySelector('i');

    // Tambahkan event listener untuk klik pada tombol
    togglePassword.addEventListener('click', function (e) {
      // Toggle tipe input antara 'password' dan 'text'
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);

      // Toggle ikon mata
      icon.classList.toggle('fa-eye-slash');
      icon.classList.toggle('fa-eye');
    });
  </script>

</body>
</html>
