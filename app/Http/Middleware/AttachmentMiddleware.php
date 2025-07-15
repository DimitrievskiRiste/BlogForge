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
            list(1 => $bearer) = $request->header("Authorization");
            $secret = $request->header("Authorization-Pass");
            (array) $jwt = JWT::decode($bearer, new Key($secret, "HS256"));
            $user = $this->findUser($jwt['user']);
            if(!is_null($user) && $user->Group->can_upload_attachments){
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
