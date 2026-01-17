<?php

namespace App\Integrations\IAM;
class User
{
    public function getByIds(array $ids): array
    {
        if (empty($ids)) return [];

        $result = api_request(
            'POST',
            '?c=UserController&m=getByIds',
            ['user_ids' => $ids]
        );

        return collect($result['body']['data'] ?? [])
            ->keyBy('uuid')
            ->toArray();
    }
}
