<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ColocationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index – list the authenticated user's colocations
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        $colocations = auth()->user()->colocations()
            ->withPivot(['role', 'joined_at', 'left_at'])
            ->latest()
            ->get();

        return view('colocations.index', compact('colocations'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create / Store
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        // Block creation if already an active member somewhere
        if (auth()->user()->hasActiveMembership()) {
            abort(403, 'You already have an active colocation membership. Leave it first.');
        }

        return view('colocations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Constraint: one active colocation per user
        if (auth()->user()->hasActiveMembership()) {
            return redirect()->route('colocations.index')
                ->withErrors(['error' => 'You already belong to an active colocation. You must leave it before creating a new one.']);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $colocation = Colocation::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id'    => auth()->id(),
            'status'      => 'active',
        ]);

        // Add creator as owner in pivot
        $colocation->members()->attach(auth()->id(), [
            'role'      => 'owner',
            'joined_at' => now(),
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation created successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */
    public function show(Colocation $colocation): View
    {
        $activeMembers = $colocation->activeMembers()->get();
        $invitations   = $colocation->invitations()->latest()->get();

        return view('colocations.show', compact('colocation', 'activeMembers', 'invitations'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit / Update  (owner only)
    |--------------------------------------------------------------------------
    */
    public function edit(Colocation $colocation): View
    {
        $this->authorizeOwner($colocation);

        return view('colocations.edit', compact('colocation'));
    }

    public function update(Request $request, Colocation $colocation): RedirectResponse
    {
        $this->authorizeOwner($colocation);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $colocation->update($validated);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation updated successfully!');
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy  (owner cancels colocation)
    |--------------------------------------------------------------------------
    */
    public function destroy(Colocation $colocation): RedirectResponse
    {
        $this->authorizeOwner($colocation);

        // Set all active memberships as left
        $memberIds = $colocation->activeMembers()->pluck('users.id')->toArray();
        foreach ($memberIds as $memberId) {
            $colocation->members()->updateExistingPivot($memberId, ['left_at' => now()]);
        }

        $colocation->update(['status' => 'cancelled']);

        return redirect()->route('colocations.index')
            ->with('success', 'Colocation has been cancelled.');
    }

    /*
    |--------------------------------------------------------------------------
    | Private helpers
    |--------------------------------------------------------------------------
    */
    private function authorizeOwner(Colocation $colocation): void
    {
        if (! $colocation->isOwner(auth()->user())) {
            abort(403, 'Only the owner can perform this action.');
        }
    }
}
