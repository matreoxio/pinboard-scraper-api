<?php 

use App\Http\Controllers\LinkController;

use Illuminate\Support\Facades\Route;


Route::get('/get-links', [LinkController::class, 'index']);