<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $inputs = $request->only('email', 'password');

        $validator = Validator::make(
            $inputs,
            [
                'email' => [
                    'required',
                    'string',
                    'min:8',
                    'max:60',
                    'email',
                    'exists:users,email'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:30'
                ]
            ],
            [
                'email.required' => 'Adresa de email este obligatorie',
                'email.string' => 'Adresa de email are un format invalid',
                'email.min' => 'Adresa de email trebuie sa aiba minim 8 caractere',
                'email.max' => 'Adresa de email trebuie sa aiba maxim 60 caractere',
                'email.email' => 'Formatul adresei de email este invalid',
                'email.exists' => 'Email sau parola gresita',

                'password.required' => 'Parola este obligatorie',
                'password.string' => 'Parola are un format invalid',
                'password.min' => 'Parola trebuie sa aiba minim 8 caractere',
                'password.max' => 'Parola trebuie sa aiba maxim 30 caractere'
            ]
        );

        if ($validator->fails()) {
            return response()->error($validator->errors()->first());
        }

        $token = Auth::attempt($inputs);
        if (!$token)
            return response()->error('Email sau parola gresita');

        $user = Auth::user();

        return response()->success([
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
