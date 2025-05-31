<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan_uks', function (Blueprint $table) {
            $table->id();
            $table->string('nis');
            $table->foreign('nis')->references('nis')->on('students')->onDelete('cascade');
            $table->string('nama');
            $table->string('kelas');
            $table->string('gejala');
            $table->text('keterangan')->nullable();
            $table->string('obat')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['masuk uks', 'keluar uks'])->default('masuk uks');
            $table->string('jam_masuk');
            $table->string('jam_keluar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_uks');
    }
};