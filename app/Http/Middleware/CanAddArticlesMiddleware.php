<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanAddArticlesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->attributes->get("userId");
        $isValidated = $request->attributes->get('isValidated');
        if(is_null($userId) || is_null($isValidated)){
            return response()->json(['hasErrors' => true, 'message' => "No permission to add articles"], 403);
        }
        $user = $this->findUser($userId);
        if(!is_null($user) && $user->Group->can_access_admincp && $user->Group->can_add_article) {
            return $next($request);
        }
        return response()->json(['hasErrors' => true, 'message' => "No permission to access this route!"], 403);
    }
    private function findUser(int $userId) :User|null {
        return User::query()->with('Group')->find($userId);
    }
}
