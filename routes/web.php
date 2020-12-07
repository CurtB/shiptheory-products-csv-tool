<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', '\App\Http\Controllers\CsvController@welcome')->name('welcome');
Route::post('/import_csv','\App\Http\Controllers\CsvController@import_csv');
Route::post('/export_csv','\App\Http\Controllers\CsvController@export_csv');
Route::get('/logout','\App\Http\Controllers\CsvController@logout');
