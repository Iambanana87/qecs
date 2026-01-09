<?php

namespace App\Integrations\IAM;
use Illuminate\Http\Request;

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

    public function getProfile(Request $request)
    {
        $authHeader = $request->header('Authorization');

		$result = api_request(
            'GET', 
            '?c=UserController&m=profile',
            [],
			[
				'Authorization' => $authHeader
			]
        );
		return $result;
    }

    public function getAllUsers()
    {
        
    }
}
