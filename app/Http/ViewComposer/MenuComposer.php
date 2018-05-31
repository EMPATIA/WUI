<?php

namespace App\Http\ViewComposer;

use App\ComModules\CM;
use App\ComModules\Orchestrator;
use Illuminate\Contracts\View\View;
use App\One\One;
use Session;

class MenuComposer
{
    /**
     * The user repository implementation.
     */
    protected $menus;

    /**
     * Create a new profile composer.
     * @return void
     */
    public function __construct()
    {

        /*Menu*/
        $menusArray = array();
        $response = Orchestrator::getAccessMenuInfo();
        $id = $response->id;
        if(!empty($id)){
            $accessMenu = CM::listByAccessId($id);
            $authToken = Session::get('X-AUTH-TOKEN', 'INVALID');
            $menusArray = $accessMenu['data'];
            if ($authToken == 'INVALID'){
                $menusArray = ONE::verifyArray($accessMenu['data']);
            }
        }
        $this->menus = $menusArray;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose()
    {
        view()->share('menus', $this->menus);
    }
}