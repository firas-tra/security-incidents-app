<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['name', 'description', 'url', 'category_id', 'icon'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
