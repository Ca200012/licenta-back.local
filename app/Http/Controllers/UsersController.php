<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

use App\Mail\MessageReceived;

use App\Http\Requests\UpdateRequest;

class UsersController extends Controller
{
    public function __construct()
    {
        // nu poate intra in functii decat daca exista un token
        $this->middleware('auth:api', ['except' => ['sendMessageReceivedEmail']]);
    }

    public function getUserData()
    {
        $id = Auth::id();

        $user = User::find($id);
        return response()->success($user);
    }

    public function updateUserData(UpdateRequest $request)
    {
        $data = $request->validated();

        $id = Auth::id();

        $user = User::find($id);
        if (!$user) {
            return response()->error("An error has occured!");
        }

        $user->update($data);

        return response()->success($user);
    }

    public function sendMessageReceivedEmail(Request $request)
    {
        try {
            Mail::to($request->email)->send(new MessageReceived());
            return response()->success("Your email has been sent!");
        } catch (\Exception $e) {

            return response()->error("Failed to send the email");
        }
    }
}
