<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/**
 * Tasks
 */
Route::controller(TaskController::class)->group(function () {
    Route::get('/', 'index')->name('tasks.index');
    Route::post('/tasks', 'store')->name('tasks.store');

    Route::post('/tasks/reorder', 'reorder')->name('tasks.reorder');

    Route::put('/tasks/{task}', 'update')->name('tasks.update');
    Route::delete('/tasks/{task}', 'destroy')->name('tasks.destroy');
});
