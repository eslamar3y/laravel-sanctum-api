<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email', // unique:users,email means that the email must be unique in the users table
            'password' => 'required|string|confirmed', // confirmed means that the password must be confirmed
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']), // bcrypt() is a helper function that hashes the password
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken; // createToken() is a method that creates a token for the user

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201); // 201 means that the request was successful and a resource was created
    }

    // login the user
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string', // confirmed means that the password must be confirmed
        ]);

        // check email
        $user = User::where('email', $fields['email'])->first();

        // check password
        if (!$user || !Hash::check($fields['password'], $user->password)) { // Hash::check() is a helper function that checks if the password matches the hashed password
            return response([
                'message' => 'Bad credentials',
            ], 401); // 401 means that the request was unauthorized
        }

        $token = $user->createToken('myapptoken')->plainTextToken; // createToken() is a method that creates a token for the user

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 201); // 201 means that the request was successful and a resource was created
    }

    // delete the user token
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete(); // delete all the tokens for the authenticated user

        return [
            'message' => 'Logged out',
        ];
    }
}
