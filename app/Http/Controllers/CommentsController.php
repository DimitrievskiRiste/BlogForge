<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CommentsController extends Controller
{
    public function add(Request $request) :Response
    {
        try {
            $data = $request->validate([
                'comment' => 'required'
            ]);
            $articleId = $request->header('articleId');
            $comment = new Comments();
            $comment->user_id = $request->attributes->get('userId');
            $comment->article_id = $articleId;
            $comment->comment = $data['comment'];
            $comment->save();
            $this->loadRepo("Comments")->addOrUpdate($comment);
            return response()->json(['success' => true, 'message' => 'Comment successfully added']);
        } catch (\Exception $e){
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Cant add new comment. Please check error logs'], 500);
        }
    }
}
