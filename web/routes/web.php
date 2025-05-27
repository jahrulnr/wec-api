<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\ApiSwitcherController;
use App\Http\Controllers\Api\ApiProxyController;
use App\Http\Controllers\ApiSwitcherDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PostmanController;
use App\Http\Middleware\ApiSwitcherMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

Route::get("/health", function() {
    return "OK";
});

Route::get("/", function(){
    return redirect()->route('homepage');
});

Route::prefix("cms-api")->group(function(){
    // Public routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // API Proxy Routes - These handle all API requests through the middleware
    Route::prefix("api")->middleware(ApiSwitcherMiddleware::class)
        ->withoutMiddleware(VerifyCsrfToken::class)
        ->group(function(){
        // This wildcard route will catch all API requests
        Route::any('{any}', ApiProxyController::class)->where('any', '.*');
    });

    // Protected routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/', function () {
            return view('admin.dashboard.index');
        })->name('homepage');
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        
        // Session check endpoint for AJAX validation
        Route::post('/session/check', function (Request $request) {
            return new JsonResponse(['status' => 'valid', 'user' => $request->user()->only(['id', 'name', 'email'])]);
        });
    });

    // API Switcher UI Dashboard Routes
    Route::prefix('api-switcher')->middleware(['auth'])->group(function() {
        Route::get('/', [ApiSwitcherDashboardController::class, 'index'])->name('api-switcher.dashboard');
        Route::get('/create', [ApiSwitcherDashboardController::class, 'create'])->name('api-switcher.create');
        Route::post('/store', [ApiSwitcherDashboardController::class, 'store'])->name('api-switcher.store.ui');
        Route::get('/edit/{id}', [ApiSwitcherDashboardController::class, 'edit'])->name('api-switcher.edit');
        Route::post('/update/{id}', [ApiSwitcherDashboardController::class, 'update'])->name('api-switcher.update.ui');
        Route::post('/delete/{id}', [ApiSwitcherDashboardController::class, 'destroy'])->name('api-switcher.delete');
        Route::post('/toggle/{id}', [ApiSwitcherDashboardController::class, 'toggle'])->name('api-switcher.toggle.ui');
        Route::get('/test', [ApiSwitcherDashboardController::class, 'test'])->name('api-switcher.test');
        Route::post('/test', [ApiSwitcherDashboardController::class, 'executeTest'])->name('api-switcher.test.execute');
        Route::get('/logs', [ApiSwitcherDashboardController::class, 'logs'])->name('api-switcher.logs');
        Route::post('/logs/clear', [ApiSwitcherDashboardController::class, 'clearLogs'])->name('api-switcher.logs.clear');
    });

    // API Switcher Management Routes
    Route::prefix("api-switcher")->middleware(['auth'])->group(function(){
        Route::get('/criteria', [ApiSwitcherController::class, 'index'])->name('api-switcher.index');
        Route::post('/criteria', [ApiSwitcherController::class, 'store'])->name('api-switcher.store');
        Route::get('/criteria/{id}', [ApiSwitcherController::class, 'show'])->name('api-switcher.show');
        Route::put('/criteria/{id}', [ApiSwitcherController::class, 'update'])->name('api-switcher.update');
        Route::delete('/criteria/{id}', [ApiSwitcherController::class, 'destroy'])->name('api-switcher.destroy');
        Route::patch('/criteria/{id}/toggle', [ApiSwitcherController::class, 'toggleActive'])->name('api-switcher.toggle');
    });

    // User Management Routes
    Route::prefix('users')->middleware(['auth', 'permission:users-view'])->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/create', [UserController::class, 'create'])->middleware('permission:users-create')->name('users.create');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users-create')->name('users.store');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('permission:users-edit')->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:users-edit')->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users-delete')->name('users.destroy');
    });

    // Profile page for authenticated user
    Route::get('/profile', [UserController::class, 'profile'])->middleware('auth')->name('profile.show');
    Route::post('/profile', [UserController::class, 'profileUpdate'])->middleware('auth')->name('profile.update');

    // Role Management Routes
    Route::prefix('roles')->middleware(['auth', 'permission:roles-view'])->group(function() {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [RoleController::class, 'create'])->middleware('permission:roles-create')->name('roles.create');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles-create')->name('roles.store');
        Route::get('/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:roles-edit')->name('roles.edit');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:roles-edit')->name('roles.update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles-delete')->name('roles.destroy');
    });

    // Permission Management Routes
    Route::prefix('permissions')->middleware(['auth', 'permission:permissions-view'])->group(function() {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/create', [PermissionController::class, 'create'])->middleware('permission:permissions-create')->name('permissions.create');
        Route::post('/', [PermissionController::class, 'store'])->middleware('permission:permissions-create')->name('permissions.store');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->middleware('permission:permissions-edit')->name('permissions.edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->middleware('permission:permissions-edit')->name('permissions.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->middleware('permission:permissions-delete')->name('permissions.destroy');
    });

    // Postmant Routes
    Route::prefix('tester')->middleware(['auth', 'permission:postman'])->group(function(){
        Route::get('/', [PostmanController::class, 'postman'])->name('postman');
        Route::post('/', [PostmanController::class, 'executePostman'])->name('postman.execute');
    });
});
