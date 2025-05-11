<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['controller' => UserController::class, 'prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{userId}', 'show')->name('show');
    Route::put('/{userId}', 'update')->name('update');
    Route::delete('/{userId}', 'destroy')->name('destroy');
});

Route::group(['controller' => RoleController::class, 'prefix' => 'role', 'as' => 'role.'], function () {
    Route::get('', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/{roleId}', 'show')->name('show');
    Route::put('/{roleId}', 'update')->name('update');
    Route::delete('/{roleId}', 'destroy')->name('destroy');
});

Route::group(['controller' => CategoryController::class, 'prefix' => 'category', 'as' => 'category.'], function () {
    Route::get('', 'index')->name('index');
    Route::get('/{categoryId}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('/{categoryId}', 'update')->name('update');
    Route::delete('/{categoryId}', 'destroy')->name('destroy');
});
