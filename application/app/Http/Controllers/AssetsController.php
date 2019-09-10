<?php

namespace App\Http\Controllers;

use App\Http\Models\Assets;

class AssetsController extends Controller
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
        $res = new Assets;

        return $res->all();
    }
}
