<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Integrations\IAM\IAM;
use App\Http\Requests\GetUsersRequest;
use App\Http\Resources\UsersResource;

class UserController extends Controller
{
    public function getProfile(Request $request)
    {
        $result = IAM::User()->getProfile($request);

        $response = [
            'status' => $result['status'],
        ];

        if ($result['status'] == 200) {
            $response['data'] = $result['body'];
        } else {
            $response['message'] = $result['body'];
        }

        return response()->json($response, $result['status']);
    }

    public function index(GetUsersRequest $request)
    {
        $result = IAM::user()->getAllUsers([
            'limit'  => $request->limit,
            'page'   => $request->page,
            'search' => $request->search,
            'role'   => $request->role,
        ]);

        // error từ IAM
        if (
            isset($result['status']) &&
            $result['status'] !== 200
        ) {
            return response()->json([
                'status'  => 'error',
                'message' => $result['body']['error'] ?? 'Lấy dữ liệu thất bại',
            ], $result['status']);
        }

        return response()->json([
            'status'             => 'success',
            'message'            => 'Lấy dữ liệu thành công',
            'data'               => UsersResource::collection($result['body']['data']),
            'total_in_all_page'  => $result['body']['total_in_all_page'],
            'total_pages'        => $result['body']['total_pages'],
        ], 200);
    }

}
