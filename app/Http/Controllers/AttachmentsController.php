<?php

namespace App\Http\Controllers;

use App\Models\Attachments;
use App\Models\User;
use App\Models\UserAttachments;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AttachmentsController extends Controller
{
    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $token = $this->getAuthenticatedAPIUser($request);
            $user = $this->extractUserData($token);
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
                    // Let's save attachment model to cache
                    $repo = $this->loadRepo("Attachments");
                    $ttl = time()+3600 * 24 * 30;
                    $repo->addOrUpdate($attachment, $ttl);
                    // Lets save user attachment to database
                    $userAttachment = new UserAttachments();
                    $userAttachment->attachment_id = $attachment->attachment_id;
                    $userAttachment->user_id = $user->id;
                    $userAttachment->save();
                    //Lets save user attachment in cache
                    $repo = $this->loadRepo("UserAttachments");
                    $repo->addOrUpdate($userAttachment, $ttl);
                    // let's now return blob data of attachment.
                    $blob = base64_encode(Storage::get($filePath));
                    return response()->json([
                        'success' => true,
                        'attachment' => [
                            'attachment_id' => $attachment->attachment_id,
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

        } catch(ValidationException $validationException) {
            return response()->json([
                'error' => true,
                'message' => 'Missing attachment or attachment type or size is not supported'
            ], 400);
        }
    }

}
