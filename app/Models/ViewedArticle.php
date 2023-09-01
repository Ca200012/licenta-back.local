<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViewedArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'times_viewed',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d-m-Y H:i');
    }
}
