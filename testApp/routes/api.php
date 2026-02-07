<?php

use App\Http\Controllers\AbsenceController;
use App\Http\Middleware\SimpleAuth;
use App\Http\Middleware\FakeAuth;
Route::middleware(FakeAuth::class)->group(function () {
    Route::post('/absences', [AbsenceController::class, 'store']);

    // Pouze pro Managery
    Route::middleware(SimpleAuth::class)->group(function () {
        Route::patch('/absences/{absence}/approve', [AbsenceController::class, 'approve']);
        Route::patch('/absences/{absence}/reject', [AbsenceController::class, 'reject']);
        Route::patch('/absences/{absence}/update', [AbsenceController::class, 'update']);
        Route::patch('/absences/{absence}/cancel', [AbsenceController::class, 'cancel']);
    });

});