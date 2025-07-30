<?php

namespace App\Http\Middleware;

use App\Models\Articles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCommentsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $articleId = $request->header("articleId");
        if(!is_null($articleId) && Articles::query()->where('id','=', $articleId)->exists()) {
            $request->attributes->set('articleId', $articleId);
            return $next($request);
        }
        return response()->json(['hasErrors' => true, 'message' => 'Invalid article!'], 400);
    }
}
