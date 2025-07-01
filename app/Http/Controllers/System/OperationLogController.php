<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\OperationLogRequest;
use App\Models\OperationLog;
use App\Services\System\OperationLogService;
use Illuminate\Http\JsonResponse;

class OperationLogController extends Controller
{
    /**
     * 获取操作日志列表
     */
    public function index(OperationLogRequest $request): JsonResponse
    {
        $limit = min($request->input('limit', 15), 50);
        $page = $request->input('page', 1);
        $logs = OperationLog::with('user');
        $filters = $request->validated();
        $logs = OperationLogService::getInstance()->handleSearch($logs, $filters);
        $logs = $logs->paginate($limit, ['*'], 'page', $page);
        return $this->successPaginated($logs);
    }

    /**
     * 获取单个操作日志详情
     */
    public function show($id): JsonResponse
    {
        $operationLog = OperationLog::with('user')->findOrFail($id);
        return $this->success($operationLog);
    }

    /**
     * 删除操作日志
     */
    public function destroy($id): JsonResponse
    {
        $operationLog = OperationLog::findOrFail($id);
        $operationLog->delete();
        return $this->success(null, '删除成功');
    }

    /**
     * 批量删除操作日志
     */
    public function batchDestroy(OperationLogRequest $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return $this->fail(400, '请选择要删除的记录');
        }
        OperationLog::whereIn('id', $ids)->delete();
        return $this->success(null, '批量删除成功');
    }

    /**
     * 获取操作统计信息
     */
    public function statistics(): JsonResponse
    {
        $today = now()->startOfDay();
        $week = now()->subWeek();
        $month = now()->subMonth();
        $statistics = [
            'today_count' => OperationLog::where('created_at', '>=', $today)->count(),
            'week_count' => OperationLog::where('created_at', '>=', $week)->count(),
            'month_count' => OperationLog::where('created_at', '>=', $month)->count(),
            'total_count' => OperationLog::count(),
            'method_stats' => OperationLog::selectRaw('method, count(*) as count')
                ->groupBy('method')
                ->pluck('count', 'method'),
            'recent_users' => OperationLog::with('user')
                ->where('created_at', '>=', $today)
                ->distinct('user_id')
                ->limit(10)
                ->get()
                ->pluck('user.name')
                ->filter()
        ];
        return $this->success($statistics);
    }
}
