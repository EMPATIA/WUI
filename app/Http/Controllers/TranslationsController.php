<?php

namespace App\Http\Controllers;

use App\ComModules\Analytics;
use App\Http\Requests\CbsRequest;
use App\ComModules\Vote;
use App\ComModules\Files;
use App\ComModules\Notify;
use App\ComModules\Auth;
use App\ComModules\CB;
use App\ComModules\EMPATIA;
use App\ComModules\Questionnaire;
use App\ComModules\Orchestrator;
use App\Http\Requests\CbTopicsExportRequest;
use App\Http\Requests\PostRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\One\One;
use Datatables;
use Illuminate\Support\Facades\URL;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;

class TranslationsController extends Controller
{
    public function index(Request $request){

        try{

            $languages = Orchestrator::getLanguageList();
            $cbKey = $request->cbKey;
            $siteKey = $request->siteKey;

            if(!empty($cbKey)){
                $translations = CB::getCBSTranslations(null,$cbKey);
            }

            if(!empty($siteKey)){
                $translations = CB::getCBSTranslations($siteKey);
                $data['title'] = trans('privateSite.site_translations');
            }

            if(!empty($cbKey)){
                $data['cbKey'] = $cbKey;
                $data['title'] = trans('privateCb.cb_translations');                
                $data['sidebar'] = 'padsType';
                $data['active'] = 'translations';
                $data['type'] = $request->type;
            }
            else{
                $data['sidebar'] = 'site';
                $data['active'] = 'site_translations';

            }
            
            if(!empty($siteKey))
                $data['siteKey'] = $siteKey;
            
            
            $data['languages'] = $languages;
            $data['translations'] = $translations;

            return view('private.cbs.translations', $data);
        
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["translation.index" => $e->getMessage()]);
        } 

    }

    public function store(Request $request){
    
        try{

            $id = $request->id;
            $cbKey = $request->cb_key;
            $siteKey = $request->site_key;
            $code = $request->code; 
            $translation = $request->trans;
            $lang_code = $request->lang_code;

            $code = CB::setTranslation($id, $code, $translation, $lang_code, $cbKey, $siteKey);

            if(!empty($siteKey)) {
                $redis_key = $siteKey."_".$lang_code;
            } else {
                $redis_key = $cbKey."_".$lang_code;
            }
            Redis::del($redis_key);

            return "ok";

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["translation.store" => $e->getMessage()]);
        }
    }

    public function deleteLine(Request $request){

        try{

            $code = $request->code;
            $id = $request->id;
            $cbKey = $request->cb_key;
            $siteKey = $request->site_key;

            return CB::deleteTranslation($code,$id,$siteKey,$cbKey);


        } catch (Exception $e) {
            return redirect()->back()->withErrors(["translation.destroy" => $e->getMessage()]);
        }

    }

    public function exportTranslations(Request $request){

        try{

            $languages = Orchestrator::getLanguageList();
            $cbKey = $request->cbKey;
            $siteKey = $request->siteKey;

            if(!empty($cbKey)){
                $translations = CB::getCBSTranslations(null,$cbKey);
            }

            if(!empty($siteKey)){
                $translations = CB::getCBSTranslations($siteKey);
            }

            $csvString = 'code';

            foreach($languages as $language){
                $csvString= $csvString.','.$language->code;
            }
            $csvString= $csvString."\r\n";

            foreach($translations as $translation){
                $csvString = $csvString.$translation->code;

                $trans = collect($translation->translation_language)->keyBy('language_code');
                foreach ($languages as $language){
                    if(isset($trans[$language->code])){
                        $csvString= $csvString.','.$trans[$language->code]->translation;
                    }
                    else {
                        $csvString= $csvString.',';
                    }

                }
                $csvString= $csvString."\r\n";
            }

            $headers = [
                'Content-Encoding'    => 'UTF-8',
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="translations.csv"',
            ];

            return \Response::make($csvString, 200, $headers);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["translation.destroy" => $e->getMessage()]);
        }

    }

    public function importTranslations(Request $request)
    {
        try{
            $cbKey = $request->cbKey ?? '';
            $siteKey = $request->siteKey ?? '';

            $file = $request->file('csv');
            
            if(is_null($file)){
                return redirect()->back()->withErrors(["translation.import" => "Doesn't contain a file"]);
            }

            $file =  \File::get($file->getRealPath());

            $response = EMPATIA::uploadFileTranslations($file,$cbKey,$siteKey);

            $response = '<p>'.trans("private.new_translations") . ': ' . $response->new ." </p><p>  ". trans("private.updated_translations") . ': ' . $response->update.'</p>';
            Session::flash('message', $response);

            return redirect()->action('TranslationsController@index', [($cbKey != '' ? 'cbKey='.$cbKey: ($siteKey != '' ? 'siteKey='.$siteKey :''  ))]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(["translation.importTranslations" => $e->getMessage()]);
        }

    }


}