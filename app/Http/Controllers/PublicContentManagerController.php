<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\One\One;
use Exception;
use Illuminate\Http\Request;
use View;
use Session;


class PublicContentManagerController extends Controller {
    public function index(Request $request, $contentType) {
        $contentsPerPage = 6;

        try {
            if (View::exists('public.' . ONE::getEntityLayout() . '.cms.lists.' . $contentType)) {
                $page = $request->get("page", 0);

                $componentData = CM::getNewContentListForPublic($contentType, $page, $contentsPerPage);

                $data["contents"] = $componentData->contents;
                $data["contentsCount"] = $componentData->contentsCount;
                $data["contentType"] = $contentType;
                $data["page"] = $page;

                $data["previousPage"] = ($page>0) ? $page-1 : null;

                if ($data["contentsCount"]>($contentsPerPage*$page))
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
        try {
            $data["content"] = CM::getNewContentForPublic($contentType,$contentKey);
            $data["type"] = $contentType;
            return view('public.' . ONE::getEntityLayout() . '.cms.index', $data);
        } catch (Exception $e) {
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
			$contents = CM::getNewContentByCode($contentCode);
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

}
