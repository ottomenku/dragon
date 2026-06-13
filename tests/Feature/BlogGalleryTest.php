<?php

namespace Tests\Feature;

use App\Models\BlogGallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_page_shows_images_in_slider_mode(): void
    {
        $gallery = BlogGallery::current();
        $gallery->update([
            'name' => 'Tavaszi kert',
            'intro' => 'Rövid bevezető',
            'display_mode' => BlogGallery::DISPLAY_SLIDER,
        ]);

        $gallery->images()->create([
            'image_path' => 'products/test.jpg',
            'title' => 'Első kép',
            'description' => 'Leírás',
            'sort_order' => 0,
        ]);

        $this->get(route('blog-gallery.index'))
            ->assertOk()
            ->assertSee('Tavaszi kert')
            ->assertSee('Első kép')
            ->assertSee('/storage/products/test.jpg', false);
    }

    public function test_public_gallery_page_shows_images_in_list_mode(): void
    {
        $gallery = BlogGallery::current();
        $gallery->update([
            'name' => 'Lista galéria',
            'display_mode' => BlogGallery::DISPLAY_LIST,
        ]);

        $gallery->images()->create([
            'image_path' => 'products/list.jpg',
            'title' => 'Lista kép',
            'sort_order' => 0,
        ]);

        $this->get(route('blog-gallery.index'))
            ->assertOk()
            ->assertSee('Lista galéria')
            ->assertSee('Lista kép');
    }

    public function test_admin_can_update_gallery_with_batch_uploaded_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['id' => 1]);

        $uploadResponse = $this->actingAs($admin)->post(route('admin.blog-gallery-images.store'), [
            'images' => [
                UploadedFile::fake()->create('photo1.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('photo2.jpg', 100, 'image/jpeg'),
            ],
        ]);

        $uploadResponse->assertOk()->assertJsonCount(2, 'images');
        $paths = collect($uploadResponse->json('images'))->pluck('path')->all();

        $this->actingAs($admin)->put(route('admin.blog-gallery.update'), [
            'name' => 'Friss galéria',
            'intro' => 'Intro',
            'description' => 'Leírás',
            'display_mode' => 'list',
            'images' => [
                [
                    'stored_image_path' => $paths[0],
                    'title' => 'Első',
                    'description' => null,
                    'sort_order' => 0,
                ],
                [
                    'stored_image_path' => $paths[1],
                    'title' => null,
                    'description' => 'Második leírás',
                    'sort_order' => 1,
                ],
            ],
        ])->assertRedirect();

        $gallery = BlogGallery::current()->fresh('images');
        $this->assertSame('Friss galéria', $gallery->name);
        $this->assertSame('list', $gallery->display_mode);
        $this->assertCount(2, $gallery->images);
        $this->assertSame('Első', $gallery->images->first()->title);
        Storage::disk('public')->assertExists($paths[0]);
    }

    public function test_admin_can_upload_multiple_gallery_images(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin)->post(route('admin.blog-gallery-images.store'), [
            'images' => [
                UploadedFile::fake()->create('photo1.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('photo2.jpg', 100, 'image/jpeg'),
            ],
        ]);

        $response->assertOk()->assertJsonStructure(['images' => [['path', 'url']]]);
        $this->assertStringStartsWith('products/', $response->json('images.0.path'));
        Storage::disk('public')->assertExists($response->json('images.0.path'));
        Storage::disk('public')->assertExists($response->json('images.1.path'));
    }

    public function test_welcome_page_has_gallery_nav_link(): void
    {
        $this->get(route('welcome'))
            ->assertOk()
            ->assertSee('Galéria')
            ->assertSee(route('blog-gallery.index'), false);
    }
}
