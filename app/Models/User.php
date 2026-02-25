<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_banned',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_banned'         => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /** All colocation memberships (including past ones). */
    public function colocations(): BelongsToMany
    {
        return $this->belongsToMany(Colocation::class, 'colocation_user')
                    ->withPivot(['role', 'joined_at', 'left_at']);
    }

    /** Only memberships where the user is still active (left_at IS NULL). */
    public function activeColocations(): BelongsToMany
    {
        return $this->belongsToMany(Colocation::class, 'colocation_user')
                    ->withPivot(['role', 'joined_at', 'left_at'])
                    ->wherePivotNull('left_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /** Check if this user has the global admin role. */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Returns true if the user currently belongs to at least one active colocation.
     * Used to enforce the single-active-colocation constraint.
     */
    public function hasActiveMembership(): bool
    {
        return $this->activeColocations()->exists();
    }
}
