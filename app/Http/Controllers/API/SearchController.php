<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Asset;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    /**
     * Unified quick-search across articles, tickets and assets.
     * Results are role-scoped: clients only see their own tickets, and assets
     * are only returned for admins (the only role that can open the assets page).
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $user = $request->user();

        if (mb_strlen($q) < 2) {
            return response()->json(['articles' => [], 'tickets' => [], 'assets' => []]);
        }

        $like = '%' . $q . '%';

        $articles = Article::with('category')
            ->where(fn ($w) => $w->where('title', 'like', $like)
                ->orWhere('keywords', 'like', $like)
                ->orWhere('content', 'like', $like))
            ->orderByDesc('views_count')
            ->limit(3)
            ->get();

        $ticketsQuery = Ticket::with(['category', 'reporter:id,name'])
            ->where(fn ($w) => $w->where('title', 'like', $like)
                ->orWhere('description', 'like', $like));
        if ($user->isClient()) {
            $ticketsQuery->where('user_id', $user->id);
        }
        $tickets = $ticketsQuery->orderByDesc('created_at')->limit(3)->get();

        $assets = new Collection();
        if ($user->isAdmin()) {
            $assets = Asset::where(fn ($w) => $w->where('name', 'like', $like)
                ->orWhere('ip_address', 'like', $like)
                ->orWhere('type', 'like', $like))
                ->limit(3)
                ->get();
        }

        return response()->json([
            'articles' => $articles,
            'tickets' => $tickets,
            'assets' => $assets,
        ]);
    }
}
