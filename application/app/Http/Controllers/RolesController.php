<?php

namespace App\Http\Controllers;

use App\Http\Models\Roles;

class RolesController extends Controller
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
        $res = new Roles;

        return $res->all();
    }
}
