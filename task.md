# Daftar Tugas Pengujian Menyeluruh (Comprehensive Testing Task List)

Daftar tugas ini merinci seluruh langkah pengujian otomatis yang akan diimplementasikan untuk memverifikasi fungsionalitas, keamanan, dan alur kerja aplikasi **Audit PCN**.

---

## ⚙️ Fase 1: Konfigurasi Lingkungan Pengujian
- [ ] Mengaktifkan konfigurasi database in-memory pada file [phpunit.xml](file:///c:/laragon/www/audit-pcn/phpunit.xml)
  - [ ] Uncomment line `<env name="DB_CONNECTION" value="sqlite"/>`
  - [ ] Uncomment line `<env name="DB_DATABASE" value=":memory:"/>`
- [ ] Menjalankan pengujian bawaan dengan `php artisan test` untuk memastikan environment sudah siap

---

## 🔐 Fase 2: Pengujian Autentikasi & Otorisasi (Security & RBAC)
- [ ] Membuat base test case helper untuk setup user berdasarkan role (`SUPER ADMIN`, `KSPI`, `ASMAN SPI`, `AUDITOR`, `AUDITEE`, `VIEW BOD`)
- [ ] Mengimplementasikan pengujian otorisasi dasar di `tests/Feature/Security/RoleAccessTest.php`:
  - [ ] Uji rute tamu (guest) harus selalu diarahkan ke halaman login
  - [ ] Uji validitas session check via endpoint `/check-session`
  - [ ] Uji hak akses menu Master Data (hanya dapat dimanipulasi oleh SUPER ADMIN, KSPI, ASMAN SPI)
  - [ ] Uji pembatasan rute transaksional audit agar menolak role AUDITEE dan VIEW BOD yang mencoba memanipulasi data (403 Forbidden)

---

## 📦 Fase 3: Pengujian CRUD Master Data
- [ ] Mengimplementasikan Feature Test CRUD untuk Master Kode AOI (`MasterKodeAoiTest.php`)
  - [ ] Uji aksesibilitas indeks, form tambah, form edit
  - [ ] Uji penyimpanan data valid & penolakan data tidak valid/kosong
  - [ ] Uji validasi kode unik AOI (mencegah duplikasi)
  - [ ] Uji pembaruan data & penghapusan data
- [ ] Mengimplementasikan Feature Test CRUD untuk Master Kode Risk (`MasterKodeRiskTest.php`)
  - [ ] Uji operasional CRUD dasar dan validasi kode risiko unik
- [ ] Mengimplementasikan Feature Test CRUD untuk Master Auditee (`MasterAuditeeTest.php`)
  - [ ] Uji pembuatan auditee beserta divisi/direktorat terkait
- [ ] Mengimplementasikan Feature Test CRUD untuk Master User (`MasterUserTest.php`)
  - [ ] Uji pembuatan user dengan hashing password otomatis & relasi role/akses

---

## 📑 Fase 4: Pengujian Alur Transaksi & Perencanaan Audit
- [ ] Mengimplementasikan pengujian modul Perencanaan Audit (`PerencanaanAuditTest.php`)
  - [ ] Uji pembuatan Surat Tugas Audit baru
  - [ ] Uji generate nomor surat tugas otomatis
  - [ ] Uji pemilihan auditor dan auditee
- [ ] Mengimplementasikan pengujian Jadwal PKPT (`JadwalPkptTest.php`)
  - [ ] Uji input rencana tahunan pengawasan
- [ ] Mengimplementasikan pengujian Program Kerja Audit (`ProgramKerjaAuditTest.php`)
  - [ ] Uji pembentukan hierarki PKA (Proses Bisnis -> Risiko -> Kontrol)
  - [ ] Uji pengunggahan dokumen pendukung PKA

---

## 🏃 Fase 5: Pengujian Pelaksanaan Audit & Evaluasi Lapangan
- [ ] Mengimplementasikan pengujian Entry & Exit Meeting (`MeetingAuditTest.php`)
  - [ ] Uji pencatatan rapat pembukaan (entry) & penutupan (exit) beserta upload dokumen
- [ ] Mengimplementasikan pengujian Walkthrough Audit (`WalkthroughAuditTest.php`)
  - [ ] Uji pencatatan walkthrough proses bisnis
- [ ] Mengimplementasikan pengujian TOD BPM & TOE (`TodToeAuditTest.php`)
  - [ ] Uji input evaluasi Test of Design (TOD) dan kecocokan desain kontrol
  - [ ] Uji pengisian lembar kerja Test of Effectiveness (TOE) untuk efektivitas kontrol

---

## ⚖️ Fase 6: Pengujian Alur Kerja Persetujuan (Approval Workflows)
- [ ] Mengimplementasikan pengujian persetujuan dokumen (`ApprovalWorkflowTest.php`)
  - [ ] Uji alur approval PKA: pengajuan (`pending`) -> disetujui (`approved`)
  - [ ] Uji alur penolakan (`rejected`) beserta pengisian alasan penolakan (`rejection_reason`)
  - [ ] Uji keamanan data: memastikan data yang sudah status `approved` tidak dapat di-edit atau dihapus kembali

---

## ✉️ Fase 7: Pengujian Pelaporan, Tindak Lanjut & Monitoring
- [ ] Mengimplementasikan pengujian Pelaporan LHA & Temuan (`PelaporanHasilAuditTest.php`)
  - [ ] Uji generate nomor LHA/LHK dan nomor ISS otomatis
  - [ ] Uji input temuan hasil audit beserta klasifikasi tingkat signifikansi dan analisis root cause
- [ ] Mengimplementasikan pengujian Tindak Lanjut oleh Auditee (`TindakLanjutTest.php`)
  - [ ] Uji pengisian respon/tanggapan dari pihak Auditee terhadap rekomendasi
  - [ ] Uji upload bukti perbaikan oleh Auditee
- [ ] Mengimplementasikan pengujian verifikasi status dan reminder (`MonitoringAuditTest.php`)
  - [ ] Uji verifikasi status tindak lanjut oleh tim SPI (selesai/belum selesai)
  - [ ] Uji pengiriman reminder notifikasi email (mocked menggunakan `Mail::fake()`)

---

## 🚀 Fase 8: Verifikasi & Laporan Akhir
- [ ] Jalankan seluruh suite test terintegrasi dengan perintah `php artisan test`
- [ ] Periksa dan pastikan cakupan pengujian (code coverage) sudah memadai
- [ ] Validasi database utama `spi-pcn` tetap steril (bebas dari data uji coba)
- [ ] Buat dokumentasi hasil akhir pengujian ([walkthrough.md](file:///C:/Users/amatr/.gemini/antigravity-ide/brain/53b91e97-fff1-4643-932c-64e2638a163e/walkthrough.md))
