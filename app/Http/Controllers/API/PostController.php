<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //Display a listing of the resource.
    //return \Illuminate\Http\Response
    public function index()
    {
        return PostResource::collection(Post::all());
    }
    public function indexTopFive()
    {
        $postsQuery = Post::withCount('comments')->orderBy('comments_count')->limit(5);
        return PostResource::collection($postsQuery->get());
    }
    // Store a newly created resource in storage.
    //param  \Illuminate\Http\Request  $request
    //return \Illuminate\Http\Response

    public function store(CreatePostRequest $request)
    {

        $file_name = null;
        // converting base64 decoded image to simple image if exist
        if (!empty($request['attachment'])) {
            // upload Attachment
            $destinationPath = storage_path('\post\users\\');
            $request_type_aux = explode("/", $request['attachment']['mime']);
            $attachment_extention = $request_type_aux[1];
            $image_base64 = base64_decode($request['attachment']['data']);
            $file_name = $request['name'] . uniqid() . '.' . $attachment_extention;
            $file = $destinationPath . $file_name;
            // saving in local storage
            file_put_contents($file, $image_base64);
        }

        $post = new Post();
        $post->attachment = $file_name;
        $request->profile_image = $file_name;
        $data = $request->validated();
        $post = Post::make($data);
        $post->user()->associate($request->user_id);
        dd($post);
        $post->save();
        return new PostResource($post->fresh());
    }
    // Display the specified resource.
    //param  \App\Post  $post
    //return \Illuminate\Http\Response
    public function show(Post $post)
    {
        return new PostResource($post);
    }
    //Update post
    public function UpdatePost(UpdatePostRequest $request)
    {
        $input = $request->validated();
        $data = Post::find($request['id']);
        $data->title = $request->input('title');
        $data->body = $request->input('body');

        if (!empty($input['attachment'])) {
            // upload Attachment
            $destinationPath = storage_path('\post\users\\');
            $input_type_aux = explode("/", $input['attachment']['mime']);
            $attachment_extention = $input_type_aux[1];
            $image_base64 = base64_decode($input['attachment']['data']);
            $file_name = uniqid() . '.' . $attachment_extention;
            $file = $destinationPath . $file_name;
            // saving in local storage
            file_put_contents($file, $image_base64);
            $data->attachment = $file_name;
            $input['profile_image'] = $file_name;
        }
        //store your file into directory and db
        $data->save();
        return response()->json([
            "success" => true,
            "message" => "Post Updated Successfully!"
        ]);
    }
    //Delete post
    public function DeletePost($id)
    {
        $user = new Post();
        $user = Post::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                "success" => true,
                "message" => "Post Deleted Successfully!!"
            ]);
        } else {
            return response()->json([
                "success" => true,
                "message" => "Post not exist"
            ]);
        }
    }
}
