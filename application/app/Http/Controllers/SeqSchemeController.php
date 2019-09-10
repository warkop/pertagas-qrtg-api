<?php

namespace App\Http\Controllers;

use App\Http\Models\SeqScheme;

class SeqSchemeController extends Controller
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
        $res = new SeqScheme;

        return $res->all();
    }
}
