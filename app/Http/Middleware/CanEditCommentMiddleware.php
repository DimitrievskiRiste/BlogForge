<?php

namespace App\Http\Middleware;

use App\Models\Comments;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CanEditCommentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $userId = $request->attributes->get('userId');
            $isValidated = $request->attributes->get("isValidated");
            $commentId = $request->header('commentId') ?? null;
            if(is_null($userId) || is_null($isValidated) || is_null($commentId)) {
                return response()->json(['hasErrors' => true, 'message' => 'Unauthorized access'], 403);
            }
            $user = $this->findUser($userId);
            $comment = $this->findComment($commentId);
            if(!is_null($user) && !is_null($comment)){
                if($user->Group->can_access_admincp) {
                    $request->attributes->set('isAdmin', true);
                }
                return $next($request);
            }
            return response()->json(['hasErrors' => true, 'message' => 'Comment not found or user is not authorized'], 403);
        } catch(\Exception $e){
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Cant edit a comment, something went wrong. Please check error logs for more details'], 500);
        }
    }
    private function findUser(int $userId) :User|null {
        return User::with(['Group'])->find($userId);
    }
    private function findComment(int $commentId) :Comments|null {
        return Comments::query()->find($commentId);
    }
}
