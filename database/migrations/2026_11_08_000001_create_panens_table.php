<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kebun_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->unsignedInteger('jumlah_tandan');
            $table->decimal('berat_kg', 12, 2);
            $table->decimal('harga_per_kg', 12, 2);
            $table->string('pembeli')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panens');
    }
};
