<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PdfController;


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

// Route::get('/', function () {
//     return view('welcome');
// });

//Client Controller
Route::get('/', [ClientController::class, 'home']);
Route::get('/shop', [ClientController::class, 'shop']);
Route::get('/cart', [ClientController::class, 'cart']);
Route::get('/checkout', [ClientController::class, 'checkout']);
Route::get('/register', [ClientController::class, 'register']);
Route::get('/signin', [ClientController::class, 'signin']);
Route::get('/addtocart/{id}', [ClientController::class, 'addtocart']);
Route::put('/cart/updateqty/{id}', [ClientController::class, 'updateqty']);
Route::get('/cart/removeitem/{id}', [ClientController::class, 'removeitem']);
Route::post('/createaccount', [ClientController::class, 'createaccount']);
Route::post('/accessaccount', [ClientController::class, 'accessaccount']);
Route::get('/logout', [ClientController::class, 'logout']);
Route::post('/payer', [ClientController::class, 'payer']);
Route::get('/payementSuccess', [ClientController::class, 'payementSuccess']);

//Admin controller
Route::get('/admin', [AdminController::class, 'home']);
Route::get('/admin/addcategory', [AdminController::class, 'addcategory']);
Route::get('/admin/categories', [AdminController::class, 'categories']);
Route::get('/admin/addslider', [AdminController::class, 'addslider']);
Route::get('/admin/sliders', [AdminController::class, 'sliders']);
Route::get('/admin/addproduct', [AdminController::class, 'addproduct']);
Route::get('/admin/products', [AdminController::class, 'products']);
Route::get('/admin/orders', [AdminController::class, 'orders']);

//Category Controller
Route::post('/admin/savecategory', [CategoryController::class, 'savecategory']);
Route::delete('/admin/deletecategory/{id}', [CategoryController::class, 'deletecategory']);
Route::get('/admin/editcategory/{id}', [CategoryController::class, 'editcategory']);
Route::put('/admin/updatecategory/{id}', [CategoryController::class, 'updatecategory']);

//Slider Controller
Route::post('/admin/saveslider', [SliderController::class, 'saveslider']);
Route::delete('/admin/deleteslider/{id}', [SliderController::class, 'deleteslider']);
Route::get('/admin/editslider/{id}', [SliderController::class, 'editslider']);
Route::put('/admin/updateslider/{id}', [SliderController::class, 'updateslider']);
Route::put('/admin/unactivateslider/{id}', [SliderController::class, 'unactivateslider']);
Route::put('/admin/activateslider/{id}', [SliderController::class, 'activateslider']);

//Product Contorller
Route::post('/admin/saveproduct', [ProductController::class, 'saveproduct']);
Route::delete('/admin/deleteproduct/{id}', [ProductController::class, 'deleteproduct']);
Route::get('/admin/editproduct/{id}', [ProductController::class, 'editproduct']);
Route::put('/admin/updateproduct/{id}', [ProductController::class, 'updateproduct']);
Route::put('/admin/activateproduct/{id}', [ProductController::class, 'activateproduct']);
Route::put('/admin/unactivateproduct/{id}', [ProductController::class, 'unactivateproduct']);

//PDF Controller
Route::get('seeorders/{id}', [PdfController::class, 'seeorders']);
