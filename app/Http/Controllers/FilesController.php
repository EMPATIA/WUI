<?php

namespace App\Http\Controllers;

use Cache;
use Exception;
use HttpClient;
use Illuminate\Http\Request;
use App\ComModules\Files;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use JildertMiedema\LaravelPlupload\Facades\Plupload;
use ONE;
use Session;

class FilesController extends Controller
{

    public function upload(Request $request)
    {
        $components = Cache::get('COMPONENTS'.env('MODULE_TOKEN'));

        if(empty($components)){

            $request = [
                'url' => env('COMPONENT_MODULE_AUTH').'/components',
                'headers' => [
                    'X-MODULE-TOKEN: '.env('MODULE_TOKEN','INVALID') ,
                    'X-SITE-KEY: '.Session::get('X-SITE-KEY','INVALID'),
                    'X-ENTITY-KEY: '.Session::get('X-ENTITY-KEY','INVALID')]
            ];
            $response = HttpClient::GET($request);
            if($response->statusCode() == 200){
                $componentData = json_decode($response->content(),true);
                $components = $componentData['data'];
                Cache::put('COMPONENTS'.env('MODULE_TOKEN'),$components, 10);
            }

        }

        $curl = curl_init();

        $file = $request->file('file');

        $data = $request->all();
        $data['file'] = curl_file_create($file->getPathname(), $file->getMimeType(), $file->getClientOriginalName());

        curl_setopt($curl, CURLOPT_URL, $components['FILES'].'/file/upload');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['X-AUTH-TOKEN: ' . $request->header('X-AUTH-TOKEN'),
            'X-UPLOAD-TOKEN: ' . $request->header('X-UPLOAD-TOKEN'),
            'X-MODULE-TOKEN: ' . env('MODULE_TOKEN', 'INVALID'),
            'Content-Type: multipart/form-data']);

        //            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($curl);

        return $response;
    }

    public function download(Request $request, $id, $code, $inline = null)
    {
        $components = Cache::get('COMPONENTS'.env('MODULE_TOKEN'));

        if(empty($components)){

            $request = [
                'url' => env('COMPONENT_MODULE_AUTH').'/components',
                'headers' => [
                    'X-MODULE-TOKEN: '.env('MODULE_TOKEN','INVALID') ,
                    'X-SITE-KEY: '.Cache::get('X-SITE-KEY','INVALID'),
                    'X-ENTITY-KEY: '.Cache::get('X-ENTITY-KEY','INVALID')]
            ];
            $response = HttpClient::GET($request);
            if($response->statusCode() == 200){
                $componentData = json_decode($response->content(),true);
                $components = $componentData['data'];
                Cache::put('COMPONENTS'.env('MODULE_TOKEN'),$components, 10);
            }

        }

        $extraParams = '';
        if(!empty($request->get('w'))){
            $extraParams .= '?w='.$request->get('w');
        }
        if(!empty($request->get('h'))){
            $extraParams .= empty($extraParams) ? '?h='.$request->get('h') : '&h='.$request->get('h');
        }
        if(!empty($request->get('fit'))){
            $extraParams .= empty($extraParams) ? '?fit='.$request->get('fit') : '&fit='.$request->get('fit');
        }


        return redirect($components['FILES'] . '/file/download/' . $id . '/' . $code . '/' . ($inline ?? '0').$extraParams);
    }

    public function downloadFile(Request $request)
    {
        $components = Cache::get('COMPONENTS'.env('MODULE_TOKEN'));

        // Request input
        $id = !empty($request->id) ? $request->id : "";
        $code = !empty($request->code) ? $request->code : "";
        $inline = !empty($request->inline) ? $request->inline : null;

        if(empty($components)){

            $request = [
                'url' => env('COMPONENT_MODULE_AUTH').'/components',
                'headers' => [
                    'X-MODULE-TOKEN: '.env('MODULE_TOKEN','INVALID') ,
                    'X-SITE-KEY: '.Cache::get('X-SITE-KEY','INVALID'),
                    'X-ENTITY-KEY: '.Cache::get('X-ENTITY-KEY','INVALID')]
            ];
            $response = HttpClient::GET($request);
            if($response->statusCode() == 200){
                $componentData = json_decode($response->content(),true);
                $components = $componentData['data'];
                Cache::put('COMPONENTS'.env('MODULE_TOKEN'),$components, 10);
            }

        }
        $response = HttpClient::GET($components['FILES'] . '/file/download/' . $id . '/' . $code . '/' . $inline ?: '');

        $file = Files::getFile($id);

        header("Content-length: " . $file->size);
        header("Content-type: " . $file->type);
        header("Content-Disposition: attachment; filename=\"" . $file->name . "\"");
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 3600));
        header("Pragma: cache");
        header("Cache-Control: max-age=1296000");
        header("User-Cache-Control: max-age=1296000");

        return $response->content();
    }
}
