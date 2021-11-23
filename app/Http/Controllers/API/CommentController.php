<?php

namespace App\Http\Controllers\API;

use App\Post;
use App\Comment;
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
        try {
            $comment = Comment::make($data);
            $comment->post()->associate($post);
            $comment->save();
            if ($comment) {
                $success['message'] =  "Comment Create Successfully";
                return response()->json([
                    $success, 200, $comment
                ]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
            return new CommentResource($comment->fresh());
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For Update Comment

    public function updateComment(UpdateCommentRequest $request)
    {
        try {
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
            if ($data) {
                $success['message'] =  "Comment Update Successfully";
                return response()->json([$success, 200, $data]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

//For Delete Comment

    public function DeleteComment($id)
    {
        try {
            $user = new Comment();
            $user = Comment::find($id);
            if ($user) {
                $user->delete();
                if ($user) {
                    $success['message'] =  "Comment Delete Successfully";
                    return response()->json([$success, 200, $user]);
                }
                return response()->json([
                    "success" => true,
                    "message" => "Comment Deleted Successfully!!",
                ]);
            } else {
                $success['message'] =  "Something went wrong";
                return response()->json($success, 404);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }
}
