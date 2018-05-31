<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\One\One;
use Exception;
use App\ComModules\LogsRequest;
use Illuminate\Http\Request;
use View;
use Session;


class PublicContentManagerController extends Controller {
    public function index(Request $request, $contentType) {
        $contentsPerPage = 100;

        try {
            if (View::exists('public.' . ONE::getEntityLayout() . '.cms.lists.' . $contentType)) {
                $page = $request->get("page", 0);

                $componentData = CM::getNewContentListForPublic($contentType, $page, $contentsPerPage);
                $data["contents"] = $componentData->contents;
                $data["contentsCount"] = $componentData->contentsCount;
                $data["contentType"] = $contentType;
                $data["page"] = $page;

                $data["previousPage"] = ($page>0) ? $page-1 : null;

                if ($data["contentsCount"]>($contentsPerPage*($page+1)))
                    $data["nextPage"] = $page+1;
                else
                    $data["nextPage"] = null;


                if ($request->ajax())
                    return view('public.' . ONE::getEntityLayout() . '.cms.lists.' . $contentType . "_list", $data);
                else
                    return view('public.' . ONE::getEntityLayout() . '.cms.lists.' . $contentType, $data);
            } else
                return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["public.contentManager.show"]);
        }
    }

    public function show(Request $request, $contentType, $contentKey) {

        return redirect()->action(
            'PublicContentManagerController@showC', ['contentKey' => $contentKey]
        );

    }

    public function showC(Request $request, $contentKey) {
        $content = [];
        try {
            $data = [];

            $time_1 = microtime(true);

            $content = CM::getNewContentForPublic($contentKey);

            $time_2 = microtime(true);

            $data["content"] = $content;
            LogsRequest::setAccess('content_show',true, null,$contentKey,null,null,null,null,null, 'content type: '.$content->content_type->code, Session::has('user') ? Session::get('user')->user_key : null );

            $time_3 = microtime(true);

            $tmp = view('public.' . ONE::getEntityLayout() . '.cms.index', $data);
            $time_4 = microtime(true);
           //dd("CM: ".($time_2-$time_1)." Log: ".($time_3-$time_2)." View: ".($time_4-$time_3));
            return $tmp;

        } catch (Exception $e){
            dd($e->getMessage());
            $jsonObj = json_encode(array('error' => "Failure: ".$e->getMessage() ,'ContentType' => json_encode($content)));
            LogsRequest::setAccess('content_show',false, null,$contentKey,null,null,null,null, $jsonObj, null, Session::has('user') ? Session::get('user')->user_key : null);
            return redirect()->back()->withErrors(["public.contentManager.show"]);
        }
    }

    static public function showCode($contentCode) {
        try {
            $data["content"] = CM::getNewContentByCode($contentCode);
            if(empty($data['content']->sections) && ONE::isAdmin()) {
                return view('public.' . ONE::getEntityLayout() . '.cms.sections.emptySection', $data);
            }
            return view('public.' . ONE::getEntityLayout() . '.cms.embedded', $data);
        } catch (Exception $e) {
            return false;
        }
    }


    static function showContent($contentType, $contentKey) {
        try {
            $contents = CM::getNewContentForPublic($contentKey);
            // $data["type"] = $contentType;
            return $contents;
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["public.contentManager.show"]);
        }
    }

    static public function getLastOf(Request $request) {
        try {
            $contentType = $request->type;
            $count = $request->count;
            $data["content"] = CM::getLastOfType($contentType,$count);
            $data["contentType"] = $contentType;
            if($data["content"]){
                return view('public.' . ONE::getEntityLayout() . '.cms.sections.'.$contentType, $data);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    static public function getSection($contentCode, $code) {
        try {
            $sections = CM::getNewContentByCode($contentCode);
            $section = collect($sections->sections)->where('code','=',$code)->first();
            return $section;
        } catch (Exception $e) {
            return false;
        }
    }

    static public function getSections($contentCode) {
        try {
            // dd(Session::all());
            // if(!empty(Session::get("content_" . $contentCode,""))) {

            //     $contents = json_decode(Session::get("content_" . $contentCode));
            // } else {
                $contents = CM::getNewContentByCode($contentCode);

                Session::put("content_" . $contentCode, json_encode($contents));
            // }

            if (is_array($contentCode)) {
                $sections = array();
                foreach ($contents as $contentKey => $content) {
                    $sections[$contentKey] = $content->sections;
                }
            } else
                $sections = $contents->sections;

            return $sections;
        } catch (Exception $e) {
            return false;
        }
    }

    static public function filterSection($sections,$code) {
        try {
            // dd($sections);
            $section = collect($sections)->where('code','=',$code)->first();
            return $section;
        } catch (Exception $e) {
            return false;
        }
    }

    static public function printSection($section){

        try {
            if(!empty($section)){
                return view("public." . ONE::getEntityLayout() . ".cms.sections." . $section->section_type->code)->render();
            }
        } catch (Exception $e) {
            return false;
        }
    }

    static public function getSectionFromContent($contentSections, $sectionCode) {
        try {
            return collect($contentSections)->where('code','=',$sectionCode)->first() ?? false;
        } catch (Exception $e) {
            return false;
        }
    }
}
