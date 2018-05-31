<?php

namespace App\One;

use Form;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Session;

//use ONE;

class OneForm
{
    private $name = null;
    private $module = null;
    private $moduleType = null;
    private $type = null;
    private $layout = null;
    private $title = null;
    private $body = '';
    private $form;

    private $id;
    private $model;
    private $controller;
    private $deleteSettings;
    private $active;
    private $btn = ['show' => true, 'edit' => true, 'delete' => true];
    private $contents = array();
    private $arrTmp = array();
    private $buffer;

    public function __construct($name, $module=null, $moduleType=null, $type, $layout, $title)
    {
        $this->name = $name;
        $this->module = $module;
        $this->moduleType = $moduleType;
        $this->type = $type;
        $this->layout = $layout;
        $this->title = $title;

        $this->active = 1;
        $this->deleteSettings = array();
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function settings($settings = array(), $options = array())
    {

        if (isset($settings["model"])) {
            $this->model = $settings["model"];
            if (!isset($settings["id"])) {
                $this->id = $this->model->id;
            }
            /*try {
                $this->active = !($this->model->trashed());
            } catch (\Exception $ex) {
                $this->active = 1;
                $this->btn['delete'] = false;
            }*/
        }

        if (isset($settings["controller"])) {
            $this->controller($settings["controller"], $options);
        }

        if (isset($settings["id"])) {
            $this->id = $settings["id"];
        }

//        if (isset($settings["deleteSettings"])) {
//            $this->deleteSettings = $settings["deleteSettings"];
//        }

        return $this;
    }

    
    private function controller($controller, $options = array())
    {
        $this->controller = $controller;

        if (!isset($options["controller"]["show"]) || $options["controller"]["show"] != false) {
            $this->show($controller . "@edit", $controller . "@destroy");
        }
        if (!isset($options["controller"]["edit"]) || $options["controller"]["edit"] != false) {
            $this->edit($controller . "@update", $controller . "@show");
        }

        if ($this->active) {
            if (!isset($options["controller"]["delete"]) || $options["controller"]["delete"] != false) {
                $this->delete($controller . "@delete");
            }

            if (!isset($options["controller"]["create"]) || $options["controller"]["create"] != false) {
                $this->create($controller . '@store', $controller . '@index');
            }
        }

        return $this;
    }

    public function show($editAction, $deleteAction, $params = [], $backAction = null, $backParams = null, $topicEditPermission = null, $topicDeletePermission = null)
    {
        $arrBtn = array();
        $module = $this->module;
        $moduleType = $this->moduleType;

        if ($this->type == 'show') {
            if ($this->active) {
                if(!is_null($editAction)) {

                    if(Session::get('user_role') == 'admin' || (!is_null($this->module))){
                        if(!is_null($topicEditPermission)){
                            if($topicEditPermission == 1 || Session::has('user_permissions') == false)
                                $arrBtn['edit'] = $editAction;
                        }else{
                            $arrBtn['edit'] = $editAction;
                        }
                    }
                }
                if ($this->btn['delete'] && !is_null($deleteAction)) {
                    if(Session::get('user_role') == 'admin' || (!is_null($this->module))){
                        if(!is_null($topicDeletePermission)){
                            if($topicDeletePermission == 1 || Session::has('user_permissions') == false)
                                $arrBtn['delete'] = $deleteAction;
                        }else{
                            $arrBtn['delete'] = $deleteAction;
                        }
                    }
                }
            }

            $this->form['show'] = [
                'name' => $this->name,
                'title' => $this->title,
                'deleteSettings' => $this->deleteSettings,
                'title_button' => ONE::actionButtons($params, ['form' => $this->name] + $arrBtn),
                'back' => !is_null($backAction) ? '<a href="' . action($backAction, $backParams) . '" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i> ' . trans('form.back') . '</a>' : null,
            ];
        }

        return $this;
    }

    public function edit($updateAction, $cancelAction, $params = [])
    {
        if ($this->type == 'edit')
            $this->form['edit'] = [
                'title' => $this->title,
                'form' => Form::model($this->model, ['method' => 'PATCH', 'url' => action($updateAction, $params), 'name' => $this->name, 'id' => $this->name]),
                'form_close' => Form::close(),
                'submit' => Form::submit(trans('form.save'), ['class' => 'btn btn-flat empatia']),
                //'cancel' => Form::button(trans('form.cancel'), ['class' => 'btn btn-flat btn-default', 'onclick' => "location.href='" . action($cancelAction, $params) . "'"]),
                'cancel' =>isset($cancelAction) ?'<a href="' . action($cancelAction, $params) . '" class="btn btn-flat btn-default"> ' . trans('form.cancel') . '</a>': null,
            ];
        return $this;
    }

    public function create($createAction, $cancelAction, $params = [])
    {
        if ($this->type == 'create')
            $this->form['create'] = [
                'title' => $this->title,
                'form' => Form::open(['action' => $this->getActionWithParams($createAction, $params), 'name' => $this->name, 'id' => $this->name]),
                //'form' => Form::open(['action' => array($createAction, isset($params['id']) ? $params['id'] : null), 'name' => $this->name, 'id' => $this->name]),
                'form_close' => Form::close(),
                'submit' => Form::submit(trans('form.create'), ['class' => 'btn btn-flat empatia']),
                //'cancel' => Form::button(trans('form.cancel'), ['class' => 'btn btn-flat btn-default', 'onclick' => "location.href='" . action($cancelAction, $params) . "'"]),
                'cancel' => isset($cancelAction) ? '<a href="' . action($cancelAction, $params) . '" class="btn btn-flat btn-default"> ' . trans('form.cancel') . '</a>' : null,
            ];

        return $this;
    }


    //TODO: Improve this!!!
    public function getActionWithParams($action, $params)
    {
        $array = array();
        $array[] = $action;

        foreach (array_values($params) as $param){
            $array[] = $param;
        }

        return $array;
    }

    public function delete($deleteAction, $settings = array())
    {

        $this->deleteSettings += $settings;
        if (!isset($this->deleteSettings["action"])) {
            $this->deleteSettings["action"] = action($deleteAction, $this->id);
        }
        return $this;
    }

    public function addExtraButton($selectedId, $actions)
    {
        if ($this->type == "show") {
            $newButton = '';
            $keys = array_keys($actions);
            for ($i = 0; $i < count($actions); $i++) {
                if ($keys[$i] != $selectedId) {
                    $newButton .= ' ' . $actions[$keys[$i]];
                }
            }
            $this->form[$this->type]["title_button"] = $newButton . " " . $this->form[$this->type]["title_button"];
        }
        return $this;

    }


    public function addField($name, $label, $input, $value, $noTop = false)
    {
        if ($this->type == 'create' || $this->type == 'edit')
            $this->addFieldEdit($name, $label, $input);
        else
            $this->addFieldShow($label, $value, $noTop);

        return $this;
    }

    private function addFieldShow($label, $value, $noTop)
    {
        $html = "<dt>" . $label . "</dt>";
        $html .= "<dd> " . $value . " </dd>";

        if ($this->body != "" && !$noTop) {
            $html = "<hr style='margin: 10px 0 10px 0'>" . $html;
        }

        $this->body .= $html;
    }

    private function addFieldEdit($name, $label, $input)
    {
        $e = "";

        if (Session::has('errors')) {
            $errors = Session::get('errors');
        }

        if (Session::has('errors') && $errors->has($name)) {
            $e = "has-error";
        }
        $html = '<div class="form-group ' . $e . '">';
        $html .= Form::label($name, $label);
        $html .= $input;

        if (Session::has('errors') && $errors->has($name)) {
            $html .= '<p class="help-block">' . $errors->first($name) . '</p>';
        }
        $html .= '</div>';

        $this->body .= $html;
    }

    public function addSelectEditCreate($name, $label, $input, $value, $noTop = false)
    {
        if ($this->type != "show") {
            $e = "";

            if (Session::has('errors')) {
                $errors = Session::get('errors');
            }

            if (Session::has('errors') && $errors->has($name)) {
                $e = "has-error";
            }
            $html = '<div class="form-group ' . $e . '">';
            $html .= Form::label($name, $label);
            $html .= $input;

            if (Session::has('errors') && $errors->has($name)) {
                $html .= '<p class="help-block">' . $errors->first($name) . '</p>';
            }
            $html .= '</div>';

            $this->body .= $html;
        } else {
            $html = "<dt>" . $label . "</dt>";
            $html .= "<dd> " . $value . " </dd>";

            if ($this->body != "" && !$noTop) {
                $html = "<hr style='margin: 10px 0 10px 0'>" . $html;
            }

            $this->body .= $html;
        }

        return $this;
    }

    public function startFormGroup($title)
    {
        $html = "<div id='block-form-group' class='one-form-group'>";
        $html .= "<div id='title-form-group' class='one-form-group-title'>" . $title . "</div>";
        $html .= "<div id='body-form-group' class='one-form-group-body'>";

        $this->body .= $html;

        return $this;
    }

    public function endFormGroup()
    {
        $this->body .= "</div> </div>";

        return $this;
    }

    public function addHTML($input)
    {
        $this->body .= $input;

        return $this;
    }

    public function open()
    {
        session(['oneForm' => $this->name]);
        ob_start();
        Form::setModel($this->model);
        echo Form::hidden('form_name', $this->name);
        return $this;
    }


    private function cleanStream()
    {
        ob_end_clean();
        ob_start();
    }

    private function saveStream()
    {
        $this->body .= ob_get_contents();
        $this->cleanStream();
    }

    public function makeTabs()
    {
        $this->closeTab();
        if(!empty($this->arrTmp['tabs'])) {
            echo Form::oneTabs($this->arrTmp['tabs']);
            unset($this->arrTmp['tabs']);
        }
        return $this;
    }

    private function closeTab()
    {
        if (isset($this->arrTmp['tabs']['id'])) {
            $idTab = $this->arrTmp['tabs']['id'];
            $this->arrTmp['tabs'][$idTab]['html'] = ob_get_contents();
            $this->cleanStream();
            unset($this->arrTmp['tabs']['id']);
        }

    }

    public function openTabs($id, $title, $options = array())
    {

        if (!isset($this->arrTmp['tabs'])) {
            $this->saveStream();
            $this->arrTmp['tabs'] = array();
        }

        $this->closeTab();

        $this->arrTmp['tabs']['id'] = $id;
        $this->arrTmp['tabs'][$id] = ['title' => $title, 'options' => $options, 'html' => ''];
        return $this;
    }

    public function openGroup($title, $options = array())
    {
        $this->saveStream();
        $this->arrTmp['group'] = ['title' => $title, 'options' => $options, 'body' => ''];
        return $this;
    }

    public function makeGroup()
    {
        $this->arrTmp['group']['body'] = trim(ob_get_contents());
        $this->cleanStream();
        if ($this->arrTmp['group']['body'] != "") {
            echo Form::oneGroup($this->arrTmp['group']);
        }
        unset($this->arrTmp['group']);
        return $this;
    }

    public function close()
    {

        $this->saveStream();
        session()->forget('oneForm');

    }

    public function make()
    {
        $this->body .= ob_get_contents();
        ob_end_clean();

        if ($this->type == 'show') $this->body = "<dl>" . $this->body . "</dl>";

        return view($this->layout, $this->form[$this->type])->with('body', $this->body);
    }

}