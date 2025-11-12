<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_dinas_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelaporan_hasil_audit_id');
            $table->string('file_nota_dinas');
            $table->enum('tujuan_nota_dinas', ['dirut', 'dekom', 'auditee']);
            $table->timestamps();
            $table->foreign('pelaporan_hasil_audit_id')->references('id')->on('pelaporan_hasil_audit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_dinas_uploads');
    }
}; 