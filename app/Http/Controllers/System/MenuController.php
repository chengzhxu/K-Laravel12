<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\MenuRequest;
use App\Models\Menu;
use App\Services\System\MenuService;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    /**
     * 获取全部菜单树结构
     * @return JsonResponse
     */
    public function tree(): JsonResponse
    {
        // 获取所有顶级菜单
        $rootMenus = MenuService::getInstance()->getRootMenus();
        // 构建树结构
        $menus = MenuService::getInstance()->buildMenuTree($rootMenus);
        return $this->success($menus);
    }

    /**
     * 获取当前用户的菜单（根据用户权限过滤）
     * @return JsonResponse
     */
    public function userMenus(): JsonResponse
    {
        $user = auth()->user();
        // 获取用户所有权限
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        // 获取所有顶级菜单
        $rootMenus = MenuService::getInstance()->getRootMenus();
        // 构建有权限访问的菜单树
        $menus = MenuService::getInstance()->buildUserMenuTree($rootMenus, $permissions);
        return $this->success($menus);
    }

    /**
     * 创建菜单、按钮
     * @param MenuRequest $request
     * @return JsonResponse
     */
    public function store(MenuRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $menu = MenuService::getInstance()->saveMenuAndPermission($validated);
        return $menu ? $this->success($menu, '菜单创建成功') : $this->fail(400, '菜单创建失败');
    }

    /**
     * 更新
     * @param MenuRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(MenuRequest $request, $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);
        $validated = $request->validated();
        // 防止将菜单的父级设为自己或其子级
        if (isset($validated['parent_id']) && $validated['parent_id'] !== 0) {
            // 检查是否将自己作为父级
            if ($validated['parent_id'] === $id) {
                return $this->fail(422, '父级菜单不能是自己');
            }
            // 获取所有子菜单ID
            $childIds = MenuService::getInstance()->getChildrenIds($id);
            if (in_array($validated['parent_id'], $childIds, true)) {
                return $this->fail(422, '父级菜单不能是自己的子菜单');
            }
        }

        $result = MenuService::getInstance()->updateMenuAndPermission($menu, $validated);
        return $result ? $this->success($menu->fresh(), '菜单更新成功') : $this->fail(400, '菜单更新失败');
    }

    /**
     * 更新菜单、按钮状态
     * @param $id
     * @return JsonResponse
     */
    public function changeStatus($id): JsonResponse
    {
        $menu = Menu::findOrFail($id);
        $menu->status = $menu->status === 0 ? 1 : 0;
        $menu->save();
        return $this->success($menu, '菜单状态更新成功');
    }

    /**
     * 删除
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $menu = Menu::findOrFail($id);
        // 检查是否有子菜单
        $childCount = Menu::where('parent_id', $id)->count();
        if ($childCount > 0) {
            return $this->fail(422, '请先删除子菜单');
        }
        $menu->delete();
        return $this->success(null, '菜单删除成功');
    }
}
