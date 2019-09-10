<?php

namespace App\Http\Controllers;

use App\Http\Models\AssetType;

class AssetTypeController extends Controller
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
        $res = new AssetType;

        return $res->all();
    }
}
