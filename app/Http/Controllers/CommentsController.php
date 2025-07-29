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
    public function edit(Request $request) :Response {
        try {
            $data = $request->validate([
                'comment' => 'required',
                'id' => 'required|integer'
            ]);
            $comment = Comments::query()->find($data['id']);
            $isAdmin = $request->attributes->get('isAdmin') ?? false;
            $userId =  $request->attributes->get('userId');
            if($isAdmin) {
                // If user is admin, he can edit any comment he want.
                $comment->comment = $data['comment'];
                $comment->save();
                $this->loadRepo("Comments")->addOrUpdate($comment);
                return response()->json(['success' => true, 'message' => 'Comment edited successfully']);
            }
                // User is regular user, not an admin. We will check for comment id and user id.
                if($comment->user_id !== $userId) {
                    return response()->json(['hasErrors' => true, 'message' => 'Not authorized to perform this action'], 403);
                }
                $comment->comment = $data['comment'];
                $comment->save();
                $this->loadRepo('Comments')->addOrUpdate($comment);
                return response()->json(['success' => true, 'message' => 'Comment successfully edited']);
        }
        catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Cant edit comment, please check error logs for more details'], 500);
        }
    }
}
