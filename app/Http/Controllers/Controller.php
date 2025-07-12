<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

abstract class Controller
{
    protected function getAuthenticatedAPIUser(Request $request) :array
    {
        $header = explode(" ", $request->header("Authorization"));
        list(1 => $token) = $header;
        $secretPass = $request->header("Authorization-Pass");
        $jwtToken = JWT::decode($token, new Key($secretPass, "HS256"));
        $user = $this->findMember($jwtToken->user);
        if(!is_null($user)){
            return [
                'user' => $user,
                'token' => $jwtToken
            ]
        } else {
            return [];
        }
    }
    private function findMember(int $userId) :User|null{
        return User::query()->with(['Group'])->find($userId) ?? null;
    }
}
