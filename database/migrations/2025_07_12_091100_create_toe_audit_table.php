<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toe_audit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('perencanaan_audit_id');
            $table->text('judul_bpm');
            $table->text('pengendalian_eksisting');
            $table->enum('status_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('perencanaan_audit_id')->references('id')->on('perencanaan_audit')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toe_audit');
    }
}; 