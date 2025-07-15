<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Riste\AbstractRepository;

abstract class Controller
{
    /**
     * Get authenticated API User. This method returns array data with user data & decoded token data.
     * @param Request $request
     * @return array
     */
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
            ];
        } else {
            return [];
        }
    }
    private function findMember(int $userId) :User|null{
        return User::query()->with(['Group'])->find($userId) ?? null;
    }
    protected function extractUserData(array $tokenData) :User|null
    {
        if(array_key_exists("user", $tokenData)){
            return $tokenData['user'];
        }
        return null;
    }

    /**
     * Load repository cached eloquent model(s)
     * @param $repoName
     * @return AbstractRepository|\Exception
     * @throws \ReflectionException
     */
    protected function loadRepo($repoName) :AbstractRepository|\Exception
    {
        try {
            $loadedClass = "\\App\\Repositories\\$repoName"."Repository";
            $class = new \ReflectionClass($loadedClass);
            if($class->isSubclassOf("\\Riste\\AbstractRepository")){
                return $class->newInstance();
            }
            return throw \Exception("Repository $repoName looked in $loadedClass does not extend \Riste\AbstractRepository class");
        } catch (\Exception $e) {
            Log::error($e);
            throw $e;
        }
    }
}
