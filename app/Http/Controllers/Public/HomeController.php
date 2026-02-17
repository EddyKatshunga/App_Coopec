<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Membre;

class HomeController extends Controller
{


    public function index()
    {
        $totalMembres = Membre::count();

        return view('public.home', compact('totalMembres'));
    }
}
