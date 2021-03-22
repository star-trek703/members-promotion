<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletController;

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
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/auth.php';

// Profile
Route::get('profile/{id}', function ($id) {
    $profile = User::findOrFail($id);

    return view('profile', [
        'profile' => $profile
    ]);
})->name('profile');

// Admin routes
Route::group(['prefix' => 'admin', 'name' => 'admin.'], function () {
    Route::get('users', [UserController::class, 'index'])->name('users');
    Route::get('wallets', [WalletController::class, 'index'])->name('wallets');
});
