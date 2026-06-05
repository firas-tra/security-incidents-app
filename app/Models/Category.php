<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
