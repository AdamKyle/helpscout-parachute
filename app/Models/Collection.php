<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Article;

class Collection extends Model
{

    protected $table = 'collections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'collection_id',
        'collection_number',
        'name',
        'visibility',
        'site_id',
    ];

    public function categories() {
        return $this->hasMany(Category::class, 'collection_id');
    }

    public function articles() {
        return $this->hasMany(Article::class, 'collection_id');
    }
}
