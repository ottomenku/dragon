<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_method_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('mpl_enabled')->default(true);
            $table->boolean('foxpost_enabled')->default(true);
            $table->boolean('dhl_enabled')->default(true);
            $table->boolean('gls_enabled')->default(true);
            $table->boolean('packeta_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_method_settings');
    }
};
