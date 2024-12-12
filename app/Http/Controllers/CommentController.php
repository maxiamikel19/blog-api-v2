<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all() ,[
            'post_id' => 'integer|required',
            'comment' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = Comment::create([
                'post_id' => $request->post_id,
                'comment' => $request->comment,
                'user_id' => Auth::user()->id,
            ]);

            return response()->json([
                'message' => 'Comment created successfully',
                'comment' => $comment
            ], 201);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
