<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Demorequest;
use App\Testrequest;
use Illuminate\Support\Facades\Auth;
use App\Schedule;
use App\User;
use App\Category;
use App\Order;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		
			return view('home',[]);
        
        
    }
}
