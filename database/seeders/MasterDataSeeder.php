<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the master data seeders only.
     * This seeder is useful when you only want to seed master data tables
     * without seeding transaction or audit-related data.
     *
     * Usage: php artisan db:seed --class=MasterDataSeeder
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Master Data Seeding...');
        $this->command->newLine();

        // 1. Master Kode AOI
        $this->command->info('📊 Seeding Master Kode AOI...');
        $this->call(MasterKodeAoiSeeder::class);
        $this->command->info('✅ Master Kode AOI seeded successfully!');
        $this->command->newLine();

        // 2. Master Kode Risk
        $this->command->info('📊 Seeding Master Kode Risk...');
        $this->call(MasterKodeRiskSeeder::class);
        $this->command->info('✅ Master Kode Risk seeded successfully!');
        $this->command->newLine();

        // 3. Master Bidang
        $this->command->info('📊 Seeding Master Bidang...');
        $this->call(MasterAuditeeSeeder::class);
        $this->command->info('✅ Master Bidang seeded successfully!');
        $this->command->newLine();

        // 4. Master Akses User
        $this->command->info('📊 Seeding Master Akses User...');
        $this->call(MasterAksesUserSeeder::class);
        $this->command->info('✅ Master Akses User seeded successfully!');
        $this->command->newLine();

        // 5. Master User (depends on MasterAksesUser and MasterAuditee)
        $this->command->info('📊 Seeding Master User...');
        $this->call(MasterUserSeeder::class);
        $this->command->info('✅ Master User seeded successfully!');
        $this->command->newLine();

        // 6. Master Jenis Audit
        $this->command->info('📊 Seeding Master Jenis Audit...');
        $this->call(MasterJenisAuditSeeder::class);
        $this->command->info('✅ Master Jenis Audit seeded successfully!');
        $this->command->newLine();

        $this->command->info('🎉 All Master Data seeded successfully!');
        $this->command->newLine();
        
        $this->command->table(
            ['Master Table', 'Status'],
            [
                ['master_kode_aoi', '✅ Seeded'],
                ['master_kode_risk', '✅ Seeded'],
                ['master_auditee (Master Bidang)', '✅ Seeded'],
                ['master_akses_user', '✅ Seeded'],
                ['master_user', '✅ Seeded'],
                ['master_jenis_audit', '✅ Seeded'],
            ]
        );
    }
}
