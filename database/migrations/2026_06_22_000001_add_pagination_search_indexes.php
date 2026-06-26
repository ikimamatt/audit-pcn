<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Menambahkan index untuk mendukung server-side pagination dengan filter & search.
 *
 * Index ini melengkapi migration 2026_06_04_010000_add_performance_indexes.php
 * yang sudah ada sebelumnya. Fokus di sini adalah kolom yang digunakan sebagai
 * parameter search/sort pada endpoint API list (LIMIT + OFFSET).
 */
return new class extends Migration
{
    public function up(): void
    {
        // perencanaan_audit — kolom search: nomor_surat_tugas, tanggal_surat_tugas
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            if (!$this->indexExists('perencanaan_audit', 'idx_pa_nomor_st')) {
                $table->index('nomor_surat_tugas', 'idx_pa_nomor_st');
            }
            if (!$this->indexExists('perencanaan_audit', 'idx_pa_tanggal_st')) {
                $table->index('tanggal_surat_tugas', 'idx_pa_tanggal_st');
            }
            if (!$this->indexExists('perencanaan_audit', 'idx_pa_jenis_audit_id')) {
                $table->index('jenis_audit_id', 'idx_pa_jenis_audit_id');
            }
        });

        // program_kerja_audit — kolom search: nomor_pka, created_at (sort default)
        Schema::table('program_kerja_audit', function (Blueprint $table) {
            if (!$this->indexExists('program_kerja_audit', 'idx_pka_created_at')) {
                $table->index('created_at', 'idx_pka_created_at');
            }
        });

        // entry_meeting — sort: created_at
        Schema::table('entry_meeting', function (Blueprint $table) {
            if (!$this->indexExists('entry_meeting', 'idx_em_created_at')) {
                $table->index('created_at', 'idx_em_created_at');
            }
        });

        // walkthrough_audit — sort: created_at, filter: status_approval
        Schema::table('walkthrough_audit', function (Blueprint $table) {
            if (!$this->indexExists('walkthrough_audit', 'idx_wa_status_approval')) {
                $table->index('status_approval', 'idx_wa_status_approval');
            }
            if (!$this->indexExists('walkthrough_audit', 'idx_wa_created_at')) {
                $table->index('created_at', 'idx_wa_created_at');
            }
        });

        // tod_bpm_audit — sort: created_at, filter: status_approval
        Schema::table('tod_bpm_audit', function (Blueprint $table) {
            if (!$this->indexExists('tod_bpm_audit', 'idx_tba_status_approval')) {
                $table->index('status_approval', 'idx_tba_status_approval');
            }
            if (!$this->indexExists('tod_bpm_audit', 'idx_tba_created_at')) {
                $table->index('created_at', 'idx_tba_created_at');
            }
        });

        // toe_audit — sort: created_at, filter: status_approval
        Schema::table('toe_audit', function (Blueprint $table) {
            if (!$this->indexExists('toe_audit', 'idx_toe_status_approval')) {
                $table->index('status_approval', 'idx_toe_status_approval');
            }
            if (!$this->indexExists('toe_audit', 'idx_toe_created_at')) {
                $table->index('created_at', 'idx_toe_created_at');
            }
        });

        // realisasi_audits (exit meeting) — sort: created_at
        Schema::table('realisasi_audits', function (Blueprint $table) {
            if (!$this->indexExists('realisasi_audits', 'idx_ra_created_at')) {
                $table->index('created_at', 'idx_ra_created_at');
            }
        });

        // pelaporan_hasil_audit — search: nomor_lha_lhk, sort: created_at
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            if (!$this->indexExists('pelaporan_hasil_audit', 'idx_pha_nomor_lha')) {
                $table->index('nomor_lha_lhk', 'idx_pha_nomor_lha');
            }
            if (!$this->indexExists('pelaporan_hasil_audit', 'idx_pha_created_at')) {
                $table->index('created_at', 'idx_pha_created_at');
            }
        });

        // penutup_lha_rekomendasi — search: nomor_surat_tugas (via PA), sort: created_at
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            if (!$this->indexExists('penutup_lha_rekomendasi', 'idx_plr_created_at')) {
                $table->index('created_at', 'idx_plr_created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_pa_nomor_st');
            $table->dropIndexIfExists('idx_pa_tanggal_st');
            $table->dropIndexIfExists('idx_pa_jenis_audit_id');
        });

        Schema::table('program_kerja_audit', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_pka_created_at');
        });

        Schema::table('entry_meeting', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_em_created_at');
        });

        Schema::table('walkthrough_audit', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_wa_status_approval');
            $table->dropIndexIfExists('idx_wa_created_at');
        });

        Schema::table('tod_bpm_audit', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_tba_status_approval');
            $table->dropIndexIfExists('idx_tba_created_at');
        });

        Schema::table('toe_audit', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_toe_status_approval');
            $table->dropIndexIfExists('idx_toe_created_at');
        });

        Schema::table('realisasi_audits', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_ra_created_at');
        });

        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_pha_nomor_lha');
            $table->dropIndexIfExists('idx_pha_created_at');
        });

        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_plr_created_at');
        });
    }

    /**
     * Cek apakah index sudah ada (agar migration idempotent / tidak error jika dijalankan ulang).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
