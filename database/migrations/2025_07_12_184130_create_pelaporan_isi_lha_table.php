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
        Schema::create('pelaporan_isi_lha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelaporan_hasil_audit_id')->constrained('pelaporan_hasil_audit')->onDelete('cascade');
            $table->string('nomor_iss');
            $table->text('permasalahan');
            $table->text('penyebab');
            $table->text('kriteria');
            $table->text('dampak_terjadi')->nullable();
            $table->text('dampak_potensi')->nullable();
            $table->enum('signifikansi', ['Tinggi', 'Medium', 'Rendah'])->default('Medium');
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
        Schema::dropIfExists('pelaporan_isi_lha');
    }
};

// Tidak ada perubahan pada file ini, migration penutup_lha_rekomendasi akan dipindahkan ke file migration baru.
