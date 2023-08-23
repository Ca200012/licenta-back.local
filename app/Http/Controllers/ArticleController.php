<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;

use App\Models\Structure\Gender;
use App\Models\Structure\Category;
use App\Models\Structure\SubCategory;
use App\Models\Structure\ArticleType;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\map;

class ArticleController extends Controller
{
    public function getArticles(Request $request, $gender = null, $category = null, $subcategory = null, $articletype = null)
    {
        $filterInputs = $request->all();
        $sort = $request->sort ?: null;

        $colours = [];
        $brands = [];
        $usages = [];
        $seasons = [];
        $patterns = [];

        foreach ($filterInputs as $key => $value) {
            if (preg_match('/colour\d{1,3}/', $key)) {
                $colours[] = $filterInputs[$key];
                continue;
            } else if (preg_match('/brand\d{1,3}/', $key)) {
                $brands[] = $filterInputs[$key];
                continue;
            } else if (preg_match('/usage\d{1,3}/', $key)) {
                $usages[] = $filterInputs[$key];
                continue;
            } else if (preg_match('/season\d{1,3}/', $key)) {
                $seasons[] = $filterInputs[$key];
                continue;
            } else if (preg_match('/pattern\d{1,3}/', $key)) {
                $patterns[] = $filterInputs[$key];
                continue;
            }
        }

        $articles_query = Article::query();

        // Gender Filter
        if ($gender) {
            $genderModel = Gender::where('name', $gender)->first();
            if ($genderModel) {
                $articles_query->where('gender_id', $genderModel->gender_id);
            }
        }

        // Category Filter
        if ($category) {
            $categoryModel = Category::where('name', $category)
                ->where('gender_id', optional($genderModel)->gender_id)
                ->first();
            if ($categoryModel) {
                $articles_query->where('category_id', $categoryModel->id);
            }
        }

        // Subcategory Filter
        if ($subcategory) {
            $subCategoryModel = SubCategory::where('name', $subcategory)
                ->where('category_id', optional($categoryModel)->id)
                ->first();
            if ($subCategoryModel) {
                $articles_query->where('subcategory_id', $subCategoryModel->id);
            }
        }

        // ArticleType Filter
        if ($articletype) {
            $articleTypeModel = ArticleType::where('name', $articletype)
                ->where('subcategory_id', optional($subCategoryModel)->id)
                ->first();
            if ($articleTypeModel) {
                $articles_query->where('articletype_id', $articleTypeModel->id);
            }
        }

        // Apply filters

        if (count($colours)) {
            $articles_query->whereIn('colour', $colours);
        }

        if (count($brands)) {
            $articles_query->whereIn('brand_name', $brands);
        }

        if (count($usages)) {
            $articles_query->whereIn('usage', $usages);
        }

        if (count($seasons)) {
            $articles_query->whereIn('season', $seasons);
        }

        if (count($patterns)) {
            $articles_query->whereIn('pattern', $patterns);
        }

        if ($sort) {
            if ($sort === 'asc') {
                $articles_query->orderBy('price', 'asc');
            } elseif ($sort === 'desc') {
                $articles_query->orderBy('price', 'desc');
            }
        }

        $filters_query = clone $articles_query;

        $articles = $articles_query->get();

        $unique_colours = $filters_query->distinct()->pluck('colour')->all();
        $unique_brand_names = $filters_query->distinct()->pluck('brand_name')->all();
        $unique_usages = $filters_query->distinct()->pluck('usage')->all();
        $unique_seasons = $filters_query->distinct()->pluck('season')->all();
        $unique_patterns = $filters_query->whereNot('pattern', "none")->distinct()->pluck('pattern')->all();

        // Create an array to return both articles and unique values
        $result = [
            'articles' => $articles,
            'filters' => [
                [
                    'key' => 'colours',
                    'title' => 'Colour',
                    'values' => $unique_colours
                ],
                [
                    'key' => 'brand_names',
                    'title' => 'Brand',
                    'values' => $unique_brand_names
                ],
                [
                    'key' => 'usages',
                    'title' => 'Usage',
                    'values' => $unique_usages
                ],
                [
                    'key' => 'seasons',
                    'title' => 'Season',
                    'values' => $unique_seasons
                ],
                [
                    'key' => 'patterns',
                    'title' => 'Pattern',
                    'values' => $unique_patterns
                ]
            ],
        ];

        return response()->success($result);
    }

    public function getArticleData($article_id)
    {
        $article_data = Article::query()->where('article_id', $article_id)->get();
        return response()->success($article_data);
    }
}
