<?php

use App\Http\Middleware\ExceptionValid;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
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

Route::redirect('/', '/admin');
Route::redirect('/exceptions', '/admin/exceptions')->middleware([ExceptionValid::class]);