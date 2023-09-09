<?php

namespace App\Models\Structure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleType extends Model
{
    use HasFactory;

    protected $table = 'articletypes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'articletype_id',
        'name',
        'category_id',
        'subcategory_id',
        'gender_id'
    ];
}
