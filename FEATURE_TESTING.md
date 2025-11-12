# Panduan Testing Fitur Sinkronisasi Status Tindak Lanjut

## Persiapan Testing

### 1. Pastikan Database Siap
```bash
php artisan migrate
php artisan db:seed
```

### 2. Pastikan Data Test Tersedia
- Ada minimal 1 rekomendasi audit di tabel `penutup_lha_rekomendasi`
- Status approval rekomendasi adalah "approved"
- Ada data temuan audit yang terkait

## Test Case 1: Sinkronisasi Status Open → Closed

### Langkah Testing
1. **Buka Halaman Pemantauan**
   - URL: `http://127.0.0.1:8000/audit/pemantauan`
   - Catat status awal rekomendasi (seharusnya "Open")

2. **Buka Form Tindak Lanjut**
   - Klik tombol "Tindak Lanjut" pada rekomendasi
   - URL: `http://127.0.0.1:8000/audit/penutup-lha-rekomendasi/{id}/tindak-lanjut`

3. **Ubah Status ke Closed**
   - Pilih status "Closed" dari dropdown
   - Isi komentar: "Tindak lanjut telah selesai"
   - Upload file evidence (opsional)
   - Klik "Simpan Tindak Lanjut"

4. **Verifikasi Sinkronisasi**
   - Redirect ke halaman pemantauan
   - Status di tabel berubah menjadi "Closed" dengan badge hijau
   - Jumlah tindak lanjut bertambah

### Expected Result
- ✅ Status berubah dari "Open" ke "Closed"
- ✅ Badge berwarna hijau dengan ikon centang
- ✅ Informasi jumlah tindak lanjut terupdate
- ✅ Data tersimpan di database

## Test Case 2: Menambah Tindak Lanjut Setelah Status Closed

### Langkah Testing
1. **Buka Form Tindak Lanjut untuk Status Closed**
   - Klik tombol "Tindak Lanjut" pada rekomendasi dengan status "Closed"
   - Form tetap bisa dibuka

2. **Verifikasi Alert Informasi**
   - Alert biru muncul: "Status Saat Ini: CLOSED"
   - Pesan: "Meskipun status tindak lanjut sudah closed, Anda masih dapat menambahkan tindak lanjut baru"

3. **Isi Form Baru**
   - Status tetap bisa dipilih (open/on_progress/closed)
   - Komentar bisa diisi
   - File bisa diupload
   - Form bisa disubmit

4. **Verifikasi Data Tersimpan**
   - Tindak lanjut baru tersimpan
   - Status di tabel pemantauan terupdate sesuai tindak lanjut terbaru

### Expected Result
- ✅ Form tetap aktif meski status closed
- ✅ Alert informasi yang jelas
- ✅ Data tersimpan dengan benar
- ✅ Status terupdate sesuai tindak lanjut terbaru

## Test Case 3: Update Status Existing Tindak Lanjut

### Langkah Testing
1. **Edit Tindak Lanjut yang Ada**
   - Buka halaman view tindak lanjut
   - Klik tombol edit pada tindak lanjut tertentu

2. **Ubah Status**
   - Ganti status dari "Closed" ke "On Progress"
   - Update komentar
   - Simpan perubahan

3. **Verifikasi Update**
   - Status di tabel pemantauan berubah menjadi "On Progress"
   - Badge berwarna biru dengan ikon jam
   - Data tersimpan dengan benar

### Expected Result
- ✅ Status berubah sesuai update
- ✅ Badge dan ikon terupdate
- ✅ Data tersimpan dengan benar
- ✅ Konsistensi antara form dan tabel

## Test Case 4: Visual Status dan Badge

### Langkah Testing
1. **Buka Halaman Pemantauan**
   - Lihat semua status yang tersedia
   - Verifikasi warna dan ikon badge

2. **Verifikasi Badge Status**
   - **Open**: Badge kuning dengan ikon alert
   - **On Progress**: Badge biru dengan ikon jam
   - **Closed**: Badge hijau dengan ikon centang

