<?php

namespace App\Http\Controllers\API;

use App\Models\Post;
use App\Services\CloudinaryUploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController
{
    protected $cloudinaryUploader;

    public function __construct(CloudinaryUploader $cloudinaryUploader)
    {
        $this->cloudinaryUploader = $cloudinaryUploader;
    }

    public function index(): JsonResponse
    {
        $posts = Post::where('active', true)
            ->orderBy('rank', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($posts);
    }

    public function show(string $id): JsonResponse
    {
        $post = Post::where('id', $id)->firstOrFail();
        return response()->json($post);
    }

    public function showWithRelated(string $id): JsonResponse
    {
        $post = Post::where('id', $id)->firstOrFail();

        // Get the previous post (if exists), ordered by created_at in descending order
        $prePost = Post::where('active', true)
            ->where('created_at', '<', $post->created_at) // You can also use 'id' for simpler logic
            ->orderBy('rank', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        // Get the next post (if exists), ordered by created_at in ascending order
        $nextPost = Post::where('active', true)
            ->where('created_at', '>', $post->created_at)
            ->orderBy('rank', 'desc')
            ->orderBy('created_at', 'asc')
            ->first();

        // Get related posts (you can define how to identify related posts, here I use category as an example)
        // You might need to add a relationship method or a field like `category_id` or `tags`
        $relatedPosts = Post::where('active', true)
            ->where('id', '!=', $post->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'post' => $post,
            'prePost' => $prePost,
            'nextPost' => $nextPost,
            'relatedPosts' => $relatedPosts
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'visible_on_website' => 'boolean',
            'rank' => 'integer',
        ]);

        $id = Str::slug($validated['title'], '-');

        $photos = [];
        if ($request->hasFile('photos')) {
            $photos = $this->cloudinaryUploader->uploadFiles($request->file('photos'));
        }

        $post = Post::create([
            'id' => $id,
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'content' => $validated['content'],
            'cover_photo_url' => $photos[0],
            'visible_on_website' => $validated['visible_on_website'] ?? false,
            'rank' => $validated['rank'] ?? 1,
            'active' => true,
            'created_at' => now()->timestamp,
        ]);

        return response()->json($post, 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
            'visible_on_website' => 'boolean',
            'rank' => 'integer',
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

        $post->update([
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'content' => $validated['content'],
            'cover_photo_url' => $photos[0],
            'visible_on_website' => $validated['visible_on_website'] ?? false,
            'rank' => $validated['rank'] ?? 1,
            'active' => $post->active,
        ]);

        return response()->json($post, 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        $post->update(['active' => false]);
        return response() -> json($post, 202);
    }
}
