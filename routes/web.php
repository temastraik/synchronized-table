<?php

use App\Http\Controllers\TextItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('text-items.index');
});

Route::resource('text-items', TextItemController::class);
Route::post('text-items/generate-random', [TextItemController::class, 'generateRandom'])->name('text-items.generate-random');
Route::post('text-items/clear-all', [TextItemController::class, 'clearAll'])->name('text-items.clear-all');
Route::post('text-items/update-google-sheet-url', [TextItemController::class, 'updateGoogleSheetUrl'])->name('text-items.update-google-sheet-url');
Route::get('fetch/{count?}', [TextItemController::class, 'fetchData'])->name('fetch.data');
