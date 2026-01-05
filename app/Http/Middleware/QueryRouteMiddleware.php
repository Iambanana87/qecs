<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class QueryRouteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $controller = $request->query('c');
        $method = $request->query('m');

        // Nếu có c và m, thì gọi controller tương ứng
        if ($controller && $method) {
            $controllerClass = "App\\Http\\Controllers\\{$controller}Controller";

            if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                try {
                    $parameters = $request->all();
                    // Gọi hàm tương ứng, inject Request nếu cần
                    
                    return app()->call("{$controllerClass}@{$method}", $parameters);
                } catch (\Throwable $e) {
                    return response()->json([
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ], 500);
                }
            }

            return response()->json([
                'error' => 'Controller or method not found',
                'path' =>  $controllerClass,
                'controller' => $controller,
                'method' => $method,
            ], 404);
        }

        // Nếu không có c&m thì qua route bình thường
        return $next($request);
    }
}
