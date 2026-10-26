<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('tipe', ['server', 'workstation', 'jaringan', 'aplikasi', 'lainnya'])->default('lainnya');
            $table->string('ip_address')->nullable();
            $table->string('lokasi');
            $table->enum('status', ['aktif', 'rusak', 'pemeliharaan'])->default('aktif');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
