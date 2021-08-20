<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentsController;
use App\Http\Controllers\Api\LikesController;
use App\Http\Controllers\Api\PostController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class,'register'])->name('registro');
Route::post('login', [AuthController::class,'login'])->name('login');
Route::get('logout',[AuthController::class,'logout'])->name('cerrar_sesion');


// Route::resource('posts', PostController::class)->except('show','edit');


Route::post('posts/create',[PostController::class,'create'])->name('post.create')->middleware('jwtAuth');
Route::post('posts/delete',[PostController::class,'delete'])->name('post.delete')->middleware('jwtAuth');
Route::post('posts/update',[PostController::class,'update'])->name('post.update')->middleware('jwtAuth');
Route::get('posts',[PostController::class,'index'])->name('post.index')->middleware('jwtAuth');

Route::post('comments/create',[CommentsController::class,'create'])->name('comment.create')->middleware('jwtAuth');
Route::post('comments/delete',[CommentsController::class,'delete'])->name('comment.delete')->middleware('jwtAuth');
Route::post('comments/update',[CommentsController::class,'update'])->name('comment.update')->middleware('jwtAuth');
Route::post('posts/comments',[CommentsController::class,'index'])->name('comment.index')->middleware('jwtAuth');



Route::post('posts/like',[LikesController::class,'like'])->name('comment.index')->middleware('jwtAuth');

// Route::group(['middleware' => ['jwt.verify']], function(){

//     Route::post('user','Api\AuthController@autenticacionUsuario');

// });
