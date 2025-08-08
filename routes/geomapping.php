<?php

use App\Services\SidlanAPIServices;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

Route::name('geomapping.')->prefix('geomapping')->group(function () {
    Route::name('iplan.')->prefix('iplan')->group(function () {

        // Route to handle the dashboard view for iReap
        // This route fetches data from the iReap API and passes it to the view
        Route::view('dashboard', 'geomapping.iplan.dashboard')->name('dashboard');
        Route::view('dashboard-2', 'geomapping.iplan.dashboard-2')->name('dashboard-2');
        Route::view('dashboard-3', 'geomapping.iplan.dashboard-3')->name('dashboard-3');

        //next route

    });
});
