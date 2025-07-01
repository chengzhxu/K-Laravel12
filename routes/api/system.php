<?php

use App\Http\Controllers\System\MenuController;
use App\Http\Controllers\System\OperationLogController;
use App\Http\Controllers\System\PermissionController;
use App\Http\Controllers\System\RoleController;
use App\Http\Controllers\System\UserController;
use App\Http\Controllers\System\WarehouseController;
use App\Http\Middleware\OperationLogMiddleware;

Route::middleware(['auth:sanctum'])->group(function () {
    // 用户管理
    Route::prefix('users')->name('users.')->group(function () {
        Route::middleware(['permission:users.view'])->get('/', [UserController::class, 'index'])->name('index');//用户列表
        Route::middleware(['permission:users.view'])->get('/{id}', [UserController::class, 'show'])->name('show');//用户信息
        Route::middleware(['permission:users.create'])->post('/', [UserController::class, 'store'])->name('store');//创建用户
        Route::middleware(['permission:users.edit'])->post('/edit/{id}', [UserController::class, 'update'])->name('update');//更新用户
        Route::middleware(['permission:users.delete'])->post('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');//删除用户
        Route::middleware(['permission:users.delete'])->post('/batch-delete', [UserController::class, 'batchDestroy'])->name('batchDestroy');//批量删除用户
        Route::middleware(['permission:users.edit'])->post('/reset/{id}', [UserController::class, 'resetPassword'])->name('resetPassword');//重置密码
        Route::middleware(['permission:roles.assign'])->post('/{id}/roles', [UserController::class, 'assignRoles'])->name('roles');//分配权限
    });

    // 菜单管理
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::middleware(['permission:menus.view'])->get('/tree', [MenuController::class, 'tree'])->name('tree');//树状结构菜单列表
        Route::get('/user-menus', [MenuController::class, 'userMenus'])->name('userMenus');//构建用户菜单、按钮权限
        Route::middleware(['permission:menus.create'])->post('/', [MenuController::class, 'store'])->name('store');//创建菜单、按钮
        Route::middleware(['permission:menus.edit'])->post('/edit/{id}', [MenuController::class, 'update'])->name('update');//更新菜单、按钮
        Route::middleware(['permission:menus.edit'])->get('/change-status/{id}', [MenuController::class, 'changeStatus'])->name('changeStatus');//更新菜单、按钮状态
        Route::middleware(['permission:menus.delete'])->post('/delete/{id}', [MenuController::class, 'destroy'])->name('destroy');//删除菜单、按钮
    });

    // 角色管理
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::middleware(['permission:roles.view'])->get('/', [RoleController::class, 'index'])->name('index');//角色列表
        Route::middleware(['permission:roles.view'])->get('/{id}', [RoleController::class, 'show'])->name('show');//角色信息
        Route::middleware(['permission:roles.create'])->post('/', [RoleController::class, 'store'])->name('store');//创建角色
        Route::middleware(['permission:roles.edit'])->post('/edit/{id}', [RoleController::class, 'update'])->name('update');//修改角色
        Route::middleware(['permission:roles.edit'])->post('/change-status/{id}', [RoleController::class, 'changeStatus'])->name('changeStatus');//修改角色状态
        Route::middleware(['permission:roles.delete'])->post('/delete/{id}', [RoleController::class, 'destroy'])->name('destroy');//删除角色
        Route::middleware(['permission:roles.assign'])->post('/{id}/permissions', [RoleController::class, 'assignPermissions'])->name('permissions');//分配权限给角色
    });

    // 权限管理
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::middleware(['permission:permissions.view'])->get('/', [PermissionController::class, 'index'])->name('index');
        Route::middleware(['permission:permissions.view'])->get('/{id}', [PermissionController::class, 'show'])->name('show');
        Route::middleware(['permission:permissions.create'])->post('/', [PermissionController::class, 'store'])->name('store');
        Route::middleware(['permission:permissions.edit'])->post('/edit/{id}', [PermissionController::class, 'update'])->name('update');
        Route::middleware(['permission:permissions.delete'])->post('/delete/{id}', [PermissionController::class, 'destroy'])->name('destroy');
    });

    // 操作日志
    Route::withoutMiddleware([OperationLogMiddleware::class])->prefix('operation-logs')->name('operation-logs.')->group(function () {
        Route::middleware(['permission:logs.view'])->get('/', [OperationLogController::class, 'index'])->name('index'); //操作日志列表
        Route::middleware(['permission:logs.view'])->get('/statistics', [OperationLogController::class, 'statistics'])->name('statistics'); //操作日志统计
        Route::middleware(['permission:logs.view'])->get('/{id}', [OperationLogController::class, 'show'])->name('show'); //操作日志详情
        Route::middleware(['permission:logs.delete'])->post('/delete/{id}', [OperationLogController::class, 'destroy'])->name('destroy'); //删除操作日志
        Route::middleware(['permission:logs.delete'])->post('/batch-delete', [OperationLogController::class, 'batchDestroy'])->name('batchDestroy'); //批量删除操作日志
    });
});
