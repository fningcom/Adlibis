<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VideoPost;
use Illuminate\Http\Request;

class VideoPostController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $cursor = $request->get('cursor');

        $query = VideoPost::orderBy('created_at', 'desc');

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        $videoPosts = $query->limit($limit + 1)->get();

        $hasMore = $videoPosts->count() > $limit;
        if ($hasMore) {
            $videoPosts = $videoPosts->take($limit);
        }

        $nextCursor = $hasMore ? $videoPosts->last()->id : null;

        return response()->json([
            'data' => $videoPosts,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $videoPost = VideoPost::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($videoPost, 201);
    }

    public function show(VideoPost $videoPost, Request $request)
    {
        $cursor = $request->get('cursor');
        $limit = $request->get('limit', 10);

        $query = $videoPost->comments()
            ->with(['user', 'replies.user', 'replies.replies'])
            ->orderBy('created_at', 'desc');

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        $comments = $query->limit($limit)->get();

        $nextCursor = $comments->last()?->id;
        $hasMore = $comments->count() === $limit;

        $videoPost->loadMissing(['comments']);

        return response()->json([
            'data' => $videoPost,
            'comments' => $comments,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ]);
    }

    public function update(Request $request, VideoPost $videoPost)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $videoPost->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($videoPost);
    }

    public function destroy(VideoPost $videoPost)
    {
        $videoPost->delete();

        return response()->json(['message' => 'Video post deleted successfully']);
    }
}