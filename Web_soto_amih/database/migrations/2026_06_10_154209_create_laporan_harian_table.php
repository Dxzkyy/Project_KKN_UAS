<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            // modal_harian sekarang nullable karena diisi owner, bukan kasir
            $table->decimal('modal_harian', 15, 2)->nullable()->change();
            // laba_bersih juga nullable sampai owner set modal
            $table->decimal('laba_bersih', 15, 2)->nullable()->change();
            // kasir_id juga nullable (laporan dikirim kasir, tapi modal diset owner)
            $table->foreignId('owner_id')->nullable()->constrained('users')->after('kasir_id');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_harian', function (Blueprint $table) {
            $table->decimal('modal_harian', 15, 2)->nullable(false)->change();
            $table->decimal('laba_bersih', 15, 2)->nullable(false)->change();
            $table->dropForeign(['owner_id']);
            $table->dropColumn('owner_id');
        });
    }
};