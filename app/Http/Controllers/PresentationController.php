<?php

namespace App\Http\Controllers;

use App\ComModules\MP;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;

class PresentationController extends Controller
{



    public function show(Request $request)
    {
        $data = [];
        $page = $request->page;
        if(empty($page)){
            return view('private.presentation.welcome',$data);
        }
        if(!View::exists('private.presentation.'.$page)){
            return view('private.presentation.welcome',$data);
        }

        if($page == 'pbTemplate'){
            $mpKey = 'mCODlOFDWbwlozQKgwWF0vijcSIanUdt';
            $mp = MP::getMp($mpKey);
            $data['mp'] = $mp;
        } elseif ($page == "voteMechanismSecond") {
            $data['cbKey'] = "HM2wdHCTSC6uWg7xONrpaiN4tl9bwWaR";
        }

        return view('private.presentation.'.$page,$data);


    }


}
