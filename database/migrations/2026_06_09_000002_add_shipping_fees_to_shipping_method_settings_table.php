<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_method_settings', function (Blueprint $table) {
            $table->unsignedInteger('mpl_fee')->default(0)->after('mpl_enabled');
            $table->unsignedInteger('foxpost_fee')->default(0)->after('foxpost_enabled');
            $table->unsignedInteger('dhl_fee')->default(0)->after('dhl_enabled');
            $table->unsignedInteger('gls_fee')->default(0)->after('gls_enabled');
            $table->unsignedInteger('packeta_fee')->default(0)->after('packeta_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_method_settings', function (Blueprint $table) {
            $table->dropColumn(['mpl_fee', 'foxpost_fee', 'dhl_fee', 'gls_fee', 'packeta_fee']);
        });
    }
};
