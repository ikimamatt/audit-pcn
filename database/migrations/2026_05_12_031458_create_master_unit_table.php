<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create master_region table
        Schema::create('master_region', function (Blueprint $table) {
            $table->id();
            $table->integer('api_id')->nullable();
            $table->string('kd_region_sap', 30)->nullable();
            $table->string('kd_region', 10)->unique();
            $table->string('nama_region', 255);
            $table->integer('masa_persiapan')->nullable();
            $table->string('kd_provinsi', 255)->nullable();
            $table->string('lat', 60)->nullable();
            $table->string('lon', 60)->nullable();
            $table->string('manager', 120)->nullable();
            $table->string('jabatan', 120)->nullable();
            $table->string('kota', 120)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('telepon', 60)->nullable();
            $table->string('facsimile', 60)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('kode_surat', 60)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 2. Create master_area table
        Schema::create('master_area', function (Blueprint $table) {
            $table->id();
            $table->integer('api_id')->nullable();
            $table->string('kd_region', 10);
            $table->string('kd_area', 10)->unique();
            $table->string('nama_area', 255);
            $table->string('manager', 120)->nullable();
            $table->string('jabatan', 120)->nullable();
            $table->string('kota', 120)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('telepon', 60)->nullable();
            $table->string('facsimile', 60)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('kode_surat', 60)->nullable();
            $table->string('lat', 60)->nullable();
            $table->string('lon', 60)->nullable();
            $table->char('base_region', 1)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('kd_region')
                  ->references('kd_region')->on('master_region')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        // 3. Drop unit_id and add area_id to perencanaan_audit
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            if (Schema::hasColumn('perencanaan_audit', 'unit_id')) {
                $table->dropColumn('unit_id');
            }
            $table->unsignedBigInteger('area_id')->nullable()->after('jenis_audit_id');
            $table->foreign('area_id')
                  ->references('id')->on('master_area')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('perencanaan_audit', function (Blueprint $table) {
            try {
                $table->dropForeign(['area_id']);
            } catch (\Exception $e) {}
            $table->dropColumn('area_id');
            
            if (!Schema::hasColumn('perencanaan_audit', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('jenis_audit_id');
            }
        });

        Schema::dropIfExists('master_area');
        Schema::dropIfExists('master_region');
    }
};
