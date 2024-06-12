<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CPImageGenerationController;
use App\Http\Controllers\ImageGenerationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CPImageGenerationController::class, 'welcomeFeed'])->name('welcome');


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/dashboard', [CPImageGenerationController::class, 'index'])->name('dashboard');
        Route::get('/published-puzzles', [CPImageGenerationController::class, 'publishedPuzzles'])->name('published_puzzles');
        // CPImageGeneration routes


Route::get('/cp-image-generation', [CPImageGenerationController::class, 'index'])->name('cp_image_generation.index');
Route::post('/cp-image-generation', [CPImageGenerationController::class, 'store'])->name('cp_image_generation.store');

Route::post('/cp-image-generation/crop', [CPImageGenerationController::class, 'crop'])->name('cp_image_generation.crop');
Route::get('cp-image-generation/cropped/{id}', [CPImageGenerationController::class, 'showCropped'])->name('cp_image_generation.cropped');
Route::post('/cp-image-generation/create-product', [CPImageGenerationController::class, 'createProduct'])->name('cp_image_generation.create_product');
Route::post('/cp-image-generation/upscale', [CPImageGenerationController::class, 'upscale'])->name('cp_image_generation.upscale');
Route::get('/cp-image-generation/upscaled', [CPImageGenerationController::class, 'showUpscaledImage'])->name('cp_image_generation.upscaled');
Route::get('cp-image-generation/upscaled/{id}', [CPImageGenerationController::class, 'showUpscaled'])->name('cp_image_generation.upscaled');

Route::get('/puzzle-feed', [CPImageGenerationController::class, 'puzzleFeed'])->name('puzzle_feed');
Route::post('/like-product', [CPImageGenerationController::class, 'likeProduct'])->name('cp_image_generation.like');   
});

// Staging routes
Route::get('/staging', [ImageGenerationController::class, 'index'])->name('staging.index');
Route::post('/staging/generate', [ImageGenerationController::class, 'generate'])->name('staging.generate');
