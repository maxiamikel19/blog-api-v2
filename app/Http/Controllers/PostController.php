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
                'post' => $post
            ], 201);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request){
        $validator = Validator::make($request->all() ,[
            'title' => 'required|max:255',
            'content' => 'required',
            'post_id' => 'integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $post = Post::find($request->id);

            if(!$post){
                return response()->json([
                    'message' => 'Post id not found'
                ], 404);
            }

            $post->title = $request->title;
            $post->content = $request->content;
            $post->update();

            return response()->json([
                'message' => 'Post updated successfully',
                'post' => $post
            ], 200);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id){
        try {
            $post = Post::with('user', 'comments')->where('id',$id)->first();
            if(!$post){
                return response()->json([
                    'message' => 'Post id not found'
                ], 404);
            }

            return response()->json([
                'post' => $post
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }

    public function index  (){
        try {
           $posts = Post::all();
           return response()->json([
            'posts' => $posts
        ], 200);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id){
        try {
            $post = Post::find($id);
            if(!$post){
                return response()->json([
                    'message' => 'Post id not found'
                ], 404);
            }
            $post->delete();

            return response()->json([
                'message' => 'Post deleted successfully'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
