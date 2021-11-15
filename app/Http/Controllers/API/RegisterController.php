<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;

class RegisterController extends BaseController
{

//For user Registration

    public function register(RegisterUserRequest $request)
    {
        //generate email verification token
        $input = $request->validated();
        $file_name = null;
        // converting base64 decoded image to simple image if exist
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
        }
        $email_varified_token = base64_encode($input['name']);
        $input['varified_token'] = $email_varified_token;
        $input['password'] = bcrypt($input['password']);
        $input['profile_image'] = $file_name;
        $user = User::create($input);
        //generate URL link
        $details['link'] = url('api/emailConfirmation/' . $user->email . '/' . $email_varified_token);
        //send link to mailtrap
        \Mail::to($request['email'])->send(new \App\Mail\verifyemail($details));
        $success['name'] =  $user->name;
        return $this->sendResponse($success, 'User register successfully.', 200);
    }

//For email verification on registration

    public function emailVarify($email, $token)

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
}
