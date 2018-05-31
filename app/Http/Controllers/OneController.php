<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Http\Exception;
use App\One\One;
use Session;

class OneController extends Controller
{
    public function __construct()
    {

    }


    /**
     * @param Request $request
     * @return bool
     */
    public function setLanguage(Request $request)
    {

        $langCode = $request->langCode;
        if(strlen($langCode) > 0){
            ONE::setAppLanguage($langCode);
            App::setLocale($langCode);
        }

        return "true";
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function setPrivateLanguage(Request $request)
    {

        $langCode = $request->langCode;
        if(strlen($langCode) > 0){
            ONE::setAppLanguage($langCode);
            App::setLocale($langCode);
        }

        return "true";
    }


    public function getContent(Request $request)
    {
        $type = $request->name;
        $parameters = Session::get("sidebarArguments");

        switch($type){
            case 'padsType':
                return view("private.sidebar.padsType", ['type' => $parameters['type'], 'cbKey' => $parameters['cbKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'topics':
                return view("private.sidebar.topics", ['type' => $parameters['type'], 'cbKey' => $parameters['cbKey'], 'topicKey' => $parameters['topicKey'], 'active' => $parameters['activeSecondMenu']]);
                break;
            case 'private':
                return view("private._private.sidebar");
                break;
            case 'mp_configurations':
                return view("private.sidebar.mp_configurations", ['mpKey' => $parameters['mpKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'loginLevelsParameters':
                return view("private.sidebar.loginLevelsParameters", ['siteKey' => $parameters['siteKey'], 'levelParameterKey' => $parameters['levelParameterKey'], 'active' => $parameters['activeSecondMenu']]);
                break;
            case 'site':
                return view("private.sidebar.site", ['siteKey' => $parameters['siteKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'menu':
                return view("private.sidebar.menu", ['accessM' => $parameters['accessM'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'registration':
                return view("private.sidebar.registration", ['active' => $parameters['activeFirstMenu']]);
                break;
            case 'q':
                return view("private.sidebar.q", ['questionnaireKey' => $parameters['questionnaireKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'manager':
                return view("private.sidebar.manager", ['userKey' => $parameters['userKey'], 'role' => $parameters['role'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'entity':
                return view("private.sidebar.entity", ['active' => $parameters['activeFirstMenu']]);
                break;
            case 'manageEntityRegistrationValues':
                return view("private.sidebar.manageEntityRegistrationValues", ['active' => $parameters['activeSecondMenu']]);
                break;
            case 'entityGroupDetails':
                return view("private.sidebar.entityGroupDetails", ['entityGroupKey' => $parameters['entityGroupKey'], 'groupTypeKey' => $parameters['groupTypeKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'functions':
                return view("private.sidebar.functions", ['roleKey' => $parameters['roleKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'cmHomePagesType':
                return view("private.sidebar.cmHomePagesType", ['homePageTypeKey' => $parameters['homePageTypeKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'votes':
                return view("private.sidebar_admin.votes", ['active' => $parameters['activeFirstMenu']]);
                break;
            case 'votesMethods':
                return view("private.sidebar_admin.votesMethods", ['voteMethodId' => $parameters['voteMethodId'], 'active' => $parameters['activeSecondMenu']]);
                break;
            case 'entities':
                return view("private.sidebar_admin.entities", ['entityKey' => $parameters['entityKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'cbs_configs':
                return view("private.sidebar_admin.cbs_configs", ['configTypeId' => $parameters['configTypeId'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'modules':
                return view("private.sidebar_admin.modules", ['moduleKey' => $parameters['moduleKey'], 'active' => $parameters['activeFirstMenu']]);
                break;
            case 'sites':
                return view("private.sidebar_admin.sites", ['entityKey' => $parameters['entityKey'], 'siteKey' => $parameters['siteKey'], 'active' => $parameters['activeSecondMenu']]);
                break;
            case 'siteConfs':
                return view("private.sidebar_admin.siteConfs", ['entityKey' => $parameters['entityKey'], 'siteKey' => $parameters['siteKey'], 'active' => $parameters['activeSecondMenu']]);
                break;
            case 'voteAnalysis':
                return view("private.sidebar.voteAnalysis", ['type' => $parameters['type'], 'cbKey' => $parameters['cbKey'], 'active' => $parameters['activeSecondMenu']]);
                break;
            case 'questionGroup':
                return view("private.sidebar.questionGroup", ['questiongroupKey' => $parameters['questiongroupKey'], 'active' => $parameters['activeSecondMenu']]);
                break;
            case 'question':
                return view("private.sidebar.question", ['questionKey' => $parameters['questionKey'], 'active' => $parameters['activeThirdMenu']]);
                break;
            case 'moderation':
                return view("private.sidebar.moderation");
                break;
            case 'sms':
                return view("private.sidebar.sms", ['active' => $parameters['activeFirstMenu']]);
                break;
            case 'email':
                return view("private.sidebar.email", ['active' => $parameters['activeFirstMenu']]);
                break;
        }

        return null;
    }

    public function getSidebar(Request $request)
    {
        $active = $request->url;
        $view = explode('.', $request->view);
        $variableToView = $request->paramsToSidebar;

        if(sizeof($view) == 1)
            return view('private.sidebar.'.$view[0], compact('variableToView', 'active'));
        else
            return view('private.'.$view[0].'.'.$view[1].'', compact('variableToView', 'active'));
    }

}
