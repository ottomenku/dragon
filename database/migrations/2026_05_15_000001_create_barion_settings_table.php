<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barion_settings', function (Blueprint $table) {
            $table->id();
            $table->text('pos_key')->nullable();
            $table->string('payee')->nullable();
            $table->boolean('use_test')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barion_settings');
    }
};
