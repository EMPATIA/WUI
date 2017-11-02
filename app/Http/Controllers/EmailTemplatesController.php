<?php

namespace App\Http\Controllers;

use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\Http\Requests\EmailTemplateRequest;
use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use Input;
use Session;

/**
 * Class EmailTemplatesController
 * @package App\Http\Controllers
 */
class EmailTemplatesController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @param $siteKey
     * @return \Illuminate\Http\Response
     */
    public function create($siteKey)
    {
        try {
            $types = Notify::getEmailTemplateTypes();

            $languages = Orchestrator::getLanguageList();

            $availableTypes = Notify::getTypesAvailable();

            foreach ($types as $type) {
                foreach ($availableTypes as $available) {
                    if ($type->type_key == $available->type_key) {
                        $typesName[$type->type_key] = $type->name;
                    }
                }

            }

            $title = trans('privateEmailTemplates.create_template');

            return view('private.entities.templates.template', compact('title', 'typesName', 'languages', 'siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("EmailTemplates.create") => $e->getMessage()]);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EmailTemplateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmailTemplateRequest $request)
    {
        try{

            $params = $request->all();

            $siteKey = $params['siteKey'];

            $languages = Orchestrator::getLanguageList();
            $translations = [];
            foreach($languages as $language){
                if(!empty($params['content_'.$language->code])){
                    $translations[] = [
                        'language_code' => $language->code,
                        'subject' => $params['subject_'.$language->code],
                        'header' => "",
                        'content' => $params['content_'.$language->code],
                        'footer' => ""];
                }
            }

            $template = Notify::postEmailTemplate($params['types'], $siteKey, $translations);

            Session::flash('message', trans('EmailTemplates.storeOk'));

            return redirect()->action('EmailTemplatesController@show', $template->email_template_key);
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("EmailTemplates.store") => $e->getMessage()]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param $templateKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($templateKey)
    {
        try {
            $emailTemplate = Notify::getEmailTemplateTranslations($templateKey);

            $siteKey = $emailTemplate->site_key;

            $languages = Orchestrator::getLanguageList();

            $title = trans('privateEmailTemplates.show_emailTemplate');

            $sidebar = 'site';
            $active = 'emailTemplates';

            Session::put('sidebarArguments', ['siteKey' => $siteKey, 'activeFirstMenu' => 'emailTemplates']);

            return view('private.entities.templates.template', compact('title', 'emailTemplate', 'languages', 'siteKey', 'templateKey', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("EmailTemplates.show") => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $templateKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function edit($templateKey)
    {
        try {
            $typesName = [];
            $languages = Orchestrator::getLanguageList();

            $emailTemplate = Notify::getEmailTemplateTranslations($templateKey);
            $availableTypes = Notify::getTypesAvailable();

            if (isset($emailTemplate->type)) {
                $typesName[$emailTemplate->type->type_key] = $emailTemplate->type->name;
            }
            foreach ($availableTypes as $available) {
                $typesName[$available->type_key] = $available->name;
            }
            $title = trans('privateEmailTemplates.edit_emailTemplate');

            $sidebar = 'site';
            $active = 'emailTemplates';
            $siteKey = Session::get('SITE_KEY');

            Session::put('sidebarArguments', ['siteKey' => Session::get('SITE_KEY'), 'activeFirstMenu' => 'emailTemplates']);

            return view('private.entities.templates.template', compact('title', 'typesName', 'languages', 'emailTemplate', 'translations', 'templateKey', 'sidebar', 'active', 'siteKey'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("EmailTemplates.edit") => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param EmailTemplateRequest|Request $request
     * @param $templateKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(EmailTemplateRequest $request, $templateKey)
    {
        try{
            $inputs = Input::all();

            $languages = Orchestrator::getLanguageList();
            $translations = [];

            foreach($languages as $language){
                if(!empty($inputs['content_'.$language->code])){
                    $translations[] = [
                        'language_code' => $language->code,
                        'subject' => $inputs['subject_'.$language->code],
                        'header' => "",
                        'content' => $inputs['content_'.$language->code],
                        'footer' => ""];
                }
            }


            Notify::editEmailTemplate($inputs['types'], $templateKey, $translations);

            Session::flash('message', trans('EmailTemplates.updateOk'));

            return redirect()->action('EmailTemplatesController@show', $templateKey);
        }catch(Exception $e) {
            return redirect()->back()->withErrors([trans("EmailTemplates.update") => $e->getMessage()]);
        }

    }

    /**
     * @param $templateKey
     * @return $this|string
     */
    public function destroy($templateKey)
    {
        try{
            $siteKey = Notify::getEmailTemplate($templateKey)->site_key;
            Notify::deleteEmailTemplate($templateKey);
            Session::flash('message', trans('emailTemplate.delete_ok'));

            return action('EntitiesSitesController@showEmailTemplates', $siteKey);

        }catch(Exception $e) {
            return redirect()->back()->withErrors(["emailTemplate.delete" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $templateKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */

    public function delete($templateKey)
    {

        $data = array();
        $data['action'] = action("EmailTemplatesController@destroy", ['templateKey' => $templateKey]);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this Email Template for this Site?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $templateKey
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */

    public function createEmailsFromTemplates($siteKey)
    {
        try {
            $languages = Orchestrator::getLanguageList();
            $languagesCreate = [];
            $defaultLanguage = null;
            $siteKeyCreate[] = $siteKey;
            foreach ( $languages as $language) {
                $languagesCreate[] = $language->code;
                if($language->default){
                    $defaultLanguage =  $language->code;
                }
            }
            $response = Notify::newSiteEmailsTemplates($siteKeyCreate, $languagesCreate, $defaultLanguage );
            Session::flash('message', trans('privateEmailTemplate.email_templates_created'));
            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->withErrors([trans("EmailTemplates.create_emails_from_templates") => $e->getMessage()]);
        }
    }

}
