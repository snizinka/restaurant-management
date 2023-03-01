<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use HttpResponses;

    public function login(LoginUserRequest $request) {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'), $request->remember == "on")) {
            return $this->error('401', ['password' => ['Wrong login or password']], 401);
        }


        $user = User::where('email', $request->email)->first();
        Auth::login($user);
        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api token'.$user->name)->plainTextToken
        ]);
    }

    public function register(StoreUserRequest $request) {
        $request->validated($request->all());
        $user = User::create([
           'name' => $request->name,
           'email' => $request->email,
           'password' => Hash::make($request->password)
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('Api token'.$user->name)->plainTextToken
        ]);
    }

    public function logout() {
       Auth::user()->currentAccessToken()->delete();

       return $this->success([
          'message' => 'Logged out.'
       ]);
    }
}
