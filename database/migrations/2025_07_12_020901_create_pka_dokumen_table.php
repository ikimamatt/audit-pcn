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
        Schema::create('pka_dokumen', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_kerja_audit_id');
            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('program_kerja_audit_id')->references('id')->on('program_kerja_audit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pka_dokumen');
    }
};
