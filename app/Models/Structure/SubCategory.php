<?php

namespace App\Models\Structure;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $table = 'subcategories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'subcategory_id',
        'name',
        'category_id',
        'gender_id'
    ];
}
