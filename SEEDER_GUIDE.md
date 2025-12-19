# 📚 Database Seeder Guide

## Master Data Seeder

File ini berisi panduan lengkap untuk menjalankan seeder database, khususnya untuk master data.

---

## 🎯 Master Data Seeder

### Daftar Master Data Tables:
1. **master_kode_aoi** - Kode Area of Interest
2. **master_kode_risk** - Kode Risk/Resiko
3. **master_auditee** - Data Auditee (Divisi/Cabang)
4. **master_akses_user** - Role/Akses User
5. **master_user** - Data User
6. **master_jenis_audit** - Jenis Audit

---

## 🚀 Cara Menjalankan Seeder

### 1. Seed HANYA Master Data
Jalankan command berikut untuk seed master data saja:

```bash
php artisan db:seed --class=MasterDataSeeder
```

**Output yang diharapkan:**
```
🌱 Starting Master Data Seeding...

📊 Seeding Master Kode AOI...
✅ Master Kode AOI seeded successfully!

📊 Seeding Master Kode Risk...
✅ Master Kode Risk seeded successfully!

📊 Seeding Master Auditee...
✅ Master Auditee seeded successfully!

📊 Seeding Master Akses User...
✅ Master Akses User seeded successfully!

📊 Seeding Master User...
✅ Master User seeded successfully!

📊 Seeding Master Jenis Audit...
✅ Master Jenis Audit seeded successfully!

🎉 All Master Data seeded successfully!
```

---

### 2. Seed SEMUA Data (Master + Transactional)
Jika ingin seed semua data termasuk data transaksi audit:

```bash
php artisan db:seed
```

atau

```bash
php artisan db:seed --class=DatabaseSeeder
```

---

### 3. Fresh Migration + Seed Master Data Only
Reset database dan seed ulang hanya master data:

```bash
php artisan migrate:fresh --seed --seeder=MasterDataSeeder
```

---

### 4. Fresh Migration + Seed Semua Data
Reset database dan seed ulang semua data:

```bash
php artisan migrate:fresh --seed
```

---

## 📋 Seed Individual Master Data

Jika hanya ingin seed satu master data tertentu:

### Master Kode AOI
```bash
php artisan db:seed --class=MasterKodeAoiSeeder
```

### Master Kode Risk
```bash
php artisan db:seed --class=MasterKodeRiskSeeder
```

### Master Auditee
```bash
php artisan db:seed --class=MasterAuditeeSeeder
```

### Master Akses User
```bash
php artisan db:seed --class=MasterAksesUserSeeder
```

### Master User
```bash
php artisan db:seed --class=MasterUserSeeder
```

### Master Jenis Audit
```bash
php artisan db:seed --class=MasterJenisAuditSeeder
```

---

## ⚠️ Catatan Penting

### Urutan Seeding (Dependencies)
Master data harus di-seed dengan urutan yang benar karena ada dependency:

1. ✅ **MasterKodeAoiSeeder** - Independen
2. ✅ **MasterKodeRiskSeeder** - Independen
3. ✅ **MasterAuditeeSeeder** - Independen
4. ✅ **MasterAksesUserSeeder** - Independen
5. ⚠️ **MasterUserSeeder** - Bergantung pada:
   - master_akses_user (untuk role)
   - master_auditee (untuk assignment divisi/cabang)
6. ✅ **MasterJenisAuditSeeder** - Independen

**MasterDataSeeder sudah mengatur urutan yang benar secara otomatis!**

---

## 🔄 Rollback & Re-seed

### Rollback 1 migration terakhir
```bash
php artisan migrate:rollback
```

### Rollback semua migrations
```bash
php artisan migrate:reset
```

### Fresh start (drop all tables + migrate + seed)
```bash
php artisan migrate:fresh --seed --seeder=MasterDataSeeder
```

---

## 🧪 Testing Seeder

Untuk memastikan seeder berjalan dengan baik:

```bash
# Check jumlah record di setiap master table
php artisan tinker

# Di tinker console:
>>> DB::table('master_kode_aoi')->count()
>>> DB::table('master_kode_risk')->count()
>>> DB::table('master_auditee')->count()
>>> DB::table('master_akses_user')->count()
>>> DB::table('master_user')->count()
>>> DB::table('master_jenis_audit')->count()
```

---

## 📦 Production Seeding

Untuk production, sebaiknya HANYA seed master data:

```bash
php artisan migrate --force
php artisan db:seed --class=MasterDataSeeder --force
```

**⚠️ JANGAN seed data transaksi di production!**

---

## 🆘 Troubleshooting

### Error: "Class MasterDataSeeder does not exist"
```bash
# Clear cache dan regenerate autoload
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Error: Foreign key constraint
Pastikan seed dengan urutan yang benar. Gunakan `MasterDataSeeder` yang sudah mengatur urutan otomatis.

### Error: Duplicate entry
```bash
# Truncate tables sebelum seed ulang
php artisan migrate:fresh --seed --seeder=MasterDataSeeder
```

---

## 📞 Support

Jika ada masalah dengan seeder, silakan hubungi tim development.

---

**Last Updated:** 2025
**Version:** 1.0.0
