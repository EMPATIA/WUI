<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\One\One;
use App\ComModules\CB;
use Session;

class AnnotatorController extends Controller
{
    //
    private $contentKey = 'ShAVLaa8tOrePz5osNtpjuLpjMhDHiHP';
    private $siteContentKey;

    /**
     * AnnotatorController constructor.
     */
    public function __construct()
    {
        ONE::getKeys();
    }

    /**
     * @return \BladeView|bool|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){

        return view('public.'.ONE::getEntityLayout().'.home.annotator');
    }

     /**
     * @param $topic_key
     * @return Exception|\Illuminate\Http\JsonResponse
     */
    public function tags($topic_key){

        try{

            $tags = CB::getAnnotationsTags($topic_key);
            $tagsArray = [];

            foreach ($tags as $i=>$tag){

                $tagsArray[$i]['text'] = $tag->value;
                $tagsArray[$i]['id'] = $tag->code;
            }

            //return response($tagsArray);
            return response()->json($tagsArray);

        }catch(Exception $e){
            return $e;
        }

    }

    /**
     * @param Request|null $request
     * @return string
     */
    public function show(Request $request = null, $topic_key){

        try{

            $collection = collect(CB::getCooperatorPermissions($topic_key));

            $isAdmin = false;
            $isAdmin = $collection->contains('edit');

            $annotations = CB::getAnnotations($topic_key);


            //Key conversion to expected annotator.js data structure

            $permissions = [];

            foreach ($annotations as $annotation){

                //set/rename new keys
                $annotation->ranges[0]->startOffset = $annotation->ranges[0]->start_offset;
                $annotation->ranges[0]->endOffset = $annotation->ranges[0]->end_offset;
                $annotation->id = $annotation->annotation_key;


                //Key conversion - "annotation_types" - to expected annotator data structure
                $tags = [];
                foreach ($annotation->annotation_types as $annotation_type){
                    $tags[] = $annotation_type->value;
                }
                $annotation->tags =  $tags;


                /* // (example) Permissions Array structure expected by Annotator Permission plugin
                 * array:8 [
                      "permissions" => array:4 [
                        "read" => []
                        "update" => []
                        "delete" => []
                        "admin" => []
                      ]
                 *
                 *
                 * */

                // Set annotations permissions for current user

                if ($isAdmin)
                    //if user in session is admin
                    $permissions['admin'][0]= Session::get('user')->user_key;
                else{
                    //only annotation creator/owner can update/delete
                    $permissions['update'][0] = $annotation->cooperator->user_key;
                    $permissions['delete'][0] = $annotation->cooperator->user_key;
                }

                $annotation->permissions = $permissions;

                //unset replaced keys
                unset($annotation->ranges[0]->start_offset);
                unset($annotation->ranges[0]->end_offset);
                unset($annotation->annotation_types);

            }

            //Include $annotations structure, inside array "rows", as expected from annotator.js
            $response['rows'] = $annotations;
            $response['total'] = count($annotations);

            return response()->json($response);

        }catch (Exception $e) {
            return redirect()->back()->withErrors(["annotator.show" => $e->getMessage()]);  //TODO - confirm if is correct
        }
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function store(Request $request){

        try{


            $annotationDetails = (object) $request->all();

            $data['topic_key'] = $annotationDetails->topicKey;
            $data['ranges'] = $annotationDetails->ranges;
            $data['quote'] = $annotationDetails->quote;
            $data['text'] = $annotationDetails->text;
            $data['tags'] = isset($annotationDetails->tags) ? $annotationDetails->tags : null;

            $response = CB::setAnnotation($data);

            $response->id = $response->annotation_key;

            return response()->json($response);

            }catch (Exception $e) {
                return redirect()->back()->withErrors(["annotator.store" => $e->getMessage()]);  //TODO - confirm if is correct
        }
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        try {

            $annotationDetails = (object) $request->all();

            $data['text'] = $annotationDetails->text;
            $data['tags'] = isset($annotationDetails->tags) ? $annotationDetails->tags : null;
            $annotationKey = $annotationDetails->id;
            $response = CB::updateAnnotation($data, $annotationKey);

            //dd($response);
            return response()->json($response);
        }
        catch(Exception $e) {

            return redirect()->back()->withErrors(["annotator.update" => $e->getMessage()]); //TODO: confirm if is correct
        }
    }

        public function destroy($annotationKey){

        try {

            $response = CB::deleteAnnotation($annotationKey);

            return response()->json($response);
        }
        catch(Exception $e) {
            return response()->withErrors(["annotator.destroy" => $e->getMessage()]); //TODO: confirm if is correct
        }
    }




}
