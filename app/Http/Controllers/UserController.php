<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Integrations\IAM\IAM;

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

    public function index()
    {
        
    }
}
