<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\ResetRequest;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
class ForgotPasswordController extends Controller
{
    //

    public function forgot(ForgotRequest $request){
        $email = $request->email;

        if(User::where('email', $email)->doesntExist()){

            return response([
                'message'=>'User Does not exists'
            ], 404);
        }

        $token = Str::random(10);

        try{
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' =>$token
            ]);

            Mail::send('Mails.forgot', ['token'=>$token], function(Message $message) use ($email){
                $message->to($email);
                $message->subject('Reset Your Password');
            });

            return response([
                'message' => 'Check Your email'
            ]);

        }catch(\Exception $exception){
            return response([
                'msg' => 'Failed',
                'err' => $exception->getMessage()
            ], 400);
        }

    }
    
    public function reset(ResetRequest $request){
        $token = $request->token;
        if(!$passwordResets = DB::table('password_resets')->where('token', $token)->first()){
            return response([
                'message' => 'Invalid Token'
            ], 400);
        }

        /** @var User $user */
        if(!$user = User::where('email', $passwordResets->email)->first()){
            return response([
                'message' => 'User doesn\'t exist!!'
            ], 401);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response([
            'message' => 'Password Changed'
        ], 200);
    }
}
