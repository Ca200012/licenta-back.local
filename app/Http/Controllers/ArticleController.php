<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function getArticles(Request $request, $gender = null, $category = null, $subCategory = null, $articleType = null)
    {
        // $query = DB::table('articles')
        //             ->leftJoin('genders', 'articles.gender_id', '=', 'genders.gender_id')
        //             ->leftJoin('categories', 'articles.category_id', '=', 'category.gender_id')


        // select('id', 'article_id', 'size_0', 'size_1', 'size_2', 'size_3', 'size_4', 'price', 'discounted_price', 'article_number', 'display_name', 'brand_name', 'colour', 'season', 'usage', 'pattern', 'first_image', 'second_image', 'description');



        // if ($gender_id) {
        //     $query->where('gender_id', $gender_id);
        // }

        // if ($category_id) {
        //     $query->where('category_id', $category_id);
        // }

        // if ($subcategory_id) {
        //     $query->where('subcategory_id', $subcategory_id);
        // }

        // if ($articletype_id) {
        //     $query->where('articletype_id', $articletype_id);
        // }

        // $result = $query->get();

        $query = Article::query();

        if ($gender) {
            $query->whereHas('gender', function ($q) use ($gender) {
                $q->where('name', $gender);
            });
        }

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        if ($subCategory) {
            $query->whereHas('subCategory', function ($q) use ($subCategory) {
                $q->where('name', $subCategory);
            });
        }

        if ($articleType) {
            $query->whereHas('articleType', function ($q) use ($articleType) {
                $q->where('name', $articleType);
            });
        }

        $articles = $query->get();

        return response()->success($articles);
    }
}
