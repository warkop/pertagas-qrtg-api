<?php

namespace App\Http\Controllers;

use App\Http\Models\Results;

class ResultsController extends Controller
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
        $res = new Results;

        return $res->all();
    }
}
