<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

Route::get('/', [IndexController::class, 'home'])->name('home');
Route::get('/issues', [IndexController::class, 'issues'])->name('listIssues');
Route::get('/statuses', [IndexController::class, 'statuses'])->name('listStatuses');
