<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // bypass the middleware for login and register methods - the user is not logged-in yet
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response([
                'message' => 'Email or password is incorrect'
            ], 422);
        }

        $user = Auth::user();

        return response()->success([
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(RegisterRequest $request)
    {
        //luam datele din request in variabila $data
        //return response()->error($request->all());
        $data = $request->validated();

        //cream un obiect de tip user care primeste datele din $data si va fi trimis in front
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'date_of_birth' => isset($data['date_of_birth']) ? $data['date_of_birth'] : null,
            'password' => Hash::make($data['password']),
        ]);
        //cream un obiect token care va fi trimis in front
        $token = Auth::login($user);

        //returnam vectorul response care contine user si token
        //acesta va deveni "data" in front(in signup axiosClient.post)
        return response()->success([
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
        //sterge sesiunea
        Auth::logout();
        return response()->success(
            'Ati fost deconectat cu success'
        );
    }

    public function refresh()
    {
        try {
            $new_token = Auth::refresh();
            return response()->success([
                'user' => Auth::user(),
                'authorization' => [
                    'token' => $new_token,
                    'type' => 'bearer',
                    'ttl' => Auth::factory()->getTTL() * 60,
                ]
            ]);
        } catch (TokenInvalidException $e) {
            return response()->error('Token is Invalid');
        } catch (TokenExpiredException $e) {
            return response()->error('Token is Expired');
        } catch (JWTException $e) {
            return response()->error('Token is not provided');
        }
    }
}
