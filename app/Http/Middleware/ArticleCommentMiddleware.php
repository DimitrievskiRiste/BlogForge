<?php

namespace App\Http\Middleware;

use App\Models\Articles;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleCommentMiddleware
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
        $articleId = $request->header('articleId') ?? null;
        if(is_null($userId) || is_null($isValidated)) {
            return response()->json(['hasErrors' => true, 'message' => 'Unauthorized'], 403);
        }
        if(is_null($articleId)) {
            return response()->json(['hasErrors' => true, 'message' => 'Invalid article'], 400);
        }
        $user = $this->findUser($userId);
        $article = $this->findArticle($articleId);
        if(!is_null($user) && !is_null($article) && $user->Group->can_comment && $article->allow_comments) {
            return $next($request);
        }
        return response()->json(['hasErrors' => true, 'message' => 'Invalid article or you do not have permission to comment here.'], 400);
    }
    private function findUser(int $userId) :User|null {
        return User::with(['Group'])->find($userId);
    }
    private function findArticle(int $articleId) :Articles|null {
        return Articles::query()->find($articleId);
    }
}
