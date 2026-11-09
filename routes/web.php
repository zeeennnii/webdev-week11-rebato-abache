
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EventQueryController;

Route::get('/events', [EventQueryController::class, 'index'])
    ->name('events.index');

Route::get('/events/naive', [EventQueryController::class, 'naive'])
    ->name('events.naive');

// Homepage automatically redirects to reports
Route::get('/', function () {
    return redirect()->route('reports.index');
});

// Event Queries
Route::get('/events', [EventQueryController::class, 'index'])
    ->name('events.index');

// Campus Events Report
Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports.index');

Route::get('/events/lazy-loading-error', [
    EventQueryController::class,
    'lazyLoadingError'
])->name('events.lazy-loading-error');

Route::get('/events/search', [EventQueryController::class, 'search'])
    ->name('events.search');