<?php

use App\Http\Controllers\DeleteController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\InstagramController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;






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
})->name('home');

//login route
Route::post('/instagram/login', [InstagramController::class, 'login'])->name('instagram.login');

//upload routes
Route::get('/instagram/upload', [UploadController::class, 'index'])->name('instagram.upload');


Route::post('/instagram/photo/upload', [UploadController::class, 'uploadPhoto'])->name('instagram.photo.upload');
Route::post('/instagram/video/upload', [UploadController::class, 'uploadVideo'])->name('instagram.video.upload');
Route::post('/instagram/photo/upload_to_story', [UploadController::class, 'uploadPhotoToStory'])->name('instagram.photo.upload_to_story');
Route::post('/instagram/video/upload_to_story', [UploadController::class, 'uploadVideoToStory'])->name('instagram.video.upload_to_story');

//best time to post route in upload controller
Route::post('/insights/account', [InstagramController::class, 'fetchAndAnalyzeAccountInsights'])->name('account.insights');

//delete routes
Route::get('/instagram/delete', [DeleteController::class, 'index'])->name('instagram.delete.index');
Route::post('/instagram/delete/{mediaType}/{mediaId}', [DeleteController::class, 'destroy'])->name('instagram.delete');

//edit routes
Route::get('/instagram/edit', [EditController::class,'index'])->name('instagram.edit.index');
Route::post('/instagram/media/{id}/edit', [EditController::class, 'editMedia'])->name('media.edit');

//insights routes 
Route::get('/instagram/insights', [InsightsController::class, 'showInsightsForm'])->name('insights.show');
Route::post('/instagram/insights/account', [InsightsController::class, 'fetchInsights'])->name('insights.fetch');

Route::post('/instagram/media', [InsightsController::class, 'fetchAndSaveUserMedia'])->name('instagram.fetch-media');
Route::get('/instagram/insights', [InsightsController::class, 'showInsights'])->name('instagram.insights');
Route::post('/instagram/insights', [InsightsController::class, 'fetchInsights'])->name('instagram.insights');


