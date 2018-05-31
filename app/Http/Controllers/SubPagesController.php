<?php

namespace App\Http\Controllers;


use App\One\One;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Session;
use View;

class SubPagesController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Show the selected view.
     *
     * @return View
     */
    public function show($folder, $page)
    {
        return view('public.'.ONE::getEntityLayout().".".$folder.".".$page);
    }


}
