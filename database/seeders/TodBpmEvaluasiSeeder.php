<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TodBpmAudit;

class TodBpmEvaluasiSeeder extends Seeder
{
    public function run(): void
    {
        $todBpmAudits = TodBpmAudit::take(2)->get();
        if ($todBpmAudits->count() < 2) {
            $this->command->warn('Tidak cukup data tod_bpm_audit. Skipping TodBpmEvaluasiSeeder.');
            return;
        }
        $data = [
            [
                'tod_bpm_audit_id' => $todBpmAudits[0]->id,
                'hasil_evaluasi' => 'Cukup',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tod_bpm_audit_id' => $todBpmAudits[1]->id,
                'hasil_evaluasi' => 'Tidak Cukup',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($data as &$row) {
            $row['id'] = (string) \Illuminate\Support\Str::uuid();
        }
        DB::table('tod_bpm_evaluasi')->insert($data);
    }
}
