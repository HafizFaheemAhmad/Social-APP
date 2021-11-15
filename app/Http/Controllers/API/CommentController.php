<?php

namespace App\Http\Controllers\API;

use App\Post;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Controllers\Controller;


class CommentController extends Controller
{
    // Display a listing of the resource.
    // @return \Illuminate\Http\Response
    public function index()
    {
        return CommentResource::collection(Comment::all());
    }
    //Store a newly created resource in storage.
    // param  \Illuminate\Http\Request  $request
    //return \Illuminate\Http\Response

//For save Comment

    public function store(CreateCommentRequest $request, Post $post)
    {
        $data = $request->validated();
        $comment = Comment::make($data);
        $comment->post()->associate($post);
        $comment->save();
        return new CommentResource($comment->fresh());
    }

//For Update Comment

    public function updateComment(UpdateCommentRequest $request)
    {
        $data = Comment::find($request['id']);
        $data->comment = $request->input('comment');
        if (!empty($input['attachment'])) {
            // upload Attachment
            $destinationPath = storage_path('\api\users\\');
            $input_type_aux = explode("/", $input['attachment']['mime']);
            $attachment_extention = $input_type_aux[1];
            $image_base64 = base64_decode($input['attachment']['data']);
            $file_name = uniqid() . '.' . $attachment_extention;
            $file = $destinationPath . $file_name;
            // saving in local storage
            file_put_contents($file, $image_base64);
            $data->attachment = $file_name;
        }
        //store your file into directory and db
        $data->save();
        return response()->json([
            "success" => true,
            "message" => "Comment Updated Successfully!"
        ]);
    }

//For Delete Comment

    public function DeleteComment($id)
    {
        $user = new Comment();
        $user = Comment::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                "success" => true,
                "message" => "Comment Deleted Successfully!!",
            ]);
        } else {
            return response()->json([
                "success" => true,
                "message" => "Comment deost not exist",
                "data" => $user
            ]);
        }
    }
}
