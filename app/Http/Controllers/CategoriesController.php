<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Riste\AbstractRepository;

class CategoriesController extends Controller
{
    public function actionList(Request $request) :JsonResponse {
        $categoriesRepo = $this->categoryRepo();
        (int) $offset = $request->get("offset", 0);
        (int) $limit = $request->get("limit",20);
        $items = $categoriesRepo->paginate($offset, $limit);
        return response()->json(["categories" => $items]);
    }
    private function categoryRepo() :\Exception|AbstractRepository{
        return $this->loadRepo("Categories");
    }
    public function actionAdd(Request $request) :JsonResponse
    {
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

    }
    public function actionGet(Request $request, string $slug) :JsonResponse
    {
        $category = $this->categoryRepo()->findWhere('category_slug',$slug);
        if(!is_null($category)) {
            return response()->json(['category' => $category]);
        } else {
            return response()->json(['hasError' => true, 'message' => 'Category not found!'], 404);
        }
    }
    public function actionEdit(Request $request) :JsonResponse
    {
            try {
                $data = $request->validate([
                    "category_id" => "required|numeric",
                    "category_name" => "regex:/^[a-zA-Z\s0-9,.!]+$/|required",
                    "parent_category_id" => "nullable|numeric",
                    "category_slug" => "regex:/^[a-zA-Z0-9\s-_]+$/|required",
                    "category_enabled" => "required|numeric",
                    "og_image" => "nullable|numeric",
                    "meta_keywords" => "nullable|regex:/^[a-zA-Z\s0-,.!]+$/",
                    "meta_description" => "nullable|regex:/^[a-zA-Z\s0-,.!]+$/"
                ]);
                if(!Categories::query()->where('category_id', '=', $data['category_id'])->exists()){
                    return response()->json(['hasError' => true, 'message' => 'Category not found'], 404);
                }
                $category = Categories::query()->find($data['category_id']);
                unset($data['category_id']);
                $category->fill($data);
                $category->save();
                $this->categoryRepo()->addOrUpdate($category);
                return response()->json(['success' => true, 'message' => 'Category successfully updated!']);
            } catch (ValidationException $e) {
                $errors = $e->errors();
                return response()->json(['hasError' => true, 'errors' => $errors], 400);
            }
    }

}
