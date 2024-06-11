<?php

use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// routes/web.php

Route::get('/staging', [ImageGenerationController::class, 'index'])->name('staging.index');
Route::post('/staging/generate', [ImageGenerationController::class, 'generate'])->name('staging.generate');


// routes/web.php

use App\Http\Controllers\CPImageGenerationController;

Route::get('/cp-image-generation', [CPImageGenerationController::class, 'index'])->name('cp_image_generation.index');
Route::post('/cp-image-generation', [CPImageGenerationController::class, 'store'])->name('cp_image_generation.store');
