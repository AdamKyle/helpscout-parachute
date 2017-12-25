<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ArticleCategory;
use App\Models\Category;

class Article extends Model
{

    protected $table = 'articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id',
        'article_number',
        'status',
        'name',
        'collection_id',
        'content',
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, 'article_category', 'article_id', 'category_id');
    }
}
