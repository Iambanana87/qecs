<?php

namespace App\Integrations\IAM;

class Auth
{
    public function login(string $username, string $password, ?string $otp = null): array
    {
        $result = api_request(
            'POST',
            '?c=AuthController&m=login',
            compact('username', 'password', 'otp')
        );

        return $result;
    }
}
