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
    Route::post('/logout', [AuthController::class, 'logout']);

    //Post
    Route::post('/add/posts', [PostController::class, 'store']);
    Route::put('/edit/posts/{id}', [PostController::class, 'update']);
    Route::delete('/delete/posts/{id}', [PostController::class, 'destroy']);

    //Comment
    Route::post('/add/comments', [CommentController::class, 'store']);

    //Like
    Route::post('/add/likes', [LikesController::class, 'store']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/single/posts/{id}', [PostController::class, 'show']);
Route::get('/all/posts', [PostController::class, 'index']);

