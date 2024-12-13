<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){

    //POST: http://localhost:8000/api/logout
    Route::post('/logout', [AuthController::class, 'logout']);

    //Post
    //POST: http://localhost:8000/api/add/posts
    Route::post('/add/posts', [PostController::class, 'store']);
    //PUT: http://localhost:8000/api/edit/posts/1
    Route::put('/edit/posts/{id}', [PostController::class, 'update']);
    //DELETE: http://localhost:8000/api/delete/posts/2
    Route::delete('/delete/posts/{id}', [PostController::class, 'destroy']);

    //Comment
    //POST: http://localhost:8000/api/add/comments
    Route::post('/add/comments', [CommentController::class, 'store']);

    //Like
    //POST: http://localhost:8000/api/add/likes
    Route::post('/add/likes', [LikesController::class, 'store']);
    //PUT: http://localhost:8000/api/remove/likes/14
    Route::put('/remove/likes/{id}', [LikesController::class, 'unlikePost']);
});

//AUTH
//POST : http://localhost:8000/api/register
Route::post('/register', [AuthController::class, 'register']);
//POST: http://localhost:8000/api/login
Route::post('/login', [AuthController::class, 'login']);

//POST
//GET: http://localhost:8000/api/all/posts & localhost:8000/api/all/posts?page=2
Route::get('/single/posts/{id}', [PostController::class, 'show']);
//GET: http://localhost:8000/api/single/posts/3
Route::get('/all/posts', [PostController::class, 'index']);
//GET http://localhost:8000/api/posts/search?page=1
Route::get('/posts/search', [PostController::class, 'search']);

