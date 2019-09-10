<?php

namespace App\Http\Controllers;

use App\Http\Models\SeqSchemeGroup;

class SeqSchemeGroupController extends Controller
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
        $res = new SeqSchemeGroup;

        return $res->all();
    }
}
