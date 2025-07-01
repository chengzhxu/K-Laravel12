<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationLog extends Model
{

    /**
     * 可批量赋值的属性
     */
    protected $fillable = [
        'user_id',
        'route',
        'method',
        'remark',
        'ip_address',
        'user_agent',
        'request_data',
        'response_data',
    ];

    /**
     * 属性类型转换
     */
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * 与用户模型的关联关系
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取格式化的请求方式
     */
    public function getFormattedMethodAttribute(): string
    {
        return strtoupper($this->method);
    }

    /**
     * 获取用户名称（如果用户存在）
     */
    public function getUserNameAttribute(): ?string
    {
        return $this->user?->name;
    }
}
