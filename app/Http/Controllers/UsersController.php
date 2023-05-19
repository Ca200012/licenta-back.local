<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        //nu poate intra in functii decat daca exista un token
        $this->middleware('auth:api');
    }

    public function getUserData()
    {
        $id = Auth::id();
        // preia user-ul care are cheia primara egala cu $id
        $user = User::find($id);
        return response()->success($user);
    }
}
