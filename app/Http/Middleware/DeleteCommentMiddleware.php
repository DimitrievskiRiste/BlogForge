<?php

namespace App\Http\Middleware;

use App\Models\Comments;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DeleteCommentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $commentId = $request->header("commentId");
            $userId = $request->attributes->get('userId');
            $IsValidated = $request->attributes->get('isValidated');
            if(is_null($userId) || is_null($IsValidated) || is_null($commentId)) {
                return response()->json(['hasErrors' => true, 'message' => 'Unauthorized or invalid api call.'],403);
            }
            $user = $this->findUser($userId);
            $comment = $this->findComment($commentId);
            if(is_null($comment)){
                return response()->json(['hasErrors' => true, 'message' => 'Invalid comment!'], 404);
            }
            if(!is_null($user) && !is_null($comment)){
                $request->attributes->set("commentId", $commentId);
                if($user->Group->can_access_admincp){
                    $request->attributes->set('isAdmin', true);
                }
                if($user->Group->can_delete_self_comment){
                    $request->attributes->set("user_can_delete_own_comment", true);
                }
                if($user->Group->can_delete_comments) {
                    $request->attributes->set("admin_can_delete_comments", true);
                }
                return $next($request);
            }
            return response()->json(['hasErrors' => true, 'message' => 'Unauthorized access'], 403);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Something went wrong, please see error logs'], 500);
        }
    }
    protected function findUser(int $userId) :User|null {
        return User::query()->with(['Group'])->find($userId);
    }
    protected function findComment(int $commentId) :Comments|null {
        return Comments::query()->find($commentId);
    }
}
