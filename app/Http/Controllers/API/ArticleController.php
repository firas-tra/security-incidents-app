<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('category');
        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->get('category_id'));
        }
        return response()->json($query->orderByDesc('created_at')->get());
    }

    public function show(Request $request, Article $article)
    {
        // Only count a view when a non-privileged visitor reads the public page.
        // Admins/analysts (e.g. loading the article to edit) must not inflate the count.
        $user = $request->user();
        $isPrivilegedViewer = $user && in_array($user->role, ['admin', 'analyst']);
        if (! $isPrivilegedViewer) {
            $article->increment('views_count');
        }

        return response()->json($article->load('category'));
    }

    /**
     * Admin-only fetch used by the edit form. Never increments views_count.
     */
    public function showForEdit(Article $article)
    {
        return response()->json($article->load('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'keywords' => 'nullable|string',
        ]);

        $article = Article::create($data);
        return response()->json($article->load('category'), 201);
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'keywords' => 'nullable|string',
        ]);

        $article->update($data);
        return response()->json($article->load('category'));
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json(['message' => 'Article deleted']);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $like = '%' . $q . '%';

        $results = Article::with('category')
            ->where(function ($w) use ($like) {
                $w->where('title', 'like', $like)
                  ->orWhere('content', 'like', $like)
                  ->orWhere('keywords', 'like', $like);
            })
            ->orderByRaw(
                '(CASE WHEN title LIKE ? THEN 3 WHEN keywords LIKE ? THEN 2 ELSE 1 END) DESC',
                [$like, $like]
            )
            ->limit(20)
            ->get();

        return response()->json($results);
    }

    public function incrementHelpful(Article $article)
    {
        $article->increment('helpful_count');
        return response()->json(['helpful_count' => $article->helpful_count]);
    }
}
