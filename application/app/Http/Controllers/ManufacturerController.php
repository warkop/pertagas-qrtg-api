<?php

namespace App\Http\Controllers;

use App\Http\Models\Manufacturer;

class ManufacturerController extends Controller
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
        $res = new Manufacturer;

        return $res->all();
    }
}
