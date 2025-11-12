<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelaporan_hasil_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perencanaan_audit_id')->constrained('perencanaan_audit')->onDelete('restrict');
            $table->string('nomor_lha_lhk'); // xxx.AA/BB/CC/SPI.PCN/yyyy
            $table->enum('jenis_lha_lhk', ['LHA', 'LHK']); // AA
            $table->enum('po_audit_konsul', ['PO AUDIT', 'KONSUL']); // BB
            $table->enum('kode_spi', ['SPI.01.02', 'SPI.01.03', 'SPI.01.04']); // CC
            $table->string('nomor_iss');
            $table->text('hasil_temuan')->nullable(); // AOI
            $table->unsignedBigInteger('kode_aoi_id')->nullable();
            $table->unsignedBigInteger('kode_risk_id')->nullable();
            $table->foreign('kode_aoi_id')->references('id')->on('master_kode_aoi')->nullOnDelete();
            $table->foreign('kode_risk_id')->references('id')->on('master_kode_risk')->nullOnDelete();
            $table->text('permasalahan')->nullable();
            $table->text('penyebab_people')->nullable();
            $table->text('penyebab_process')->nullable();
            $table->text('penyebab_policy')->nullable();
            $table->text('penyebab_system')->nullable();
            $table->text('penyebab_eksternal')->nullable();
            $table->text('kriteria')->nullable();
            $table->text('dampak_terjadi')->nullable();
            $table->text('dampak_potensi')->nullable();
            $table->enum('signifikan', ['Tinggi', 'Medium', 'Rendah'])->nullable();
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('master_user')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelaporan_hasil_audit');
    }
};
