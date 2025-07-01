<?php

use App\Http\Middleware\OperationLogMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'))
                ->group(function () {
                    $routeFiles = glob(base_path('routes/api/*.php'));
                    foreach ($routeFiles as $routeFile) {
                        require $routeFile;
                    }
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            OperationLogMiddleware::class,
        ]);
        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'operation_log'      => OperationLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 设置异常错误等返回信息为 json 格式
        $exceptions->shouldRenderJsonWhen(function () {
            return TRUE;
        });
        $exceptions->render(function (Throwable $e) {
            if ($e instanceof UnauthorizedException) {
                $code = !empty($e->getStatusCode()) ? $e->getStatusCode() : 410;

                return response()->json(['code' => $code, 'message' => $e->getMessage()]);
            }
            if ($e instanceof NotFoundHttpException) {
                return response()->json(['code' => 404, 'message' => '抱歉，未找到数据！']);
            }
            if ($e instanceof ValidationException) {
                return response()->json(['code' => 402, 'message' => $e->errors()]);
            }

            return FALSE;
        });
    })->create();
