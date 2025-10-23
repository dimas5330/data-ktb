<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// KTB Member CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('ktb-members', App\Http\Controllers\KtbMemberController::class);

    // Mentee relationship management
    Route::get('ktb-members/{member}/add-mentee', [App\Http\Controllers\KtbMemberController::class, 'addMentee'])->name('ktb-members.add-mentee');
    Route::post('ktb-members/{member}/store-mentee', [App\Http\Controllers\KtbMemberController::class, 'storeMentee'])->name('ktb-members.store-mentee');
    Route::get('ktb-members/{member}/mentee/{relationship}/edit', [App\Http\Controllers\KtbMemberController::class, 'editMentee'])->name('ktb-members.edit-mentee');
    Route::put('ktb-members/{member}/mentee/{relationship}', [App\Http\Controllers\KtbMemberController::class, 'updateMentee'])->name('ktb-members.update-mentee');
    Route::delete('ktb-members/{member}/mentee/{relationship}', [App\Http\Controllers\KtbMemberController::class, 'destroyMentee'])->name('ktb-members.destroy-mentee');
});

// KTB Group CRUD
Route::middleware(['auth'])->group(function () {
    Route::resource('ktb-groups', App\Http\Controllers\KtbGroupController::class);
    Route::get('ktb-groups/{ktbGroup}/assign-members', [App\Http\Controllers\KtbGroupController::class, 'assignMembers'])->name('ktb-groups.assign-members');
    Route::put('ktb-groups/{ktbGroup}/update-members', [App\Http\Controllers\KtbGroupController::class, 'updateMembers'])->name('ktb-groups.update-members');
});

// KTB Tree Visualization
Route::middleware(['auth'])->group(function () {
    Route::get('ktb-tree', [App\Http\Controllers\KtbTreeController::class, 'index'])->name('ktb-tree.index');
    Route::get('ktb-tree/data', [App\Http\Controllers\KtbTreeController::class, 'getTreeData'])->name('ktb-tree.data');
    Route::get('ktb-tree/member/{id}', [App\Http\Controllers\KtbTreeController::class, 'showMemberTree'])->name('ktb-tree.member');
});
