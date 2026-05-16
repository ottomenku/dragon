<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('barion_payment_id', 64)->nullable()->after('payment_method');
            $table->string('payment_status', 32)->nullable()->after('barion_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['barion_payment_id', 'payment_status']);
        });
    }
};
