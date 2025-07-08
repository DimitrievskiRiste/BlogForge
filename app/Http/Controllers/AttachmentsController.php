<?php

namespace App\Http\Controllers;

use App\Models\Attachments;
use App\Models\User;
use App\Models\UserAttachments;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AttachmentsController extends Controller
{
    public function upload(Request $request)  {
        try {
            $header = explode(" ", $request->header("Authorization"));
            list(1 => $token) = $header;
            $secretPass = $request->header("Authorization-Pass");
            $jwtToken = JWT::decode($token, new Key($secretPass, "HS256"));
            $user = $this->findMember($jwtToken->user);
            if($user) {
                if(!$user->Group->can_upload_attachments){
                    return response()->json([
                        'error' => true,
                        'message' => 'Missing permission can_upload_attachments, action aborted!'
                    ], 403);
                }
                $request->validate([
                    'file' => 'required|mimes:zip,png,bmp,jpg,jpeg'
                ]);
                if($request->file('file')->isValid()) {
                    $file = $request->file('file');
                    $filePath = $file->store("images/{$user->id}");
                    $mimeType = $file->getMimeType();
                    $ext = $file->guessExtension();
                    $size = $file->getSize();
                    // lets save attachment data to database.
                    $attachment = new Attachments();
                    $attachment->attachment_ext = $ext;
                    $attachment->attachment_path = $filePath;
                    $attachment->attachment_name = $file->getFilename();
                    $attachment->size = $size;
                    $attachment->mime_type = $mimeType;
                    $attachment->save();
                    // Lets save user attachment to database
                    $userAttachment = new UserAttachments();
                    $userAttachment->attachment_id = $attachment->attachment_id;
                    $userAttachment->user_id = $user->id;
                    $userAttachment->save();
                    // let's now return blob data of attachment.
                    $blob = base64_encode(Storage::get($filePath));
                    return response()->json([
                        'success' => true,
                        'attachment' => [
                            'name' => $attachment->attachment_name,
                            'blob' => $blob,
                            'mime' => $mimeType
                        ]
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        'message' => 'Attachment type not supported'
                    ], 403);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Not authorized'
                ], 403);
            }
        } catch(ValidationException $validationException) {
            return response()->json([
                'error' => true,
                'message' => 'Missing attachment or attachment type or size is not supported'
            ], 400);
        }
    }
    private function findMember(int $userId) :User|null{
        return User::query()->with(['Group'])->find($userId) ?? null;
    }
}
