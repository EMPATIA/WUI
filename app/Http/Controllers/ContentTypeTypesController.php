<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\Files;
use App\ComModules\Orchestrator;
use App\One\One;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Exception;
use Session;
use Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use View;

class ContentTypeTypesController extends Controller
{
    public function __construct(){

    }

    /**
     * @return View
     */
    public function index()
    {
        //Page title
        $title = trans('privateContentTypeTypes.list_content_type_types');

        return view('private.contentTypeTypes.index', compact('title'));
    }

    /**
     * @return $this
     */
    public function tableContentTypeTypes()
    {
        try {

            if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('cm', 'content_subtypes')){
                //Get all Content Type types
                $contentTypeTypes = CM::listByEntityContentTypeTypes();

                // in case of json
                $contentTypeTypes = Collection::make($contentTypeTypes);
            }else {
                $contentTypeTypes = Collection::make([]);
            }

            $edit = Session::get('user_permissions') == 'admin' || ONE::verifyUserPermissionsUpdate('cm', 'content_subtypes');
            $delete = Session::get('user_permissions') == 'admin' || ONE::verifyUserPermissionsDelete('cm', 'content_subtypes');

            //  Datatable with Content Type Types list
            return Datatables::of($contentTypeTypes)
                ->editColumn('name', function ($contentTypeType) {
                    $contentTypeType->name =  ($contentTypeType->name == "")? trans("privateContentTypeTypes.no_translations") : $contentTypeType->name;
                    return "<a href='" . action('ContentTypeTypesController@show', $contentTypeType->content_type_type_key) . "'>" . $contentTypeType->name  . "</a>";
                })
                ->editColumn('type', function ($contentTypeType) {
                    return $contentTypeType->content_type->code;
                })
                ->editColumn('color', function ($contentTypeType) {
                    return "<span class=\"badge\" style=\"background-color:".$contentTypeType->color ."\">&nbsp;&nbsp;&nbsp;</span>";
                })
                ->editColumn('text_color', function ($contentTypeType) {
                    return "<span class=\"badge\" style=\"background-color:".$contentTypeType->text_color ."\">&nbsp;&nbsp;&nbsp;</span>";
                })
                ->addColumn('action', function ($contentTypeTypes) use($edit, $delete) {
                    if($edit == true and $delete == true)
                        return ONE::actionButtons($contentTypeTypes->content_type_type_key, ['edit' => 'ContentTypeTypesController@edit', 'delete' => 'ContentTypeTypesController@delete', 'form' => 'contentTypeTypes']);
                    elseif($edit == false and $delete == true)
                        return ONE::actionButtons($contentTypeTypes->content_type_type_key, ['delete' => 'ContentTypeTypesController@delete', 'form' => 'contentTypeTypes']);
                    elseif($edit == true and $delete == false)
                        return ONE::actionButtons($contentTypeTypes->content_type_type_key, ['edit' => 'ContentTypeTypesController@edit', 'form' => 'contentTypeTypes']);
                    else
                        return null;
                })
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["contentTypeTypes.tableContentTypeTypes" => $e->getMessage()]);
        }
    }


    public function show(Request $request, $contentTypeTypeKey)
    {
        try {

            $contentTypeType = CM::getContentTypeTypeByKey($contentTypeTypeKey);

            // Form title (layout)
            $title = trans('privateContentTypeTypes.show_content_type_type');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['id'] = $contentTypeType->id;
            $data['contentTypeType'] = $contentTypeType;
            $data['contentTypeTypeKey'] = $contentTypeType->content_type_type_key;

            return view('private.contentTypeTypes.contentTypeType', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["contentTypeTypes.show" => $e->getMessage()]);
        }
    }


    public function create(Request $request)
    {
        $languages = Orchestrator::getLanguageList();

        try {
            // Form title (layout)
            $title = trans('privateContentTypeTypes.create_content_type_type');

            $contentTypes = CM::getAllContentTypes();

            $contentTypeSelect = [];
            foreach($contentTypes as $contentType){
                $contentTypeSelect[$contentType->id] = $contentType->code;
            }
            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['languages'] = $languages;
            $data['contentTypes'] = $contentTypeSelect;
            $data['uploadKey'] = Files::getUploadKey();

            return view('private.contentTypeTypes.contentTypeType', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["contentTypeTypes.create" => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            $translations = [];
            foreach($languages as $language){
                if(!empty($request->input("name_".$language->code))){
                    $translations[] = [
                        'language_code' =>  $language->code,
                        'name'       =>    $request->input("name_".$language->code)
                    ];
                }
            }
            //Call to Com Module set method
            CM::setContentTypeType($request, $translations);

            // Message to show + redirect To
            Session::flash('message', trans('privateContentTypeTypes.store_ok'));
            return redirect()->action('ContentTypeTypesController@index');

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["contentTypeTypes.store" => $e->getMessage()]);
        }
    }



    public function edit(Request $request, $contentTypeTypeKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();

            //get object
            $contentTypeType = CM::getContentTypeTypeWithTranslations($contentTypeTypeKey);

            //get content Types
            $contentTypes = CM::getAllContentTypes();

            //prepare content types for select
            $contentTypeSelect = [];
            foreach($contentTypes as $contentType){
                $contentTypeSelect[$contentType->id] = $contentType->code;
            }

            if(!empty($contentTypeType->file)) {
                $contentTypeTypeFile = json_decode($contentTypeType->file);
            }

            //set Form title (layout)
            $title = trans('privateContentTypeTypes.edit_content_type_type');

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['id'] = $contentTypeType->id;
            $data['contentTypeType'] = $contentTypeType;
            $data['contentTypeTypeKey'] = $contentTypeType->content_type_type_key;
            $data['languages'] = $languages;
            $data['translations'] = collect($contentTypeType->translations)->keyBy('language_code')->toArray();
            $data['contentTypes'] = $contentTypeSelect;
            $data['uploadKey'] = Files::getUploadKey();
            $data['file'] = $contentTypeTypeFile ?? null;

            return view('private.contentTypeTypes.contentTypeType', $data);

        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["contentTypeTypes.edit" => $e->getMessage()]);
        }
    }


