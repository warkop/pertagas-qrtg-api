<?php

namespace App\Http\Controllers;

use App\Http\Models\SeqScheme;

class SeqSchemeController extends Controller
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
        $res = new SeqScheme;
        
        $this->responseData = $res->all();
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
    
    public function seeTheFlow()
    {
        $res = new SeqScheme;
        $this->responseData = $res->showFlow();
        $this->responseCode = 200;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
