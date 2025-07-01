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
        Schema::table('roles', static function (Blueprint $table) {
            $table->string('role_name')->nullable()->comment('角色名称')->after('name');
            $table->tinyInteger('status')->default(1)->comment('状态 0:禁用 1:启用')->after('guard_name');
            $table->unsignedInteger('sort')->default(0)->comment('排序')->after('status');
            $table->string('remark')->nullable()->comment('备注')->after('sort');
            $table->softDeletes();
        });

        Schema::table('permissions', static function (Blueprint $table) {
            $table->string('permission_name')->nullable()->comment('权限名称')->after('name');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', static function (Blueprint $table) {
            $table->dropColumn('role_name');
            $table->dropColumn('status');
            $table->dropColumn('sort');
            $table->dropColumn('remark');
            $table->dropSoftDeletes();
        });

        Schema::table('permissions', static function (Blueprint $table) {
            $table->dropColumn('permission_name');
            $table->dropSoftDeletes();
        });
    }
};
