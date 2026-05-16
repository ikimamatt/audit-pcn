<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PkaAllSeeder extends Seeder
{
    /**
     * Menjalankan semua seeder PKA secara berurutan.
     *
     * Urutan penting:
     * 1. ProgramKerjaAuditSeeder — insert PKA + hierarki (PB → Risiko → Kontrol) + Dokumen
     * 2. PkaMilestoneSeeder      — insert milestone per PKA
     *
     * Catatan:
     * - PkaRiskBasedAuditSeeder  → tidak dijalankan (data lama, hierarki baru ada di ProgramKerjaAuditSeeder)
     * - PkaHierarkiSeeder        → tidak dijalankan (hanya untuk migrasi data lama)
     * - PkaDokumenSeeder         → sudah dihandle di dalam ProgramKerjaAuditSeeder
     */
    public function run(): void
    {
        $this->command->info('Starting PKA seeding process...');

        $this->call(ProgramKerjaAuditSeeder::class);
        $this->call(PkaMilestoneSeeder::class);

        $this->command->info('PKA seeding process completed successfully!');
    }
}