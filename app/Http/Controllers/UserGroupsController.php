<?php

namespace App\Http\Controllers;

use App\Models\UserGroups;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Riste\AbstractRepository;

class UserGroupsController extends Controller
{
    public function list(Request $request) : \Illuminate\Http\JsonResponse
    {
        try{
            $offset = $request->get("offset",0);
            $limit = $request->get('limit', 25);
            $userGroups = $this->groupsRepo()->paginate($offset,$limit);
            return response()->json([
                'success' => true,
                'groups' => $userGroups
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return Response()->json(['hasErrors' => true, 'message' => 'Unable to get user groups. See error logs for more details'], 500);
        }
    }
    private function groupsRepo():AbstractRepository|\Exception{
        return $this->loadRepo("UserGroups");
    }
    public function add(Request $request) : \Illuminate\Http\JsonResponse
    {
        try {
            $data =  $request->validate([
                'group_name' => 'required|regex:/^[a-zA-Z\s0-9#!,.]+$/',
                'group_title' => 'required|regex:/^[a-zA-Z\s0-9#!,.]+$/',
                'can_access_admincp' => 'boolean',
                'can_access_users' => 'boolean',
                'can_add_users' => 'boolean',
                'can_remove_users' => 'boolean',
                'can_access_categories' => 'boolean',
                'can_add_categories' => 'boolean',
                'can_remove_categories' => 'boolean',
                'can_edit_categories' => 'boolean',
                'can_access_articles' => 'boolean',
                'can_add_article' => 'boolean',
                'can_remove_article' => 'boolean',
                'can_edit_article' => 'boolean',
                'can_comment' => 'boolean',
                'can_delete_comments' => 'boolean',
                'can_delete_self_comment' => 'boolean',
                'can_edit_self_comment' => 'boolean',
                'can_change_settings' => 'boolean',
                'can_manage_admins' => 'boolean',
                'can_upload_attachments' => 'boolean',
                'can_remove_self_attachments' => 'boolean',
                'can_remove_attachments' => 'boolean',
                'can_add_groups' => 'boolean',
                'can_edit_groups' => 'boolean',
                'can_remove_groups' => 'boolean'
            ]);
            $userGroup = new UserGroups();
            $userGroup->fill($data);
            $userGroup->save();
            $this->groupsRepo()->addOrUpdate($userGroup,now()->addDays(30));
            return response()->json(['success' => true, 'message' => 'Group added successfully', 'group' => $userGroup]);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Group failed to be created, check error logs'], 500);
        }
    }
    public function edit(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data =  $request->validate([
                'group_id' => 'required|integer',
                'group_name' => 'required|regex:/^[a-zA-Z\s0-9#!,.]+$/',
                'group_title' => 'required|regex:/^[a-zA-Z\s0-9#!,.]+$/',
                'can_access_admincp' => 'boolean',
                'can_access_users' => 'boolean',
                'can_add_users' => 'boolean',
                'can_remove_users' => 'boolean',
                'can_access_categories' => 'boolean',
                'can_add_categories' => 'boolean',
                'can_remove_categories' => 'boolean',
                'can_edit_categories' => 'boolean',
                'can_access_articles' => 'boolean',
                'can_add_article' => 'boolean',
                'can_remove_article' => 'boolean',
                'can_edit_article' => 'boolean',
                'can_comment' => 'boolean',
                'can_delete_comments' => 'boolean',
                'can_delete_self_comment' => 'boolean',
                'can_edit_self_comment' => 'boolean',
                'can_change_settings' => 'boolean',
                'can_manage_admins' => 'boolean',
                'can_upload_attachments' => 'boolean',
                'can_remove_self_attachments' => 'boolean',
                'can_remove_attachments' => 'boolean',
                'can_add_groups' => 'boolean',
                'can_edit_groups' => 'boolean',
                'can_remove_groups' => 'boolean'
            ]);
            $group = UserGroups::query()->find($data['group_id']);
            if(!is_null($group)){
                unset($data['group_id']);
                $group->fill($data);
                $group->save();
                $this->groupsRepo()->addOrUpdate($group, now()->addDays(30));
                return response()->json(['success' => true, 'message' => 'group updated successfully', 'group' => $group]);
            }
            return response()->json(['hasErrors' => true, 'message' => 'Failed to update group, group not found!']);
        } catch(\Exception $e){
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Group failed to be updated, check error logs'], 500);
        }
    }
    public function delete(Request $request) : \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validate(['group_id' => 'integer|required']);
            $model = UserGroups::query()->find($data);
            if(!is_null($model)){
                $model->delete();
                $this->groupsRepo()->removeItem('group_id', $data['group_id']);
                return response()->json(['success' => true, 'message' => 'Group deleted successfully']);
            }
            return response()->json(['hasErrors' => true, 'message' => 'Group failed to delete!']);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Group failed to delete, please check error logs for more details'], 500);
        }
    }
}
