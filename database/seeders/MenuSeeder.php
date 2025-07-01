<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空已有数据
        Menu::truncate();

        // 创建基本菜单
        $menus = [
            // 仪表盘
            [
                'parent_id' => 0,
                'name' => '仪表盘',
                'path' => '/dashboard',
                'component' => 'dashboard/index',
                'permission' => 'dashboard',
                'icon' => 'HomeFilled',
                'type' => 0,
                'sort' => 1,
            ],

            // 系统管理
            [
                'parent_id' => 0,
                'name' => '系统管理',
                'path' => '/system',
                'component' => '',
                'permission' => 'system',
                'icon' => 'Tools',
                'type' => 0,
                'sort' => 2,
            ],

            // 用户管理
            [
                'parent_id' => 2, // 系统管理
                'name' => '用户管理',
                'path' => '/system/users',
                'component' => 'system/user/index',
                'permission' => 'users.view',
                'icon' => 'UserFilled',
                'type' => 0,
                'sort' => 1,
            ],

            // 角色管理
            [
                'parent_id' => 2, // 系统管理
                'name' => '角色管理',
                'path' => '/system/roles',
                'component' => 'system/role/index',
                'permission' => 'roles.view',
                'icon' => 'Avatar',
                'type' => 0,
                'sort' => 2,
            ],

            // 权限管理
            [
                'parent_id' => 2, // 系统管理
                'name' => '权限管理',
                'path' => '/system/permissions',
                'component' => 'system/permission/index',
                'permission' => 'permissions.view',
                'icon' => 'Checked',
                'type' => 0,
                'sort' => 3,
                'deleted_at' => now(),
            ],

            // 菜单管理
            [
                'parent_id' => 2, // 系统管理
                'name' => '菜单管理',
                'path' => '/system/menus',
                'component' => 'system/menu/index',
                'permission' => 'menus.view',
                'icon' => 'Menu',
                'type' => 0,
                'sort' => 4,
            ],

            // 用户管理-按钮权限
            [
                'parent_id' => 3, // 用户管理
                'name' => '用户创建',
                'permission' => 'users.create',
                'type' => 1,
                'sort' => 1,
            ],
            [
                'parent_id' => 3, // 用户管理
                'name' => '用户编辑',
                'permission' => 'users.edit',
                'type' => 1,
                'sort' => 2,
            ],
            [
                'parent_id' => 3, // 用户管理
                'name' => '用户删除',
                'permission' => 'users.delete',
                'type' => 1,
                'sort' => 3,
            ],
            [
                'parent_id' => 3, // 用户管理
                'name' => '分配角色',
                'permission' => 'roles.assign',
                'type' => 1,
                'sort' => 4,
            ],

            // 角色管理-按钮权限
            [
                'parent_id' => 4, // 角色管理
                'name' => '角色创建',
                'permission' => 'roles.create',
                'type' => 1,
                'sort' => 1,
            ],
            [
                'parent_id' => 4, // 角色管理
                'name' => '角色编辑',
                'permission' => 'roles.edit',
                'type' => 1,
                'sort' => 2,
            ],
            [
                'parent_id' => 4, // 角色管理
                'name' => '角色删除',
                'permission' => 'roles.delete',
                'type' => 1,
                'sort' => 3,
            ],
            [
                'parent_id' => 4, // 角色管理
                'name' => '分配权限',
                'permission' => 'permissions.assign',
                'type' => 1,
                'sort' => 4,
            ],

            // 权限管理-按钮权限
            [
                'parent_id' => 5, // 权限管理
                'name' => '权限创建',
                'permission' => 'permissions.create',
                'type' => 1,
                'sort' => 1,
                'deleted_at' => now(),
            ],
            [
                'parent_id' => 5, // 权限管理
                'name' => '权限编辑',
                'permission' => 'permissions.edit',
                'type' => 1,
                'sort' => 2,
                'deleted_at' => now(),
            ],
            [
                'parent_id' => 5, // 权限管理
                'name' => '权限删除',
                'permission' => 'permissions.delete',
                'type' => 1,
                'sort' => 3,
                'deleted_at' => now(),
            ],

            // 菜单管理-按钮权限
            [
                'parent_id' => 6, // 菜单管理
                'name' => '菜单创建',
                'permission' => 'menus.create',
                'type' => 1,
                'sort' => 1,
            ],
            [
                'parent_id' => 6, // 菜单管理
                'name' => '菜单编辑',
                'permission' => 'menus.edit',
                'type' => 1,
                'sort' => 2,
            ],
            [
                'parent_id' => 6, // 菜单管理
                'name' => '菜单删除',
                'permission' => 'menus.delete',
                'type' => 1,
                'sort' => 3,
            ],

            // 操作日志
            [
                'parent_id' => 2, // 系统管理
                'name' => '操作日志',
                'path' => '/system/operationsLogs',
                'component' => '/system/operationsLogs/index',
                'permission' => 'logs.view',
                'icon' => 'List',
                'type' => 0,
                'sort' => 5,
            ],
        ];

        foreach ($menus as $index => $menu) {
            $menuModel = new Menu();
            $menuModel->id = $index + 1; // 确保ID与parent_id引用一致
            $menuModel->parent_id = $menu['parent_id'];
            $menuModel->name = $menu['name'];
            $menuModel->path = $menu['path'] ?? null;
            $menuModel->component = $menu['component'] ?? null;
            $menuModel->permission = $menu['permission'] ?? null;
            $menuModel->icon = $menu['icon'] ?? null;
            $menuModel->redirect = $menu['redirect'] ?? null;
            $menuModel->type = $menu['type'];
            $menuModel->hidden = $menu['hidden'] ?? 0;
            $menuModel->status = $menu['status'] ?? 1;
            $menuModel->sort = $menu['sort'];
            $menuModel->deleted_at = $menu['deleted_at'] ?? null;
            $menuModel->save();
        }
    }
}
