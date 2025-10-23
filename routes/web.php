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
Route::resource('ktb-members', App\Http\Controllers\KtbMemberController::class)->middleware('auth');

// KTB Tree Visualization
Route::middleware(['auth'])->group(function () {
    Route::get('ktb-tree', [App\Http\Controllers\KtbTreeController::class, 'index'])->name('ktb-tree.index');
    Route::get('ktb-tree/data', [App\Http\Controllers\KtbTreeController::class, 'getTreeData'])->name('ktb-tree.data');
    Route::get('ktb-tree/member/{id}', [App\Http\Controllers\KtbTreeController::class, 'showMemberTree'])->name('ktb-tree.member');
});
