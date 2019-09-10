<?php

namespace App\Http\Controllers;

use App\Http\Models\Transactions;

class TransactionsController extends Controller
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
        $res = new Transactions;

        return $res->all();
    }
}
