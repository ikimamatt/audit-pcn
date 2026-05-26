<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MasterUnitSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding Master Region, Area and Sub-Bidang from SQL files...');

        // 1. Seed master_region
        $regionSqlPath = base_path('master_region (UP).sql');
        if (File::exists($regionSqlPath)) {
            $sql = File::get($regionSqlPath);
            // Split SQL statements by semicolon
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            $inserted = 0;
            foreach ($statements as $statement) {
                if (stripos($statement, 'INSERT INTO') !== false) {
                    DB::unprepared($statement);
                    $inserted++;
                }
            }
            $this->command->info("✅ Seeded master_region: {$inserted} insert statements executed.");
        } else {
            $this->command->error("❌ File not found: {$regionSqlPath}");
        }

        // 2. Seed master_area
        $areaSqlPath = base_path('master_area (UL).sql');
        if (File::exists($areaSqlPath)) {
            $sql = File::get($areaSqlPath);
            // Split SQL statements by semicolon
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            $inserted = 0;
            foreach ($statements as $statement) {
                if (stripos($statement, 'INSERT INTO') !== false) {
                    DB::unprepared($statement);
                    $inserted++;
                }
            }
            $this->command->info("✅ Seeded master_area: {$inserted} insert statements executed.");
        } else {
            $this->command->error("❌ File not found: {$areaSqlPath}");
        }

        // 3. Seed master_sub_bidang
        $subBidangSqlPath = base_path('master_sub_bidang.sql');
        if (File::exists($subBidangSqlPath)) {
            $sql = File::get($subBidangSqlPath);
            // Split SQL statements by semicolon
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            $inserted = 0;
            foreach ($statements as $statement) {
                if (stripos($statement, 'INSERT INTO') !== false) {
                    DB::unprepared($statement);
                    $inserted++;
                }
            }
            $this->command->info("✅ Seeded master_sub_bidang: {$inserted} insert statements executed.");
        } else {
            $this->command->error("❌ File not found: {$subBidangSqlPath}");
        }
    }
}
