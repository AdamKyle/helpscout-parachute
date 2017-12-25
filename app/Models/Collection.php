<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Model\Category;

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
}
