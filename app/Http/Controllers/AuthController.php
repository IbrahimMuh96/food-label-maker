<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponser;

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|min:5|max:100',
            'email' => 'required|string|email:rfc,dns|unique:users,email',
            'password' => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|confirmed',
            'role' => 'required|in:user,admin',
        ]);

        if ($validation->fails()) {
            return $this->error('Validation Error.', 422, $validation->errors());
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        $success['token'] = $user->createToken('app')->plainTextToken;
        $success['user'] = $user;

        return $this->success($success, 'Registered Successfully.', 201);
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email:rfc,dns|exists:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return $this->error('Validation Error.', 422, $validation->errors());
        }

        $user_auth = Auth::attempt(['email' => $request->email, 'password' => $request->password]);

        if ($user_auth) {
            $user = Auth::user();

            $success['token'] = $user->createToken('app')->plainTextToken;
            $success['user'] = $user;

            return $this->success($success, 'Logged in Successfully.', 201);
        }

        return $this->error('These credentials do not match our records.', 401);

    }
}
