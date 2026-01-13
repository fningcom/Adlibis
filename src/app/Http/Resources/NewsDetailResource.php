<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'comments' => $this->getCommentsData($request),
        ];
    }

    private function getCommentsData($request)
    {
        // Получаем данные из контроллера, если они были переданы
        $commentsData = $request->route()->parameter('commentsData') ?? [];

        return [
            'data' => CommentTreeResource::collection($commentsData['comments'] ?? collect()),
            'next_cursor' => $commentsData['next_cursor'] ?? null,
            'has_more' => $commentsData['has_more'] ?? false,
        ];
    }
}