<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToeEvaluasiSeeder extends Seeder
{
    public function run(): void
    {
        $toeAudits = DB::table('toe_audit')->take(2)->get();
        if ($toeAudits->count() < 2) {
            $this->command->warn('Tidak cukup data toe_audit. Skipping ToeEvaluasiSeeder.');
            return;
        }
        DB::table('toe_evaluasi')->insert([
            [
                'toe_audit_id' => $toeAudits[0]->id,
                'hasil_evaluasi' => 'Evaluasi TOE 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'toe_audit_id' => $toeAudits[1]->id,
                'hasil_evaluasi' => 'Evaluasi TOE 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 