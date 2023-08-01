<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function logout(Request $request)
    {
        $user = $request->user();
        $otherUser = $request->user();
        $user->tokens()->delete();
            $response = [
                'data' => $user,
                'message'=> 'Logged out successfully'
            ];
             return response($response, 201);
            
    }
}