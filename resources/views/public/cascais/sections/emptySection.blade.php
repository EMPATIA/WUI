@if(Session::has("SITE-CONFIGURATION.show_editable_sections") && Session::get("SITE-CONFIGURATION.show_editable_sections") == true)
    <a href="{{ action('ContentManagerController@show', ["contentType" => 'pages', "content_key" => $content->content_key,"version"=>"","siteKey"=>Session::get('X-SITE-KEY')]) }}">
        <div class="editable-section">
            <em class="fa fa-plus fa-5x"></em>
        </div>
    </a>
@endif