//    public function update(GroupTypeRequest $request, $contentTypeTypeKey)
    public function update(Request $request, $contentTypeTypeKey)
    {

        try {
            $languages = Orchestrator::getLanguageList();

            $translations = [];
            foreach($languages as $language){
                if(!empty($request->input("name_".$language->code))) {
                    $translations[] = [
                        'language_code' => $language->code,
                        'name' => $request->input("name_" . $language->code)
                    ];
                }
            }
            //Call to Com Module update method
            CM::updateContentTypeType($request,$translations, $contentTypeTypeKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateContentTypeTypes.update_ok'));
            return redirect()->action('ContentTypeTypesController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["contentTypeTypes.edit" => $e->getMessage()]);
        }


    }


    /**
     * @param $contentTypeTypeKey
     * @return View
     */
    public function delete($contentTypeTypeKey)
    {

        $data = array();

        $data['action'] = action("ContentTypeTypesController@destroy", $contentTypeTypeKey);
        $data['title'] =  trans('privateContentTypeTypes.delete');
        $data['msg'] = trans('privateContentTypeTypes.are_you_sure you_want_to_delete_this_content_type_type') . "?";
        $data['btn_ok'] = trans('privateContentTypeTypes.delete');
        $data['btn_ko'] = trans('privateContentTypeTypes.cancel');

        return view("_layouts.deleteModal", $data);
    }

    /**
     * @param $contentTypeTypeKey
     * @return $this|string
     */
    public function destroy($contentTypeTypeKey)
    {
        try {
            //Call to Com Module delete method
            CM::deleteContentTypeType($contentTypeTypeKey);

            // Message to show + redirect To
            Session::flash('message', trans('privateContentTypeTypes.delete_ok'));
            return action('ContentTypeTypesController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('privateContentTypeTypes.delete_nok') => $e->getMessage()]);
        }
    }


}
