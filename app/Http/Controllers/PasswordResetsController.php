<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\User;
use App\Models\PasswordReset;

use App\Mail\ResetPasswordVerification;

class PasswordResetsController extends Controller
{
	public function checkEmailAndGenerateCode(Request $request)
	{
		try {

			$user = User::where('email', $request->email)->first();
			if (!$user) {
				return response()->success("A confirmation code that expires in 15 minutes will be sent to your email address given you already have an account!");
			}

			// Deactivate all previous codes
			PasswordReset::where([
				'user_id' => $user->user_id,
				'active' => 1
			])->update([
				'active' => 0
			]);

			$current_reset = PasswordReset::create([
				'user_id' => $user->user_id,
				'code' => strval(random_int(100000, 999999))
			]);

			Mail::to($request->email)->send(new ResetPasswordVerification($current_reset->code));

			return response()->success("A confirmation code that expires in 15 minutes will be sent to your email address given you already have an account!");
		} catch (\Exception $e) {
			return response()->error($e->getMessage());
			return response()->error("Error on verifying your email!");
		}
	}

	public function checkResetCode(Request $request)
	{
		try {

			$user = User::where('email', $request->email)->first();
			if (!$user) {
				return response()->error("The code has expired or is incorrect!");
			}

			$validator = Validator::make($request->only('code'), [
				'code' => 'required|numeric|digits:6',
			]);

			if ($validator->fails()) {
				throw new ValidationException($validator);
			}

			$existing_code = PasswordReset::where([
				['user_id', '=', $user->user_id],
				['code', '=', $request->code],
				['active', '=', 1],
				['created_at', '>=', Carbon::now()->subMinutes(15)]
			])->first();

			if (!$existing_code) {
				return response()->error("The code has expired or is incorrect!");
			}

			return response()->success("Code confirmed! Please reset your password now!");
		} catch (ValidationException $e) {
			return response()->error($e->getMessage());
		} catch (\Exception $e) {
			return response()->error("Error on verifying your code!");
		}
	}

	public function resetPassword(Request $request)
	{
		try {

			$user = User::where('email', $request->email)->first();
			if (!$user) {
				return response()->error("Error on resetting your password!");
			}

			$validator = Validator::make($request->only('password', 'password_confirmation'), [
				'password' => 'required|string|min:8|confirmed',
				'password_confirmation' => 'required|string|min:8',
			]);

			if ($validator->fails()) {
				throw new ValidationException($validator);
			}

			$existing_code = PasswordReset::where([
				['user_id', '=', $user->user_id],
				['code', '=', $request->code],
				['active', '=', 1],
				['created_at', '>=', Carbon::now()->subMinutes(15)]
			])->first();

			if (!$existing_code) {
				return response()->error("The code has expired or is incorrect!");
			}

			$existing_code->update([
				'active' => 0
			]);

			$user->update([
				'password' => Hash::make($request->password)
			]);

			return response()->success("Pasword updated! You may login now!");
		} catch (ValidationException $e) {
			return response()->error($e->getMessage());
		} catch (\Exception $e) {
			return response()->error("Error on resetting your password!");
		}
	}
}
