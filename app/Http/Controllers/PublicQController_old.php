<?php

namespace App\Http\Controllers;

use App\ComModules\Questionnaire;
use App\Http\Requests\CBRequest;
use App\Http\Requests\QRequest;
use App\Http\Requests\UserRequest;
use App\One\One;
use Datatables;
use Illuminate\Support\Facades\URL;
use Session;
use View;
use Breadcrumbs;
use Exception;
use Illuminate\Support\Collection;


class PublicQController_OLD extends Controller
{

    public function __construct()
    {

        View::share('title', 'Questionnaire');

    }

    public function intro($questionnaireId)
    {
        try {
            $completed = Questionnaire::verifyReply($questionnaireId);
            if($completed){
                $message = 1;
                return view('public.'.ONE::getEntityLayout().'.questionnaire.index', compact('message'));
            }
            return redirect()->action('PublicQController@showQ',$questionnaireId);
        } catch (Exception $e) {
            return redirect()->action('PublicQController@showQ',$questionnaireId);
        }
    }

    /**
     * Display a question of the resource.
     *
     * @return Response
     */
    public function showQ($questionnaireId)
    {
        try {

            $completed = Questionnaire::verifyReply($questionnaireId);;
            if ($completed) {
                $message = 1;
                return view('public.' . ONE::getEntityLayout() . '.questionnaire.index', compact('message'));
            }


            $form = Questionnaire::getFormConstruction($questionnaireId);
            $formId = $form->id;
            $titleForum = $form->title;

            $list = $form->question_groups;
            $questions = '';
            $stepper = '';
            $i = 1;
            $activeStepper = true;
            $maxCount = count($list);

            $questions .= '<input id="questionnaire_id" type="hidden" value="' . $formId . '" name="questionnaire_id">';
            foreach ($list as $item) {
                $id = $item->id;
                $title = $item->title;
                $description = $item->description;

                if ($activeStepper) {
                    /* Stepper */
                    $stepper .= '<div class="stepwizard-step"><a href="#step-' . $i . '" type="button" class="btn btn-primary btn-circle">' . $i . '</a></div>';
                    $questions .= '<div class="row setup-content" id="step-' . $i . '">';
                } else {
                    $questions .= '<div class="row setup-content" id="step-' . $i . '" style="display:none">';
                    $stepper .= '<div class="stepwizard-step"><a href="#step-' . $i . '" type="button" class="btn btn-primary btn-circle disabled btn-circle-disabled" >' . $i . '</a></div>';
                }

                $questions .= '<div class="col-md-12">';
                $questions .= '<div style="border-bottom: 1px solid #f4f4f4; padding-bottom: 15px; margin-top: 15px; margin-bottom: 20px;padding-left: 40px; padding-right: 40px;">';

                $questions .= '<h2>' . $titleForum . '</h2>';
                if (count($description) > 0) {
                    $questions .= '<h5 style="text-align: justify;">' . $description . '</h5>';
                }

                $questions .= '<p id="stepper_number_step-' . $i . '" style="display:none">Step ' . $i . ' of ' . $maxCount . '</p>';
                $questions .= '</div>';

                $questions .= ' <h3 style="text-align: center">' . $title . '</h3><br>';


                /* ----- Questions ----- */
                // $questions .= $this->createLegend($item->questions);;

                $questions .= $this->itemsByGroup($id, $item->questions);;
                /* ----- END Questions  ----- */
                $questions .= '</div>';
                $questions .= '</div>';

                $i++;
                if ($activeStepper) {
                    if ($item->all_questions_answered == false) {
                        $activeStepper = false;
                    }
                }

            }

            return view('public.' . ONE::getEntityLayout() . '.questionnaire.form', compact('stepper', 'questions', 'formId'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(["form.list" => $e->getMessage()]);
        }

    }

    public function createLegend($list)
    {


        $html = '';
        foreach ($list as $item) {

            $id = $item->id;
            $typeId = $item->question_type_id;
            $type = strtoupper(preg_replace('/\s+/', '', $item->question_type->name));
            $question = $item->question;
            $mandatory = $item->mandatory;
            $position = $item->position;

            Switch ($type) {
                case 'RADIOBUTTONS':
                case 'CHECKBOX':
                    $html .= '<div class="row" style="margin-bottom: 30px">';
                    $html .= '<div style="text-align: center;">';
                    $html .= '<div class="col-xs-3 col-md-3 col-sm-3">';
                    $html .= '</div>';



                    /*$html .= '<div class="col-xs-2  col-md-2 col-sm-2">';
                    $html .= '<a style="color: #777;">';
                    $html .= '<i class="fa fa-thumbs-o-up" style="color: #777;"></i>';
                    $html .= '<br>Yes</a>';
                    $html .= '</div>';

                    $html .= '<div class="col-xs-2  col-md-2 col-sm-2">';
                    $html .= '<a style="color: #777;">';
                    $html .= '<i class="fa fa-thumbs-o-down" style="color: #777;"></i>';
                    $html .= '<br>No</a>';
                    $html .= '</div>';

                    $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
                    $html .= '<a style="color: #777;">';
                    $html .= '<i class="fa fa-question" style="color: #777;"></i>';
                    $html .= '<br>Not Sure</a>';
                    $html .= '</div>';*/

                    $html .= '<div class="col-xs-3 col-md-3 col-sm-3">';
                    $html .= '</div>';

                    $html .= '</div>';
                    $html .= '</div>';

                    break;
            }

        }



        /*$maxOption = 0;
        foreach ($list as $item) {
            $numberOptions = count($item->question_options);

            if($numberOptions > $maxOption){
                $maxOption = $numberOptions;
            }
        }


        if($maxOption == 3){
            $html .= '<div class="row" style="margin-bottom: 30px">';
            $html .= '<div style="text-align: center;">';
            $html .= '<div class="col-xs-3 col-md-3 col-sm-3">';
            $html .= '</div>';

            $html .= '<div class="col-xs-2  col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="fa fa-thumbs-o-up" style="color: #777;"></i>';
            $html .= '<br>Yes</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2  col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="fa fa-thumbs-o-down" style="color: #777;"></i>';
            $html .= '<br>No</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="fa fa-question" style="color: #777;"></i>';
            $html .= '<br>Not Sure</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-3 col-md-3 col-sm-3">';
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';
        }else if($maxOption == 5){
            $html .= '<div class="row" style="margin-bottom: 30px">';
            $html .= '<div style="text-align: center;">';
            $html .= '<div class="col-xs-1 col-md-1 col-sm-1">';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-emoticon" style="color: #777;"></i>';
            $html .= '<br>Least Preferred</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square" style="color: #777;"></i>';
            $html .= '<br>Less Preferred</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square-3" style="color: #777;"></i>';
            $html .= '<br>Neutral</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square-1" style="color: #777;"></i>';
            $html .= '<br>Preferred</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square-2" style="color: #777;"></i>';
            $html .= '<br>Most Preferred</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-1 col-md-1 col-sm-1">';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        } else if($maxOption == 6){
            $html .= '<div class="row" style="margin-bottom: 30px">';
            $html .= '<div style="text-align: center;">';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-emoticon" style="color: #777;margin-left: 0px;"></i>';
            $html .= '<br>Extremely Disagree</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square-4" style="color: #777;margin-left: 0px;"></i>';
            $html .= '<br>Disagree</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square" style="color: #777;margin-left: 0px;"></i>';
            $html .= '<br>Slightly Disagree</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square-3" style="color: #777;margin-left: 0px;"></i>';
            $html .= '<br>Neutral</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square-1" style="color: #777;margin-left: 0px;"></i>';
            $html .= '<br>Slightly Agree</a>';
            $html .= '</div>';

            $html .= '<div class="col-xs-2 col-md-2 col-sm-2">';
            $html .= '<a style="color: #777;">';
            $html .= '<i class="glyph-icon flaticon-square-2" style="color: #777;margin-left: 0px;"></i>';
            $html .= '<br>Extremely Agree</a>';
            $html .= '</div>';

            $html .= '</div>';
            $html .= '</div>';
        }*/

        return $html;
    }



    public function itemsByGroup($idGroup, $questions)
    {

        $html = '';
        $radioButton = 1;
        $htmlTable = 1;
        $htmlTableInit = 0;


        foreach ($questions as $item) {


            $id = $item->id;
            $typeId = $item->question_type_id;
            $type = strtoupper(preg_replace('/\s+/', '', $item->question_type->name));
            $question = $item->question;
            $mandatory = $item->mandatory;
            $position = $item->position;
            $answer = $item->reply;

            if($answer == null)
                $answer = '';

            if($htmlTable == 1){
                Switch($type){
                    case 'TEXT':
                        $html .= '<div class="form-group" style="padding-bottom: 20px;">';
                        if($mandatory == 1){
                            $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                            $html .= '<input type="text" name="text_' . $id . '" class="form-control" id="text_' . $id . '" value="'.$answer.'" required>';
                        }else{
                            $html .= '<label>' . $question . '</label>';
                            $html .= '<input type="text" name="text_' . $id . '" class="form-control" id="text_' . $id . '" value="'.$answer.'">';
                        }
                        $html .= '</div>';
                        break;
                    case 'RADIOBUTTONS':
                        $required = "";
                        $html .= '<div class="form-group" style="padding-bottom: 20px;">';
                        if($mandatory == 1){
                            $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                            $required = "required";
                        }else{
                            $html .= '<label>' . $question . '</label>';
                        }


                        $html .= $this->questionsOptionRadioButtonInLine($id, $item->question_options,$required, $answer);
                        $html .= '</div>';
                        break;
                    case 'CHECKBOX':
                        $required = "";
                        $html .= '<div class="form-group">';
                        if($mandatory == 1){
                            $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                            $required = "required";
                        }else{
                            $html .= '<label>' . $question . '</label>';
                        }
                        $html .= $this->questionsOptionCheckBoxInLine($id,$item->question_options,$required, $answer);
                        $html .= '</div>';

                        break;
                    case 'TEXTAREA':
                        $html .= '<div class="form-group">';
                        if($mandatory == 1){
                            $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                            $html .= '<textarea name="textarea_' . $id . '" rows="4" style="resize:vertical;" class="form-control" id="textarea_' . $id . '" value="'.$answer.'" required></textarea>';
                        }else{
                            $html .= '<label>' . $question . '</label>';
                            $html .= '<textarea name="textarea_' . $id . '" rows="4" style="resize:vertical;" class="form-control" id="textarea_' . $id . '" value="'.$answer.'"></textarea>';
                        }
                        $html .= '</div>';
                        break;
                    case 'DROPDOWN':
                        $html .= '<div class="form-group">';
                        if($mandatory == 1){
                            $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                            $html .= '<p></p><select class="form-control" id="optionSelect" name="optionsDropdown_'.$id.'" required>';
                            foreach ($item->question_options as $option) {
                                $html .= '<option  value="' . $option->id . '"> ' . $option->label . '</option>';
                            }
                            $html .= '</select>';

                        }else{
                            $html .= '<label>' . $question . '</label>';
                            $html .= '<p></p><select class="form-control" id="optionSelect" name="optionsDropdown_'.$id.'">';
                            foreach ($item->question_options as $option) {
                                $html .= '<option  value="' . $option->id . '"> ' . $option->label . '</option>';
                            }
                            $html .= '</select>';
                        }
                        $html .= '</div>';
                        break;
                }

            }else{
                $html .= '<div class="col-sm-12">';

                // Text
                if ($typeId == 1) {
                    $html .= '<div class="form-group">';
                    if($mandatory == 1){
                        $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                        $html .= '<input type="text" name="text_' . $id . '" class="form-control" id="text_' . $id . '"  value="'.$answer.'" required>';
                    }else{
                        $html .= '<label>' . $question . '</label>';
                        $html .= '<input type="text" name="text_' . $id . '" class="form-control" id="text_' . $id . '"  value="'.$answer.'">';
                    }
                    $html .= '</div>';

                }//Radio button
                else if ($typeId == 2) {

                    $html .= '<div class="form-group radioGroup">';
                    if($mandatory == 1){
                        $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                    }else{
                        $html .= '<label>' . $question . '</label>';
                    }

                    if($radioButton == 1){
                        $html .= $this->questionsOptionRadioButton($id, $item->question_options);

                    }else{
                        $html .= $this->questionsOption($id, $item->question_options);
                    }
                    $html .= '</div>';
                } //Text area
                else if ($typeId == 3) {
                    $html .= '<div class="form-group">';
                    if($mandatory == 1){
                        $html .= '<label>' . $question . ' <span style="color:red">*</span></label>';
                        $html .= '<textarea name="textarea_' . $id . '" rows="4" style="resize:vertical;" class="form-control" id="textarea_' . $id . '" required  value="'.$answer.'"></textarea>';
                    }else{
                        $html .= '<label>' . $question . '</label>';
                        $html .= '<textarea name="textarea_' . $id . '" rows="4" style="resize:vertical;" class="form-control" id="textarea_' . $id . '"  value="'.$answer.'"></textarea>';
                    }
                    $html .= '</div>';

                }
                $html .= '</div>';
            }
        }
        return $html;
    }


    public function questionsOption($idQuestion, $options)
    {
        $html = '<div class ="row">';
        $mdCounter = 0;
        $smCounter = 0;


        foreach ($options as $item) {
            $id = $item->id;
            $label = $item->label;

            $html .= '<div class="col-md-3 col-sm-4 col-xs-12">';
            $html .= '<div class="radio">';
            $html .= '<label>';

            $html .= ' <input type="radio" name="optionsRadios_' . $idQuestion . '" id="radio_' . $id . '" value="radio_' . $id . '">';
            $html .= $label;
            $html .= '</label>';

            $html .= '</div>';
            $html .= '</div>';
            $mdCounter++;
            $smCounter++;


            if ($mdCounter % 4 == 0) {
                $html .='<div class="clearfix visible-lg visible-md"></div>';
            }

            if ($smCounter % 3 == 0) {
                $html .='<div class="clearfix visible-sm"></div>';
            }

        }
        $html .= '</div>';

        return $html;
    }




    public function questionsOptionRadioButton($idQuestion, $options)
    {
        $html = '<div class="row" style="padding-left: 15px">';
        $html .= '<div class="btn-group" data-toggle="buttons">';

        foreach ($options as $item) {
            $id = $item->id;
            $label = $item->label;

            $html .= '<label class="btn btn-default">';

            $html .= '<input type="radio" name="optionsRadios_' . $idQuestion . '" id="radio_' . $id . '" value="radio_' . $id . '" autocomplete="off">';
            $html .= $label;
            $html .= '</label>';

        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }



    public function questionsOptionRadioButtonInLine($idQuestion, $options,$required, $answer)
    {

        $html = '<div>';
        $html .= '<div class="btn-group" data-toggle="buttons"> ';
        $i = 0;

        foreach ($options as $item) {
            $id = $item->id;
            $label = $item->label;

            if($answer == $id){
                $html .= '<label class="btn btn-default active" id="radio_label_' . $id . '" title="' . $item->label . '">';
                $html .= '<input type="radio" name="optionsRadios_' . $idQuestion . '" id="radio_' . $id . '" value="radio_' . $id . '" checked autocomplete="off" '.$required.'>';
            }else{
                $html .= '<label class="btn btn-default" id="radio_label_' . $id . '" title="' . $item->label . '">';
                $html .= '<input type="radio" name="optionsRadios_' . $idQuestion . '" id="radio_' . $id . '" value="radio_' . $id . '" autocomplete="off" '.$required.'>';
            }

            if($item->icon != null){
                $html.= '<img src="'.URL::action("FilesController@download",[$item->icon->file_id, $item->icon->file_code, 1]).'"   id="questionOptionImage" style="height:50px">';
            }
            else{
                $html .= $label;
            }

            $html .= '</label>';
            $i++;
        }

        $html .= '</div>';
        $html .= '</div>';


        return $html;
    }


    public function questionsOptionCheckBoxInLine($idQuestion, $options,$required, $answer)
    {

        $html = '<div>';
        $i = 0;

        foreach ($options as $item) {
            $html .= '<div class="checkbox"> ';

            $id = $item->id;
            $label = $item->label;
            $html .= '<label id="radio_label_' . $id . '" title="' . $item->label . '">';

            if($item->icon != null){
                $html.= '<img src="'. URL::action("FilesController@download",[$item->icon->file_id, $item->icon->file_code, 1]).'"  id="questionOptionImage" style="height:50px">';
                if($answer == $id) {
                    $html .= '<input type="checkbox" name="optionsCheck_' . $idQuestion . '[]" id="check_' . $id . '" value="check_' . $id . '" autocomplete="off" '.$required.'>';
                }else{
                    $html .= '<input type="checkbox" name="optionsCheck_' . $idQuestion . '[]" id="check_' . $id . '" value="check_' . $id . '" autocomplete="off" '.$required.' checked>';
                }            }
            else{
                if($answer == $id) {
                    $html .= '<input type="checkbox" name="optionsCheck_' . $idQuestion . '[]" id="check_' . $id . '" value="check_' . $id . '" autocomplete="off" '.$required.'>';
                }else{
                    $html .= '<input type="checkbox" name="optionsCheck_' . $idQuestion . '[]" id="check_' . $id . '" value="check_' . $id . '" autocomplete="off" '.$required.' checked>';
                }                $html .= $label.'';
            }

            $html .= '</label>';
            $i++;
            $html .= '</div>';

        }

        $html .= '</div>';
        return $html;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QRequest $request
     * @return $this|View
     * @internal param CBRequest $requestCB
     */
    public function store(QRequest $request)
    {
        $requestForm = $request->all();
        $questions = [];

        foreach($requestForm as $question => $answer){
            if(strpos($question, "text_") !== false){
                $questions[] = [
                    'question_id' => str_replace('text_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsRadios_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsRadios_', '', $question),
                    'question_option_id' => str_replace('radio_', '', $answer),
                ];
            }
            else if(strpos($question, "textarea_") !== false){
                $questions[] = [
                    'question_id' => str_replace('textarea_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsCheck_") !== false){

                foreach ($answer as $checkOption){
                    $newAnswer [] = str_replace('check_', '', $checkOption);
                }
                $questions[] = [
                    'question_id' => str_replace('optionsCheck_', '', $question),
                    'question_option_id' => $newAnswer
                ];
            }
            else if(strpos($question, "optionsDropdown_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsDropdown_', '', $question),
                    'question_option_id' => $answer
                ];
            }
        }
        try {
            Questionnaire::setFormReply($question, true, null, $questions);
            $message = 0;
            return view('public.'.ONE::getEntityLayout().'.questionnaire.index', compact('message'));

        } catch (Exception $e) {
            //TODO: save inputs
            return redirect()->back()->withErrors(["form.store" => $e->getMessage()]);
        }
    }



    /**
     * Store a step newly created resource.
     *
     * @param QRequest $request
     * @return $this|View
     * @internal param CBRequest $requestCB
     */
    public function storeStep(QRequest $request)
    {
        $requestForm = $request->all();
        $questions = [];

        foreach($requestForm as $question => $answer){
            if(strpos($question, "text_") !== false){
                $questions[] = [
                    'question_id' => str_replace('text_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsRadios_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsRadios_', '', $question),
                    'question_option_id' => str_replace('radio_', '', $answer),
                ];
            }
            else if(strpos($question, "textarea_") !== false){
                $questions[] = [
                    'question_id' => str_replace('textarea_', '', $question),
                    'answer' => $answer
                ];
            }
            else if(strpos($question, "optionsCheck_") !== false){

                foreach ($answer as $checkOption){
                    $newAnswer [] = str_replace('check_', '', $checkOption);
                }
                $questions[] = [
                    'question_id' => str_replace('optionsCheck_', '', $question),
                    'question_option_id' => $newAnswer
                ];
            }
            else if(strpos($question, "optionsDropdown_") !== false){
                $questions[] = [
                    'question_id' => str_replace('optionsDropdown_', '', $question),
                    'question_option_id' => $answer
                ];
            }
        }

        try {
            Questionnaire::storeFormReply($request->questionnaire_id, $questions);
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }





}
