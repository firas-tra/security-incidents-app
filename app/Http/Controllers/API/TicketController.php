<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Ticket::with(['asset', 'category', 'reporter:id,name,role', 'assignee:id,name,role'])
            ->orderByDesc('created_at');

        if ($user && $user->isClient()) {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'severity' => 'required|in:low,medium,high,critical',
            'type' => 'required|in:malware,phishing,unauthorized_access,data_breach,other',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'asset_id' => 'nullable|exists:assets,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'open';

        $ticket = Ticket::create($data);
        return response()->json($ticket->load(['asset', 'category', 'reporter']), 201);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if ($user->isClient() && (int) $ticket->user_id !== (int) $user->id) {
            abort(403, 'Forbidden');
        }

        return response()->json($ticket->load(['asset', 'category', 'reporter', 'assignee']));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if ($user->isClient() && (int) $ticket->user_id !== (int) $user->id) {
            abort(403, 'Forbidden');
        }

        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'severity' => 'sometimes|in:low,medium,high,critical',
            'type' => 'sometimes|in:malware,phishing,unauthorized_access,data_breach,other',
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'asset_id' => 'sometimes|exists:assets,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $ticket->update($data);
        return response()->json($ticket->load(['asset', 'category', 'reporter', 'assignee']));
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        // Clients can delete their own tickets; admins/analysts can delete any.
        if ($user->isClient() && (int) $ticket->user_id !== (int) $user->id) {
            abort(403, 'Forbidden');
        }
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted']);
    }

    public function markResolved(Request $request, Ticket $ticket)
    {
        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
        return response()->json($ticket->load(['asset', 'category', 'reporter', 'assignee']));
    }

    public function markClosed(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        if ((int) $ticket->user_id !== (int) $user->id) {
            abort(403, 'Only the reporter can close their ticket.');
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
        return response()->json($ticket->load(['asset', 'category', 'reporter', 'assignee']));
    }

    public function assign(Request $request, Ticket $ticket)
    {
        // Immutable once assigned: an existing assignee can never be changed.
        abort_if($ticket->assigned_to !== null, 422, 'Ticket already has an assignee');

        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Only an analyst or admin may be assigned a ticket.
        $assignee = User::findOrFail($validated['assigned_to']);
        abort_unless(in_array($assignee->role, ['analyst', 'admin']), 422, 'Assignee must be an analyst or admin');

        $ticket->assigned_to = $assignee->id;

        // Auto-transition: a freshly assigned open ticket moves into progress.
        // Never revert a ticket that is already in_progress/resolved/closed.
        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';
        }

        $ticket->save();

        return response()->json($ticket->fresh()->load(['asset', 'category', 'reporter', 'assignee']));
    }
}
