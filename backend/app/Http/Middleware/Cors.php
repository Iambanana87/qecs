<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Xử lý CORS cho development.
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedOrigin = 'http://localhost:5173'; // thay nếu FE chạy ở port khác
        $origin = $request->header('Origin');

        // Danh sách các domain/IP được phép (cho vào mảng để dễ quản lý)
        // Trong môi trường dev nội bộ, bạn có thể cho phép tất cả bằng cách kiểm tra nếu $origin tồn tại
        // LƯU Ý: Khi deploy thật thì nên giới hạn cụ thể.
        $allowedOrigin = $origin ?: '*';
        

        // Nếu là preflight request (OPTIONS) trả luôn
        if ($request->getMethod() === 'OPTIONS') {
            $headers = [
                'Access-Control-Allow-Origin'      => $allowedOrigin,
                'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
                'Access-Control-Allow-Headers'     => 'Content-Type, X-Requested-With, Authorization, X-CSRF-TOKEN, Accept',
                'Access-Control-Allow-Credentials' => 'true',
            ];
            return response()->noContent(204)->withHeaders($headers);
        }

        $response = $next($request);

        // Gắn header vào tất cả responses
        $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization, X-CSRF-TOKEN, Accept');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}