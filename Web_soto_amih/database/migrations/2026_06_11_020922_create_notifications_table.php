<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('pesan');
            $table->string('ikon')->default('bell'); // bell, check, fire
            $table->string('warna')->default('orange'); // orange, green, purple, red
            $table->enum('untuk_role', ['kasir', 'chef', 'owner']); // target role
            $table->foreignId('untuk_user_id')->nullable()->constrained('users')->nullOnDelete(); // null = broadcast ke semua role
            $table->foreignId('dari_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};