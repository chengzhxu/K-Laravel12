<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\RoleRequest;
use App\Models\Role;
use App\Services\System\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * 获取角色列表
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 15), 50);
        $page = $request->input('page', 1);
        $roles = Role::with(['permissions' => function ($query) {
            $query->select('id', 'name', 'permission_name');
        }]);
        $roles = RoleService::getInstance()->handleSearch($roles, $request);
        $roles = $roles->paginate($limit, ['*'], 'page', $page);
        return $this->successPaginated($roles);
    }

    /**
     * 获取单个角色信息
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $role = Role::with('permissions')->findOrFail($id);
        return $this->success($role);
    }

    /**
     * 创建角色
     * @param RoleRequest $request
     * @return JsonResponse
     */
    public function store(RoleRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $role = Role::create($validated);
        // 如果请求中包含权限，则分配权限
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }
        return $this->success($role, '角色创建成功');
    }

    /**
     * 更新角色
     * @param RoleRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(RoleRequest $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $validated = $request->validated();
        $role->update($validated);
        // 如果请求中包含权限，则同步权限
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }
        return $this->success($role->load('permissions'), '角色更新成功');
    }

    /**
     * 删除角色
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);
        // 不允许删除管理员角色
        if ($role->name === 'admin' || $role->name === 'super-admin') {
            return $this->fail(422, '不能删除系统角色');
        }
        $role->delete();
        return $this->success(null, '角色删除成功');
    }

    /**
     * 更新角色状态
     * @param $id
     * @return JsonResponse
     */
    public function changeStatus($id): JsonResponse
    {
        $menu = Role::findOrFail($id);
        $menu->status = $menu->status === 0 ? 1 : 0;
        $menu->save();
        return $this->success($menu, '角色状态更新成功');
    }

    /**
     * 分配权限给角色
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function assignPermissions(Request $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);
        $role->syncPermissions($validated['permissions']);
        return $this->success(null, '权限分配成功');
    }
}
