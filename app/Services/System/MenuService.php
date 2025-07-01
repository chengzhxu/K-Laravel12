<?php

namespace App\Services\System;

use App\Models\Menu;
use App\Services\BaseService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class MenuService extends BaseService
{

    /**
     * 获取所有顶级菜单
     * @return Collection
     */
    public function getRootMenus(): Collection
    {
        return Menu::where('parent_id', 0)
            ->orderBy('sort')
            ->get();
    }

    /**
     * 递归构建菜单树
     * @param $menus
     * @return array
     */
    public function buildMenuTree($menus): array
    {
        $result = [];
        foreach ($menus as $menu) {
            $menuItem = $menu->toArray();
            $children = Menu::where('parent_id', $menu['id'])
                ->orderBy('sort')
                ->get();
            if ($children->isNotEmpty()) {
                $menuItem['children'] = $this->buildMenuTree($children);
            } else {
                $menuItem['children'] = [];
            }
            $result[] = $menuItem;
        }
        return $result;
    }

    /**
     * 递归构建用户有权限访问的菜单树
     * @param Collection $menus 所有顶级菜单
     * @param array $permissions 用户所有权限
     * @return array
     * @noinspection SlowArrayOperationsInLoopInspection
     */
    public function buildUserMenuTree(Collection $menus, array $permissions): array
    {
        $result = [];
        $buttons = [];
        foreach ($menus as $menu) {
            // 如果菜单需要权限且用户没有该权限，则跳过
            if ($menu->permission && !in_array($menu->permission, $permissions, true)) {
                continue;
            }
            $menuItem = $menu->toArray();
            $children = Menu::where('parent_id', $menuItem['id'])
                ->where('status', 1)
                ->orderBy('sort')
                ->get();
            // 按钮类型
            if (($menu->type === 1) && $menu->permission) {
                $buttons[] = $menu->permission;
                continue; // 按钮不作为菜单项显示
            }

            if ($children->isNotEmpty()) {
                $childResult = $this->buildUserMenuTree($children, $permissions);
                if (!empty($childResult) && !empty($childResult['menus'])) {
                    $menuItem['children'] = $childResult['menus'];
                } else {
                    if (empty($menu->component)) {
                        continue;
                    }
                    $menuItem['children'] = [];
                }

                if (isset($childResult['buttons'])) {
                    $buttons = array_merge($buttons, $childResult['buttons']);
                }
            } else {
                $menuItem['children'] = [];
            }
            $result[] = $menuItem;
        }
        return [
            'menus' => $result,
            'buttons' => array_unique($buttons)
        ];
    }

    /**
     * 获取所有子菜单ID
     * @param int $menuId
     * @return array
     */
    public function getChildrenIds(int $menuId): array
    {
        $children = Menu::where('parent_id', $menuId)->pluck('id')->all();
        return array_reduce(
            $children,
            fn(array $ids, int $childId) => [...$ids, ...$this->getChildrenIds($childId)],
            $children
        );
    }

    /**
     * 保存菜单数据，并保存权限
     * @param array $validated
     * @return Menu|false
     */
    public function saveMenuAndPermission(array $validated): Menu|false
    {
        try {
            // 检查并保存权限
            if (!empty($validated['permission'])) {
                $permission = Permission::where('name', $validated['permission'])->first();
                if (!$permission) {
                    Permission::create([
                        'name' => $validated['permission'],
                        'guard_name' => 'web',
                    ]);
                }
            }

            $validated['parent_id'] = $validated['parent_id'] ?? 0;
            $validated['hidden'] = $validated['hidden'] ?? 0;
            $validated['status'] = $validated['status'] ?? 1;
            $validated['sort'] = $validated['sort'] ?? 0;
            $menu = Menu::create($validated);
        } catch (Exception $exception) {
            Log::error('create menu error:' . $exception->getMessage());
            return false;
        }
        return $menu;
    }

    /**
     * 更新菜单数据，并保存权限
     * @param Menu $menu
     * @param array $validated
     * @return bool
     */
    public function updateMenuAndPermission(Menu $menu, array $validated): bool
    {
        try {
            // 检查并保存权限
            if (!empty($validated['permission'])) {
                $permission = Permission::where('name', $validated['permission'])->first();
                if (!$permission) {
                    Permission::create([
                        'name' => $validated['permission'],
                        'guard_name' => 'web',
                    ]);
                }
            }

            $menu->update($validated);
        } catch (Exception $exception) {
            Log::error('update menu error:' . $exception->getMessage());
            return false;
        }
        return true;
    }
}
