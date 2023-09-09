<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure\Gender;
use App\Models\Structure\Category;
use App\Models\Structure\SubCategory;
use App\Models\Structure\ArticleType;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'article_id',
        'price',
        'discounted_price',
        'article_number',
        'display_name',
        'brand_name',
        'colour',
        'season',
        'usage',
        'pattern',
        'first_image',
        'second_image',
        'description',
        'gender_id',
        'category_id',
        'subcategory_id',
        'articletype_id',
        'size_S_availability',
        'size_M_availability',
        'size_L_availability',
        'size_XL_availability',
        'size_XXL_availability',
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id', 'gender_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function articleType()
    {
        return $this->belongsTo(ArticleType::class, 'articletype_id');
    }

    public function cartItems()
    {
        return $this->belongsTo(CartItem::class, 'id', 'article_id');
    }

    public function viewedArticle()
    {
        return $this->belongsTo(ViewedArticle::class, 'id', 'article_id');
    }
}
