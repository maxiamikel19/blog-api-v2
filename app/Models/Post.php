<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Post",
 *     type="object",
 *     required={"title", "content","user_id"},
 *     @OA\Property(property="id", type="integer", description="ID of the post", example="1"),
 *     @OA\Property(property="title", type="string", description="Title of the post", example="My post"),
 *     @OA\Property(property="content", type="text", description="Content of the post", example="Description of rst postmy fi"),
 *     @OA\Property(property="user_id", type="integer", description="The user ID, owm of the post", example="1"),
 * )
 */
class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'user_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

}
