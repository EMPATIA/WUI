<?php

namespace App\Http\Controllers;

use App\ComModules\CM;
use App\Http\Requests\MailRequest;
use App\One\One;
use Datatables;
use Illuminate\Support\Collection;
use Session;
use View;

class MailsController extends Controller
{
    public function __construct()
    {
        View::share('private.mails', trans('mail.mail'));



    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('private.mails.index');
    }

    /**
     * Create a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('private.mails.mail');
    }

    /**
     *Store a newly created resource in storage.
     *
     * @param MailRequest $request
     * @return $this|View
     */
    public function store(MailRequest $request)
    {
        try {
            $mail = CM::setMail($request->all());
            Session::flash('message', trans('mail.store_ok'));
            return redirect()->action('MailsController@show', $mail->mail_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["mail.store" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $mail_key
     * @return Response
     */
    public function show($mail_key)
    {
        try {
            $mail = CM::getMail($mail_key);

            return view('private.mails.mail', compact('mail'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["mail.show" => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $mail_key
     * @return View
     */
    public function edit($mail_key)
    {
        try {
            $mail = CM::getMail($mail_key);

            return view('private.mails.mail', compact('mail'));
        }
        catch(Exception $e) {
            return redirect()->back()->withErrors(["mail.edit" => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MailRequest $request
     * @param $mail_key
     * @return $this|View
     */
    public function update(MailRequest $request, $mail_key)
    {

        try {
            $mail = CM::updateMail($request->all(), $mail_key);
            Session::flash('message', trans('mail.update_ok'));
            return redirect()->action('MailsController@show', $mail->mail_key);
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["mail.update" => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $mail_key
     * @return Response
     */
    public function destroy($mail_key){

        try {
            CM::deleteMail($mail_key);
            Session::flash('message', trans('mail.delete_ok'));
            return action('MailsController@index');
        }
        catch(Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["mail.destroy" => $e->getMessage()]);
        }
    }

    public function delete($id){
        $data = array();

        $data['action'] = action("MailsController@destroy", $id);
        $data['title'] = "DELETE";
        $data['msg'] = "Are you sure you want to delete this E-mail?";
        $data['btn_ok'] = "Delete";
        $data['btn_ko'] = "Cancel";

        return view("_layouts.deleteModal", $data);
    }

    /**
     * Get the specified resource.
     *
     *
     */
    public function tableMails()
    {
        $manage = CM::listMail();

        // in case of json
        $mail = Collection::make($manage);

        return Datatables::of($mail)
            ->editColumn('subject', function ($mail) {
                return "<a href='".action('MailsController@show', $mail->mail_key)."'>".$mail->subject."</a>";
            })
            ->addColumn('action', function ($mail) {
                return ONE::actionButtons($mail->mail_key, ['edit' => 'MailsController@edit', 'delete' => 'MailsController@delete']);
            })
            ->make(true);
    }
}
