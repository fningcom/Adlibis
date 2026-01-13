<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $cursor = $request->get('cursor');

        $query = News::orderBy('created_at', 'desc');

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        $news = $query->limit($limit + 1)->get();

        $hasMore = $news->count() > $limit;
        if ($hasMore) {
            $news = $news->take($limit);
        }

        $nextCursor = $hasMore ? $news->last()->id : null;

        return response()->json([
            'data' => $news,
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

        $news = News::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($news, 201);
    }

    public function show(News $news, Request $request)
    {
        $cursor = $request->get('cursor');
        $limit = $request->get('limit', 10);

        $query = $news->comments()
            ->with(['user', 'replies.user', 'replies.replies'])
            ->orderBy('created_at', 'desc');

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        $comments = $query->limit($limit)->get();

        $nextCursor = $comments->last()?->id;
        $hasMore = $comments->count() === $limit;

        $news->loadMissing(['comments']);

        return response()->json([
            'data' => $news,
            'comments' => $comments,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ]);
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $news->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($news);
    }

    public function destroy(News $news)
    {
        $news->delete();

        return response()->json(['message' => 'News deleted successfully']);
    }
}