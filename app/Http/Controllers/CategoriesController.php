<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoriesController extends Controller
{
    public function actionList(Request $request) {
        $categoriesRepo = $this->categoryRepo();
        (int) $offset = $request->get("offset", 0);
        (int) $limit = $request->get("limit",20);
        $items = $categoriesRepo->paginate($offset, $limit);
        return response()->json(["categories" => $items]);
    }
    private function categoryRepo(){
        return $this->loadRepo("Categories");
    }
    public function actionAdd(Request $request)
    {
        $tokenData = $this->getAuthenticatedAPIUser($request);
        $user = $this->extractUserData($tokenData);
        if($user->Group->can_add_categories && $user->Group->can_access_admincp) {
            try {
                $data = $request->validate([
                    "category_name" => "regex:/^[a-zA-Z\s0-9,.!]+$/|required",
                    "parent_category_id" => "nullable|numeric",
                    "category_slug" => "regex:/^[a-zA-Z0-9\s-_]+$/|required",
                    "category_enabled" => "required|numeric",
                    "og_image" => "nullable|numeric",
                    "meta_keywords" => "nullable|regex:/^[a-zA-Z\s0-,.!]+$/",
                    "meta_description" => "nullable|regex:/^[a-zA-Z\s0-,.!]+$/"
                ]);
                if(Categories::query()->where('category_slug', '=', $data['category_slug'])->exists()){
                    return response()->json(['hasError' => true, 'message' => 'Category with that slug already exists'], 400);
                }
                if(Categories::query()->where("category_name", "=", $data['category_name'])->exists()){
                    return response()->json(["hasError" => true, "message" => "Category with that name already exists"], 400);
                }
                $category = new Categories();
                $category->fill($data);
                $category->save();
                // lets save now in cache as well.
                $ttl = time()+3600 * 24 * 30;
                $this->categoryRepo()->addOrUpdate($category, $ttl);
                return response()->json(['success' => true]);
            } catch (ValidationException $e) {
                $errors = $e->errors();
                return response()->json(['hasError' => true, 'errors' => $errors], 400);
            }
        } else {
            return response()->json(['hasError' => true, 'message' => "Your account doesn't have required permission!"], 403);
        }
    }
    public function actionGet(Request $request, string $slug)
    {

    }

}
