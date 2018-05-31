<?php

namespace App\Http\Controllers;

use App\ComModules\Orchestrator;
use App\One\One;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;


class NewsletterController extends Controller
{
    /**
     * Register the email in the Newsletter.
     *
     * @param $typeId
     * @return response
     */
    public function register(Request $request)
    {
        if(isset($request->email)){
            Orchestrator::setNewsletterSubscription($request->email, 1);
        }else{
            return response()->json(['error' => 'Unauthorized'], 400);
        }
    }
}
