<?php

use App\Services\SidlanAPIServices;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

Route::name('geomapping.')->prefix('geomapping')->group(function () {
    Route::name('iplan.')->prefix('iplan')->group(function () {

        // Route to handle the dashboard view for iReap
        // This route fetches data from the iReap API and passes it to the view
        Route::get('dashboard', function (): View {
            $apiService = new SidlanAPIServices();
            $irZeroOneData = $apiService->executeRequest();
            return view('geomapping.iplan.dashboard', [
                'irZeroOneData' => $irZeroOneData,
            ]);
        })->name('dashboard');

        //next route

    });
});
