<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Friend;

class FriendController extends Controller
{

    public function addFriend(Request $req)
    {
        $key = $req->token;
        $fid = $req->fid;
        $data = DB::table('users')
            ->where('jwt_token', $key)->get();
        $id = $data[0]->id;
        if ($id != $fid) {
            if (DB::table('users')->where('id', $fid)->exists()) {
                if (DB::table('friends_request')
                    ->where(['userid_1' => $id, 'userid_2' => $fid])
                    ->orwhere(['userid_1' => $fid, 'userid_2' => $id])
                    ->doesntExist()
                ) {
                    $numrows = count($data);
                    if ($numrows > 0) {
                        $friend = new Friend;
                        $friend->userid_1 = $id;
                        $friend->userid_2 = $fid;
                        $friend->save();
                        return response()->json(["messsage" => "you are friend now of" . $fid]);
                    } else {
                        return response()->json(["messsage" => "Currently you are not login"]);
                    }
                } else {
                    return response(["message" => "User with id = " . $fid . " is already your friend"]);
                }
            } else {
                return response(["message" => "User with id = " . $fid . " is not registerd on our application"]);
            }
        } else {
            return response(["message" => "you are not allow to be friend of yourself"]);
        }
    }
}
