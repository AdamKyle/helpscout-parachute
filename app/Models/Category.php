<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ArticleCategory;

class Category extends Model
{

    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'category_number',
        'name',
        'collection_id',
    ];

    public function articles() {
        return $this->belongsToMany(ArticleCategory::class, 'article_category', 'category_id', 'article_id');
    }
}
