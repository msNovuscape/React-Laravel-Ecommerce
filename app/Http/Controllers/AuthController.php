<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserPassswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $isAdmin = ($request->route()->getPrefix() === 'api/admin') ? 1 : 0;

        $userData = [
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'is_admin' => $isAdmin
        ];
        $user = User::create($userData);
        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request){

        $isAdmin = ($request->route()->getPrefix() === 'api/admin');

        if(!Auth::attempt($request->only('email','password'))){
            return response([
                'error' => 'Invalid Credentials'
            ],Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        if ($isAdmin && !$user->is_admin) {
            return response([
                'error' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $jwtAbilities = $isAdmin ? ['admin'] : ['ambassador'];
        $jwt = $user->createToken('token', $jwtAbilities)->plainTextToken;
        $cookie  = cookie('jwt',$jwt,60*24);//1day
        return response([
            'msg' => 'Success'
        ])->withCookie($cookie);
    }

    public function user(Request $request){
        $user =  $request->user();
        return new UserResource($user);
    }

    public function updateInfo(UpdateUserRequest $request){
        $user = $request->user();
        $updatedData = [
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
        ];
        $user->update($updatedData);
        return response($user, Response::HTTP_ACCEPTED);
    }
    public function updatePassword(UpdateUserPassswordRequest $request){
        $user = $request->user();
        $updatedData = [
            'password' => bcrypt($request['password']),
        ];
        $user->update($updatedData);
        return response($user, Response::HTTP_ACCEPTED);
    }

    public function logout(){
        $cookie  = Cookie::forget('jwt');
        return response([
            'msg' => 'Success'
        ])->withCookie($cookie);

    }
}
