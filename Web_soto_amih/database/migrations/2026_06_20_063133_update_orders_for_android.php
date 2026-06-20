<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini:
 * 1. Membuat kasir_id nullable (supaya order dari Android bisa tanpa kasir)
 * 2. Menambah kolom 'catatan' (catatan dari pelanggan)
 * 3. Menambah kolom 'email_pembeli' (untuk kirim resi)
 * 4. Menambah kolom 'sumber' untuk bedain order dari web vs android
 *
 * Jalankan: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Ubah kasir_id jadi nullable
            $table->unsignedBigInteger('kasir_id')->nullable()->change();

            // Tambah kolom baru
            $table->string('catatan')->nullable()->after('status');
            $table->string('email_pembeli')->nullable()->after('catatan');
            $table->enum('sumber', ['web', 'android'])->default('web')->after('email_pembeli');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('kasir_id')->nullable(false)->change();
            $table->dropColumn(['catatan', 'email_pembeli', 'sumber']);
        });
    }
};