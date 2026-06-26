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
        $regionSqlPath = database_path('sql/master_region.sql');
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
        $areaSqlPath = database_path('sql/master_area.sql');
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
        $subBidangSqlPath = database_path('sql/master_sub_bidang.sql');
        if (File::exists($subBidangSqlPath)) {
            $sql = File::get($subBidangSqlPath);
            if (preg_match('/VALUES\s*\((.*)\)\s*;/is', $sql, $matches)) {
                $valuesStr = $matches[1];
                $rows = preg_split('/\),\s*\(/', $valuesStr);
                $auditeeMap = DB::table('master_auditee')->pluck('id', 'kd_bidang')->toArray();
                $insertData = [];
                foreach ($rows as $rowStr) {
                    if (preg_match('/^\s*(\d+)\s*,\s*\'(.*?)\'\s*,\s*(\d+)/', $rowStr, $rowMatches)) {
                        $oldId = $rowMatches[1];
                        $nama = $rowMatches[2];
                        $oldBidangId = $rowMatches[3];
                        $kdBidang = str_pad($oldBidangId, 2, '0', STR_PAD_LEFT);
                        $uuidBidang = $auditeeMap[$kdBidang] ?? null;
                        $insertData[] = [
                            'id' => (string) \Illuminate\Support\Str::uuid(),
                            'nama' => $nama,
                            'master_bidang_id' => $uuidBidang,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
                DB::table('master_sub_bidang')->insert($insertData);
                $this->command->info("✅ Seeded master_sub_bidang: " . count($insertData) . " records inserted.");
            } else {
                $this->command->error("❌ Could not parse VALUES block in master_sub_bidang.sql");
            }
        } else {
            $this->command->error("❌ File not found: {$subBidangSqlPath}");
        }
    }
}
