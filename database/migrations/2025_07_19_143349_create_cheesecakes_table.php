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
        Schema::create('cheesecakes', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->string('ukuran', 100);
            $table->text('deskripsi')->nullable();
            $table->integer('jumlah')->default(0);
            $table->string('harga', 50);
            $table->text('gambar')->nullable();
            $table->date('tanggal_dibuat');
            $table->text('qr_code')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheesecakes');
    }
};
