<!-- bundle -->
<script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-approve-swal').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = btn.closest('form');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Approve Jadwal?',
                        text: 'Yakin ingin approve jadwal ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Approve',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Yakin ingin approve jadwal ini?')) {
                        form.submit();
                    }
                }
            });
        });
    });
</script>
<!-- App js -->
@yield('script-bottom')
