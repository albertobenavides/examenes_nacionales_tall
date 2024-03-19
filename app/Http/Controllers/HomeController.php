<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use Illuminate\Http\Request;
use Auth;

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
        //echo Auth::user()->rol_id ;
        if (Auth::user()->rol_id == 3){
            return redirect('/admin');
        }
        if (Auth::user()->rol_id == 1){
            return redirect('admin');
        }else{
            return view('cursos.index');    
        }
        
    }
}
