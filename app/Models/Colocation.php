<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colocation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'owner_id', 'status'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    /** The user who created / owns this colocation. */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * All users in the pivot table (includes left members).
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'colocation_user')
                    ->withPivot(['role', 'joined_at', 'left_at'])
                    ->withTimestamps();
    }

    /**
     * Only currently active members (left_at IS NULL).
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'colocation_user')
                    ->withPivot(['role', 'joined_at', 'left_at'])
                    ->wherePivotNull('left_at');
    }

    /** Invitations sent for this colocation. */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function isMember(User $user): bool
    {
        return $this->activeMembers()
                    ->where('user_id', $user->id)
                    ->exists();
    }
}
