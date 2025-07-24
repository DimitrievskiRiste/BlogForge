<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\Attachments;
use App\Models\Categories;
use App\Models\ContentTranslations;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ArticlesController extends Controller
{
    public function list(Request $request) :Response
    {
        try {
            $offset = $request->get("offset", 0);
            $limit = $request->get("limit", 25);
            $articles = $this->loadRepo("Articles")->paginate($offset, $limit);
            $translationsRepo = $this->loadRepo("ContentTranslations");
            foreach($articles as $key => $article) {
                if($article instanceof Articles) {
                    $articles[$key]["translations"] = [];
                    $articles[$key]["translations"][] = $translationsRepo->findMany([["article_id" => $article->id]]);
                }
            }
            return response()->json(['articles' => $articles]);
        } catch (Exception $e){
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Something went wrong when getting articles data, check error logs'], 500);
        }
    }
    public function add(Request $request) :Response
    {
        try {
            $data = $request->validate([
                'title' => 'required',
                'meta_description' => 'required',
                'meta_keywords' => 'required',
                'og_image' => 'nullable|integer',
                'parent_category_id' => 'integer',
                'content' => 'required',
                'locale' => 'required',
                'allow_comments' => 'boolean',
                'translations' => 'nullable|array'
            ]);
            $newData = $data;
            if(isset($newData['translations'])){
                unset($newData['translations']);
            }
            $article = new Articles();
            $article->fill($newData);
            $article->save();
            $this->loadRepo("Articles")->addOrUpdate($article);
            $translationsModels = [];
            if(isset($data['translations'])) {
                if(isset($data['og_image']) && !Attachments::query()->where('attachment_id', '=', $data['og_image'])->exists()){
                    return response()->json(['hasErrors' => true, 'message' => 'Invalid attachment specified!'], 400);
                }
                $contentTranslation = new ContentTranslations();
                foreach($data['translations'] as $key => $value) {
                    $contentTranslation->locale = $value['locale'];
                    $contentTranslation->content = $value['translation_content'];
                    $contentTranslation->article_id = $article->id;
                    $contentTranslation->save();
                    $translationsModels[] = $contentTranslation;
                }
                // Save to the cache as well
                $this->loadRepo("ContentTranslations")->addOrUpdate($contentTranslation);
            }
            return response()->json(['success' => true, 'message' => 'Content successfully added', 'article' => $article, 'translations' => $translationsModels]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Something went wrong, please check error logs!'], 500);
        }
    }
    public function edit(Request $request) :Response {
        try {
            $data = $request->validate([
                'id' => 'required|integer',
                'title' => 'required',
                'meta_description' => 'required',
                'meta_keywords' => 'required',
                'og_image' => 'nullable|integer',
                'parent_category_id' => 'integer',
                'content' => 'required',
                'locale' => 'required',
                'allow_comments' => 'boolean',
                'translations' => 'nullable|array'
            ]);
            if(!empty($data) && array_key_exists('id', $data) && array_key_exists('parent_category_id', $data)){
                $article = Articles::query()->with(['ContentTranslations'])->where('id','=', $data['id']);
                if(!$article->exists()){
                    return response()->json(['hasErrors' => true, 'message' => 'Article not found!'], 400);
                }
                if(!Categories::query()->where('category_id', '=', $data['parent_category_id'])->exists()){
                    return response()->json(['hasErrors' => true, 'message' => 'Invalid parent category. Parent category is required!'], 400);
                }
                $translations = $data['translations'] ?? null;
                if(!is_null($translations) && !empty($translations)){
                    $contentTranslation = new ContentTranslations();
                    foreach($translations as $translation) {
                        if(!array_key_exists('id', $translation)){
                            // The model is still not saved, so we have to save
                            $contentTranslation->fill($translation);
                            $contentTranslation->save();
                            $this->loadRepo("ContentTranslations")->addOrUpdate($contentTranslation);
                        } else if (array_key_exists('id', $translation)){
                            $item = $contentTranslation->query()->where('id', '=', $translations['id']);
                            if($item->exists()){
                                $data = $item->first();
                                $data->fill($translation);
                                $data->save();
                                $this->loadRepo("ContentTranslations")->addOrUpdate($data);
                            }
                        } else {
                            Log::error("--- Displaying debug for edit article route ---");
                            Log::error($translation);
                        }
                    }
                }
                // so we will now perform again query to get relations for ContentTranslations if we added/updated translation we need to update it in cache as well.
                // Since we're getting translations directly via Article's cache relationship we will need to update it in article cache.
                $article = Articles::query()->with(['ContentTranslations'])->where('id','=', $data['id'])->first();
                unset($data['id']);
                unset($data['translations']);
                $article->fill($data);
                $article->save();
                $this->loadRepo("Articles")->addOrUpdate($article);
                return response()->json(['success' => true, 'message' => 'Article successfully updated']);
            } else {
                return response()->json(['hasErrors' => true, 'message' => 'Article not found'], 400);
            }
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Failed to update article'], 400);
        }
    }
    public function delete(Request $request) :Response
    {
        try {
            $data = $request->validate([
                'id' => 'required|integer'
            ]);
            if(!empty($data) && array_key_exists('id', $data)){
                $article = Articles::query()->with(['ContentTranslations'])->find($data['id']);
                if(!is_null($article)) {
                    $article->delete();
                    return response()->json(['success' => true, 'message' => "Article successfully removed!"]);
                }
                return response()->json(['hasErrors' => true, 'message' => 'article not found'], 400);
            } else {
                return response()->json(['hasErrors' => true, 'message' => 'article not found'], 400);
            }
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['hasErrors' => true, 'message' => 'Cant remove article, see error logs for more details.'], 400);
        }
    }
}
