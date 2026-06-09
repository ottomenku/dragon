<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_content_settings', function (Blueprint $table) {
            $table->id();
            $table->longText('contact_content');
            $table->longText('footer_content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_content_settings');
    }
};
