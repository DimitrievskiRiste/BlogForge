<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AttachmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if(is_null($request->attributes->get("userId")) || is_null($request->attributes->get("isValidated"))){
                return response()->json(['hasError' => true, 'message' => 'Not authorized'], 403);
            }
            $user = $this->findUser($request->attributes->get("userId"));
            if(!is_null($user) && $user->Group->can_upload_attachments && $request->attributes->get("isValidated")){
                return $next($request);
            } else {
                return response()->json(['hasErrors' => true, 'message' => "Your account doesn't have permission for this action"], 403);
            }
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasError' => true, 'message' => 'Upload failed'], 403);
        }
    }
    private function findUser(int $userId) :User|null
    {
        return User::query()->with('Group')->find($userId) ?? null;
    }
}
