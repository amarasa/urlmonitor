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
            $table->integer('number_of_urls')->default(0);
            $table->boolean('is_index')->default(false);
            $table->boolean('enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sitemaps', function (Blueprint $table) {
            $table->dropColumn('number_of_urls');
            $table->dropColumn('is_index');
            $table->dropColumn('enabled');
        });
    }
};
