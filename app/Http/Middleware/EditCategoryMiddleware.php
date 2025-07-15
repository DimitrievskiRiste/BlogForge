<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EditCategoryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            list(1 => $token) = $request->header("Authorization");
            $secret = $request->header("Authorization-Pass");
            $jwt = JWT::decode($token, new Key($secret, "HS256"));
            $user = $this->findUser($jwt['user']);
            if(!is_null($user) && $user->Group->can_access_admincp && $user->Group->can_edit_categories){
                return $next($request);
            } else {
                return response()->json(['hasErrors' => true, 'message' => 'Access is denied'], 403);
            }
        } catch (\Exception $e){
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Unable to edit category'], 403);
        }
    }
    private function findUser(int $userId) :User|null{
        return User::query()->with('Group')->find($userId) ?? null;
    }
}
