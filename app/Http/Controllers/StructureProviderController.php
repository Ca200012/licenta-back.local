<?php

namespace App\Http\Controllers;

use App\Models\Structure\ArticleType;
use App\Models\Structure\Category;
use App\Models\Structure\SubCategory;
use Illuminate\Http\Request;

class StructureProviderController extends Controller
{

    public function getCategories($gender_id)
    {
        if (!$gender_id) {
            return response()->error('Please provide a gender!');
        }

        return response()->success(
            Category::select('id AS category_id', 'name')
                ->where('gender_id', $gender_id)
                ->orderBy('name')
                ->get()
        );
    }

    public function getSubCategories($category_id)
    {
        if (!$category_id) {
            return response()->error('Please provide a category!');
        }

        return response()->success(
            SubCategory::select('id AS subcategory_id', 'name')
                ->where('category_id', $category_id)
                ->orderBy('name')
                ->get()
        );
    }

    public function getArticleTypes($subcategory_id)
    {
        if (!$subcategory_id) {
            return response()->error('Please provide a subcategory!');
        }

        return response()->success(
            ArticleType::select('id AS articletype_id', 'name')
                ->where('subcategory_id', $subcategory_id)
                ->orderBy('name')
                ->get()
        );
    }
}
