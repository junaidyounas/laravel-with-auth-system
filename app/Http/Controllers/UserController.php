<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Str;
use App\Models\User;
use Validator;

class UserController extends Controller
{
    //
    function signUp(Request $request)
    {
        $rules = array(
            "name" => "required",
            "email" => "required",
            "password" => "required",
        );

        $valid = Validator::make($request->all(), $rules);
        if ($valid->fails()) {
            $response = [
                'data' => response()->json($valid->errors())
            ];
            return response($response, 401);
        } else {

            $user = new User;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->name = $request->name;
            $user->save();
            if ($user) {
                $response = [
                    'data' => $user
                ];
                return response($response, 201);
            }
        }

    }

    function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        // print_r($data);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match our records.']
            ], 404);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = [
            'data' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => now(),
        ]);
        return response()->json(['token' => $token]);
    }

    function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required|string',
        ]);

        // Find the user by email
        $user = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid token or email'], 422);
        }

        // Update the user's password
        DB::table('users')
            ->where('email', $request->email)
            ->update(['password' => bcrypt($request->password)]);

        // Remove the used token from the password_resets table
        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();
        return response()->json(['message' => 'Password reset successful']);

    }
}