<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Menambahkan composite index yang masih kurang berdasarkan audit optimasi.
 *
 * Index ini melengkapi migration sebelumnya dan fokus pada:
 * 1. penutup_lha_rekomendasi_pic — untuk query myReminders (JOIN on user_id + pic_type)
 * 2. penutup_lha_rekomendasi — composite (status_tindak_lanjut, status_approval) untuk filter kombinasi
 * 3. pelaporan_temuan — composite (status_approval, pelaporan_hasil_audit_id) sering difilter bersamaan
 * 4. pka_milestone — untuk pre-aggregate JOIN di buildRencanaPkptData
 * 5. pka_proses_bisnis — untuk pre-aggregate JOIN risiko
 */
return new class extends Migration
{
    public function up(): void
    {
        // penutup_lha_rekomendasi_pic — digunakan di myReminders JOIN + filter pic_type
        Schema::table('penutup_lha_rekomendasi_pic', function (Blueprint $table) {
            if (!$this->indexExists('penutup_lha_rekomendasi_pic', 'idx_pic_user_type')) {
                $table->index(['master_user_id', 'pic_type'], 'idx_pic_user_type');
            }
            if (!$this->indexExists('penutup_lha_rekomendasi_pic', 'idx_pic_rekom_id')) {
                $table->index('penutup_lha_rekomendasi_id', 'idx_pic_rekom_id');
            }
        });

        // penutup_lha_rekomendasi — composite untuk filter kombinasi di myReminders & pemantauan
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            if (!$this->indexExists('penutup_lha_rekomendasi', 'idx_plr_status_combo')) {
                $table->index(['status_tindak_lanjut', 'status_approval'], 'idx_plr_status_combo');
            }
            if (!$this->indexExists('penutup_lha_rekomendasi', 'idx_plr_real_waktu')) {
                $table->index('real_waktu', 'idx_plr_real_waktu');
            }
        });

        // pelaporan_temuan — composite untuk filter combined yang sering dipakai
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            if (!$this->indexExists('pelaporan_temuan', 'idx_pt_approval_pha')) {
                $table->index(['status_approval', 'pelaporan_hasil_audit_id'], 'idx_pt_approval_pha');
            }
        });

        // pka_milestone — untuk GROUP BY pada pre-aggregate JOIN di buildRencanaPkptData
        Schema::table('pka_milestone', function (Blueprint $table) {
            if (!$this->indexExists('pka_milestone', 'idx_ms_pka_nama')) {
                $table->index(['program_kerja_audit_id', 'nama_milestone'], 'idx_ms_pka_nama');
            }
        });

        // pka_proses_bisnis — untuk JOIN ke pka_risiko di aggregate query
        Schema::table('pka_proses_bisnis', function (Blueprint $table) {
            if (!$this->indexExists('pka_proses_bisnis', 'idx_ppb_pka_id')) {
                $table->index('program_kerja_audit_id', 'idx_ppb_pka_id');
            }
        });

        // pka_risiko — untuk COUNT JOIN dari pka_proses_bisnis
        Schema::table('pka_risiko', function (Blueprint $table) {
            if (!$this->indexExists('pka_risiko', 'idx_pr_proses_bisnis')) {
                $table->index('pka_proses_bisnis_id', 'idx_pr_proses_bisnis');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penutup_lha_rekomendasi_pic', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_pic_user_type');
            $table->dropIndexIfExists('idx_pic_rekom_id');
        });

        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_plr_status_combo');
            $table->dropIndexIfExists('idx_plr_real_waktu');
        });

        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_pt_approval_pha');
        });

        Schema::table('pka_milestone', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_ms_pka_nama');
        });

        Schema::table('pka_proses_bisnis', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_ppb_pka_id');
        });

        Schema::table('pka_risiko', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_pr_proses_bisnis');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
