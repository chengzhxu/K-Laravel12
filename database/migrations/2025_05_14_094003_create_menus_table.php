<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级菜单ID');
            $table->string('name')->comment('菜单名称');
            $table->string('path')->nullable()->comment('前端路由路径');
            $table->string('component')->nullable()->comment('前端组件路径');
            $table->string('permission')->nullable()->comment('权限标识');
            $table->string('icon')->nullable()->comment('图标');
            $table->string('redirect')->nullable()->comment('重定向路径');
            $table->tinyInteger('type')->default(0)->comment('类型 0:菜单 1:按钮');
            $table->tinyInteger('hidden')->default(0)->comment('是否隐藏 0:显示 1:隐藏');
            $table->tinyInteger('status')->default(1)->comment('状态 0:禁用 1:启用');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
