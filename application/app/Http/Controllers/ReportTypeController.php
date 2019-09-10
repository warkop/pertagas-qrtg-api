<?php

namespace App\Http\Controllers;

use App\Http\Models\ReportType;

class ReportTypeController extends Controller
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
        
        $res = new ReportType;

        $responseData = $res->all();
        $responseCode = 200;

        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }
}
