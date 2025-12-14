<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddJenisAuditIdColumn extends Command
{
    protected $signature = 'migrate:add-jenis-audit-id';
    protected $description = 'Add jenis_audit_id column to pelaporan_hasil_audit table';

    public function handle()
    {
        try {
            // Check if column already exists
            if (Schema::hasColumn('pelaporan_hasil_audit', 'jenis_audit_id')) {
                $this->info('Column jenis_audit_id already exists!');
                return;
            }

            // Add column
            DB::statement('ALTER TABLE pelaporan_hasil_audit ADD COLUMN jenis_audit_id BIGINT UNSIGNED NULL AFTER kode_spi');
            $this->info('Column jenis_audit_id added successfully!');

            // Add foreign key if master_jenis_audit table exists
            if (Schema::hasTable('master_jenis_audit')) {
                try {
                    DB::statement('ALTER TABLE pelaporan_hasil_audit ADD CONSTRAINT pelaporan_hasil_audit_jenis_audit_id_foreign FOREIGN KEY (jenis_audit_id) REFERENCES master_jenis_audit(id) ON DELETE SET NULL');
                    $this->info('Foreign key constraint added successfully!');
                } catch (\Exception $e) {
                    $this->warn('Foreign key might already exist: ' . $e->getMessage());
                }
            }

            // Modify kode_spi to string if it's still enum
            try {
                DB::statement("ALTER TABLE pelaporan_hasil_audit MODIFY COLUMN kode_spi VARCHAR(255) NOT NULL");
                $this->info('Column kode_spi modified to VARCHAR successfully!');
            } catch (\Exception $e) {
                $this->warn('kode_spi modification: ' . $e->getMessage());
            }

            // Remove po_audit_konsul column if it exists
            if (Schema::hasColumn('pelaporan_hasil_audit', 'po_audit_konsul')) {
                try {
                    DB::statement('ALTER TABLE pelaporan_hasil_audit DROP COLUMN po_audit_konsul');
                    $this->info('Column po_audit_konsul removed successfully!');
                } catch (\Exception $e) {
                    $this->warn('po_audit_konsul removal: ' . $e->getMessage());
                }
            }

            $this->info('All migrations completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

