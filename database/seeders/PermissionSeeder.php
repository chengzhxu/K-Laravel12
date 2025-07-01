<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建权限
        $permissions = [
            'dashboard' => '仪表盘',
            'system' => '系统设置',

            // 用户管理权限
            'users.view' => '查看用户列表',
            'users.create' => '创建用户',
            'users.edit' => '编辑用户',
            'users.delete' => '删除用户',

            // 角色管理权限
            'roles.view' => '查看角色列表',
            'roles.create' => '创建角色',
            'roles.edit' => '编辑角色',
            'roles.delete' => '删除角色',
            'roles.assign' => '分配角色',

            // 权限管理权限
            'permissions.view' => '查看权限列表',
            'permissions.create' => '创建权限',
            'permissions.edit' => '编辑权限',
            'permissions.delete' => '删除权限',
            'permissions.assign' => '分配权限',

            // 菜单管理权限
            'menus.view' => '查看菜单列表',
            'menus.create' => '创建菜单',
            'menus.edit' => '编辑菜单',
            'menus.delete' => '删除菜单',

            // 操作日志
            'logs.view' => '查看操作日志',
        ];

        foreach ($permissions as $name => $description) {
            Permission::create(['name' => $name, 'guard_name' => 'web', 'permission_name' => $description]);
        }

        // 创建角色
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web', 'role_name' => '管理员']);
        $userRole = Role::create(['name' => 'user', 'guard_name' => 'web', 'role_name' => '普通用户']);

        // 为管理员角色分配所有权限
        $adminRole->givePermissionTo(Permission::all());

        // 为普通用户角色分配基本权限
        $userRole->givePermissionTo([
            'system',
            'users.view',
        ]);

        // 创建管理员用户
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('admin');

        // 创建普通用户
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('user');

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

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
