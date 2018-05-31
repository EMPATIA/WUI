<?php

namespace App\Http\Controllers;

use App\ComModules\EMPATIA;
use App\ComModules\Notify;
use App\ComModules\Orchestrator;
use App\ComModules\Questionnaire;
use App\Http\Requests\ContentRequest;
use Datatables;
use Exception;
use Illuminate\Http\Request;
use One;
use Session;

class PrivateNewslettersController extends Controller
{

    /**
     * Returns Newsletters List View
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        try {
            $title = trans('privateNewsletters.newsletters');
            $sidebar = 'email';
            $active = 'newsletters';

            Session::put('sidebarArguments', ['activeFirstMenu' => 'newsletters']);
            return view('private.newsletters.index', compact('title', 'sidebar', 'active'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.index" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request){
        try {
            $title = trans('privateNewsletters.create');

            $questionnaireKey = $request->get('qKey') ?? null;
            $questionnaires = Questionnaire::getQuestionnaireList();

            return view('private.newsletters.newsletter', compact('title', 'questionnaireKey', 'questionnaires'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.create" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request) {
        try {
            $data['title']          = $request->input('title');
            $data['subject']        = $request->input('subject');
            $data['content']        = $request->input('content');
            $data['created_by']     = Session::get('user')->user_key;
            $data['questionnaire']  = $request->input('questionnaire');

            $newsletter = Notify::createNewsletter($data);

            Session::flash('message', trans('newsletter.store_ok'));
            return redirect()->action('PrivateNewslettersController@show',$newsletter->newsletter_key);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.store" => $e->getMessage()]);
        }
    }

    public function show(Request $request, $newsletterKey)
    {
        try {
            $title = trans('privateNewsletters.show');

            $newsletter = Notify::getNewsletter($newsletterKey);

            return view('private.newsletters.newsletter', compact('title', 'newsletter'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.show" => $e->getMessage()]);
        }
    }

    public function edit(Request $request, $newsletterKey){
        try {
            $title = trans('privateNewsletters.edit');

            $newsletter = Notify::getNewsletter($newsletterKey);

            return view('private.newsletters.newsletter', compact('title', 'newsletter'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.edit" => $e->getMessage()]);
        }
    }

    public function update(Request $request, $newsletterKey){
        try {
            $data['created_by'] = Session::get('user')->user_key;
            $data['title'] = $request->input('title');
            $data['subject'] = $request->input('subject');
            $data['content'] = $request->input('content');

            $newsletter = Notify::updateNewsletter($newsletterKey,$data);

            Session::flash('message', trans('newsletter.update_ok'));
            return view('private.newsletters.newsletter', compact('title', 'newsletter'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.update" => $e->getMessage()]);
        }
    }

    public function delete(Request $request, $newsletterKey){

        $data = array();
        $data['action'] = action("PrivateNewslettersController@destroy", ['newsletterKey' => $newsletterKey]);
        $data['title'] = trans('privateCbs.delete');
        $data['msg'] = trans('privateCbs.are_you_sure_you_want_to_delete').' ?';
        $data['btn_ok'] = trans('privateCbs.delete');
        $data['btn_ko'] = trans('privateCbs.cancel');

        return view("_layouts.deleteModal", $data);
    }

    public function destroy(Request $request, $newsletterKey){
        try {
            Notify::deleteNewsletter($newsletterKey);

            Session::flash('message', trans('privateNewsletters.delete_ok'));
            return action('PrivateNewslettersController@index');
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.destroy" => $e->getMessage()]);
        }
    }


    public function getIndexTable(Request $request)
    {
        try {
            $response = Notify::getNewsletters($request);
            $newsletters = collect($response->newsletters ?? []);
            $recordsTotal = $response->recordsTotal;
            $recordsFiltered = $response->recordsFiltered;

            return Datatables::of($newsletters)
                ->editColumn('subject', function ($newsletters) {
                    return "<a href='".action('PrivateNewslettersController@show', ['id' => $newsletters->newsletter_key]) . "'>" . $newsletters->subject . "</a>";
                })
                ->editColumn('created_by', function ($newsletters) {
                    return $newsletters->created_by;
                })
                ->editColumn('created_at', function ($newsletters) {
                    return $newsletters->created_at;
                })
                ->editColumn('tested', function ($newsletters) {
                    return $newsletters->tested;
                })
                ->addColumn('action', function ($newsletters) {
                    return ONE::actionButtons($newsletters->newsletter_key, ['form' => 'newsletters', 'show' => 'PrivateNewslettersController@show', 'delete' => 'PrivateNewslettersController@delete']);
                })
                ->rawColumns(['subject','action'])
                ->with('filtered', $recordsFiltered ?? 0)
                ->skipPaging()
                ->setTotalRecords($recordsTotal ?? 0)
                ->make(true);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["privateNewsletters.getIndexTable" => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @param $newsletterKey
     * @param $flag
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sendNewsletter(Request $request, $newsletterKey, $flag){
        try {
            $site = Orchestrator::getSite(Session::get('X-SITE-KEY'));

            $newsletter = Notify::getNewsletter($newsletterKey);

            if($flag == '0') {
                $users = collect(Session::get('user'));
                $data['users'] = [$users['email']];
                $userKeys = collect($users['user_key']);
            } else {
                $users = collect(Orchestrator::getListOfAvailableUsersToSendEmails());
                $data['users'] = $users->pluck('email');
                $userKeys = $users->pluck('user_key');
            }
            $data['newsletter_id'] = $newsletter->id;
            $data['subject'] = $newsletter->subject;
            $data['message'] = $newsletter->content;

            if (!empty(json_decode($newsletter->extra_data))){

                $extraData = json_decode($newsletter->extra_data);
                if (!is_null($extraData->questionnaire) && $extraData->questionnaire!="null") {

                    $usersData = EMPATIA::generateUniqueKey($userKeys, $extraData->questionnaire);

                    if ($usersData){
                        $dataArray = [];

                        foreach ($usersData as $key => $userData){
                            $dataArray[$userData->email] = action('PublicQController@autoLoginQ', [$userData->questionnaire_key, $key, $userData->unique_key]);
                        }

                        if (!empty($dataArray)){
                            $data['action_url'] = $dataArray;
                        }
                    }
                }
            }

            $sendNewsletter = Notify::createEmails((object)$data,(object)$site);

            if($flag == '0' && $sendNewsletter){
                $newsletter = Notify::testNewsletter($newsletterKey, Session::get('user')->user_key);
                Session::flash('message', trans('privateNewsletters.test_ok'));
            }

            if($flag == '1' && $sendNewsletter){
                Session::flash('message', trans('privateNewsletters.send_ok'));
            }

            return view('private.newsletters.newsletter', compact('title', 'newsletter'));
        }
        catch(Exception $e) {
            dd($e);
            // return redirect()->back()->withErrors(["privateNewsletters.testNewsletter" => $e->getMessage()]);
        }
    }

}
