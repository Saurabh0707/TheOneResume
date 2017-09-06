<?php

namespace App\Http\Controllers\developer\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class clientController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:admin');
    }

    /**
     * Show all the client.
     *
     * @return \Illuminate\Http\Response
     */
    public function createClient()
    {
        return view('developer.admin.clients');
    }
}
