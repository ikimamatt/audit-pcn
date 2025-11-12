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
        Schema::create('pelaporan_temuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelaporan_hasil_audit_id')->constrained('pelaporan_hasil_audit')->onDelete('cascade');
            $table->text('hasil_temuan');
            $table->foreignId('kode_aoi_id')->constrained('master_kode_aoi')->onDelete('restrict');
            $table->foreignId('kode_risk_id')->constrained('master_kode_risk')->onDelete('restrict');
            $table->string('nomor_iss'); // ISS.xxx/PO PCN/MM/NN/PP/yyyy
            $table->year('tahun');
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
        Schema::dropIfExists('pelaporan_temuan');
    }
};
