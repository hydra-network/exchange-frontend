<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the application app.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (isset($_GET['test'])) {
            return view('guest.home');
        } else {
            return view('guest.landing_content');
        }
    }
}
