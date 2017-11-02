<?php

namespace App\Http\Controllers;

use App\ComModules\Auth;
use App\ComModules\Notify;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use App\Http\Requests\ContentRequest;
use App\ComModules\Orchestrator;
use One;
use Session;

class emailsController extends Controller
{

    /**
     * emailsController constructor.
     */
    public function __construct()
    {

    }


    /**
     * Returns Emails List View
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //Page title
        $title = trans('privateEmails.list_emails');

        return view('private.emails.index', compact('title'));
    }


    /**
     *
     * Returns data to datatable with emails list
     *
     * @param Request $request
     * @return $this
     */
    public function tableEmails(Request $request)
    {
        try {
            //Get all sent emails
            if (Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsShow('wui', 'email')){
                $response = Notify::getEmails($request);

                $sentEmails = collect($response->emails);
                $recordsTotal = $response->recordsTotal;
                $recordsFiltered = $response->recordsFiltered;
            } else

                $sentEmails = Collection::make([]);

            return Datatables::of($sentEmails)
                ->editColumn('recipient', function ($sentEmails) {
                    if(count(json_decode($sentEmails->recipient)) >1 )
                        return "<a href='" . action('EmailsController@show', $sentEmails->email_key) . "'>" . 'Multiple Recipients' . "</a>";
                    else
                        return "<a href='" . action('EmailsController@show', $sentEmails->email_key) . "'>" . $sentEmails->recipient . "</a>";
                })
                ->editColumn('subject', function ($sentEmails) {
                    return $sentEmails->subject ?? null;
                })
                ->editColumn('sent', function ($sentEmails) {
                    return $sentEmails->sent == '1' ? $sentEmails->updated_at : trans("privateEmails.not_sent");
                })
                ->addColumn('action', function ($sentEmails) {
                    return ONE::actionButtons($sentEmails->email_key, ['show' => 'EmailsController@show']);
                })
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["groupTypes.tableGroupTypes" => $e->getMessage()]);
        }
    }

    /**
     * Shows Email details from a given email Key
     *
     * @param Request $request
     * @param $emailKey
     * @return $this
     */
    public function show(Request $request, $emailKey)
    {
        try {
            $email = Notify::getEmail($emailKey);

            // Form title (layout)
            $title = trans('privateEmail.show_email');

            try {
                $user = Auth::getUserByKey($email->created_by);
                $userData = $user->name.'   ('.$user->email.') ';
            } catch (Exception $e){
                $userData = $email->created_by;
            }

            // Return the view with data
            $data = [];
            $data['title'] = $title;
            $data['email'] = $email;
            $data['userData'] = $userData;

            return view('private.emails.email', $data);
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["email.show" => $e->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $languages = Orchestrator::getLanguageList();
        $users = Orchestrator::getListOfAvailableUsersToSendEmails();
        $title = trans('privateEmail.create_email');

        return view('private.emails.email', compact('title', 'languages','users'));
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request){
        try {
            $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));
            if(isset($request->send_to_all)){
                dd('PLEASE WAIT FOR SUPPORT');
                $request->users = collect(Orchestrator::getListOfAvailableUsersToSendEmails())->pluck('email');
            }
            Notify::createEmails($request,$site);

            Session::flash('message', trans('email.store_ok'));
            return redirect()->action('EmailsController@index');
        }
        catch(Exception $e) {
            //   //TODO: save inputs
            return redirect()->back()->withErrors(["email.store" => $e->getMessage()]);
        }
    }
}
