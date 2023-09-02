<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;

use App\Models\Structure\Gender;
use App\Models\Structure\Category;
use App\Models\Structure\SubCategory;
use App\Models\Structure\ArticleType;
use App\Models\ViewedArticle;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function checkIfCanPurchase(Request $request)
    {
        $sizeColumnName = 'size_' . strtoupper($request->size) . '_availability';

        $article = Article::where([
            ['article_id', $request->id],
            [$sizeColumnName, '>', $request->quantity]
        ])->first();

        if (!$article) {
            return response()->error(false);
        } else {
            return response()->success(true);
        }
    }

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

        $total_articles = $articles_query->count('id');

        $filters_query = clone $articles_query;

        $articles = $articles_query->paginate(16);

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
            'current_page' => $request->page ?? 1,
            'last_page' => ceil($total_articles / 16)
        ];

        return response()->success($result);
    }

    public function getArticleData($article_id)
    {
        $article_data = Article::query()->where('article_id', $article_id)->get();
        return response()->success($article_data);
    }

    public function addViewedArticle(Request $request)
    {
        $user_id = Auth::id();
        $article = Article::where('article_id', $request->article_id)->first();

        $article_id = $article->id;

        if (!$user_id)
            return response()->error('You must be logged-in to perform this action!');

        $viewedArticle = ViewedArticle::where('user_id', $user_id)
            ->where('article_id', $article_id)
            ->first();

        // Check if the article already exists in viewed_articles
        if ($viewedArticle) {
            if ($viewedArticle->times_viewed < 20) {
                $viewedArticle->increment('times_viewed');
            }
        } else {
            $count = ViewedArticle::where('user_id', $user_id)->count();

            if ($count >= 10) {
                // Delete the oldest record
                ViewedArticle::where('user_id', $user_id)
                    ->orderBy('created_at', 'asc')
                    ->limit(1)
                    ->delete();
            }

            // Insert new record
            ViewedArticle::create([
                'user_id' => $user_id,
                'article_id' => $article_id,
            ]);
        }

        return response()->success(['message' => 'Article viewed']);
    }

    public function getViewedArticles()
    {
        $user_id = Auth::id();

        // Eager-load the viewedArticle along with the Article model
        $result = Article::with(['viewedArticle' => function ($query) use ($user_id) {
            $query->where('user_id', $user_id)->orderBy('created_at', 'desc');
        }])
            ->whereHas('viewedArticle', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->get()
            ->sortByDesc(function ($article, $key) {
                return $article->viewedArticle->created_at;
            })
            ->values(); // Reset the keys on the sorted collection

        return response()->success($result);
    }
}
