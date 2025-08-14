<?php

use App\Services\SidlanAPIServices;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

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
