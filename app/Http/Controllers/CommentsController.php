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
    public function edit(Request $request) :Response
    {
        try {
            $data = $request->validate([
                'comment' => 'required',
                'id' => 'required|integer'
            ]);
            $comment = Comments::query()->find($data['id']);
            $isAdmin = $request->attributes->get('isAdmin') ?? false;
            $userId = $request->attributes->get('userId');
            if ($isAdmin) {
                // If user is admin, he can edit any comment he want.
                $comment->comment = $data['comment'];
                $comment->save();
                $this->loadRepo("Comments")->addOrUpdate($comment);
                return response()->json(['success' => true, 'message' => 'Comment edited successfully']);
            }
            // User is regular user, not an admin. We will check for comment id and user id.
            if ($comment->user_id !== $userId) {
                return response()->json(['hasErrors' => true, 'message' => 'Not authorized to perform this action'], 403);
            }
            $comment->comment = $data['comment'];
            $comment->save();
            $this->loadRepo('Comments')->addOrUpdate($comment);
            return response()->json(['success' => true, 'message' => 'Comment successfully edited']);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Cant edit comment, please check error logs for more details'], 500);
        }
    }

    /**
     * Get paginated comments for specific article. Required parameter in header articleId
     * @param Request $request
     * @return Response
     */
    public function get(Request $request) :Response
    {
        try {
            $offset = $request->get('offset', 0);
            $limit = $request->get('limit', 25);
            $articleId = $request->attributes->get('articleId');
            $repo = $this->loadRepo("Comments");
            $data = $repo
                ->findMany([["article_id" => $articleId]]);
            $comments = $repo->paginate($offset, $limit, $data);
            return response()->json(['data' => $comments]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Something went wrong, please check error logs'], 500);
        }
    }
    public function delete(Request $request) :Response {
        try {
            $commentId = $request->attributes->get('commentId');
            $isAdmin = $request->attributes->get('isAdmin');
            $repo = $this->loadRepo("Comments");
            $userId = $request->attributes->get('userId');
            $userCanDelete = $request->attributes->get("user_can_delete_own_comment");
            $adminCanDelete = $request->attributes->get("admin_can_delete_comments");
            if($isAdmin && $adminCanDelete){
                // User is admin, can delete any comment if have permission.
                Comments::query()->where('id', '=', $commentId)->delete();
                // lets now delete it from the cache as well.
                $repo->removeItem('id', $commentId);
                return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
            }
            // user is regular user, can only delete his own message if have permission
            $comment = Comments::query()->where('id', '=', $commentId)->where('user_id', '=', $userId);
            if($comment->exists() && $userCanDelete) {
                // comment exists, now lets delete.
                $comment->delete();
                // remove the comment model from cache as well.
                $repo->removeItem('id', $commentId);
                return response()->json(['success' => true, 'message' => 'Comment successfully removed!']);
            }
            // If comment with the specified criteria not found, we will return error and message
            return response()->json(['hasErrors' => true, 'message' => 'Comment for that user doesnt exists or you do not have permission to delete this comment!'], 400);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Something went wrong. Please check error logs!'], 500);
        }
    }
}
