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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->unsignedBigInteger('kasir_id');
            $table->string('nama_pelanggan')->nullable();
            $table->decimal('total_harga', 12, 2);
            $table->decimal('bayar', 12, 2);
            $table->decimal('kembalian', 12, 2);
            $table->enum('status', ['pending', 'selesai', 'dibatalkan'])->default('pending');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'kartu'])->default('tunai');
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_transaksi');
            $table->timestamps();
            
            $table->foreign('kasir_id')->references('id')->on('users');
            $table->index(['status', 'tanggal_transaksi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
