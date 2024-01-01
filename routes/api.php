<?php

use Illuminate\Http\Request;
use App\Models\ImageManipulation;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\AlbumController;
use App\Http\Controllers\V1\ImageManipulationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/





Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::apiResource("album", AlbumController::class);
        Route::get("image", [ImageManipulationController::class, "index"]);
        Route::get("image/by-album/{album}", [ImageManipulationController::class, "byAlbum"]);
        Route::get("image/{image}", [ImageManipulationController::class, "show"]);
        Route::delete("image/delete/{image}", [ImageManipulationController::class, "destroy"]);
        Route::post("image/resize", [ImageManipulationController::class, "resize"])->name("image-resize");
    });
});
