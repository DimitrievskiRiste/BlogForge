<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanEditArticlesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->attributes->get('userId') ?? null;
        $isValidated = $request->attributes->get('IsValidated') ?? null;
        if(is_null($userId) || is_null($isValidated)) {
            return response()->json(['hasErrors' => true, 'message' => 'Access is denied'], 403);
        }
        $user = $this->findUser($userId);
        if(!is_null($user) && $user->Group->can_edit_article && $user->Group->can_access_admincp) {
            return $next($request);
        }
        return response()->json(['hasErrors' => true, 'message' => 'Access is denied'], 403);
    }
    protected function findUser(int $userId) : User|null
    {
        return User::with(['Group'])->find($userId);
    }
}
