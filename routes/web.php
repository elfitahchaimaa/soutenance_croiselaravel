<?php

use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Colocations (full resource)
    Route::resource('colocations', ColocationController::class);

    // Membership: leave & remove member
    Route::delete('colocations/{colocation}/leave', [MembershipController::class, 'leave'])
        ->name('colocations.leave');
    Route::delete('colocations/{colocation}/members/{user}', [MembershipController::class, 'remove'])
        ->name('colocations.members.remove');

    // Invitations: send (POST) — requires auth
    Route::post('colocations/{colocation}/invitations', [InvitationController::class, 'store'])
        ->name('invitations.store');

    // Invitation accept/refuse — requires auth
    Route::post('invitations/{token}/accept', [InvitationController::class, 'accept'])
        ->name('invitations.accept');
    Route::post('invitations/{token}/refuse', [InvitationController::class, 'refuse'])
        ->name('invitations.refuse');
});

/*
|--------------------------------------------------------------------------
| Public Invitation Page (can be viewed without login to show instructions)
|--------------------------------------------------------------------------
*/
Route::get('invitations/{token}', [InvitationController::class, 'show'])
    ->name('invitations.show');

require __DIR__.'/auth.php';
