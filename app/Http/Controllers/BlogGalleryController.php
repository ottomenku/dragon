<?php

namespace App\Http\Controllers;

use App\Models\BlogGallery;
use Illuminate\View\View;

class BlogGalleryController extends Controller
{
    public function show(): View
    {
        $gallery = BlogGallery::current();
        $gallery->load('images');

        return view('blog-gallery.show', compact('gallery'));
    }
}
