<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\ComModules\EMPATIA;
use App\ComModules\Orchestrator;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Datatables;
use Session;
use View;
use Illuminate\Support\Collection;
use App\One\One;

class AccountRecoveryController extends Controller
{
    public function index() {
        try {
            $title = trans('privateAccountRecovery.list_title');
            return view('private.accountRecovery.index', compact('title'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["accountRecovery.index" => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            $registerParameters = Orchestrator::getEntityRegisterParameters();
            $registerParameters = collect($registerParameters)->pluck("name","parameter_user_type_key")->merge(["email"=>trans("privateAccountRecovery.email_address")])->toArray();
            return view('private.accountRecovery.accountRecovery', compact('registerParameters'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["accountRecovery.index" => $e->getMessage()]);
        }
        $languages = Orchestrator::getAllLanguages();
        $sectionTypeParameters = CM::getSectionTypeParameters();
    }

    public function store(Request $request)
    {
        try {
            $dataToSend = array(
                "parameter_user_type_key" => $request->get("parameter_user_type",""),
                "send_token" => $request->get("send_token",0),
            );

            $accountRecoveryParameter = EMPATIA::createAccountRecoveryParameters($dataToSend);
            Session::flash('message', trans('privateAccountRecovery.store_ok'));
            return redirect()->action('AccountRecoveryController@show', $accountRecoveryParameter->table_key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('privateAccountRecovery.update_error') => $e->getMessage()]);
        }
    }

    public function show($accountRecoveryParameterKey)
    {
        try {
            $accountRecoveryParameter = EMPATIA::getAccountRecoveryParameter($accountRecoveryParameterKey);
            if ($accountRecoveryParameter->parameter_user_type_key == "email")
                $accountRecoveryParameter->name = trans("privateAccountRecovery.email_address");
            else
                $accountRecoveryParameter->name = $accountRecoveryParameter->parameter_user_type->name;

            return view('private.accountRecovery.accountRecovery', compact('accountRecoveryParameter'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["accountRecovery.show" => $e->getMessage()]);
        }
    }

    public function edit($accountRecoveryParameterKey)
    {
        try {
            $accountRecoveryParameter = EMPATIA::getAccountRecoveryParameter($accountRecoveryParameterKey);

            $registerParameters = Orchestrator::getEntityRegisterParameters();
            $registerParameters = collect($registerParameters)->pluck("name","parameter_user_type_key")->merge(["email"=>trans("privateAccountRecovery.email_address")])->toArray();

            return view('private.accountRecovery.accountRecovery', compact('accountRecoveryParameter','registerParameters'));
        } catch(Exception $e) {
            return redirect()->back()->withErrors(["accountRecovery.edit" => $e->getMessage()]);
        }
    }

    public function update(Request $request, $accountRecoveryParameterKey) {
        try {
            $dataToSend = array(
                "parameter_user_type_key" => $request->get("parameter_user_type",""),
                "send_token" => $request->get("send_token",0),
            );

            $accountRecoveryParameter = EMPATIA::editAccountRecoveryParameters($accountRecoveryParameterKey,$dataToSend);
            Session::flash('message', trans('privateAccountRecovery.update_ok'));
            return redirect()->action('AccountRecoveryController@show', $accountRecoveryParameter->table_key);
        } catch (Exception $e){
            return redirect()->back()->withErrors([ trans('accountRecovery.update_error') => $e->getMessage()]);
        }
    }

    public function delete($accountRecoveryParameterKey) {
        $data = array();

        $data['action'] = action("AccountRecoveryController@destroy", $accountRecoveryParameterKey);
        $data['title'] = trans('privateAccountRecovery.delete');
        $data['msg'] = trans('privateAccountRecovery.are_you_sure_you_want_to_delete');
        $data['btn_ok'] = trans('privateAccountRecovery.delete');
        $data['btn_ko'] = trans('privateAccountRecovery.cancel');

        return view("_layouts.deleteModal", $data);
    }

    public function destroy($accountRecoveryParameterKey) {
        try {
            EMPATIA::deleteAccountRecoveryParameters($accountRecoveryParameterKey);
            Session::flash('message', trans('privateAccountRecovery.delete_ok'));
            return action('AccountRecoveryController@index');
        } catch(Exception $e) {
            return redirect()->back()->withErrors([ trans('accountRecovery.delete_error') => $e->getMessage()])->getTargetUrl();
        }
    }

    public function getIndexTable(){
        // Request for Data List
        $accountRecoveryParameters = EMPATIA::getAccountRecoveryParameters();

        // JSON data collection
        $collection = Collection::make($accountRecoveryParameters);

        // Render Datatable
        return Datatables::of($collection)
            ->addColumn('key', function ($accountRecoveryParameter) {
                return "<a href='".action('AccountRecoveryController@show', $accountRecoveryParameter->table_key)."'>".$accountRecoveryParameter->table_key."</a>";
            })
            ->addColumn('parameter_user_type', function ($accountRecoveryParameter) {
                if ($accountRecoveryParameter->parameter_user_type_key == "email")
                    return trans("privateAccountRecovery.email_address");
                else
                    return $accountRecoveryParameter->parameter_user_type->name;
            })
            ->editColumn('send_token',function ($accountRecoveryParameter) {
                return "<i class='fa fa-" . (($accountRecoveryParameter->send_token==true) ? "check" : "times") . "'></i>";
            })
            ->addColumn('action', function ($accountRecoveryParameter) {
                return ONE::actionButtons($accountRecoveryParameter->table_key, ['form' => 'accountRecovery','edit' => 'AccountRecoveryController@edit', 'delete' => 'AccountRecoveryController@delete'] );
            })
            ->make(true);
    }
}