3. **Verifikasi Informasi Tambahan**
   - Jumlah tindak lanjut ditampilkan
   - Tooltip pada tombol tindak lanjut untuk status closed

### Expected Result
- ✅ Badge dengan warna yang sesuai
- ✅ Ikon yang relevan dengan status
- ✅ Informasi jumlah tindak lanjut
- ✅ Tooltip yang informatif

## Test Case 5: Error Handling dan Validasi

### Langkah Testing
1. **Test Validasi Komentar Kosong**
   - Buka form tindak lanjut
   - Kosongkan semua komentar
   - Coba submit form

2. **Test File Upload Terlalu Besar**
   - Upload file > 2MB
   - Verifikasi error message

3. **Test Status Invalid**
   - Coba input status yang tidak valid
   - Verifikasi validasi

### Expected Result
- ✅ Error message untuk komentar kosong
- ✅ Error message untuk file terlalu besar
- ✅ Validasi status yang tepat
- ✅ Form tidak bisa disubmit jika ada error

## Test Case 6: Database Consistency

### Langkah Testing
1. **Verifikasi Foreign Key**
   - Cek relasi antara tabel
   - Pastikan cascade delete berfungsi

2. **Verifikasi Data Integrity**
   - Cek data tersimpan dengan benar
   - Verifikasi timestamp dan user yang melakukan perubahan

### Expected Result
- ✅ Foreign key constraints berfungsi
- ✅ Data tersimpan dengan format yang benar
- ✅ Timestamp dan user tracking berfungsi

## Checklist Testing

### Functional Testing
- [ ] Status berubah sesuai input user
- [ ] Sinkronisasi antara form dan tabel
- [ ] Form tetap aktif meski status closed
- [ ] Validasi input berfungsi
- [ ] File upload berfungsi
- [ ] Error handling berfungsi

### UI/UX Testing
- [ ] Badge warna sesuai status
- [ ] Ikon relevan dengan status
- [ ] Alert informasi yang jelas
- [ ] Tooltip berfungsi
- [ ] Responsive design

### Database Testing
- [ ] Data tersimpan dengan benar
- [ ] Foreign key constraints
- [ ] Cascade operations
- [ ] Data consistency

### Integration Testing
- [ ] Controller methods berfungsi
- [ ] View rendering
- [ ] Route accessibility
- [ ] Session handling

## Troubleshooting Testing

### Issue: Status Tidak Berubah
**Checklist:**
- [ ] Database connection
- [ ] Migration status
- [ ] Cache browser
- [ ] Session data

### Issue: Form Tidak Bisa Submit
**Checklist:**
- [ ] JavaScript errors
- [ ] CSRF token
- [ ] Validation rules
- [ ] Required fields

### Issue: File Upload Gagal
**Checklist:**
- [ ] Storage permissions
- [ ] File size limits
- [ ] File type validation
- [ ] Disk space

## Performance Testing

### Load Testing
- Test dengan multiple concurrent users
- Monitor response time
- Check memory usage

### Database Performance
- Monitor query execution time
- Check index usage
- Monitor connection pool

## Security Testing

### Input Validation
- Test SQL injection
- Test XSS attacks
- Test file upload security

### Access Control
- Test unauthorized access
- Test role-based permissions
- Test session security

## Reporting Testing Results

### Template Report
```
Test Case: [Nama Test Case]
Status: [Pass/Fail]
Date: [Tanggal Testing]
Tester: [Nama Tester]
Environment: [Local/Staging/Production]
Notes: [Catatan khusus]
```

### Metrics
- Total test cases: X
- Passed: X
- Failed: X
- Success rate: X%
- Execution time: X minutes

## Kesimpulan Testing

Setelah semua test case selesai, fitur sinkronisasi status tindak lanjut seharusnya:
- ✅ Berfungsi sesuai requirement
- ✅ UI/UX yang user-friendly
- ✅ Database yang konsisten
- ✅ Error handling yang robust
- ✅ Performance yang acceptable
- ✅ Security yang adequate

Jika ada test case yang fail, dokumentasikan issue dan buat bug report untuk perbaikan.
