<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanSeeUserGroupsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->attributes->get('userId');
        $isValidated = $request->attributes->get('isValidated');
        if(is_null($userId) || is_null($isValidated)){
            return response()->json(['hasErrors' => true, 'message' => 'No permission'], 403);
        }
        $member = $this->findMember($userId);
        if(!is_null($member) && $member->Group->can_access_admincp && $member->Group->can_edit_groups && $member->Group->can_add_groups){
            return $next($request);
        }
        return response()->json(['hasErrors' => true, 'message' => 'No permission to see user group lists'], 403);
    }
    private function findMember(int $userId):User|null{
        return User::query()->with('Group')->find($userId);
    }
}
