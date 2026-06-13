<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_galleries', function (Blueprint $table) {
            $table->string('display_mode')->default('slider')->after('description');
        });

        Schema::create('blog_gallery_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_gallery_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        $galleryId = DB::table('blog_galleries')->orderBy('sort_order')->orderBy('id')->value('id');

        if (! $galleryId) {
            $galleryId = DB::table('blog_galleries')->insertGetId([
                'name' => 'Galéria',
                'slug' => 'galeria',
                'intro' => '',
                'description' => null,
                'display_mode' => 'slider',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('blog_galleries')->where('id', $galleryId)->update([
                'display_mode' => 'slider',
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('blog_gallery_unit_media')) {
            $legacyImages = DB::table('blog_gallery_unit_media')
                ->join('blog_gallery_units', 'blog_gallery_units.id', '=', 'blog_gallery_unit_media.blog_gallery_unit_id')
                ->where('blog_gallery_unit_media.type', 'image')
                ->whereNotNull('blog_gallery_unit_media.image_path')
                ->orderBy('blog_gallery_units.sort_order')
                ->orderBy('blog_gallery_units.id')
                ->orderBy('blog_gallery_unit_media.sort_order')
                ->orderBy('blog_gallery_unit_media.id')
                ->get([
                    'blog_gallery_unit_media.image_path',
                    'blog_gallery_unit_media.caption',
                    'blog_gallery_unit_media.description',
                    'blog_gallery_unit_media.sort_order',
                ]);

            foreach ($legacyImages as $index => $image) {
                DB::table('blog_gallery_images')->insert([
                    'blog_gallery_id' => $galleryId,
                    'image_path' => $image->image_path,
                    'title' => $image->caption,
                    'description' => $image->description,
                    'sort_order' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::dropIfExists('blog_gallery_unit_media');
        Schema::dropIfExists('blog_gallery_units');

        DB::table('blog_galleries')->where('id', '!=', $galleryId)->delete();
    }

    public function down(): void
    {
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

        Schema::dropIfExists('blog_gallery_images');

        Schema::table('blog_galleries', function (Blueprint $table) {
            $table->dropColumn('display_mode');
        });
    }
};
