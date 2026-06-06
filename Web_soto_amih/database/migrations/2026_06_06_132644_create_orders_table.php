<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_order')->unique(); // e.g., ORD001
            $table->string('nama_pembeli')->nullable();
            $table->enum('tipe', ['dine_in', 'takeaway']);
            $table->string('nomor_meja')->nullable();
            $table->enum('metode_bayar', ['tunai', 'qris', 'bank']);
            $table->decimal('diskon', 10, 2)->default(0);
            $table->enum('tipe_diskon', ['persen', 'nominal'])->default('persen');
            $table->decimal('total', 15, 2);
            $table->enum('status', ['pending', 'proses', 'selesai', 'dibatalkan'])->default('pending');
            $table->foreignId('kasir_id')->constrained('users'); // Sesuai saran tambahan sebelumnya
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
