<?php

namespace App\Http\Controllers;

use App\Models\Attachments;
use App\Models\WebsiteSettings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Riste\AbstractRepository;

class WebsiteSettingsController extends Controller
{
    public function list(Request $request) :Response
    {
        $settings = $this->getRepo();
        $repoData = $settings->get();
        $firstKey = array_key_first($repoData);
        if(!is_null($firstKey)) {
            $data = $repoData[$firstKey];
            return response()->json(['setting' => $data]);
        }
        return response()->json(['hasErrors' => true, 'message' => "Can't load settings data"],404);
    }
    private function getRepo() :AbstractRepository|\Exception {
        return $this->loadRepo('WebsiteSettings');
    }
    public function save(Request $request) :Response
    {
        try {
            $data = $request->validate([
                'website_name' => 'required',
                'registration_enabled' => 'boolean',
                'verify_email_address' => 'boolean',
                'registration_min_age' => 'integer',
                'website_logo' => 'integer|nullable',
                'tos_text' => 'nullable',
                'privacy_text' => 'nullable'
            ]);
            if(array_key_exists('website_logo', $data) && is_null(Attachments::query()->find($data['website_logo']))){
                return response()->json(['hasErrors' => true, 'message' => 'Invalid Logo attachment'],404);
            }
            $model = WebsiteSettings::query()->firstOrCreate();
            $model->fill($data);
            $model->save();
            $this->getRepo()->addOrUpdate($model);
            return response()->json(['success' => true, 'message' => 'settings updated successfully']);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => "Something went wrong. Unable to save settings, check error logs"], 500);
        }
    }
}
