# Panduan Integrasi Audit PCN ke ERP PLN

> File-file di folder ini adalah **template referensi** untuk tim ERP PLN.
> Tambahkan/modifikasi di repository ERP PLN, **bukan** di repository Audit PCN.

## Prasyarat

1. Repository ERP PLN sudah di-clone
2. Branch baru sudah dibuat: `git checkout -b feature/audit-integration`
3. File `config/erp.php` dan `app/Services/ERPTokenService.php` sudah ada di repo ERP (sesuai dokumentasi ERP)
4. `BasedController` sudah tersedia di `app/Http/Controllers/BasedController.php`

## Langkah Integrasi

### 1. Tambahkan Service Audit di `config/service-api.yaml`

Copy isi dari `config/service-api.yaml` di folder ini, lalu merge ke file `config/service-api.yaml` yang sudah ada di repo ERP.

### 2. Set Environment Variables

Tambahkan di file `.env` ERP:

```env
ERP_SHARED_SECRET=rahasia_shared_secret_erp_pln
```

> **PENTING**: Nilai `ERP_SHARED_SECRET` harus **identik** dengan yang ada di `.env` Audit PCN.

### 3. Tambahkan Controller Audit

Copy `controllers/AuditController.php` ke `app/Http/Controllers/Audit/AuditController.php` di repo ERP.

### 4. Tambahkan Routes

Tambahkan di `routes/web.php` ERP:

```php
use App\Http\Controllers\Audit\AuditController;

Route::prefix('audit')->name('audit.')->group(function () {
    Route::get('/',           [AuditController::class, 'dashboard'])->name('dashboard');
    Route::get('/perencanaan',[AuditController::class, 'perencanaan'])->name('perencanaan');
    Route::get('/pkpt',       [AuditController::class, 'pkpt'])->name('pkpt');
    Route::get('/pka',        [AuditController::class, 'pka'])->name('pka');
    Route::get('/walkthrough',[AuditController::class, 'walkthrough'])->name('walkthrough');
    Route::get('/tod-bpm',    [AuditController::class, 'todBpm'])->name('tod-bpm');
    Route::get('/toe',        [AuditController::class, 'toe'])->name('toe');
    Route::get('/entry-meeting',  [AuditController::class, 'entryMeeting'])->name('entry-meeting');
    Route::get('/exit-meeting',   [AuditController::class, 'exitMeeting'])->name('exit-meeting');
    Route::get('/pelaporan',      [AuditController::class, 'pelaporan'])->name('pelaporan');
    Route::get('/penutup-lha',    [AuditController::class, 'penutupLha'])->name('penutup-lha');
    Route::get('/pemantauan',     [AuditController::class, 'pemantauan'])->name('pemantauan');
    Route::get('/monitoring',     [AuditController::class, 'monitoring'])->name('monitoring');
    Route::get('/persetujuan',    [AuditController::class, 'persetujuan'])->name('persetujuan');
});
```

### 5. Tambahkan Menu Sidebar

Copy snippet dari `views/sidebar-audit.blade.php` ke `resources/views/layouts/sidebar.blade.php` ERP.

### 6. Tambahkan Blade Views

Copy isi folder `views/audit/` ke `resources/views/audit/` di repo ERP.

### 7. Tambahkan NIP ke Token Payload

**PENTING**: Di file `app/Services/ERPTokenService.php` ERP, tambahkan field `nip` ke payload token:

```php
$data = [
    'user_id'     => $user->id,
    'nip'         => $user->nip,       // ← TAMBAHKAN INI
    'name'        => $user->name,
    'email'       => $user->email,
    'roles'       => $user->getRoleNames()->toArray(),
    'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
    'domain'      => config('erp.domain'),
    'issued_at'   => time(),
    'expires_at'  => time() + $this->ttl,
];
```

Audit PCN akan mencocokkan `nip` ini dengan field `nip` di tabel `master_user` untuk menentukan role lokal.

### 8. Clear Cache

```bash
php artisan route:clear && php artisan config:clear
```

## Endpoint API Audit PCN

Base URL: `{AUDIT_PCN_URL}/api/audit`

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/health` | GET | Health check |
| `/dashboard/analitik` | GET | Dashboard analitik |
| `/perencanaan` | GET/POST | Perencanaan audit |
| `/pkpt` | GET/POST | Jadwal PKPT |
| `/pka` | GET/POST | Program Kerja Audit |
| `/walkthrough` | GET/POST | Walkthrough |
| `/tod-bpm` | GET/POST | TOD BPM |
| `/toe` | GET/POST | TOE |
| `/entry-meeting` | GET/POST | Entry Meeting |
| `/exit-meeting` | GET/POST | Exit Meeting |
| `/pelaporan-hasil-audit` | GET/POST | Pelaporan Hasil Audit |
| `/penutup-lha` | GET/POST | Penutup LHA |
| `/tindak-lanjut/pemantauan` | GET | Pemantauan |
| `/tindak-lanjut/monitoring` | GET | Monitoring |
| `/persetujuan` | GET/POST | Persetujuan |
| `/master/*` | GET | Master data (read-only) |
