<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_document_settings', function (Blueprint $table) {
            $table->id();
            $table->longText('aszf_content');
            $table->longText('shipping_terms_content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_document_settings');
    }
};
