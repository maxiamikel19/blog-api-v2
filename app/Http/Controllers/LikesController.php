<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikesController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/add/likes",
     *     summary="Create a new like",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Liked successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Post id is required or Post Id must be an integer",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="You can like a post only once!",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     )
     * )
     */
    public function store(Request $request){
        $LIKE=1;
        $validator = Validator::make($request->all() ,[
            'post_id' => 'integer|required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $likedBefore = Like::where('user_id', auth()->user()->id)->where('post_id', $request->post_id)->first();
            if($likedBefore){
                return response()->json([
                    'message' => 'You can like a post only once!'
                ], 403);
            }else{
                Like::create([
                    'post_id' => $request->post_id,
                    'like' => $LIKE,
                    'user_id' => Auth::user()->id,
                ]);

                return response()->json([
                    'message' => 'Liked successfully'
                ], 201);
            }

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * @OA\Put(
     *     path="/api/remove/likes/{id}",
     *     summary="Remove a like to a post",
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Liked successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     )
     * )
     */
    public function unlikePost($id){
        $user = auth()->user();
        $unlike = 0;
        $like = Like::where('id', $id)
                ->where('user_id', $user->id)
                ->where('like', 1)
                ->first();
        if($like == null){
            return null;
        }
        $like->delete();
        return response()->json([
            'message' => 'Post sucessfully unliked'
        ], 200);
    }
}
