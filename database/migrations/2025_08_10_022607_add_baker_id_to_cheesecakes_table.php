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
        Schema::table('cheesecakes', function (Blueprint $table) {
            $table->unsignedBigInteger('baker_id')->nullable()->after('id');
            $table->foreign('baker_id')->references('id')->on('users')->onDelete('set null');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheesecakes', function (Blueprint $table) {
            $table->dropForeign(['baker_id']);
            $table->dropColumn('baker_id');
            $table->dropSoftDeletes();
        });
    }
};
