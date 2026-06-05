<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = ['title', 'slug', 'content', 'category_id', 'keywords', 'views_count', 'helpful_count'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Article $article) {
            if (empty($article->slug)) {
                $article->slug = static::makeUniqueSlug($article->title, $article->id);
            }
        });
    }

    protected static function makeUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'article';
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
