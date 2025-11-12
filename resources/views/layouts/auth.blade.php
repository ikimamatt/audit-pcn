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

 @yield('content')
    <script>
    // Pilih elemen tombol dan input password
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    if (togglePassword && password) {
      const icon = togglePassword.querySelector('i');
      togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        icon.classList.toggle('fa-eye-slash');
        icon.classList.toggle('fa-eye');
      });
    }
  </script>

</body>
</html>
