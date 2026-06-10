<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barion_settings', function (Blueprint $table) {
            $table->boolean('pixel_footer_enabled')->default(false)->after('pixel_id');
        });
    }

    public function down(): void
    {
        Schema::table('barion_settings', function (Blueprint $table) {
            $table->dropColumn('pixel_footer_enabled');
        });
    }
};
