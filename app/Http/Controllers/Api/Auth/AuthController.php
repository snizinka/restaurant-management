<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\StorePassword;
use App\Http\Requests\StoreUserRequest;
use App\Jobs\ResetEmailJob;
use App\Jobs\WelcomeEmailJob;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Event\Exception;

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

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        WelcomeEmailJob::dispatch($user);

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

    public function reset(ResetPassword $resetPassword) {
        $resetPassword->validated($resetPassword->all());

        $user = User::where('email', $resetPassword->email)->first();
        if($user != null)
        {
            $password = Str::random(8);
            $generated = Hash::make($password);

            try {
                DB::beginTransaction();
                $user->update([
                    'password' => $generated
                ]);
                DB::commit();
            } catch(Exception $ex) {
                DB::rollBack();
                abort(500);
            }

            ResetEmailJob::dispatch($user, $password);
        }

        return $this->success([
            'message' => 'Reset'
        ]);
    }

    public function confirmReset(StorePassword $storePassword) {
        $storePassword->validated($storePassword->all());

        if ($storePassword->input('password') != $storePassword->input('confirm')) {
            return $this->error('401', ['password' => ['Different values in password and confirm password fields']], 401);
        }

        $user = User::where('id', Auth::id())->first();

        try {
            DB::beginTransaction();
            $user->update([
                'password' => Hash::make($storePassword->input('password'))
            ]);
            DB::commit();
        } catch(Exception $ex) {
            DB::rollBack();
            abort(500);
        }

        return $this->success([
            'message' => 'Reset'
        ]);
    }
}
