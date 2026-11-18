<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kebun_id')->nullable()->constrained()->nullOnDelete();
            $table->string('jenis');
            $table->string('judul', 100);
            $table->date('tanggal_mulai');
            $table->unsignedInteger('ulang_setiap')->nullable();
            $table->string('ulang_satuan')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'tanggal_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
