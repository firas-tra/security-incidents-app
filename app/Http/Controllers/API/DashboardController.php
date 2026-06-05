<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Single role-aware payload powering the dashboard command center.
     * Clients see only their own tickets; staff see everything.
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        $isClient = $user->isClient();

        $base = fn () => $isClient
            ? Ticket::where('user_id', $user->id)
            : Ticket::query();

        $tickets = $base()->with(['category', 'reporter:id,name'])->get();

        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);
        $twoWeeksAgo = $now->copy()->subDays(14);

        $openCount = $tickets->where('status', 'open')->count();
        $inProgress = $tickets->where('status', 'in_progress')->count();

        $resolved7d = $tickets->filter(fn ($t) => $t->resolved_at && Carbon::parse($t->resolved_at)->gte($weekAgo))->count();
        $resolvedPrev7d = $tickets->filter(fn ($t) => $t->resolved_at
            && Carbon::parse($t->resolved_at)->lt($weekAgo)
            && Carbon::parse($t->resolved_at)->gte($twoWeeksAgo))->count();

        $createdThisWeek = $tickets->filter(fn ($t) => Carbon::parse($t->created_at)->gte($weekAgo))->count();
        $createdPrevWeek = $tickets->filter(fn ($t) => Carbon::parse($t->created_at)->lt($weekAgo)
            && Carbon::parse($t->created_at)->gte($twoWeeksAgo))->count();

        // Average first-response approximation: created → resolved (no per-message timing here).
        $resolvedWithTimes = $tickets->filter(fn ($t) => $t->created_at && $t->resolved_at);
        $avgResolutionHours = $resolvedWithTimes->count()
            ? round($resolvedWithTimes->avg(fn ($t) =>
                Carbon::parse($t->resolved_at)->diffInMinutes(Carbon::parse($t->created_at)) / 60), 1)
            : 0;

        // 7-day sparklines (oldest → newest)
        $sparkOpened = [];
        $sparkResolved = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->toDateString();
            $sparkOpened[] = $tickets->filter(fn ($t) => Carbon::parse($t->created_at)->toDateString() === $day)->count();
            $sparkResolved[] = $tickets->filter(fn ($t) => $t->resolved_at && Carbon::parse($t->resolved_at)->toDateString() === $day)->count();
        }

        // 30-day trend
        $trendLabels = [];
        $trendCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $trendLabels[] = $day->format('m-d');
            $trendCounts[] = $tickets->filter(fn ($t) => Carbon::parse($t->created_at)->toDateString() === $day->toDateString())->count();
        }

        $bySeverity = [
            'low' => $tickets->where('severity', 'low')->count(),
            'medium' => $tickets->where('severity', 'medium')->count(),
            'high' => $tickets->where('severity', 'high')->count(),
            'critical' => $tickets->where('severity', 'critical')->count(),
        ];

        $byStatus = [
            'open' => $openCount,
            'in_progress' => $inProgress,
            'resolved' => $tickets->where('status', 'resolved')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
        ];

        $byCategory = Category::all()->map(fn ($c) => [
            'name' => $c->name,
            'count' => $tickets->where('category_id', $c->id)->count(),
        ])->filter(fn ($row) => $row['count'] > 0)->values();

        $severityRank = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
        $topTickets = $tickets
            ->whereIn('status', ['open', 'in_progress'])
            ->sortByDesc(fn ($t) => [$severityRank[$t->severity] ?? 0, Carbon::parse($t->created_at)->timestamp])
            ->take(5)
            ->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'severity' => $t->severity,
                'status' => $t->status,
                'created_at' => $t->created_at,
            ])->values();

        // Activity feed from ticket lifecycle timestamps
        $activity = collect();
        foreach ($tickets as $t) {
            $activity->push([
                'type' => 'created',
                'message' => ($t->reporter->name ?? 'Someone') . ' opened “' . $t->title . '”',
                'ticket_id' => $t->id,
                'at' => $t->created_at,
            ]);
            if ($t->resolved_at) {
                $activity->push(['type' => 'resolved', 'message' => '“' . $t->title . '” was resolved', 'ticket_id' => $t->id, 'at' => $t->resolved_at]);
            }
            if ($t->closed_at) {
                $activity->push(['type' => 'closed', 'message' => '“' . $t->title . '” was closed', 'ticket_id' => $t->id, 'at' => $t->closed_at]);
            }
        }
        $activity = $activity->sortByDesc('at')->take(20)->values();

        $payload = [
            'role' => $user->role,
            'name' => $user->name,
            'kpis' => [
                'open' => $openCount,
                'in_progress' => $inProgress,
                'resolved_7d' => $resolved7d,
                'avg_resolution_hours' => $avgResolutionHours,
            ],
            'deltas' => [
                'opened_week' => $this->pctDelta($createdThisWeek, $createdPrevWeek),
                'resolved_week' => $this->pctDelta($resolved7d, $resolvedPrev7d),
            ],
            'sparkline_opened' => $sparkOpened,
            'sparkline_resolved' => $sparkResolved,
            'trend' => ['labels' => $trendLabels, 'counts' => $trendCounts],
            'by_severity' => $bySeverity,
            'by_status' => $byStatus,
            'by_category' => $byCategory,
            'top_tickets' => $topTickets,
            'activity' => $activity,
        ];

        if ($isClient) {
            $payload['suggested_articles'] = Article::with('category')
                ->orderByDesc('views_count')->limit(4)->get();
        }

        return response()->json($payload);
    }

    private function pctDelta(int $current, int $previous): ?int
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }
        return (int) round((($current - $previous) / $previous) * 100);
    }
}
