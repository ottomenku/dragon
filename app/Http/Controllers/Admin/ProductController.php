<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\PublicUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('created_at')->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:gyogyteak,illoolajok,kozmetikumok'],
            'intro' => ['required', 'string', 'max:255'],
            'moreinfo' => ['nullable', 'string'],
            'ar' => ['required', 'integer', 'min:0'],
            'kedv' => ['required', 'integer'],
            'public' => ['boolean'],
            'tomain' => ['boolean'],
        ]);

        $validated['public'] = $request->boolean('public');
        $validated['tomain'] = $request->boolean('tomain');

        if ($request->hasFile('image')) {
            $validated['image'] = PublicUploads::storeImage($request->file('image'));
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Termék létrehozva.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:gyogyteak,illoolajok,kozmetikumok'],
            'intro' => ['required', 'string', 'max:255'],
            'moreinfo' => ['nullable', 'string'],
            'ar' => ['required', 'integer', 'min:0'],
            'kedv' => ['required', 'integer'],
            'public' => ['boolean'],
            'tomain' => ['boolean'],
        ]);

        $validated['public'] = $request->boolean('public');
        $validated['tomain'] = $request->boolean('tomain');

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = PublicUploads::storeImage($request->file('image'));
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Termék frissítve.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Termék törölve.');
    }
}
