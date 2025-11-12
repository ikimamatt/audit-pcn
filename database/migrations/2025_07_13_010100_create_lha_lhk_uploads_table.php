<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lha_lhk_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelaporan_hasil_audit_id');
            $table->string('file_lha_lhk');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->foreign('pelaporan_hasil_audit_id')->references('id')->on('pelaporan_hasil_audit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lha_lhk_uploads');
    }
}; 