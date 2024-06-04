<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;

// admin controller
use App\Http\Controllers\Admin\{
    AdminAuthController,
    AdminUserController,
    PageController,
    ContactController,
    NotificationController,
    UnitController,
    ItemController
};

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


Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::middleware(['auth'])->group(function () {

});


// Admin Route

Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/', [AdminAuthController::class, 'index']);

    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('login', 'postLogin')->name('login.post');
        Route::get('forget-password', 'showForgetPasswordForm')->name('forget.password.get');
        Route::post('forget-password', 'submitForgetPasswordForm')->name('forget.password.post');
        Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset.password.get');
        Route::post('reset-password', 'submitResetPasswordForm')->name('reset.password.post');
    });

    Route::middleware('admin')->group(function () {
        Route::controller(AdminAuthController::class)->group(function () {
            Route::get('dashboard', 'adminDashboard')->name('dashboard');
            Route::get('change-password', 'changePassword')->name('change.password');
            Route::post('update-password', 'updatePassword')->name('update.password');
            Route::get('logout', 'logout')->name('logout');
            Route::get('profile', 'adminProfile')->name('profile');
            Route::post('profile', 'updateAdminProfile')->name('update.profile');
        });

        // Route::name('users.')->prefix('users')->controller(AdminUserController::class)->group(function () {
        //     Route::get('/', 'index')->name('index');
        //     Route::get('alluser', 'getallUser')->name('alluser');
        //     Route::post('status', 'userStatus')->name('status');
        //     Route::delete('delete/{id}', 'destroy')->name('dpostroy');    //     Route::get('{id}', 'show')->name('show');
        // });

        // Route::name('contacts.')->prefix('contacts')->controller(ContactController::class)->group(function () {
        //     Route::get('/', 'index')->name('index');
        //     Route::get('all', 'getallcontact')->name('allcontact');
        //     Route::delete('delete/{id}', 'destroy')->name('destroy');
        // });

        // Route::name('page.')->prefix('page')->controller(PageController::class)->group(function () {
        //     Route::get('create/{key}', 'create')->name('create');
        //     Route::put('update/{key}', 'update')->name('update');
        // });

        // Route::name('notifications.')->prefix('notifications')->controller(NotificationController::class)->group(function () {
        //     Route::get('index', 'index')->name('index');
        //     Route::get('clear', 'clear')->name('clear');
        //     Route::delete('delete/{id}', 'destroy')->name('destroy');
        // });

        // Master All Route
        Route::name('master.')->prefix('master')->group(function () {
            // Unit Master
            Route::name('unit.')->prefix('unit')->controller(UnitController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::get('allunit', 'allunit')->name('allunit');
                Route::post('store', 'store')->name('store');
                Route::post('status', 'status')->name('status');
                Route::post('delete', 'delete')->name('delete');
                Route::post('edit', 'edit')->name('edit');
                Route::post('update', 'update')->name('update');
            });

            // Item Master
            Route::name('item.')->prefix('item')->controller(ItemController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::get('allitems', 'allitems')->name('allitems');
                Route::post('store', 'store')->name('store');
                Route::post('status', 'status')->name('status');
                Route::post('delete', 'delete')->name('delete');
                Route::post('edit', 'edit')->name('edit');
                Route::post('update', 'update')->name('update');
            });
        });
    });
});


