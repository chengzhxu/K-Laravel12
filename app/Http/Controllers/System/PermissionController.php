<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * 获取权限列表
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $permissions = Permission::paginate($perPage);
        return $this->successPaginated($permissions);
    }

    /**
     * 获取单个权限信息
     */
    public function show($id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        return $this->success($permission);
    }

    /**
     * 创建权限
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'guard_name' => 'sometimes|string|max:255',
        ]);
        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => $validated['guard_name'] ?? 'web',
        ]);
        return $this->success($permission, '权限创建成功');
    }

    /**
     * 更新权限
     */
    public function update(Request $request, $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:permissions,name,' . $id,
            'guard_name' => 'sometimes|string|max:255',
        ]);
        if (isset($validated['name'])) {
            $permission->name = $validated['name'];
        }
        if (isset($validated['guard_name'])) {
            $permission->guard_name = $validated['guard_name'];
        }
        $permission->save();
        return $this->success($permission, '权限更新成功');
    }

    /**
     * 删除权限
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        // 不允许删除基本权限
        $systemPermissions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete',
            'permissions.assign', 'roles.assign'
        ];
        if (in_array($permission->name, $systemPermissions, true)) {
            return $this->fail(422, '不能删除系统权限');
        }
        $permission->delete();
        return $this->success(null, '权限删除成功');
    }
}
