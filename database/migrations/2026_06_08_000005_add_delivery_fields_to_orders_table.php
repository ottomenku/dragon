<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_type', 16)->default('home')->after('shipping_method');
            $table->string('pickup_point_external_id', 64)->nullable()->after('delivery_type');
            $table->string('pickup_point_name')->nullable()->after('pickup_point_external_id');
            $table->string('pickup_point_address')->nullable()->after('pickup_point_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_type',
                'pickup_point_external_id',
                'pickup_point_name',
                'pickup_point_address',
            ]);
        });
    }
};
