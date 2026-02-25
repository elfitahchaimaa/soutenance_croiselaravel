<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class MembershipController extends Controller
{
    /**
     * Member leaves the colocation voluntarily.
     * (Owner cannot leave — they must cancel the colocation instead.)
     */
    public function leave(Colocation $colocation): RedirectResponse
    {
        $user = auth()->user();

        if (! $colocation->isActive()) {
            return redirect()->route('colocations.index')
                ->withErrors(['error' => 'This colocation is no longer active.']);
        }

        // Owner cannot simply leave — they must cancel
        if ($colocation->isOwner($user)) {
            return back()->withErrors(['error' => 'As the owner, you cannot leave. You must cancel the colocation instead.']);
        }

        // Must be an active member
        if (! $colocation->isMember($user)) {
            return redirect()->route('colocations.index')
                ->withErrors(['error' => 'You are not an active member of this colocation.']);
        }

        // Set left_at on the pivot row
        $colocation->members()
            ->wherePivotNull('left_at')
            ->where('user_id', $user->id)
            ->updateExistingPivot($user->id, ['left_at' => now()]);

        // Reputation will be updated in Day 5

        return redirect()->route('colocations.index')
            ->with('success', 'You have successfully left the colocation.');
    }

    /**
     * Owner removes a member from the colocation.
     * (If removed member has debt, it will be imputed to owner — to be handled in Day 4.)
     */
    public function remove(Colocation $colocation, User $user): RedirectResponse
    {
        $owner = auth()->user();

        if (! $colocation->isOwner($owner)) {
            abort(403, 'Only the owner can remove members.');
        }

        if (! $colocation->isActive()) {
            return redirect()->route('colocations.show', $colocation)
                ->withErrors(['error' => 'This colocation is no longer active.']);
        }

        // Owner cannot remove themselves
        if ($user->id === $owner->id) {
            return back()->withErrors(['error' => 'You cannot remove yourself. Cancel the colocation instead.']);
        }

        if (! $colocation->isMember($user)) {
            return back()->withErrors(['error' => 'This user is not an active member of this colocation.']);
        }

        // Set left_at on the pivot row
        $colocation->members()
            ->wherePivotNull('left_at')
            ->where('user_id', $user->id)
            ->updateExistingPivot($user->id, ['left_at' => now()]);

        // Debt imputation to owner will be handled in Day 4 (expenses/balances)

        return redirect()->route('colocations.show', $colocation)
            ->with('success', "{$user->name} has been removed from the colocation.");
    }
}
