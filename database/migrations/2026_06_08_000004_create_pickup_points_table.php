<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_points', function (Blueprint $table) {
            $table->id();
            $table->string('carrier', 32);
            $table->string('external_id', 64);
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('zip', 16)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('point_type', 32)->default('locker');
            $table->timestamps();

            $table->unique(['carrier', 'external_id']);
            $table->index(['carrier', 'city']);
            $table->index(['carrier', 'zip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_points');
    }
};
