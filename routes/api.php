<?php

use App\Http\Controllers\Api\Admin\AuthorController as AdminAuthorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\FrontController;
use App\Http\Controllers\Api\PublicationController;
use App\Http\Controllers\Api\PublisherController;

Route::group(['controller' => AuthController::class, 'as' => 'auth.'], function () {
    Route::group(['middleware' => 'guest:sanctum'], function () {
        Route::post('login', 'login')->name('login');
        Route::post('register', 'register')->name('register');
        Route::post('forgot-password', 'sendResetLink')->name('forgot-password');
        Route::post('rest-password', 'resetPassword')->name('reset-password');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('logout', 'logout')->name('logout');
    });
});

Route::group(['controller' => UserController::class, 'middleware' => ['auth:sanctum'], 'as' => 'user.', 'prefix' => 'user'], function () {
    Route::get('', 'show')->name('show');
    Route::get('cabinet', 'cabinet')->name('cabinet');
    Route::put('', 'update')->name('update');
    Route::post('avatar', 'updateAvatar')->name('update.avatar');
});

Route::group(['controller' => AuthorController::class, 'middleware' => [], 'as' => 'author.', 'prefix' => 'author'], function () {
    Route::get('', 'index')->name('index');
    Route::get('{authorId}', 'show')->name('show');
});

Route::group(['controller' => CategoryController::class, 'middleware' => [], 'as' => 'category.', 'prefix' => 'category'], function () {
    Route::get('', 'index')->name('index');
    Route::get('{categoryId}', 'show')->name('show');
});

Route::group(['controller' => PublicationController::class, 'middleware' => [], 'as' => 'publication.', 'prefix' => 'publication'], function () {
    Route::get('', 'index')->name('index');
    Route::get('{publicationId}', 'show')->name('show');
});

Route::group(['controller' => PublisherController::class, 'middleware' => [], 'as' => 'publisher.', 'prefix' => 'publisher'], function () {
    Route::get('', 'index')->name('index');
    Route::get('{publisherId}', 'show')->name('show');
});

Route::group(['controller' => FrontController::class], function () {
    Route::get('search', 'search')->name('search');
    Route::get('statistic', 'statistic')->name('statistic');
    Route::get('top-authors', 'topAuthors')->name('topAuthors');
});

Route::get('call', function () {
    return Artisan::call('app:import-openalex', [
        'type' => request('type', 'works'),
    ]);
});
