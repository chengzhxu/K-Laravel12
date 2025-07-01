<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清空已有数据
        User::truncate();

        // 创建管理员用户
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
        ]);

        // 创建普通用户
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
        ]);

        // 分配角色
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        if ($userRole) {
            $user->assignRole($userRole);
        }
    }
}
