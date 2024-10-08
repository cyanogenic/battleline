<?php

use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/game/create',     [GameController::class, 'createGame']);
    Route::post('/game/play',       [GameController::class, 'playCard']);
    Route::post('/game/claim',      [GameController::class, 'claimLine']);
    Route::post('/game/draw',       [GameController::class, 'drawCard']);
    Route::post('/game/hand',       [GameController::class, 'checkHand']);
});
