<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Sistem Informasi Satuan Pengawasan Internal (SPI) - PT PLN Nusa Daya" />
  <title>{{ $title ?? 'Login' }} - Audit SPI PT PLN Nusa Daya</title>
  <link rel="shortcut icon" href="/images/logo-pln.png">
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet"
  />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
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

 @yield('content')
    <script>
    // Pilih elemen tombol dan input password (jika ada pada child view)
    const togglePassword = document.querySelector('#togglePassword') || document.querySelector('#toggle-password');
    const password = document.querySelector('#password');
    if (togglePassword && password) {
      const icon = togglePassword.querySelector('i');
      togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        if (icon) {
          icon.classList.toggle('fa-eye-slash');
          icon.classList.toggle('fa-eye');
        }
      });
    }
  </script>

</body>
</html>
