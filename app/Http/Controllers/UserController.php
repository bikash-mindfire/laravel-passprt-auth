<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \App\Models\User;
use \App\Http\Requests\RegisterRequest;

class UserController extends Controller
{
    public function hello () {
        return "hello";
    }

    public function login(Request $request){
        try{
        if(Auth::attempt($request->only('email', 'password'))){
            /** @var User $user */

            $user = Auth::user();
            $token = $user->createToken('app')->accessToken;

            return response([
                'msg' => 'Success',
                'user' => $user,
                'token'=> $token
            ], 200);
        }
            
        } catch(\Exception $exception){
            return response([
                'msg' => 'Failed',
            ], 400);
        }
        return response([
            'msg'=> 'err',

        ], 401);
    }

    public function me() {

        try{

            $user = Auth::user();

            if($user){
                return response([
                            'msg' => 'Success',
                            'user' => $user
                        ], 200);
            }elseif($user === null){
                return response([
                            'msg' => 'Un Authorized',
                        ], 401);
            }            
        }
        catch(\Exception $exception){
            return response([
                'msg' => 'Failed',
            ], 400);
        }

    }



    // To make the server side validation work have to pass the below header
    // * HEADER => [Accept: 'application/json']
    public function register(RegisterRequest $request){
        try{
            /** @var User $user */

            $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => 'user',
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('app')->accessToken;


        return response([
            'msg' => 'success',
            'user' => $user,
            'token' => $token
        ], 400);
    }catch(\Exception $exception){
            return response([
                'msg' => 'Failed',
                'err' => $exception->getMessage()
            ], 400);
        }
    }
}
