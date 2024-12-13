<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Comment",
 *     type="object",
 *     required={"comment"},
 *     @OA\Property(property="id", type="integer", description="Comment id"),
 *     @OA\Property(property="comment", type="string", description="Comment content"),
 * )
 */


class Comment extends Model
{
    protected $fillable = ['user_id', 'post_id','comment'];

    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
