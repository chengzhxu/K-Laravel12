<?php

namespace App\Http\Middleware;

use App\Models\OperationLog;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Symfony\Component\HttpFoundation\Response;

class OperationLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 异步记录操作日志，避免影响响应性能
        $this->logOperation($request, $response);

        return $response;
    }

    /**
     * 记录操作日志
     */
    private function logOperation(Request $request, Response $response): void
    {
        if ($request->method() === 'POST') {
            try {
                // 获取路由信息
                $route = $request->route();
                $routePath = $route ? $request->getPathInfo() : $route->getName();

                // 准备请求数据（过滤敏感信息）
                $requestData = $this->filterSensitiveData($request->all());

                // 创建操作日志记录
                OperationLog::create([
                    'user_id' => Auth::id(),
                    'route' => $routePath ?: $request->getPathInfo(),
                    'method' => $request->getMethod(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'request_data' => $requestData,
                    'response_data' => null, // 暂时不记录响应数据
                ]);
            } catch (Exception $e) {
                // 记录日志失败不应该影响正常业务流程
                Log::error('操作日志记录失败: ' . $e->getMessage());
            }
        }
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
