<?php

namespace App\Models\Structure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleType extends Model
{
    use HasFactory;

    protected $table = 'articletypes';

    protected $primaryKey = 'id'; // auto increment

    protected $fillable = [
        'articletype_id', // cel real
        'name',
        'category_id',
        'subcategory_id',
        'gender_id'
    ];
}
