<?php

namespace App\Http\Controllers;

use App\Http\Models\Stations;

class StationsController extends Controller
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
        $res = new Stations;

        return $res->all();
    }
}
