<?php

use App\Http\Controllers\Api\ShortUrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('short-urls', ShortUrlController::class)->only([
    'index', 'show', 'store', 'destroy',
]);


// Route::prefix('short-url')->group(function () {
//     Route::get('/', [ShortUrlController::class, 'index']);
//     Route::post('/', [ShortUrlController::class, 'store']);
//     Route::get('/{code}', [ShortUrlController::class, 'show']);
// });
