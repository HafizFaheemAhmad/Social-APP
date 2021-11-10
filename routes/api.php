<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PostController;




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// for registration user
Route::post('register', [RegisterController::class, 'register']);
//for login user
Route::post('login', [RegisterController::class, 'login']);
//for email verification
Route::get('emailConfirmation/{email}/{token}', [RegisterController::class, 'emailVarify']);
//for verify email test check mail is send to mail trap for testing
Route::get('verifyemail', function () {
    $details = [
        'title' => 'Mail from hafizfaheem',
        'body' => 'This is for testing email using smtp'
    ];
    Mail::to('hafizfaheem034@gmail.com')->send(new \App\Mail\verifyemail($details));
    dd("Email is Sent.");
});
//for logout user
Route::post('logout', [RegisterController::class, 'logout']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/top-five',[PostController::class,'indexTopFive']);
Route::get('/posts/{post}',[PostController::class,'show']);
Route::post('/post', [PostController::class,'store']);
