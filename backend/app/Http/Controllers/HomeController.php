<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /** Show the welcome view with alloewd origins value */
    public function welcome()
    {
        $allowedOrigins = Config::get('cors.allowed_origins');

        return view('welcome')->with('allowedOrigins', $allowedOrigins);
    }

    // /**
    //  * Show the login view
    //  */
    // public function login()
    // {
    //     // return view('home');
    // }
}
