<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\ForgotPasswordController;
use App\Http\Controllers\FriendController;

//Route For Middleware
Route::middleware([JwtAuth::class])->group(function () {

//Routes for Users

    //Route for logout user
    Route::post('logout', [RegisterController::class, 'logout']);
    //Route for Update user
    Route::put('updateUser/{id}', [RegisterController::class, 'UpdateUser']);
    //Route for delete user
    Route::delete('deleteUser/{id}', [RegisterController::class, 'DeleteUser']);
    //Route for search user
    Route::get('search/{name}', [RegisterController::class, 'SearchUser']);

//Routes for Post

    //Route for show posts
    Route::get('/posts', [PostController::class, 'index']);
    //Route for show post top-five
    Route::get('/posts/top-five', [PostController::class, 'indexTopFive']);
    //Route for show post
    Route::get('/posts/{post}', [PostController::class, 'show']);
    //Route for save post
    Route::post('/post', [PostController::class, 'store']);
    //Route for update post
    Route::put('update', [PostController::class, 'UpdatePost']);
    //Route for delele post
    Route::delete('delete/{id}', [PostController::class, 'DeletePost']);

//Routes for Comment

    //Route for show comment
    Route::get('/comments', [CommentController::class, 'index']);
    //Route for save comment
    Route::post('/posts/{post}/comment', [CommentController::class, 'store']);
    //Route for delete comment
    Route::delete('DeleteComment/{id}', [CommentController::class, 'DeleteComment']);
    //Route for update comment
    Route::put('updateComment', [CommentController::class, 'updateComment']);

    //Route for Add Friend
    Route::post('/addfriend', [FriendController::class, 'addFriend']);
});

// Route for registration user
Route::post('register', [RegisterController::class, 'register']);
//Route for login user
Route::post('login', [RegisterController::class, 'login']);
//Route for email verification
Route::get('emailConfirmation/{email}/{token}', [RegisterController::class, 'emailVarify']);
//Route for Forgetpassword
Route::get('/forgotPassword', [ForgotPasswordController::class, 'forgotPassword']);
//Route for verify email test check mail is send to mail trap for testing
Route::get('verifyemail', function () {
    $details = [
        'title' => 'Mail from hafizfaheem',
        'body' => 'This is for testing email using smtp'
    ];
    \Mail::to('hafizfaheem034@gmail.com')->send(new \App\Mail\verifyemail($details));
    dd("Email is Sent.");
});
