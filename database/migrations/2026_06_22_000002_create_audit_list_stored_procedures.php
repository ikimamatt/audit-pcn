<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Membuat Stored Procedures untuk semua endpoint list API audit.
 *
 * Setiap SP menggantikan pola ->get() di API controller dengan query
 * yang menggunakan LIMIT + OFFSET untuk server-side pagination.
 *
 * Fix hydration: gunakan select `table.*` agar data terisi lengkap saat di-hydrate.
 * Fix collation: gunakan COLLATE utf8mb4_unicode_ci pada ekspresi LIKE.
 *
 * Signature umum:
 *   CALL sp_get_xxx(p_limit INT, p_offset INT, p_search VARCHAR, ...)
 *
 * Output (2 result set):
 *   1) SELECT COUNT(*) AS total_count
 *   2) SELECT ... LIMIT p_limit OFFSET p_offset
 */
return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────
        // 1. SP: Perencanaan Audit
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_perencanaan_audit`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_perencanaan_audit`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_jenis_id   VARCHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    -- Total count
    SELECT COUNT(*) AS total_count
    FROM perencanaan_audit pa
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_jenis_id IS NULL OR pa.jenis_audit_id = p_jenis_id);

    -- Paginated rows with joined data
    SELECT
        pa.*,
        ma.nama_bidang           AS auditee_nama_bidang,
        mja.nama_jenis_audit     AS jenis_audit_nama,
        mu_koor.nama             AS koordinator_nama,
        mu_kt.nama               AS ketua_tim_nama
    FROM perencanaan_audit pa
    LEFT JOIN master_auditee    ma      ON ma.id  = pa.auditee_id
    LEFT JOIN master_jenis_audit mja    ON mja.id = pa.jenis_audit_id
    LEFT JOIN master_user        mu_koor ON mu_koor.id = pa.koordinator_id
    LEFT JOIN master_user        mu_kt   ON mu_kt.id  = pa.ketua_tim_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_jenis_id IS NULL OR pa.jenis_audit_id = p_jenis_id)
    ORDER BY pa.nomor_surat_tugas ASC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 2. SP: Program Kerja Audit (PKA)
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_pka`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_pka`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM program_kerja_audit pka
    INNER JOIN perencanaan_audit pa ON pa.id = pka.perencanaan_audit_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR pka.status_approval = p_status);

    SELECT
        pka.*,
        pa.nomor_surat_tugas,
        ma.nama_bidang AS auditee_nama_bidang
    FROM program_kerja_audit pka
    INNER JOIN perencanaan_audit pa ON pa.id = pka.perencanaan_audit_id
    LEFT JOIN  master_auditee   ma  ON ma.id = pa.auditee_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR pka.status_approval = p_status)
    ORDER BY pka.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 3. SP: Entry Meeting
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_entry_meeting`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_entry_meeting`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM entry_meeting em
    INNER JOIN program_kerja_audit pka ON pka.id = em.program_kerja_audit_id
    INNER JOIN perencanaan_audit   pa  ON pa.id  = pka.perencanaan_audit_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR em.status_approval = p_status);

    SELECT
        em.*,
        pka.no_pka AS nomor_pka,
        pa.nomor_surat_tugas,
        ma.nama_bidang AS auditee_nama_bidang
    FROM entry_meeting em
    INNER JOIN program_kerja_audit pka ON pka.id = em.program_kerja_audit_id
    INNER JOIN perencanaan_audit   pa  ON pa.id  = pka.perencanaan_audit_id
    LEFT JOIN  master_auditee      ma  ON ma.id  = pa.auditee_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR em.status_approval = p_status)
    ORDER BY em.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 4. SP: Walkthrough Audit
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_walkthrough`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_walkthrough`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM walkthrough_audit wa
    INNER JOIN program_kerja_audit pka ON pka.id = wa.program_kerja_audit_id
    INNER JOIN perencanaan_audit   pa  ON pa.id  = pka.perencanaan_audit_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR wa.status_approval = p_status);

    SELECT
        wa.*,
        pka.no_pka AS nomor_pka,
        pa.nomor_surat_tugas,
        ma.nama_bidang AS auditee_nama_bidang
    FROM walkthrough_audit wa
    INNER JOIN program_kerja_audit pka ON pka.id = wa.program_kerja_audit_id
    INNER JOIN perencanaan_audit   pa  ON pa.id  = pka.perencanaan_audit_id
    LEFT JOIN  master_auditee      ma  ON ma.id  = pa.auditee_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR wa.status_approval = p_status)
    ORDER BY wa.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 5. SP: TOD BPM Audit
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_tod_bpm`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_tod_bpm`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM tod_bpm_audit tba
    INNER JOIN perencanaan_audit pa ON pa.id = tba.perencanaan_audit_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR tba.status_approval = p_status);

    SELECT
        tba.*,
        pka.no_pka AS nomor_pka,
        pa.nomor_surat_tugas,
        ma.nama_bidang AS auditee_nama_bidang
    FROM tod_bpm_audit tba
    INNER JOIN perencanaan_audit pa ON pa.id = tba.perencanaan_audit_id
    LEFT JOIN program_kerja_audit pka ON pka.perencanaan_audit_id = pa.id
    LEFT JOIN master_auditee      ma  ON ma.id  = pa.auditee_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR tba.status_approval = p_status)
    ORDER BY tba.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 6. SP: TOE Audit
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_toe`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_toe`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM toe_audit ta
    INNER JOIN perencanaan_audit pa ON pa.id = ta.perencanaan_audit_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR ta.status_approval = p_status);

    SELECT
        ta.*,
        pka.no_pka AS nomor_pka,
        pa.nomor_surat_tugas,
        ma.nama_bidang AS auditee_nama_bidang
    FROM toe_audit ta
    INNER JOIN perencanaan_audit pa ON pa.id = ta.perencanaan_audit_id
    LEFT JOIN program_kerja_audit pka ON pka.perencanaan_audit_id = pa.id
    LEFT JOIN master_auditee      ma  ON ma.id  = pa.auditee_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR ta.status_approval = p_status)
    ORDER BY ta.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 7. SP: Exit Meeting (Realisasi Audit)
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_exit_meeting`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_exit_meeting`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM realisasi_audits ra
    INNER JOIN perencanaan_audit pa ON pa.id = ra.perencanaan_audit_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR ra.status = p_status);

    SELECT
        ra.*,
        pa.nomor_surat_tugas,
        ma.nama_bidang AS auditee_nama_bidang
    FROM realisasi_audits ra
    INNER JOIN perencanaan_audit pa ON pa.id = ra.perencanaan_audit_id
    LEFT JOIN  master_auditee   ma  ON ma.id = pa.auditee_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR ra.status = p_status)
    ORDER BY ra.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 8. SP: Pelaporan Hasil Audit (LHA/LHK)
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_pelaporan`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_pelaporan`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_jenis      VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM pelaporan_hasil_audit pha
    INNER JOIN perencanaan_audit pa ON pa.id = pha.perencanaan_audit_id
    WHERE (v_search IS NULL OR pha.nomor_lha_lhk LIKE v_search OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR pha.status_approval = p_status)
      AND (p_jenis  IS NULL OR p_jenis  = '' OR pha.jenis_lha_lhk   = p_jenis);

    SELECT
        pha.*,
        pa.nomor_surat_tugas,
        ma.nama_bidang AS auditee_nama_bidang,
        mja.nama_jenis_audit,
        COUNT(pt.id) AS jumlah_iss
    FROM pelaporan_hasil_audit pha
    INNER JOIN perencanaan_audit pa  ON pa.id  = pha.perencanaan_audit_id
    LEFT JOIN  master_auditee   ma   ON ma.id  = pa.auditee_id
    LEFT JOIN  master_jenis_audit mja ON mja.id = pha.jenis_audit_id
    LEFT JOIN  pelaporan_temuan  pt  ON pt.pelaporan_hasil_audit_id = pha.id
    WHERE (v_search IS NULL OR pha.nomor_lha_lhk LIKE v_search OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR pha.status_approval = p_status)
      AND (p_jenis  IS NULL OR p_jenis  = '' OR pha.jenis_lha_lhk   = p_jenis)
    GROUP BY pha.id
    ORDER BY pha.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 9. SP: Penutup LHA & Rekomendasi
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_penutup_lha`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_penutup_lha`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_status     VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(DISTINCT pa.nomor_surat_tugas) AS total_count
    FROM penutup_lha_rekomendasi plr
    INNER JOIN pelaporan_temuan pt ON pt.id = plr.pelaporan_isi_lha_id
    INNER JOIN pelaporan_hasil_audit pha ON pha.id = pt.pelaporan_hasil_audit_id
    INNER JOIN perencanaan_audit pa ON pa.id = pha.perencanaan_audit_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR plr.status_approval = p_status);

    SELECT
        pa.nomor_surat_tugas,
        plr.status_approval,
        COUNT(plr.id)                              AS total_rekomendasi,
        SUM(plr.status_tindak_lanjut = 'selesai')  AS total_selesai,
        SUM(plr.status_tindak_lanjut != 'selesai') AS total_belum_selesai,
        MAX(plr.created_at)                        AS latest_created_at,
        pa.id                                      AS perencanaan_audit_id,
        ma.nama_bidang                             AS auditee_nama_bidang
    FROM penutup_lha_rekomendasi plr
    INNER JOIN pelaporan_temuan pt ON pt.id = plr.pelaporan_isi_lha_id
    INNER JOIN pelaporan_hasil_audit pha ON pha.id = pt.pelaporan_hasil_audit_id
    INNER JOIN perencanaan_audit pa ON pa.id = pha.perencanaan_audit_id
    LEFT JOIN master_auditee    ma ON ma.id = pa.auditee_id
    WHERE (v_search IS NULL OR pa.nomor_surat_tugas LIKE v_search)
      AND (p_status IS NULL OR p_status = '' OR plr.status_approval = p_status)
    GROUP BY pa.nomor_surat_tugas, plr.status_approval, pa.id, ma.nama_bidang
    ORDER BY latest_created_at DESC
    LIMIT p_limit OFFSET p_offset;
