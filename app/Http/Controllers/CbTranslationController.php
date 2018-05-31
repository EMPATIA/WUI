<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use App\One\One;
use Session;
use View;

use Exception;

class CbTranslationController extends Controller
{
    /**
     * Display the specified resource.
     * @param $type
     * @param $cbKey
     * @return View
     */
    public function showCbTranslation($type, $cbKey)
    {
        $cb = CB::getCb($cbKey);
        $CbsEntity = [];

        switch ($type) {
            case $type == "idea":
                $title = trans('privateIdeas.show_securityconfigurations');
                break;
            case $type == "forum":
                $title = trans('privateForums.show_securityconfigurations');
                break;
            case $type == "discussion":
                $title = trans('privateDiscussions.show_securityconfigurations');
                break;
            case $type == "proposal":
                $title = trans('privateProposals.show_securityconfigurations');
                break;
            case $type == "publicConsultation":
                $title = trans('privatePublicConsultations.show_securityconfigurations');
                break;
            case $type == "tematicConsultation":
                $title = trans('privateTematicConsultations.show_securityconfigurations');
                break;
            case $type == "survey":
                $title = trans('privateSurveys.show_securityconfigurations');
                break;
            case $type == "phase1":
                $title = trans('privatePhaseOne.show_securityconfigurations');
                break;
            case $type == "phase2":
                $title = trans('privatePhaseTwo.show_securityconfigurations');
                break;
            case $type == "phase3":
                $title = trans('privatePhaseThree.show_securityconfigurations');
                break;
            case $type == "qa":
                $title = trans('privateQA.show_securityconfigurations');
                break;
        }

        $sidebar = 'padsType';
        $active = 'cbtranslation';
        $title = trans('privateCbsTranslations.cbTranslation');

        $cb_translations = CB::getCbTranslations($cb);
        $languages = Orchestrator::getLanguageList();
        $entities = Orchestrator::getEntities();
        $user = ONE::userRole();

        Session::put('sidebarArguments', ['type' => $type, 'cbKey' => $cbKey, 'activeFirstMenu' => 'cbtranslation']);
        Session::put('sidebars', [0 => 'private', 1=> 'padsType']);

        return view('private.cbsTranslations.cbTranslation', compact('title', 'cb_translations','languages', 'type', 'cbKey','sidebar','active', 'entities', 'CbsEntity','user','cb'));
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewTranslation(Request $request, $type, $cbKey)
    {
        $cb = CB::getCb($cbKey);
        $title = trans('privateCbsTranslations.cbTranslation');
        $cb_translations=CB::getCbTranslations($cb);
        $languages=Orchestrator::getLanguageList();
        $user = ONE::userRole();
        return view('private.cbsTranslations.cbByTranslation', compact('title', 'languages', 'type', 'cb','cbKey','user'));

    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return $this
     */
    public function storeOrUpdate(Request $request, $cbKey)
    {
        try {
            $store = CB::storeOrUpdate($cbKey, $request);

            if($store == 'UpdateOk' || $store == 'StoreOk'){
                Session::flash('message', trans("privateCbsTranslations.".$store));
            }else{
                Session::flash('message', trans("privateCbsTranslations.".$store));
            }

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbTranslation.show" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $type
     * @param $cbKey
     * @return $this
     */
    public function delete(Request $request, $type, $cbKey)
    {
        try {
            $delete = CB::delete($cbKey,$request);
            Session::flash('message', trans("privateCbsTranslations.".$delete));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbTranslation.show" => $e->getMessage()]);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCpyTranslation(Request $request)
    {
        $user = ONE::userRole();
        $entity = $request->input('entity');
        $cbsEntity = CB::getCbEntity($entity, $user);

        if(empty($cbsEntity)){
            return 'false';
        }

        return view('private.cbsTranslations.cbTranslationCopy', compact('cbsEntity'));
    }


    /**
     * @param Request $request
     * @param $cbKey
     * @return $this
     */
    public function viewConfirmTranslation(Request $request, $cbKey)
    {
        try {
            $store = CB::storeCodeAdminOrManager($cbKey,$request);
            Session::flash('message', trans("privateCbsTranslations.".$store));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbTranslation.show" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewCopyAll(Request $request)
    {
        try {
            $translation = $request->translation;

            $translations = [];
            foreach ($translation as $key => $value) {
                $lang = explode('_',$key);
                $translations[$lang[1]] = $value;
            }

            $code = $request->cod;
            $languages = Orchestrator::getLanguageList();

            return view('private.cbsTranslations.cbCopyAllTranslation', compact('translations', 'code', 'languages'));

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbTranslation.show" => $e->getMessage()]);
        }

    }


    /**
     * @param Request $request
     * @param $cbKey
     * @return $this|mixed
     */
    public function getCode(Request $request, $cbKey)
    {
        try {
            $code = CB::getCode($cbKey,$request->code);
            return $code;
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbTranslation.show" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $cbKey
     * @return $this|bool
     */
    public function getStatusTranslations(Request $request, $cbKey)
    {
        try {
            $translations = CB::getStatusTranslations($cbKey);

            return $translations;
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["cbTranslation.show" => $e->getMessage()]);
        }
    }
}
