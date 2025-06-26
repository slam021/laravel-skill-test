<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        // dd('yes');
        $posts = Post::published()
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_draft' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $post = Auth::user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    public function show(Post $post): JsonResponse
    {
        if ($post->is_draft || $post->published_at > Carbon::now()) {
            abort(404);
        }

        return response()->json($post->load('user'));
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'is_draft' => 'sometimes|boolean',
            'published_at' => 'nullable|date',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(null, 204);
    }
}
