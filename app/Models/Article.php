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

    protected $primaryKey = 'id'; // auto increment

    protected $fillable = [
        'article_id', // cel real
        'size_0',
        'size_1',
        'size_2',
        'size_3',
        'size_4',
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
        'third_image',
        'description',
        'gender_id',
        'category_id',
        'subcategory_id',
        'articletype_id',
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
}