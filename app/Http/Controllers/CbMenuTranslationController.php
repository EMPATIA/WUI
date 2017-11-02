<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\Orchestrator;
use Illuminate\Http\Request;
use App\One\One;
use Session;
use View;

use Exception;

class CbMenuTranslationController extends Controller
{
    private $menuCodes;
    public function __construct() {
        $this->menuCodes = array(
            "header",
            "details",
            "pads_topic",
            "pads_parameter",
            "pads_vote",
            "pads_moderators",
            "pads_configurations",
            "vote_analysis",
            "notifications",
            "empaville_analytics",
            "export_topics",
            "pads_security_configurations",
            "cb_group_permissions",
            "comments",
            "flags",
            "cbsQuestionnaires",
            "technical_analysis_process",
            "CbTranslation",
            "cbMenuTranslation",
            "operation_schedules",

            /* Topic Detail options */
            "topic_header",
            "topic_details",
            "topic_posts",
            "topic_reviews",
            "topic_technical_analysis",
            "topic_cooperators"
        );
        View::share("menuCodes",$this->menuCodes);
    }

    public function index($type, $cbKey) {
        try {
            $cbMenuTranslations = collect(CB::getCbMenuTranslations($cbKey));
            $cbMenuTranslations = $cbMenuTranslations->sortBy(function($element, $key) {
                $arrayIndex = array_search($key, $this->menuCodes);
                if (!empty($arrayIndex))
                    return $arrayIndex;
                else
                    return -1;
            });

            $languages = Orchestrator::getLanguageList();

            $user = ONE::userRole();

            $entities = [];
            $cbsEntity = [];

            if ($user=="admin")
                $entities = Orchestrator::getEntities();
            else
                $cbsEntity = CB::getEntityCbsWithMenuTranslations($user);

            $sidebar = 'padsType';
            $active = 'getCbMenuTranslation';
            $title = trans('privateCbsMenuTranslations.cbMenuTranslation');

            return view('private.cbsMenuTranslations.cbMenuTranslation', compact('title', 'cbMenuTranslations', 'languages', 'type', 'cbKey', 'sidebar', 'active', 'entities', 'cbsEntity', 'user'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.cbMenuTranslation.index" => $e]);
        }
    }

    public function getNewTranslationForm($type, $cbKey) {
        try {
            $languages = Orchestrator::getLanguageList();
            $user = ONE::userRole();
            return view('private.cbsMenuTranslations.form', compact( 'languages', 'user'));
        } catch (Exception $e) {
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    public function storeOrUpdate(Request $request, $type, $cbKey) {
        try {
            CB::storeOrUpdateCbMenuTranslation($cbKey, $request);
            $this->clearCbMenuCache($cbKey);

            return response()->json(["success"=>true]);
        } catch (Exception $e) {
            $this->clearCbMenuCache($cbKey);
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    public function delete(Request $request, $type, $cbKey) {
        try {
            CB::deleteCbMenuTranslation($cbKey,$request->get("code"));

            $this->clearCbMenuCache($cbKey);
            return response()->json(["success"=>true]);
        } catch (Exception $e) {
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    public function getEntityCbsWithMenuTranslation(Request $request, $type, $cbKey) {
        try {
            $user = ONE::userRole();
            $cbsEntity = CB::getEntityCbsWithMenuTranslations($user, $request->input('entity'));

            if (empty($cbsEntity))
                return response()->json(["noCbs"=>true],200);

            return view('private.cbsMenuTranslations.cbMenuTranslationCopyEntities', compact('cbKey','cbsEntity'));
        } catch (Exception $e) {
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    public function copyMenuTranslationsFromCb(Request $request, $type, $cbKey){
        try {
            CB::copyCbMenuTranslations($request->get("origin"),$cbKey);
            $this->clearCbMenuCache($cbKey);

            return response()->json(["success"=>true]);
        } catch (Exception $e) {
            $this->clearCbMenuCache($cbKey);
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    public function isCodeUsed(Request $request, $type, $cbKey){
        try {
            return CB::isCbMenuTranslationCodeUsed($cbKey,$request->code);
        } catch (Exception $e) {
            return response()->json(["error"=>$e->getMessage()],500);
        }
    }

    public function clearCbMenuCache($cbKey) {
        \Cache::forget('menuCbTranslations_' . $cbKey);
    }
}
