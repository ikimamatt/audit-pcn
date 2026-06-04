<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // perencanaan_audit — used in almost every dashboard JOIN
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            $table->index('auditee_id', 'idx_pa_auditee');
            $table->index('area_id', 'idx_pa_area');
            $table->index(['auditee_id', 'area_id'], 'idx_pa_auditee_area');
        });

        // pelaporan_hasil_audit — bridge between perencanaan and temuan
        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->index('perencanaan_audit_id', 'idx_pha_perencanaan');
            $table->index('status_approval', 'idx_pha_status_approval');
        });

        // pelaporan_temuan — heavily queried for dashboard charts
        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            $table->index('pelaporan_hasil_audit_id', 'idx_pt_pelaporan_ha');
            $table->index('kode_risk_id', 'idx_pt_kode_risk');
            $table->index('status_approval', 'idx_pt_status_approval');
        });

        // penutup_lha_rekomendasi — monitoring, aging, progress dashboards
        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            $table->index('pelaporan_isi_lha_id', 'idx_plr_isi_lha');
            $table->index('status_tindak_lanjut', 'idx_plr_status_tl');
            $table->index('target_waktu', 'idx_plr_target_waktu');
            $table->index('status_approval', 'idx_plr_status_approval');
            $table->index(['status_tindak_lanjut', 'target_waktu'], 'idx_plr_status_target');
        });

        // realisasi_audits — dashboard pelaksanaan
        Schema::table('realisasi_audits', function (Blueprint $table) {
            $table->index('perencanaan_audit_id', 'idx_ra_perencanaan');
            $table->index('status', 'idx_ra_status');
        });

        // entry_meeting — dashboard PKPT
        Schema::table('entry_meeting', function (Blueprint $table) {
            $table->index('program_kerja_audit_id', 'idx_em_pka');
            $table->index('status_approval', 'idx_em_status_approval');
        });

        // program_kerja_audit — links perencanaan to pelaksanaan
        Schema::table('program_kerja_audit', function (Blueprint $table) {
            $table->index('perencanaan_audit_id', 'idx_pka_perencanaan');
            $table->index('status_approval', 'idx_pka_status_approval');
        });

        // exit_meeting_uploads — dashboard, unggah dokumen
        Schema::table('exit_meeting_uploads', function (Blueprint $table) {
            $table->index('auditee_id', 'idx_emu_auditee');
        });
    }

    public function down(): void
    {
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            $table->dropIndex('idx_pa_auditee');
            $table->dropIndex('idx_pa_area');
            $table->dropIndex('idx_pa_auditee_area');
        });

        Schema::table('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->dropIndex('idx_pha_perencanaan');
            $table->dropIndex('idx_pha_status_approval');
        });

        Schema::table('pelaporan_temuan', function (Blueprint $table) {
            $table->dropIndex('idx_pt_pelaporan_ha');
            $table->dropIndex('idx_pt_kode_risk');
            $table->dropIndex('idx_pt_status_approval');
        });

        Schema::table('penutup_lha_rekomendasi', function (Blueprint $table) {
            $table->dropIndex('idx_plr_isi_lha');
            $table->dropIndex('idx_plr_status_tl');
            $table->dropIndex('idx_plr_target_waktu');
            $table->dropIndex('idx_plr_status_approval');
            $table->dropIndex('idx_plr_status_target');
        });

        Schema::table('realisasi_audits', function (Blueprint $table) {
            $table->dropIndex('idx_ra_perencanaan');
            $table->dropIndex('idx_ra_status');
        });

        Schema::table('entry_meeting', function (Blueprint $table) {
            $table->dropIndex('idx_em_pka');
            $table->dropIndex('idx_em_status_approval');
        });

        Schema::table('program_kerja_audit', function (Blueprint $table) {
            $table->dropIndex('idx_pka_perencanaan');
            $table->dropIndex('idx_pka_status_approval');
        });

        Schema::table('exit_meeting_uploads', function (Blueprint $table) {
            $table->dropIndex('idx_emu_auditee');
        });
    }
};
