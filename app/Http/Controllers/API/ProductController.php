<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\CloudinaryUploader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $cloudinaryUploader;

    public function __construct(CloudinaryUploader $cloudinaryUploader)
    {
        $this->cloudinaryUploader = $cloudinaryUploader;
    }

    public function index(): JsonResponse
    {
        $products = Product::where('active', true)->with('category')->get();
        return response()->json($products);
    }

    public function outstanding(): JsonResponse
    {
        $products = Product::where('visible_on_home_page', true)
            ->where('active', true)
            ->with('category')
            ->get();
        return response()->json($products);
    }

    public function byCategoryName(string $categoryName): JsonResponse
    {
        $category = Category::where('name', $categoryName)->first();
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 400);
        }

        $products = Product::where('category_id', $category->id)
            ->where('active', true)
            ->with('category')
            ->get();
        return response()->json($products);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::where('id', $id)->with('category')->firstOrFail();
        return response()->json($product);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|string|exists:categories,id',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'details' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'visible_on_home_page' => 'boolean',
        ]);

        $photos = [];
        if ($request->hasFile('photos')) {
            $photos = $this->cloudinaryUploader->uploadFiles($request->file('photos'));
        }

        $product = Product::create([
            'id' => Str::uuid()->toString(),
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'features' => $validated['features'],
            'details' => $validated['details'],
            'price' => $validated['price'],
            'photos' => $photos,
            'visible_on_home_page' => $validated['visible_on_home_page'] ?? false,
            'active' => true,
            'created_at' => now()->timestamp,
        ]);

        return response()->json($product->load('category'), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|string|exists:categories,id',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'details' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'photos.*' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'existing_photos' => 'nullable|json',
            'visible_on_home_page' => 'boolean',
        ]);

        $photos = [];
        if ($request->has('existing_photos')) {
            $existingPhotos = json_decode($request->input('existing_photos'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($existingPhotos)) {
                $photos = $existingPhotos;
            } else {
                return response()->json(['message' => 'Invalid existing_photos JSON format'], 422);
            }
        }

        if ($request->hasFile('photos')) {
            $newPhotos = $this->cloudinaryUploader->uploadFiles($request->file('photos'));
            $photos = array_merge($photos, $newPhotos);
        }

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'features' => $validated['features'],
            'details' => $validated['details'],
            'price' => $validated['price'],
            'photos' => $photos,
            'visible_on_home_page' => $validated['visible_on_home_page'] ?? $product->visible_on_home_page,
            'active' => $product->active,
        ]);

        return response()->json($product->load('category'));
    }

    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['active' => false]);
        return response()->json($product->load('category'));
    }

    public function markVisibleOnHomePage(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['visible_on_home_page' => true]);
        return response()->json($product->load('category'));
    }
}
