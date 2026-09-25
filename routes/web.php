<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\ProjectController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('divisions', DivisionController::class);

Route::resource('positions', PositionController::class);

Route::resource('projects', ProjectController::class);