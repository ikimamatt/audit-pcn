<form action="{{ route('audit.tod-bpm-evaluasi.store') }}" method="POST">
    @csrf
    <input type="hidden" name="tod_bpm_audit_id" value="{{ $bpm->id }}">
    <div class="mb-3">
        <label for="hasil_evaluasi" class="form-label">Tambah Hasil Evaluasi BPM</label>
        <textarea name="hasil_evaluasi" id="hasil_evaluasi" class="form-control" rows="2" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Tambah Evaluasi</button>
</form>
<hr>
<h5>Daftar Hasil Evaluasi</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Hasil Evaluasi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bpm->evaluasi as $i => $ev)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>
                @if(request('edit') == $ev->id)
                    <form action="{{ route('audit.tod-bpm-evaluasi.update', $ev->id) }}" method="POST" style="display:inline-block">
                        @csrf @method('PUT')
                        <textarea name="hasil_evaluasi" class="form-control" rows="2" required>{{ $ev->hasil_evaluasi }}</textarea>
                        <button type="submit" class="btn btn-success btn-sm mt-1">Simpan</button>
                        <button type="button" class="btn btn-secondary btn-sm mt-1" onclick="window.location.reload()">Batal</button>
                    </form>
                @else
                    {{ $ev->hasil_evaluasi }}
                @endif
            </td>
            <td>
                <form action="{{ route('audit.tod-bpm-evaluasi.update', $ev->id) }}" method="GET" style="display:inline-block">
                    <input type="hidden" name="edit" value="{{ $ev->id }}">
                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.href='?edit={{ $ev->id }}'">Edit</button>
                </form>
                <form action="{{ route('audit.tod-bpm-evaluasi.destroy', $ev->id) }}" method="POST" style="display:inline-block" class="delete-form">
                    @csrf @method('DELETE')
                    <input type="hidden" name="tod_bpm_audit_id" value="{{ $bpm->id }}">
                    <button type="submit" class="btn btn-danger btn-sm btn-delete-swal">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('#evaluasi-modal-content form').forEach(function(form) {
    form.addEventListener('submit', function() {
        setTimeout(function() { window.location.reload(); }, 500);
    });
});

// Delete confirmation for evaluasi
document.querySelectorAll('.btn-delete-swal').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const form = btn.closest('form');

        Swal.fire({
            title: 'Hapus Evaluasi?',
            text: 'Yakin ingin menghapus evaluasi ini?',
            icon: 'warning',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script> 