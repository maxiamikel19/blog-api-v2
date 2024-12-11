<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all() ,[
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json([
                'message' => 'Post created successfully',
                'post_id' => $post->id
            ], 201);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
