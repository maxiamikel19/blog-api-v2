<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostShowResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      title="API Laravel Documentation",
 *      version= "1.0.0",
 *      description="Blog API",
 *      @OA\Contact(
 *          name= "Amikel Maxi",
 *          email="maxloversist@gmail.com"
 *      )
 * )
 */

class PostController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/add/posts",
     *     summary="Cria um novo post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Post created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *      @OA\Response(
     *         response="422",
     *         description="Title is required or Title length must be less than 255 or Content is required ",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *      @OA\Response(
     *         response="500",
     *         description="Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     )
     * )
     */
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
            ], 201);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }


     /**
     * @OA\Put(
     *     path="/api/edit/posts/{id}",
     *     summary="Update a post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Post updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Title is required or Content is required or Title must be less than 255 catacteres",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Post Id not found",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="You are not owm of this post or Internal server error",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     )
     * )
     */
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

            $response = Gate::authorize('update', $post);

                if($response->allowed()){
                    $post->title = $request->title;
                $post->content = $request->content;
                $post->update();

                return response()->json([
                    'message' => 'Post updated successfully',
                    'post' => $post
                ], 200);
            }

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

      /**
     * @OA\Get(
     *     path="/api/single/posts/{id}",
     *     summary="Return a specific post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Return the Post",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Post id not found"
     *     )
     * )
     */
    public function show($id){
        try {
            //$post = Post::with('user', 'comments', 'likes')->where('id',$id)->first();
            $post = Post::where('id', $id)->first();
            $post_a = new PostShowResource($post);
            if(!$post){
                return response()->json([
                    'message' => 'Post id not found'
                ], 404);
            }

            return response()->json([
                'post' => $post_a
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/all/posts?page=1",
     *     summary="List all posts using pagination of 10 posts per page",
     *     @OA\Response(
     *         response="200",
     *         description="List the 10 last posted posts ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function index  (){
        try {
           $posts = Post::orderBy('id', 'desc')->paginate(10);
           return response()->json([
            'posts' => $posts
        ], 200);

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

     /**
     * @OA\Delete(
     *     path="/api/delete/posts/{id}",
     *     summary="Delete a post",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Post successfully deleted"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Post id not found"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="You are not owm of this post"
     *     )
     * )
     */
    public function destroy($id){
        try {
            $post = Post::find($id);
            if(!$post){
                return response()->json([
                    'message' => 'Post id not found'
                ], 404);
            }
            $response = Gate::authorize('delete', $post);

                 if($response->allowed()){
                        $post->delete();
                        return response()->json([
                            'message' => 'Post deleted successfully'
                        ], 200);
                 }

        } catch (\Exception $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 401);
        }
    }



      /**
     * @OA\Get(
     *     path="/api/posts/search",
     *     summary="Return a specific post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Like")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Return the list of post based on your query text ",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="To search posts, please fill the input box"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No post found in this context"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['message' => 'To search posts, please fill the input box'], 400);
        }

        $posts = Post::where('title', 'like', "%$query%")
            ->orWhere('content', 'like', "%$query%")
            ->orWhere('created_at','like', "%$query%")
            ->paginate(10);

        if ($posts->isEmpty()) {
            return response()->json(['message' => 'No post found in this context.'], 404);
        }

        return response()->json(['posts' => $posts], 200);
    }

}
