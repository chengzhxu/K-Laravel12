<?php

namespace App\Services\System;

use App\Models\OperationLog;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationLogService extends BaseService
{
    /**
     * 记录操作日志
     */
    public function log(array $data): OperationLog
    {
        return OperationLog::create([
            'user_id' => $data['user_id'] ?? Auth::id(),
            'route' => $data['route'],
            'method' => $data['method'],
            'remark' => $data['remark'] ?? null,
            'ip_address' => $data['ip_address'] ?? request()->ip(),
            'user_agent' => $data['user_agent'] ?? request()->userAgent(),
            'request_data' => $data['request_data'] ?? null,
            'response_data' => $data['response_data'] ?? null,
        ]);
    }

    /**
     * 从请求对象记录日志
     */
    public function logFromRequest(Request $request, ?string $remark = null): OperationLog
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : $request->getPathInfo();

        return $this->log([
            'route' => $routeName ?: $request->getPathInfo(),
            'method' => $request->getMethod(),
            'remark' => $remark ?? '',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_data' => $this->filterSensitiveData($request->all()),
        ]);
    }

    /**
     * 批量记录日志
     */
    public function batchLog(array $logs): void
    {
        OperationLog::insert($logs);
    }

    /**
     * 获取操作日志列表
     */
    public function handleSearch(Builder $query, array $filters = []): Builder
    {
        // 用户筛选
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // 路由筛选
        if (!empty($filters['route'])) {
            $query->where('route', 'like', '%' . $filters['route'] . '%');
        }

        // 请求方式筛选
        if (!empty($filters['method'])) {
            $query->where('method', $filters['method']);
        }

        // 时间范围筛选
        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * 过滤敏感数据
     */
    private function filterSensitiveData(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'secret'];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***';
            }
        }

        return $data;
    }
}
