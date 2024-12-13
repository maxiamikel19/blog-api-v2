<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class CommentController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/add/comments",
     *     summary="Create a new Comment",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Comment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Post id is required or Comment is required or Post id must be an integer",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/Comment")
     *     )
     * )
     */
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
                'message' => 'Comment created successfully'
            ], 201);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }
}
