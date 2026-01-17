<?php

namespace App\Integrations\IAM;

class Permission
{
    public function check(string $userId, string $permission): bool
    {
        $result = api_request(
            'POST',
            '?c=PermissionController&m=check',
            [
                'user_id'    => $userId,
                'permission' => $permission
            ]
        );

        return (bool) ($result['body']['allowed'] ?? false);
    }
}
