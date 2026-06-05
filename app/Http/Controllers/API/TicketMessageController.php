<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketMessageController extends Controller
{
    public function index(Request $request, Ticket $ticket)
    {
        $this->authorize($request->user(), $ticket);

        $messages = $ticket->messages()
            ->with('user:id,name,role')
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $user = $request->user();
        $this->authorize($user, $ticket);

        $data = $request->validate([
            'body' => 'required|string',
            'is_internal_note' => 'sometimes|boolean',
        ]);

        // Clients can never write internal notes.
        if ($user->isClient()) {
            $data['is_internal_note'] = false;
        }

        $message = $ticket->messages()->create([
            'user_id' => $user->id,
            'body' => $data['body'],
            'is_internal_note' => $data['is_internal_note'] ?? false,
        ]);

        return response()->json($message->load('user:id,name,role'), 201);
    }

    /**
     * The reporter, plus any analyst or admin (regardless of assignment),
     * can read/write messages. Analysts collaborate on every ticket.
     */
    private function authorize($user, Ticket $ticket): void
    {
        if (! $user) {
            abort(401);
        }

        $allowed = in_array($user->role, ['analyst', 'admin'], true)
            || (int) $ticket->user_id === (int) $user->id;

        if (! $allowed) {
            abort(403, 'Forbidden');
        }
    }
}
