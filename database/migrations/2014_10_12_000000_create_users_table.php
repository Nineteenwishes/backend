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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique(); // Ganti email dengan username
            $table->string('password');
            $table->enum('role', ['admin', 'staff', 'user'])->default('user');
            $table->timestamps();
            
            // Hapus kolom berikut karena tidak digunakan lagi:
            // - email
            // - email_verified_at
            // - remember_token
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};