<?php

namespace App\Http\Controllers;

use App\ComModules\CB;
use App\ComModules\CM;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use Carbon\Carbon;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use Session;
use View;
use ONE;
use HttpClient;
use Illuminate\Support\Collection;


/*
 * The Content types are defined in the routes file. Search for $newContentTypes in the routes file
 */

class ContentManagerController extends Controller
{
    private $siteKey;
    public function __construct() {
        View::share("sectionIcons", array(
            "default"                   => "fa fa-question-circle",

            "contentSection"            => "fa fa-font",
            "multipleFilesSection"      => "fa fa-files-o",
            "multipleImagesSection"     => "fa fa-picture-o",
            "singleFileSection"         => "fa fa-file-o",
            "singleImageSection"        => "fa fa-picture-o",
            "slideShowSection"          => "fa fa-picture-o",
            "headingSection"            => "fa fa-header",
            "externalVideoSection"      => "fa fa-youtube-play",
            "internalVideoSection"      => "fa fa-file-video-o",
            "bannerSection"             => "fa fa-minus",
            "linkedBanner"              => "fa fa-minus",
            "buttonSection"             => "fa fa-square-o",
            "supportStatistics"         => "fa fa-check",
            "voteStatistics"            => "fa fa-check-square-o"
        ));
        $this->siteKey = \Request::get("siteKey",null);
        View::share("siteKey",$this->siteKey);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $contentType
     * @return Response
     */
    public function index(Request $request, $contentType, $topicKey = null) {
        View::share('title', trans('privateContentManager.title_' . $contentType));

        $secondTitle = trans("privateContentManager." . $contentType);
        return view('private.contentManager.index',compact("contentType","secondTitle","topicKey"));
    }

    /**
     * Create a resource.
     *
     * @return Response
     */
    public function create($contentType, $topicKey = null) {
        try {
            $data = [];
            $data['contentType'] = $contentType;
            $data['sites'] = Orchestrator::getSiteList();
            $data['sectionTypes'] = CM::getSectionTypes();
            $data['title'] = trans('privateContentManager.title_' . $contentType);
            $data['secondTitle'] = trans("privateContentManager." . $contentType);
            $data['languages'] = Orchestrator::getLanguageList();
            $data["uploadKey"] = Files::getUploadKey();
            $data["topicKey"] = $topicKey;

            return response()->view('private.contentManager.contentManager',$data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.edit" => $e->getMessage()]);
        }
    }

    /**
     * Store the specified resource.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $contentType)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $dataToSend = array(
                "content_type_code"     => $contentType,
                "name"                  => $request->input("name",null),
                "code"                  => $request->input("code",null),
                "active"                => $request->input("active",0),
                "start_date"            => $request->input("startdate",null),
                "publish_date"          => $request->input("publishdate",null),
                "end_date"              => $request->input("endate",null),
                "highlight"             => $request->input("highlight",0),
                "sections"              => array(),
                "site_keys"             => $request->input("sites",[]),
            );

	    $topicKey = $request->input('topicKey',null); 

            if (!is_null($dataToSend["start_date"]))
                $dataToSend["start_date"] = Carbon::parse($dataToSend["start_date"])->format("Y-m-d H:i:s");
            if (!is_null($dataToSend["publish_date"]))
                $dataToSend["publish_date"] = Carbon::parse($dataToSend["publish_date"])->format("Y-m-d H:i:s");
            if (!is_null($dataToSend["end_date"]))
                $dataToSend["end_date"] = Carbon::parse($dataToSend["end_date"])->format("Y-m-d H:i:s");

            foreach (explode("&", $request->input("sortOrder",[])) as $section) {
                if (!empty($section)) {
                    $temp = explode("=", $section);
                    //0 => index, 1 => section key
                    if (count($temp)==2) {
                        $newSection = array(
                            "section_type_key"      => $temp[1],
                            "code"                  => $request->input($temp[1] . "_" . $temp[0] . "_code" ,null),
                            "section_parameters"    => array()
                        );

                        try {
                            $sectionType = CM::getSectionType($temp[1]);
                            foreach ($sectionType->section_type_parameters as $parameter) {
                                $newParameter = array(
                                    "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                                    "code"                          => "",
                                );

                                if ($request->get("translationStatus_" . ($temp[0]),0)==1) {
                                    foreach ($languages as $language) {
                                        $newParameter["translations"][] = array(
                                            "language_code" => $language->code,
                                            "value"         => $request->input($parameter->section_type_parameter_key . "_" . $language->code . "_" . ($temp[0]),null)
                                        );
                                    }
                                } else
                                    $newParameter["value"] = $request->input($parameter->section_type_parameter_key . "_" . ($temp[0]),null);

                                $newSection["section_parameters"][] = $newParameter;
                            }
                        } catch(Exception $e){
                            continue;
                        }

                        $dataToSend["sections"][] = $newSection;
                    }
                }
            }
            $response = CM::createNewContent($dataToSend);

	    if(!is_null($topicKey) && !empty($topicKey))
		    CB::addTopicNews($topicKey,$response->content_key);

            return redirect()->action("ContentManagerController@show",["contentType"=>$contentType,"contentKey"=>$response->content_key,"versionNumber" => null, "topicKey" => $topicKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.store" => $e->getMessage()]);
        }
    }

    /**
     * Edit a existent resource.
     *
     * @param Request $request
     * @param $contentType
     * @param $contentKey
     * @param null $versionNumber
     * @return Response
     * @internal param $id
     */
    public function edit(Request $request, $contentType, $contentKey, $versionNumber = null)
    {
        try {
	    $topicKey = $request->input('topicKey',null); 
            
	    $data = array(
                "contentType"   => $contentType,
                "content"       => CM::getNewContent($contentKey,$versionNumber),
                "sites"         => Orchestrator::getSiteList(),
                "secondTitle"   => trans("privateContentManager." . $contentType),
                "sectionTypes"  => CM::getSectionTypes(),
                "languages"     => Orchestrator::getLanguageList(),
                "uploadKey"     => Files::getUploadKey(),
		"topicKey"	=> $topicKey
            );

            foreach ($data["content"]->sections as &$section) {
                $section->section_type_data = CM::getSectionType($section->section_type->section_type_key);
                $section_parameters_temp = collect($section->section_parameters)->keyBy("section_type_parameter.section_type_parameter_key");
                foreach ($section->section_type_data->section_type_parameters as &$section_type_parameter) {
                    if ($section_parameters_temp->has($section_type_parameter->section_type_parameter_key))
                        $section_type_parameter->section_param = $section_parameters_temp->get($section_type_parameter->section_type_parameter_key);
                    else
                        $section_type_parameter->section_param = null;
                }
            }
            return view('private.contentManager.contentManager',$data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.edit" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show(Request $request, $contentType, $contentKey, $versionNumber = null) {
        try {
	    $topicKey = $request->input('topicKey',null); 

            $data = array(
                "contentType"   => $contentType,
                "content"       => CM::getNewContent($contentKey,$versionNumber),
                "sites"         => Orchestrator::getSiteList(),
                "selectedSites" => [],
                "secondTitle"   => trans("privateContentManager." . $contentType),
                "sectionTypes"  => CM::getSectionTypes(),
                "languages"     => Orchestrator::getLanguageList(),
		"topicKey"	=> $topicKey
            );

            $data["selectedSites"] = isset($data["content"]->content_sites) ? collect($data["content"]->content_sites)->pluck("site_key") : collect([]);


            if (isset($data["content"]->sections)) {
                foreach ($data["content"]->sections as &$section) {
                    $section->section_type_data = CM::getSectionType($section->section_type->section_type_key);
                    $section_parameters_temp = collect($section->section_parameters)->keyBy("section_type_parameter.section_type_parameter_key");

                    foreach ($section->section_type_data->section_type_parameters as &$section_type_parameter) {
                        $section_type_parameter->section_param = $section_parameters_temp->get($section_type_parameter->section_type_parameter_key);
                    }
                }
            }
            return view('private.contentManager.contentManager',$data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource.
     *
     * @param Request $request
     * @param $contentType
     * @param $contentKey
     * @return Response
     * @internal param TopicRequest $requestForum
     * @internal param int $id
     */
    public function update(Request $request, $contentType, $contentKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $dataToSend = array(
                "content_type_code"     => $contentType,
                "name"                  => $request->input("name",null),
                "code"                  => $request->input("code",null),
                "active"                => $request->input("active",0),
                "start_date"            => $request->input("startdate",null),
                "publish_date"          => $request->input("publishdate",null),
                "end_date"              => $request->input("endate",null),
                "highlight"             => $request->input("highlight",0),
                "sections"              => array(),
                "site_keys"             => $request->input("sites",[]),
            );
	    
	    $topicKey = $request->input('topicKey',null); 

            if (!is_null($dataToSend["start_date"]))
                $dataToSend["start_date"] = Carbon::parse($dataToSend["start_date"])->format("Y-m-d H:i:s");
            if (!is_null($dataToSend["publish_date"]))
                $dataToSend["publish_date"] = Carbon::parse($dataToSend["publish_date"])->format("Y-m-d H:i:s");
            if (!is_null($dataToSend["end_date"]))
                $dataToSend["end_date"] = Carbon::parse($dataToSend["end_date"])->format("Y-m-d H:i:s");

            foreach (explode("&", $request->input("sortOrder",[])) as $section) {
                if (!empty($section)) {
                    $temp = explode("=", $section);
                    //0 => section ID, 1 => section key
                    if (count($temp)==2) {
                        $newSection = array(
                            "section_type_key"      => $temp[1],
                            "code"                  => $request->input($temp[1] . "_" . $temp[0] . "_code" ,null),
                            "section_parameters"    => array()
                        );

                        try {
                            $sectionType = CM::getSectionType($temp[1]);
                            foreach ($sectionType->section_type_parameters as $parameter) {
                                $newParameter = array(
                                    "section_type_parameter_key"    => $parameter->section_type_parameter_key,
                                    "code"                          => "",
                                );

                                if ($request->get("translationStatus_" . ($temp[0]),0)==1) {
                                    foreach ($languages as $language) {
                                        $newParameter["translations"][] = array(
                                            "language_code" => $language->code,
                                            "value"         => $request->input($parameter->section_type_parameter_key . "_" . $language->code . "_" . ($temp[0]),null)
                                        );
                                    }
                                } else
                                    $newParameter["value"] = $request->input($parameter->section_type_parameter_key . "_" . ($temp[0]),null);

                                $newSection["section_parameters"][] = $newParameter;
                            }
                        } catch(Exception $e){
                            continue;
                        }

                        $dataToSend["sections"][] = $newSection;
                    }
                }
            }
            $response = CM::updateNewContent($contentKey,$dataToSend);
            return redirect()->action("ContentManagerController@show",["contentType"=>$contentType,"contentKey"=>$contentKey,"version"=>$response->version, "topicKey" => $topicKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.store" => $e->getMessage()]);
        }
    }

    public function previewVersion(Request $request, $contentType, $contentKey, $versionNumber, $topicKey = null) {
        try {
            $data["content"] = CM::getNewContentForPreview($contentKey,$versionNumber);
            $data["type"] = $contentType;
            $data["topicKey"] = $topicKey;
            return view('public.' . ONE::getEntityLayout() . '.cms.index', $data);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.preview"]);
        }
    }

    public function delete($contentType, $contentKey, $topicKey = null)
    {
        $data = array();

        $data['action'] = action("ContentManagerController@destroy", ["contentType"=> $contentType, "contentKey" => $contentKey,"topicKey" => $topicKey]);
        $data['title'] = trans('privateContentManager.delete');
        $data['msg'] = trans('privateContentManager.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateContentManager.delete');
        $data['btn_ko'] = trans('privateContentManager.cancel');

        return view("_layouts.deleteModal", $data);
    }
    /**
     * Destroy the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy(Request $r, $contentType, $contentKey)
    {
        try {
	    $topicKey = $r->input('topicKey',null);

	    if(!is_null($topicKey)) CB::deleteTopicNews($topicKey,$contentKey);
            
	    CM::deleteNewContent($contentKey);
            return redirect()->action('ContentManagerController@index',["contentType"=>$contentType,"topicKey" => $topicKey])->getTargetUrl();
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.destroy" => $e->getMessage()])->getTargetUrl();
        }
    }


    /**
     * @param $contentType
     * @return mixed
     */
    public function getIndexTable($contentType, $topicKey = null) {
        if(Session::get('user_role') || (ONE::verifyUserPermissionsShow('orchestrator', 'entity_site', 'show') and ONE::verifyUserPermissionsShow('cm', $contentType, 'show'))){
        // Request for Data List
        $contentList = CM::getNewContents($contentType,$this->siteKey);
        // JSON data collection
        $collection = Collection::make($contentList);
	if (!is_null($topicKey)){
		$keys = array();	 
		$topicNews = CB::getTopicNews($topicKey);
		if(!empty($topicNews->data)){
			foreach($topicNews->data as $t){
				$keys[] = $t->news_key;
			}
			
			$collection = $collection->whereIn('content_key',$keys);
		}else{
            		$collection = Collection::make([]);
		}
		
	}
        }else
            $collection = Collection::make([]);

        $delete = Session::get('user_role') || (ONE::verifyUserPermissionsDelete('orchestrator', 'entity_site') and ONE::verifyUserPermissionsDelete('cm', $contentType));

        // Render Datatable
        return Datatables::of($collection)
            ->editColumn('name', function ($content) use ($contentType, $topicKey) {
                $name = (!empty($content->name) ? $content->name : trans("privateContentManager.unnamed_content"));
                return "<a href='".action('ContentManagerController@show', ["contentType" => $contentType, "content_key" => $content->content_key,"version"=>"","siteKey"=>$this->siteKey,"topicKey" => $topicKey])."'>". $name ."</a>";
            })
            ->editColumn('code', function ($content) use ($contentType, $topicKey) {
                $code = (isset($content->code) && !empty($content->code)) ? $content->code : trans("privateContentManager.uncoded_content");
                return "<a href='".action('ContentManagerController@show', ["contentType" => $contentType, "content_key" => $content->content_key,"version"=>"","siteKey"=>$this->siteKey,"topicKey" => $topicKey])."'>".  $code ."</a>";
            })
            ->addColumn('action', function ($content) use ($contentType, $delete, $topicKey) {
                if($delete)
                    return ONE::actionButtons(["contentType" => $contentType, "content_key" => $content->content_key,"version"=>"","siteKey"=>$this->siteKey,"topicKey" => $topicKey], ['form' => 'ContentManager', 'edit' => 'ContentManagerController@edit','delete' => 'ContentManagerController@delete'] );
                else
                    return ONE::actionButtons(["contentType" => $contentType, "content_key" => $content->content_key,"version"=>"","siteKey"=>$this->siteKey,"topicKey" => $topicKey], ['form' => 'ContentManager', 'edit' => 'ContentManagerController@edit'] );

            })
            ->make(true);
    }

    /**
     * @param Request $request
     * @return string|View
     */
    public function serveSection(Request $request) {
        try{
            $sectionTypeKey = $request->get("sectionTypeKey",null);
            if (!is_null($sectionTypeKey)) {
                $data = array(
                    "sectionType"       => CM::getSectionType($sectionTypeKey),
                    "sectionNumber"     => $request->get("section_id",1),
                    "uploadKey"         => $request->get("upload_key",null),
                    "languages"         => Orchestrator::getLanguageList(),
                    "topicKey"         => $request->get("topic_key",null)
                );
                return view("private.contentManager.sectionTemplate",$data);
            } else
                return 'false';
        }catch (Exception $e) {
            return 'false';
        }
    }

    public function changeVersionActiveStatus(Request $request, $contentType, $contentKey, $versionNumber, $newStatus, $topicKey = null) {
        try {
            CM::toggleNewContentActiveStatus($contentKey, $versionNumber, $newStatus);
            return redirect()->action("ContentManagerController@show", ["contentType" => $contentType, "contentKey" => $contentKey, "version" => $versionNumber, "topicKey" => $topicKey]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["private.contentManager.changeVersionActiveStatus" => $e->getMessage()]);
        }
    }

    public function getTinyMCE()
    {
        return view('private._private.tinymce')->with('action', action('ContentManagerController@getTinyMCEView'));
    }
    public function getTinyMCEView($type = null)
    {
        $types[] = trans('contents.files');

        $uploadKey = Files::getUploadKey();
        $contentTypes = CM::getAllContentTypes();

        foreach($contentTypes->data as $contentType) {
            $types[$contentType->id] = $contentType->translations[0]->title;
        }

        return view('private._private.tinymce-content', compact('uploadToken', 'types'));
    }
}