END
        ");

        // ─────────────────────────────────────────────────────────────
        // 10. SP: Pemantauan (Tindak Lanjut per Surat Tugas)
        // ─────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS `sp_get_pemantauan`");
        DB::unprepared("
CREATE PROCEDURE `sp_get_pemantauan`(
    IN p_limit      INT,
    IN p_offset     INT,
    IN p_search     VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    IN p_nomor_st   VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
)
BEGIN
    DECLARE v_search   VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;
    DECLARE v_nomor_st VARCHAR(257) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL;

    SET v_nomor_st = p_nomor_st;

    IF p_search IS NOT NULL AND p_search != '' THEN
        SET v_search = CONCAT('%', p_search, '%');
    END IF;

    SELECT COUNT(*) AS total_count
    FROM penutup_lha_rekomendasi plr
    INNER JOIN pelaporan_temuan pt ON pt.id = plr.pelaporan_isi_lha_id
    INNER JOIN pelaporan_hasil_audit pha ON pha.id = pt.pelaporan_hasil_audit_id
    INNER JOIN perencanaan_audit pa ON pa.id = pha.perencanaan_audit_id
    WHERE pa.nomor_surat_tugas = v_nomor_st
      AND (v_search IS NULL OR plr.rekomendasi LIKE v_search);

    SELECT
        plr.*,
        ma.nama_bidang AS auditee_nama_bidang
    FROM penutup_lha_rekomendasi plr
    INNER JOIN pelaporan_temuan pt ON pt.id = plr.pelaporan_isi_lha_id
    INNER JOIN pelaporan_hasil_audit pha ON pha.id = pt.pelaporan_hasil_audit_id
    INNER JOIN perencanaan_audit pa ON pa.id = pha.perencanaan_audit_id
    LEFT JOIN master_auditee    ma ON ma.id = pa.auditee_id
    WHERE pa.nomor_surat_tugas = v_nomor_st
      AND (v_search IS NULL OR plr.rekomendasi LIKE v_search)
    ORDER BY plr.created_at ASC
    LIMIT p_limit OFFSET p_offset;
END
        ");
    }

    public function down(): void
    {
        $procedures = [
            'sp_get_perencanaan_audit',
            'sp_get_pka',
            'sp_get_entry_meeting',
            'sp_get_walkthrough',
            'sp_get_tod_bpm',
            'sp_get_toe',
            'sp_get_exit_meeting',
            'sp_get_pelaporan',
            'sp_get_penutup_lha',
            'sp_get_pemantauan',
        ];

        foreach ($procedures as $proc) {
            DB::unprepared("DROP PROCEDURE IF EXISTS `{$proc}`");
        }
    }
};
