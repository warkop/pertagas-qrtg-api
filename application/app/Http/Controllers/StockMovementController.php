<?php

namespace App\Http\Controllers;

use App\Http\Models\StockMovement;

class StockMovementController extends Controller
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
        $res = new StockMovement;

        return $res->all();
    }
}
