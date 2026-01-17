<?php

namespace App\DTOs;

class AuthTokenDTO
{
    public function __construct(
        public readonly string $token,
        public readonly int $expired,
    ) {}
}