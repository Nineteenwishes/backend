<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            $table->string('hari')->nullable()->after('kelas'); // Tambahkan kolom hari setelah kolom kelas
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_pikets', function (Blueprint $table) {
            $table->dropColumn('hari');
        });
    }
};
