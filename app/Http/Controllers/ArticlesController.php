<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\Attachments;
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
}
