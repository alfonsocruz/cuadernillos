<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CuadernillosController;
use App\Http\Controllers\CatController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect('admin');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('token', function(){
        return csrf_token();
    });

    Route::group(['middleware' => ['admin.user']], function(){
        Route::name('voyager.')->group(function(){

            Route::name('cuadernillos.')->group(function(){
                Route::group(['prefix' => 'cuadernillos'], function () {
                    Route::get('/data/test', [CuadernillosController::class, 'data'])->name('data');
                    Route::post('/upload/file', [CuadernillosController::class, 'upload'])->name('file.upload');
                    Route::post('/store/file', [CuadernillosController::class, 'storeFile'])->name('file.store');
                });
            });

            Route::name('catalogs.')->group(function(){
                Route::group(['prefix' => 'catalogs'], function () {
                    Route::post('/get', [CatController::class, 'get'])->name('get');
                });
            });
        });
    });
});
