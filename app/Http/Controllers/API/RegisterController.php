<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
// use Validator;
use \Firebase\JWT\JWT;

class RegisterController extends BaseController
{
    //For user Registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        //check validation
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 400);
        }
        //generate email verification token
        $input = $request->all();
        $email_varified_token = base64_encode($input['name']);
        $input['varified_token'] = $email_varified_token;
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        //generate URL link
        $details['link'] = url('api/emailConfirmation/' . $user->email . '/' . $email_varified_token);
        //send link to mailtrap
        \Mail::to($request['email'])->send(new \App\Mail\verifyemail($details));
        $success['name'] =  $user->name;
        return $this->sendResponse($success, 'User register successfully.', 200);
    }
    //For user Login
    public function login(Request $request)
    {
        //check email and password for authentication
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
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
            return $this->sendResponse($success, 'User successfully Logut', 200);
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
    //email verification on registration
    public function emailVarify(Request $request, $email, $token)

    {
        $user = User::where('email', $email)->where('varified_token', $token)->first();
        //check user is not empty
        if (!empty($user['email'])) {
            //get time of confirm verification
            $user->email_verified_at = date('Y-m-d h:i:s');
            $user->varified_token = '';
            //save confirmation time in database filed name "email_verified_at"
            $user->save();
            $success = true;
            //send responsed if Email link is confirm
            return $this->sendResponse($success, 'Your Email is confirm.Now you are successfully Register', 200);
        } else {
            //send responsed if Email link  is already use
            return    $this->sendError('Unauthorised.', ['error' => 'Link already used', 'detail' => 'this link already in use please create anotherone']);
        }
    }
    //for logout user
    public function logout(Request $request)
    {
        $token=$request->bearerToken();
        $delete=User::where("jwt_token",$token)->update(["jwt_token"=>NULL]);
        if ($delete) {
            return response()->json(['message' => 'User successfully Logout'], 200);
        }

    }
}
