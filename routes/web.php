<?php


use App\Http\Controllers\ImageScraperController;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('scraper');
});

Route::post('/scraper', [ImageScraperController::class, 'scrape'])->name('scrape');
