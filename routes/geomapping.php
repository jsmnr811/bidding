<?php

use Illuminate\View\View;
use App\Livewire\UserList;
use App\Livewire\CodeLogin;
use App\Models\GeomappingUser;
use App\Services\SidlanAPIServices;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Route;
use App\Livewire\InvestmentRegistration;
use App\Http\Controllers\GeomappingUsersTableController;

Route::get('/code-login', CodeLogin::class)->name('investment-forum');

Route::get('/investment-forum-registration', InvestmentRegistration::class)->name('investment.registration');
// Route::get('/investment-forum-user-list', UserList::class)->name('investment.user-list');
Route::get('/investment-forum-user-list', [GeomappingUsersTableController::class, 'index'])->name('investment.user-list');
Route::get('/geomapping-users/{id}/id-card', [App\Http\Controllers\GeomappingUsersTableController::class, 'idCard'])->name('geomapping-users.id-card');



Route::name('geomapping.')->prefix('geomapping')->group(function () {
    Route::name('iplan.')->prefix('iplan')->group(function () {

        Route::view('login', 'geomapping.iplan.login')
            ->name('login')
            ->middleware('guest-geo:geomapping');

        Route::middleware('auth-geo:geomapping')->group(function () {
            Route::view('dashboard', 'geomapping.iplan.dashboard')->name('dashboard');
            Route::view('dashboard-2', 'geomapping.iplan.dashboard-2')->name('dashboard-2');
            Route::view('dashboard-3', 'geomapping.iplan.dashboard-3')->name('dashboard-3');
            Route::view('landing', 'geomapping.iplan.landing')->name('landing');

        });



        //next route

    });



});
  Route::get('sidlaner', function ():void
    {
        $user = GeomappingUser::find(1);
        $fileName = 'user-image-' . $user->id . '.png';
        $storagePath = storage_path('app/public/' . $fileName);

        // Render the blade view to HTML
        $html = view('emails.user-id', ['user' => $user])->render();

        // Generate PNG with fixed window size
        Browsershot::html($html)
            ->windowSize(350, 566)
            ->waitUntilNetworkIdle() // ensure all images load
            ->save($storagePath);
    })->name('sidlan');
