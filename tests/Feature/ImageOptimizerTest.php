<?php

namespace Tests\Feature;

use App\Support\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    public function test_optimizer_compresses_large_uploaded_image(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is required.');
        }

        $file = UploadedFile::fake()->image('large.jpg', 2400, 1800);

        $optimized = ImageOptimizer::tryOptimizeToJpeg($file);

        $this->assertNotNull($optimized);
        $this->assertLessThan($file->getSize(), strlen($optimized));

        $image = imagecreatefromstring($optimized);
        $this->assertInstanceOf(\GdImage::class, $image);
        $this->assertLessThanOrEqual(1600, max(imagesx($image), imagesy($image)));
        imagedestroy($image);
    }
}
