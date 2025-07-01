<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operation_logs', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('操作用户ID');
            $table->string('route')->comment('请求路由');
            $table->string('method')->comment('请求方式');
            $table->string('ip_address')->nullable()->comment('操作IP地址');
            $table->text('user_agent')->nullable()->comment('用户代理信息');
            $table->json('request_data')->nullable()->comment('请求数据');
            $table->json('response_data')->nullable()->comment('响应数据');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();

            // 添加索引
            $table->index(['user_id', 'created_at']);
            $table->index(['route', 'method']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_logs');
    }
};
