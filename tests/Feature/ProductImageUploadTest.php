<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_product_image_on_update(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['id' => 1]);
        $product = Product::create([
            'title' => 'Teszt illóolaj',
            'category' => 'illoolajok',
            'intro' => 'Rövid intro',
            'ar' => 1100,
            'kedv' => 0,
            'public' => true,
            'tomain' => false,
        ]);

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'title' => $product->title,
            'category' => $product->category,
            'intro' => $product->intro,
            'moreinfo' => $product->moreinfo,
            'ar' => $product->ar,
            'kedv' => $product->kedv,
            'public' => '1',
            'tomain' => '0',
            'image' => UploadedFile::fake()->create('thyme.jpg', 100, 'image/jpeg'),
        ])->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertNotNull($product->image);
        $this->assertStringStartsWith('products/', $product->image);
        Storage::disk('public')->assertExists($product->image);
    }
}
