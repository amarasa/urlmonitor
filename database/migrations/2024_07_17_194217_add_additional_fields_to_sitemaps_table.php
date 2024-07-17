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
        Schema::table('sitemaps', function (Blueprint $table) {
            $table->integer('errors')->default(0);
            $table->boolean('is_pending')->default(false);
            $table->timestamp('last_downloaded')->nullable();
            $table->timestamp('last_submitted')->nullable();
            $table->integer('warnings')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sitemaps', function (Blueprint $table) {
            $table->dropColumn(['errors', 'is_pending', 'last_downloaded', 'last_submitted', 'warnings']);
        });
    }
};
