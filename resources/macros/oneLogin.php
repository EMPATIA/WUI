<?php

//Display Login for code

Html::macro('oneLoginCode', function() {
    $html = "";

    $html .= "<form action='".URL::action('AuthController@verifyLoginCode')."'method=\"POST\">" ;
    $html .='<input type="hidden" name="_token" value="'. csrf_token().' ">';
    $html.= csrf_field();
    $html .='<div class="form-group has-feedback">';
    $html .='<input type="text" name="code" class="form-control" placeholder="'.trans('login.yourCode').'">';
    $html .='<span class="glyphicon glyphicon-lock form-control-feedback"></span>';
    $html .='</div>';
    $html .='<div class="row">';
    $html .='<div class="col-xs-6"></div>';
    $html .='<div class="col-xs-6">';
    $html .='<button type="submit" class="btn btn-block btn-flat" style="background-color: #62a351; color:white">Enter</button>';
    $html .='</div>';
    $html .='</div>';
    $html .='</form>';

    return $html;
});

Html::macro('oneLoginCodeWithParams', function($formAction, $method, $btnClasses, $layout) {
    $html = "";

    $html .= '<form action="'.URL::action(''.$formAction.'').'"method="'.$method.'">' ;
    $html .= '<input type="hidden" name="_token" value="'. csrf_token().' \">';
    $html .= csrf_field();
    $html .='<div class="form-group has-feedback">';
    $html .='<input type="text" name="code" class="form-control" placeholder="'.trans(''.$layout.'.your_code').'">';
    $html .='<span class="glyphicon glyphicon-lock form-control-feedback"></span>';
    $html .='</div>';
    $html .='<div class="row">';
    $html .='<div class="col-xs-12">';
    $html .='<button type="submit" class="'.$btnClasses.'">'.trans(''.$layout.'.enter').'</button>';
    $html .='</div>';
    $html .='</div>';
    $html .='</form>';

    return $html;
});


Html::macro('oneLoginFacebook', function() {
    $html = "";

    $html .= '<a href="'. action('AuthSocialNetworkController@redirectToFacebook').'" class="btn btn-block btn-social btn-facebook btn-flat" ><i class="fa fa-facebook"></i>'.trans('auth.signInUsingFacebook').'</a>' ;

    return $html;
});