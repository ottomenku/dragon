<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barion_settings', function (Blueprint $table) {
            $table->string('pixel_id', 32)->nullable()->after('use_test');
        });
    }

    public function down(): void
    {
        Schema::table('barion_settings', function (Blueprint $table) {
            $table->dropColumn('pixel_id');
        });
    }
};
