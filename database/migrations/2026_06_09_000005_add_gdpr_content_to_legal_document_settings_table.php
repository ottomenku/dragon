<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('legal_document_settings', function (Blueprint $table) {
            $table->longText('gdpr_content')->nullable()->after('shipping_terms_content');
        });
    }

    public function down(): void
    {
        Schema::table('legal_document_settings', function (Blueprint $table) {
            $table->dropColumn('gdpr_content');
        });
    }
};
