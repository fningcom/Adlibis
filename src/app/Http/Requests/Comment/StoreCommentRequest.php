<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'content' => 'required|string',
            'commentable_type' => 'required|string|in:App\Models\News,App\Models\VideoPost',
            'commentable_id' => 'required|integer|exists:' . $this->getTableName(),
            'parent_id' => 'nullable|integer|exists:comments,id',
        ];
    }

    private function getTableName()
    {
        $type = $this->input('commentable_type');
        
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