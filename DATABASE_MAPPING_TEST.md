# Test Mapping Database Status Tindak Lanjut

## Overview
Dokumen ini memverifikasi bahwa mapping antara form input dan database untuk status tindak lanjut berfungsi dengan benar.

## Mapping yang Diharapkan

### Form Input → Database Value
- **"Open"** → `open` (string)
- **"On Progress"** → `on_progress` (string)  
- **"Closed"** → `closed` (string)

### Database Value → Display
- `open` → **"Open"** (badge kuning)
- `on_progress` → **"On Progress"** (badge biru)
- `closed` → **"Closed"** (badge hijau)

## Test Case: Status Closed

### 1. Buka Form Tindak Lanjut
- URL: `http://127.0.0.1:8000/audit/penutup-lha-rekomendasi/{id}/tindak-lanjut`
- Dropdown menampilkan status saat ini (sesuai database)

### 2. Pilih Status "Closed"
- Klik dropdown "Status Tindak Lanjut"
- Pilih opsi "Closed"
- Value yang dikirim: `closed`

### 3. Submit Form
- Isi komentar minimal
- Klik "Simpan Tindak Lanjut"
- Data tersimpan dengan `status_tindak_lanjut = 'closed'`

### 4. Verifikasi Database
```sql
-- Cek tabel tindak_lanjut
SELECT status_tindak_lanjut FROM penutup_lha_tindak_lanjut 
WHERE penutup_lha_rekomendasi_id = {id} 
ORDER BY created_at DESC LIMIT 1;
-- Expected: closed

-- Cek tabel rekomendasi utama
SELECT status_tindak_lanjut FROM penutup_lha_rekomendasi 
WHERE id = {id};
-- Expected: closed
```

### 5. Verifikasi Display
- Halaman pemantauan menampilkan badge hijau "Closed"
- Form tindak lanjut menampilkan status "Closed" sebagai selected

## Test Case: Status On Progress

### 1. Pilih Status "On Progress"
- Value yang dikirim: `on_progress`

### 2. Verifikasi Database
```sql
SELECT status_tindak_lanjut FROM penutup_lha_tindak_lanjut 
WHERE penutup_lha_rekomendasi_id = {id} 
ORDER BY created_at DESC LIMIT 1;
-- Expected: on_progress
```

### 3. Verifikasi Display
- Badge biru "On Progress"
- Dropdown menampilkan "On Progress" sebagai selected

## Test Case: Status Open

### 1. Pilih Status "Open"
- Value yang dikirim: `open`

### 2. Verifikasi Database
```sql
SELECT status_tindak_lanjut FROM penutup_lha_tindak_lanjut 
WHERE penutup_lha_rekomendasi_id = {id} 
ORDER BY created_at DESC LIMIT 1;
-- Expected: open
```

### 3. Verifikasi Display
- Badge kuning "Open"
- Dropdown menampilkan "Open" sebagai selected

## Struktur Database

### Tabel `penutup_lha_tindak_lanjut`
```sql
CREATE TABLE penutup_lha_tindak_lanjut (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    penutup_lha_rekomendasi_id bigint unsigned NOT NULL,
    real_waktu date NULL,
    komentar text NULL,
    file_eviden varchar(255) NULL,
    status_tindak_lanjut enum('open','on_progress','closed') DEFAULT 'open',
    created_at timestamp NULL,
    updated_at timestamp NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (penutup_lha_rekomendasi_id) REFERENCES penutup_lha_rekomendasi(id) ON DELETE CASCADE
);
```

### Tabel `penutup_lha_rekomendasi`
```sql
CREATE TABLE penutup_lha_rekomendasi (
    id bigint unsigned NOT NULL AUTO_INCREMENT,
    -- ... other fields ...
    status_tindak_lanjut enum('open','on_progress','closed') DEFAULT 'open',
    -- ... other fields ...
    PRIMARY KEY (id)
);
```

## Validation Rules

### Controller Validation
```php
'status_tindak_lanjut' => 'nullable|in:open,on_progress,closed'
```

### Database Constraints
- ENUM values: `'open'`, `'on_progress'`, `'closed'`
- Default value: `'open'`
- NOT NULL constraint

## Expected Behavior

### 1. Form Submission
- User pilih "Closed" → `closed` tersimpan di database
- User pilih "On Progress" → `on_progress` tersimpan di database
- User pilih "Open" → `open` tersimpan di database

### 2. Status Update
- Status di tabel rekomendasi utama terupdate sesuai tindak lanjut terbaru
- Konsistensi antara kedua tabel terjaga

### 3. Display Consistency
- Badge warna sesuai status
- Dropdown menampilkan status yang benar
- Informasi status terbaru selalu akurat

## Troubleshooting

### Issue: Status Tidak Tersimpan
**Checklist:**
- [ ] Form validation berhasil
- [ ] Database connection aktif
- [ ] Foreign key constraint valid
- [ ] ENUM value sesuai

### Issue: Status Tidak Terupdate
**Checklist:**
- [ ] Tindak lanjut berhasil dibuat
- [ ] Update query berhasil
- [ ] Cache browser di-clear
- [ ] Session data valid

### Issue: Display Tidak Konsisten
**Checklist:**
- [ ] Data di database benar
- [ ] View logic benar
- [ ] JavaScript tidak error
- [ ] CSS loading sempurna

## Test Results Template

```
Test Case: [Nama Test Case]
Date: [Tanggal]
Status: [Pass/Fail]
Database Value: [Value yang tersimpan]
Display Value: [Value yang ditampilkan]
Notes: [Catatan khusus]
```

## Kesimpulan

Mapping database untuk status tindak lanjut harus:
- ✅ **Konsisten**: Form input → Database → Display
- ✅ **Valid**: Hanya nilai ENUM yang diizinkan
- ✅ **Real-time**: Update langsung tanpa refresh
- ✅ **Persistent**: Data tersimpan dengan benar
- ✅ **User-friendly**: Interface yang intuitif

Jika semua test case pass, maka fitur sinkronisasi status tindak lanjut berfungsi dengan sempurna.
