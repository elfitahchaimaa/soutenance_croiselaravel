<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Colocation;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    /**
     * Owner sends invitation to an email address.
     */
    public function store(Request $request, Colocation $colocation): RedirectResponse
    {
        // Only the owner can invite
        if (! $colocation->isOwner(auth()->user())) {
            abort(403, 'Only the owner can send invitations.');
        }

        if (! $colocation->isActive()) {
            return back()->withErrors(['error' => 'Cannot invite to a cancelled colocation.']);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        // Prevent duplicate pending invitations
        $existing = Invitation::where('colocation_id', $colocation->id)
            ->where('email', $validated['email'])
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->withErrors(['email' => 'An invitation is already pending for this email.']);
        }

        $invitation = Invitation::create([
            'colocation_id' => $colocation->id,
            'email'         => $validated['email'],
            'token'         => Str::uuid()->toString(),
            'status'        => 'pending',
            'expires_at'    => now()->addDays(7),
        ]);

        // Send invitation email
        Mail::to($validated['email'])->send(new InvitationMail($invitation));

        return back()->with('success', "Invitation sent to {$validated['email']}!");
    }

    /**
     * Show the accept/refuse page for a given token.
     */
    public function show(string $token): View
    {
        $invitation = Invitation::with('colocation')
            ->where('token', $token)
            ->firstOrFail();

        if (! $invitation->isPending() || $invitation->isExpired()) {
            return view('invitations.expired', compact('invitation'));
        }

        return view('invitations.show', compact('invitation'));
    }

    /**
     * Authenticated user accepts the invitation.
     */
    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::with('colocation')
            ->where('token', $token)
            ->firstOrFail();

        // Validations
        if (! $invitation->isPending() || $invitation->isExpired()) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'This invitation is no longer valid.']);
        }

        // Verify email matches the invited email
        if (auth()->user()->email !== $invitation->email) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'This invitation was sent to a different email address.']);
        }

        // Constraint: user must not have an active membership
        if (auth()->user()->hasActiveMembership()) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'You already belong to an active colocation. Leave it first to accept this invitation.']);
        }

        // Accept: add user to colocation pivot
        $invitation->colocation->members()->attach(auth()->id(), [
            'role'      => 'member',
            'joined_at' => now(),
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect()->route('colocations.show', $invitation->colocation_id)
            ->with('success', 'You have joined the colocation!');
    }

    /**
     * User refuses the invitation.
     */
    public function refuse(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isPending()) {
            $invitation->update(['status' => 'refused']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Invitation refused.');
    }
}
