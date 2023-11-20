<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\TaskController;
use \App\Http\Controllers\Api\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['api', 'auth.basic'])
    ->group(function() {
        Route::controller(TaskController::class)
            ->prefix('tasks')
            ->name('.tasks')
            ->group(function () {
                Route::get('/',                     'index');
                Route::post('/',                    'store');
                Route::get('/{id}',                 'show');
                Route::put('/{id}',                 'update');
                Route::put('/change-state/{id}',    'changeState');
                Route::delete('/{id}',              'destroy');
            });

        Route::controller(UserController::class)
            ->prefix('users')
            ->name('.users')
            ->group(function () {
                Route::get('/',         'index');
                Route::post('/',        'store');
                Route::get('/{id}',     'show');
                Route::put('/{id}',     'update');
                Route::delete('/{id}',  'destroy');
            });
    });

Route::fallback(function () {
    response()->json(['404']);
});
