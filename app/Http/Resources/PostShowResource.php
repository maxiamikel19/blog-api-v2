<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author_id' => $this->user_id,
            'published_at' => $this->created_at->diffForHumans(),
            'last_update' => $this->updated_at->diffForHumans(),
            'author' => User::find($this->user_id),
            'total_comments' => Comment::where('post_id',$this->id)->count(),
            'comments' => Comment::where('post_id', $this->id)->get(),
            'total_likes' => Like::where('post_id', $this->id)->where('like', 1)->count()
        ];
    }
}
