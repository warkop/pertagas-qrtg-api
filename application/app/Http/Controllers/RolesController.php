<?php

namespace App\Http\Controllers;

use App\Http\Models\Roles;
use App\Http\Models\Users;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function __construct()
    {
        //
    }

    public function index()
    {
        $res = new Roles;
        $this->responseData = $res->all();
        $this->responseCode = 200;
        
        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function getUsers($user_id='')
    {
        if (!empty($user_id)) {
            $res = Users::find($user_id)->first();
            $this->responseData = $res;
            $this->responseCode = 200;
        } else {
            $res = new Users();
            $this->responseData = $res->all();
            $this->responseCode = 200;
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
