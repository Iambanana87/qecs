<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('api_request')) {
    function api_request($method, $endpoint, $data = [], $headers = [])
    {
        // Load từ config của Laravel
        $baseUrl = rtrim(config('api.api_base_url'), '/');

        // Build URL
        if (preg_match('#^https?://#', $endpoint)) {
            $url = $endpoint;
        } else {
            $url = $baseUrl . '/' . ltrim($endpoint, '/');
        }

        Log::debug("API REQUEST → {$method} {$url}");

        $ch = curl_init();
        $method = strtoupper($method);

        switch ($method) {
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "PUT":
            case "PATCH":
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case "GET":
                if (!empty($data)) {
                    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
                }
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $defaultHeaders = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        $headers = array_merge($defaultHeaders, $headers);

        $formatted = [];
        foreach ($headers as $k => $v) {
            $formatted[] = is_string($k) ? "$k: $v" : $v;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $formatted);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            // curl_close($ch);
            $ch = null;

            Log::error("API REQUEST ERROR: {$error}");

            return [
                'status'  => 0,
                'error'   => $error,
                'body'    => null,
                'raw'     => null,
                'headers' => []
            ];
        }

        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $rawHeader  = substr($response, 0, $headerSize);
        $rawBody    = substr($response, $headerSize);

        // curl_close($ch);
        $ch = null;

        Log::debug("API RESPONSE ({$httpCode}): " . $rawBody);

        return [
            'status'  => $httpCode,
            'error'   => null,
            'body'    => json_decode($rawBody, true),
            'raw'     => $rawBody,
            'headers' => explode("\r\n", trim($rawHeader))
        ];
    }
}
