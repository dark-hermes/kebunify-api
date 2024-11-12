<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'writer' => $this->whenLoaded('writer'),
            'tags' => $this->tags,
            'created_at' => $this->formatted_created_at,
            'comments' => ForumCommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
