<?php

namespace App\Exceptions;

use App\ComModules\LogsRequest;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use App\One\One;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
      /*  AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class*/
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {


        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {

        if(env('PERFORMANCE_FLAG', 'false')=='true' && (env('LOGS_FLAG', 'false')=='true')){
            $response = LogsRequest::updateMessageException($e);


            $lastLog = LogsRequest::getLastLog();

            $method = "ERROR_MSG";
            $resultSaving = LogsRequest::saveTrackingDataToDB($lastLog, $method, $e);

        }



        /*        if($e){
            //Redirect::back();
            //Session::flash('message', $e->getMessage());
            dd($request->server('HTTP_REFERER')."\n".$e->getMessage());
            //return response()->view('errors.503');
            dd($e->getMessage());
            //$request->session()->flash('alert-success', $e->getMessage());
        }else{
            return parent::render($request, $e);
        }

        //dd($request);
        //if($this->isHttpExcehaption($e)){

           // dd($e->getMessage());
        //}*/
       return parent::render($request, $e);
    }
}
