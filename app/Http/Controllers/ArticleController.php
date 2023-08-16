<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;

use App\Models\Structure\Gender;
use App\Models\Structure\Category;
use App\Models\Structure\SubCategory;
use App\Models\Structure\ArticleType;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    public function getArticles(Request $request, $gender = null, $category = null, $subcategory = null, $articletype = null)
    {

        $sort = $request->sort ?: null;

        $query = Article::query();

        // Gender Filter
        if ($gender) {
            $genderModel = Gender::where('name', $gender)->first();
            if ($genderModel) {
                $query->where('gender_id', $genderModel->gender_id);
            }
        }

        // Category Filter
        if ($category) {
            $categoryModel = Category::where('name', $category)
                ->where('gender_id', optional($genderModel)->gender_id)
                ->first();
            if ($categoryModel) {
                $query->where('category_id', $categoryModel->id);
            }
        }

        // Subcategory Filter
        if ($subcategory) {
            $subCategoryModel = SubCategory::where('name', $subcategory)
                ->where('category_id', optional($categoryModel)->id)
                ->first();
            if ($subCategoryModel) {
                $query->where('subcategory_id', $subCategoryModel->id);
            }
        }

        // ArticleType Filter
        if ($articletype) {
            $articleTypeModel = ArticleType::where('name', $articletype)
                ->where('subcategory_id', optional($subCategoryModel)->id)
                ->first();
            if ($articleTypeModel) {
                $query->where('articletype_id', $articleTypeModel->id);
            }
        }

        if ($sort) {
            if ($sort === 'asc') {
                $query->orderBy('price', 'asc');
            } elseif ($sort === 'desc') {
                $query->orderBy('price', 'desc');
            }
        }

        $articles = $query->get();

        return response()->success($articles);
    }

    public function getColours()
    {
        return response()->success(
            Article::select('colour')->distinct()->get()
        );
    }

    public function getBrands()
    {
        return response()->success(
            Article::select('brand_name')->distinct()->get()
        );
    }

    public function getSeasons()
    {
        return response()->success(
            Article::select('season')->distinct()->get()
        );
    }

    public function getUsages()
    {
        return response()->success(
            Article::select('usage')->distinct()->get()
        );
    }

    public function getPatterns()
    {
        return response()->success(
            Article::select('pattern')->distinct()->get()
        );
    }
}
