# Database Seeders

## 🎯 Quick Commands

### Seed Master Data Only
```bash
php artisan db:seed --class=MasterDataSeeder
```

### Seed All Data (Master + Transactions)
```bash
php artisan db:seed
```

### Fresh Migration + Master Data
```bash
php artisan migrate:fresh --seed --seeder=MasterDataSeeder
```

### Fresh Migration + All Data
```bash
php artisan migrate:fresh --seed
```

---

## 📁 Master Data Seeders

| Seeder Class | Table | Description |
|-------------|-------|-------------|
| `MasterKodeAoiSeeder` | master_kode_aoi | Area of Interest codes |
| `MasterKodeRiskSeeder` | master_kode_risk | Risk codes |
| `MasterAuditeeSeeder` | master_auditee | Auditee (divisions/branches) |
| `MasterAksesUserSeeder` | master_akses_user | User roles/access levels |
| `MasterUserSeeder` | master_user | System users |
| `MasterJenisAuditSeeder` | master_jenis_audit | Audit types |

---

## 📊 Transactional Seeders

| Seeder Class | Table | Dependencies |
|-------------|-------|--------------|
| `PerencanaanAuditSeeder` | perencanaan_audit | master_auditee |
| `ProgramKerjaAuditSeeder` | program_kerja_audit | perencanaan_audit |
| `PelaporanHasilAuditSeeder` | pelaporan_hasil_audit | perencanaan_audit |
| ... and more | ... | ... |

---

## ⚙️ Custom Seeder: MasterDataSeeder

The `MasterDataSeeder` class seeds all master data tables in the correct order.

**Benefits:**
- ✅ Seeds only master data (no transactional data)
- ✅ Handles dependencies automatically
- ✅ Shows progress with colored output
- ✅ Displays summary table at the end
- ✅ Perfect for development and production setup

---

## 📖 Full Documentation

See [SEEDER_GUIDE.md](../../SEEDER_GUIDE.md) for complete documentation.

---

**Created:** December 2025
