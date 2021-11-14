<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Http\Requests\ForgotRequest;

use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public $successStatus = 200;
    public function forgotPassword(ForgotRequest $request)
    {
        $input = $request->validated();
        $user_data = User::where('email', $input['email'])->first();
        $string = "ABC";

        $password = substr(str_shuffle(str_repeat($string, 12)), 0, 12);
        $user_data->password = bcrypt($password);
        $user_data->save();
        //for generate link in URL
        $details['link'] = url('api/forogtpassword/' . $user_data->password . 'api/email/' . $user_data->email . '/');
        Mail::to($input['email'])->send(new ForgotPassword($details));
        return response()->json(['success' => "New Password Send to Your Mail!"], $this->successStatus);
    }
}
