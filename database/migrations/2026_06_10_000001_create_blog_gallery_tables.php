<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('intro');
            $table->longText('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_gallery_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_gallery_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('intro')->nullable();
            $table->longText('text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_gallery_unit_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_gallery_unit_id')->constrained('blog_gallery_units')->cascadeOnDelete();
            $table->string('type');
            $table->string('image_path')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_gallery_unit_media');
        Schema::dropIfExists('blog_gallery_units');
        Schema::dropIfExists('blog_galleries');
    }
};
