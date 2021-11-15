<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Request;
use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// use Validator;
use \Firebase\JWT\JWT;


class UserController extends BaseController
{

//For user Login

    public function login(LoginUserRequest $request)
    {
        $input = $request->validated();
        //check email and password for authentication
        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $user = Auth::user();
            $user_data = array(
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email
            );
            $iss = "localhost";
            $iat = time();
            $nbf = $iat + 10;
            $exp = $iat + 1800;
            $aud = "User";

            $payload_info = array(
                "iss" => $iss,
                "iat" => $iat,
                "nbf" => $nbf,
                "exp" => $exp,
                "aud" => $aud,
                "data" => $user_data
            );
            //generate Token use firebase library
            $key = 'example';
            $jwt = JWT::encode($payload_info, $key);
            $user->jwt_token = $jwt;
            //update Token in database fieldname "jwt_token"
            //  $user->update();
            User::where("email", $user->email)->update(["jwt_token" => $jwt]);
            $success['message'] =  " User Successful login.";
            $success['Authentication'] = $jwt;
            return $this->sendResponse($success, 'User successfully Login', 200);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

//for logout user

    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $delete = User::where("jwt_token", $token)->update(["jwt_token" => NULL]);
        if ($delete) {
            return response()->json(['message' => 'User successfully Logout'], 200);
        }
    }

//For Delete User

    public function DeleteUser($id)
    {
        $user = new User();
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                "success" => true,
                "message" => "User Deleted Successfully!!",
                "data" => $user
            ]);
        } else {
            return response()->json([
                "success" => true,
                "message" => "User not exist",
                "data" => $user
            ]);
        }
    }

//For Search User

    public function SearchUser($name)
    {
        return User::where('name', 'like', '%' . $name . '%')->get();
    }

//For Update user

    public function UpdateUser(Request $request, $id)
    {
        $user = User::find($id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $request->input('password');

        $input = $request;
        $file_name = null;
        if (!empty($input['attachment'])) {
            // upload Attachment
            $destinationPath = storage_path('\api\users\\');
            $input_type_aux = explode("/", $input['attachment']['mime']);
            $attachment_extention = $input_type_aux[1];
            $image_base64 = base64_decode($input['attachment']['data']);
            $file_name = $input['name'] . uniqid() . '.' . $attachment_extention;
            $file = $destinationPath . $file_name;
            // saving in local storage
            file_put_contents($file, $image_base64);
            $input['profile_image'] = $file_name;
        }
        $user->save();
        return response()->json([
            "success" => true,
            "message" => " User Updated Successfully",
            "data" => $user
        ]);
    }
}
