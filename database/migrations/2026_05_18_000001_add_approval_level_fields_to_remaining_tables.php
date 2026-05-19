<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Hanya pka_dokumen yang belum punya approval level fields.
     * walkthrough_audit, tod_bpm_audit, toe_audit sudah ada dari migration sebelumnya.
     * Tambahan: update status_approval enum untuk semua tabel yang perlu.
     */
    public function up(): void
    {
        // 1. Tambah level fields ke pka_dokumen
        DB::statement("ALTER TABLE `pka_dokumen` MODIFY COLUMN `status_approval` ENUM('pending', 'approved_level1', 'approved', 'rejected_level1', 'rejected') DEFAULT 'pending'");

        Schema::table('pka_dokumen', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by_level1')->nullable()->after('approved_at');
            $table->timestamp('approved_at_level1')->nullable()->after('approved_by_level1');
            $table->unsignedBigInteger('rejected_by_level1')->nullable()->after('approved_at_level1');
            $table->timestamp('rejected_at_level1')->nullable()->after('rejected_by_level1');
            $table->text('rejection_reason_level1')->nullable()->after('rejected_at_level1');

            $table->unsignedBigInteger('approved_by_level2')->nullable()->after('rejection_reason_level1');
            $table->timestamp('approved_at_level2')->nullable()->after('approved_by_level2');
            $table->unsignedBigInteger('rejected_by_level2')->nullable()->after('approved_at_level2');
            $table->timestamp('rejected_at_level2')->nullable()->after('rejected_by_level2');
            $table->text('rejection_reason_level2')->nullable()->after('rejected_at_level2');

            $table->foreign('approved_by_level1')->references('id')->on('master_user')->onDelete('set null');
            $table->foreign('rejected_by_level1')->references('id')->on('master_user')->onDelete('set null');
            $table->foreign('approved_by_level2')->references('id')->on('master_user')->onDelete('set null');
            $table->foreign('rejected_by_level2')->references('id')->on('master_user')->onDelete('set null');
        });

        // 2. Update enum untuk tabel yang sudah ada tapi enum-nya belum diperbarui
        $tablesNeedingEnumUpdate = ['walkthrough_audit', 'tod_bpm_audit', 'toe_audit'];
        foreach ($tablesNeedingEnumUpdate as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `status_approval` ENUM('pending', 'approved_level1', 'approved', 'rejected_level1', 'rejected') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        Schema::table('pka_dokumen', function (Blueprint $table) {
            $table->dropForeign(['approved_by_level1']);
            $table->dropForeign(['rejected_by_level1']);
            $table->dropForeign(['approved_by_level2']);
            $table->dropForeign(['rejected_by_level2']);

            $table->dropColumn([
                'approved_by_level1', 'approved_at_level1',
                'rejected_by_level1', 'rejected_at_level1', 'rejection_reason_level1',
                'approved_by_level2', 'approved_at_level2',
                'rejected_by_level2', 'rejected_at_level2', 'rejection_reason_level2',
            ]);
        });

        DB::statement("ALTER TABLE `pka_dokumen` MODIFY COLUMN `status_approval` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
