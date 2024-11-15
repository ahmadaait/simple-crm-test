<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Permission\PermissionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'index');

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', 'logout');
        });
    });

    Route::middleware('auth:api')->group(function () {
        Route::prefix('permissions')->controller(PermissionController::class)->group(function () {
            Route::get('/', 'index')->middleware('permission:permissions.index');
            Route::get('/all', 'all')->middleware('permission:permissions.index');
        });

        Route::get('/roles/all', [\App\Http\Controllers\Api\Role\RoleController::class, 'all'])
            ->middleware('permission:roles.index');

        Route::apiResource('/roles', App\Http\Controllers\Api\Role\RoleController::class)
            ->middleware('permission:roles.index|roles.store|roles.update|roles.delete');

        Route::apiResource('/companies', App\Http\Controllers\Api\Company\CompanyController::class)
            ->middleware('permission:companies.index|companies.show|companies.create|companies.update|companies.delete');

        Route::apiResource('/users', App\Http\Controllers\Api\User\UserController::class)
            ->middleware('permission:users.index|users.show|users.create|users.update|users.delete');
    });
});
