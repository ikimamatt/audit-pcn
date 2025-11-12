<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TodBpmAudit;

class TodBpmEvaluasiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID dari tod_bpm_audit yang sudah ada
        $todBpmAudits = TodBpmAudit::take(2)->get();
        
        if ($todBpmAudits->count() < 2) {
            $this->command->warn('Tidak cukup data tod_bpm_audit. Skipping TodBpmEvaluasiSeeder.');
            return;
        }

        DB::table('tod_bpm_evaluasi')->insert([
            [
                'tod_bpm_audit_id' => $todBpmAudits[0]->id,
                'hasil_evaluasi' => 'Evaluasi BPM 1 - Satu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tod_bpm_audit_id' => $todBpmAudits[1]->id,
                'hasil_evaluasi' => 'Evaluasi BPM 2 - Satu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 