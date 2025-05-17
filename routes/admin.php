<?php

use App\Models\Publisher;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\AuthorController;
use App\Http\Controllers\Api\Admin\WeightController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\PublisherController;

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

Route::group(['controller' => AuthorController::class, 'prefix' => 'author', 'as' => 'author.'], function () {
    Route::get('', 'index')->name('index');
    Route::get('/{authorId}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('/{authorId}', 'update')->name('update');
    Route::delete('/{authorId}', 'destroy')->name('destroy');
});

Route::group(['controller' => PublisherController::class, 'prefix' => 'publisher', 'as' => 'publisher.'], function () {
    Route::get('', 'index')->name('index');
    Route::get('/{publisherId}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('/{publisherId}', 'update')->name('update');
    Route::delete('/{publisherId}', 'destroy')->name('destroy');
});

Route::group(['controller' => WeightController::class, 'prefix' => 'weight', 'as' => 'weight.'], function () {
    Route::get('', 'index')->name('index');
    Route::get('/{weightId}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('/{weightId}', 'update')->name('update');
    Route::delete('/{weightId}', 'destroy')->name('destroy');
});
