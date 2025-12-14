-- Add jenis_audit_id column to pelaporan_hasil_audit table
ALTER TABLE `pelaporan_hasil_audit` 
ADD COLUMN `jenis_audit_id` BIGINT UNSIGNED NULL AFTER `kode_spi`;

-- Add foreign key constraint
ALTER TABLE `pelaporan_hasil_audit` 
ADD CONSTRAINT `pelaporan_hasil_audit_jenis_audit_id_foreign` 
FOREIGN KEY (`jenis_audit_id`) REFERENCES `master_jenis_audit` (`id`) ON DELETE SET NULL;

