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
        $res = new ReportType;

        return $res->all();
    }
}
