<?php

namespace App\Http\Controllers;

use App\Http\Models\Users;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $res = new Users;

        $responseData = $res->all();
        $responseCode = 200;

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }

    public function (Type $var = null)
    {
        # code...
    }
}
