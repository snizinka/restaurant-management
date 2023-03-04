<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    use HttpResponses;
    public function handle(Request $request, Closure $next): Response
    {
        $user = PersonalAccessToken::findToken($request->bearerToken());

        if (!$user) {
            return $this->error('401', ['unauthorized' => ['You need to be authorized first']], 401);
        }else {
            $us = User::where('id', $user->tokenable_id)->first();
            if ($us->role == 'admin') {
                return $next($request);
            }else {
                return $this->error('401', ['unauthorized' => ["You don't have enough rights"]], 401);
            }
        }
    }
}
