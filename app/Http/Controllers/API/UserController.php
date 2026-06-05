<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::withCount(['reportedTickets', 'assignedTickets'])
                ->orderBy('name')
                ->get()
        );
    }

    /**
     * Users who may be assigned a ticket. Only analysts — admins assign,
     * they are not part of the assignable pool.
     */
    public function assignable(Request $request)
    {
        return response()->json(
            User::whereIn('role', ['analyst'])
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role'])
        );
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:client,analyst,admin',
        ]);

        $user->update($data);
        return response()->json($user->loadCount(['reportedTickets', 'assignedTickets']));
    }
}
