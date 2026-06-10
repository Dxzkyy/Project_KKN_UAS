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
        Schema::create('modal_harian', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->unique(); // satu modal per hari
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('catatan', 500)->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modal_harian');
    }
};
