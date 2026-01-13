<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $cursor = $request->get('cursor');
        $commentableType = $request->get('commentable_type');
        $commentableId = $request->get('commentable_id');

        $query = Comment::with(['user', 'replies.user', 'replies.replies'])
            ->orderBy('created_at', 'desc');

        if ($commentableType && $commentableId) {
            $query->where('commentable_type', $commentableType)
                  ->where('commentable_id', $commentableId)
                  ->whereNull('parent_id'); // Только корневые комментарии
        }

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        $comments = $query->limit($limit)->get();

        $nextCursor = $comments->last()?->id;
        $hasMore = $comments->count() === $limit;

        return response()->json([
            'data' => $comments,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'commentable_type' => 'required|string|in:App\Models\News,App\Models\VideoPost',
            'commentable_id' => 'required|integer|exists:' . $this->getTableNameFromType($request->commentable_type) . ',id',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        $comment = Comment::create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
            'commentable_type' => $request->commentable_type,
            'commentable_id' => $request->commentable_id,
            'parent_id' => $request->parent_id,
        ]);

        $comment->load(['user', 'replies']);

        return response()->json($comment, 201);
    }

    public function show(Comment $comment)
    {
        $comment->load(['user', 'replies.user', 'replies.replies']);

        return response()->json($comment);
    }

    public function update(Request $request, Comment $comment)
    {
        if (!$comment->isAuthor($request->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        $comment->load(['user', 'replies']);

        return response()->json($comment);
    }

    public function destroy(Request $request, Comment $comment)
    {
        if (!$comment->isAuthor($request->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    private function getTableNameFromType($type)
    {
        switch ($type) {
            case 'App\Models\News':
                return 'news';
            case 'App\Models\VideoPost':
                return 'video_posts';
            default:
                return '';
        }
    }
}