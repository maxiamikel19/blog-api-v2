<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LikesController extends Controller
{
    public function store(Request $request){
        $LIKE=1;
        $validator = Validator::make($request->all() ,[
            'post_id' => 'integer|required',
            'like' => 'integer',
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
                ]);
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
}
