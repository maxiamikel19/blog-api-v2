<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Like",
 *     type="object",
 *     required={"like"},
 *     @OA\Property(property="id", type="integer", description="The like id"),
 *     @OA\Property(property="like", type="boolean", description="Like (true or false)"),
 * )
 */
class Like extends Model
{
    protected $fillable = ['user_id', 'post_id','like'];
    public function post(){
        return $this->belongsTo(Post::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
