<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthTokenResource;
use App\Integrations\IAM\IAM;
use App\DTOs\AuthTokenDTO;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $loginRequest)
    {
        $result = IAM::auth()->login(
            $loginRequest->username,
            $loginRequest->password,
            $loginRequest->otp
        );

        if (isset($result['body']['error']))
        {
            return response()->json([
                'status' => false,
                'message' => $result['body']['error'],
            ], $result['status']);
        }

        $authTokenDTO = new AuthTokenDTO(
            token: $result['body']['accessToken'],
            expired: $result['body']['expires_in']
        );

        return response()->json([
            'status' => true,
            'data' => new AuthTokenResource($authTokenDTO),
        ], $result['status']);

    }
}
