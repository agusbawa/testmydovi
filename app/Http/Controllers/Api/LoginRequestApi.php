<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginRequestApi extends Controller
{
    public function authRequest(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $accessToken = Auth::user()->createToken('authToken')->accessToken;
            return response(['token' => $accessToken]);
            // dd($accessToken);
            // $response = ['token' => $accessToken];
        }else{
            $response = ['error' => 'Invalid Credential'];
        }

        // return response()->json($response);
    }
}
