<?php

namespace App\Http\Controllers\Kiosk;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ProductCollection;
use Intervention\Image\Laravel\Facades\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if(Auth::check() && Auth::user()->isAdmin()) {
            return new ProductCollection(Product::orderBy('available', 'desc')->get());
        }

        return new ProductCollection(Product::where('available', 1)->orderBy('id', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'image' => ['required', 'image', 'file', 'max:1028'],
            'category_id' => ['required', 'exists:categories,id'],
        ], [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not be greater than 255 characters.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'image.required' => 'The image field is required.',
            'image.image' => 'The image must be an image.',
            'image.file' => 'The image must be a file.',
            'image.max' => 'The image must not be greater than 1028 kilobytes.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The category does not exist.',
        ]);

        $file = $request->file('image');
        $name = Str::uuid() . '.' . $file->extension();
        
        $image = Image::read($file);
        $image->cover(500, 600);

        if(!File::exists(Storage::path('products'))) {
            File::makeDirectory(Storage::path('products'));
        }

        $image->save(Storage::path('products/'.$name));

        $product = Product::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'image' => $name,
            'available' => 1,
            'category_id' => $data['category_id'],
        ]);

        return response()->json([
            'product' => $product,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductCollection([$product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric'],
            'image' => ['nullable', 'image', 'file', 'max:1028'],
            'available' => ['nullable', 'boolean'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ], [
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name must not be greater than 255 characters.',
            'price.numeric' => 'The price must be a number.',
            'image.image' => 'The image must be an image.',
            'image.file' => 'The image must be a file.',
            'image.max' => 'The image must not be greater than 1028 kilobytes.',
            'available.boolean' => 'The available must be a boolean.',
            'category_id.exists' => 'The category does not exist.',
        ]);
        
        $file = $request->file('image');

        if($file) {
            $name = Str::uuid() . '.' . $file->extension();
            $image = Image::read($file);
            $image->cover(500, 600);
    
            if(!File::exists(Storage::path('products'))) {
                File::makeDirectory(Storage::path('products'));
            }

            Storage::delete('products/'.$product->image);

            $image->save(Storage::path('products/'.$name));
            $product->image = $name;
        }
        
        $product->name = $data['name'] ?? $product->name;
        $product->price = $data['price'] ?? $product->price;
        $product->available = $data['available'] ?? $product->available;
        $product->category_id = $data['category_id'] ?? $product->category_id;

        $product->save();

        return response()->json([
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Storage::delete('products/'.$product->image);
        $product->delete();

        return response()->noContent();
    }
}
