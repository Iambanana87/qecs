<?php

if (!function_exists('base64url_decode_jwt')) {
    function base64url_decode_jwt($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

if (!function_exists('jwt_decode_manual')) {
    function jwt_decode_manual($jwt, $secret)
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            throw new Exception('Invalid token format');
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;

        $header  = json_decode(base64url_decode_jwt($headerB64), true);
        $payload = json_decode(base64url_decode_jwt($payloadB64), true);

        if (!$header || !$payload) {
            throw new Exception('Invalid token encoding');
        }

        // Kiểm tra thuật toán (chỉ hỗ trợ HS256)
        if (($header['alg'] ?? '') !== 'HS256') {
            throw new Exception('Unsupported JWT alg');
        }

        // Tạo signature để verify
        $expectedSignature = rtrim(strtr(
            base64_encode(hash_hmac('sha256', "$headerB64.$payloadB64", $secret, true)),
            '+/', '-_'
        ), '=');

        if (!hash_equals($expectedSignature, $signatureB64)) {
            throw new Exception('Signature verification failed');
        }

        return $payload;
    }
}

if (!function_exists('get_jwt_sub')) {
    function get_jwt_sub()
    {
        $request = request();
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            abort(response()->json(['error' => 'Authorization token not found'], 401));
        }

        $token = $matches[1];
        $secret = config('app.key_jwt'); // tự tạo key trong .env

        try {
            $decoded = jwt_decode_manual($token, $secret);

            $uuid = $decoded['sub'] ?? null;

            if (!$uuid) {
                abort(response()->json(['error' => 'UUID not found in token'], 400));
            }

            return $uuid;

        } catch (\Exception $e) {
            abort(response()->json([
                'error'   => 'Invalid token',
                'message' => $e->getMessage()
            ], 401));
        }
    }
}
