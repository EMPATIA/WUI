<?php
$projects = $space->getNodes("projects");
$markers = array();
?>
<div class="projects-list">
<div class="row">
@foreach($projects as $p)
<?php $files = $space->getAttribute($p,'files');?>
<div class="col-xs-12 col-sm-6 col-md-4">       
<div class="card-project">
<a href="{{action('SecondCycleController@show',["cbKey" => $cbKey,"level" => "projects", "topicKey" => $p])}}">
<div class="img-card-project" @if (isset($files->images)) style="background-image: url('{{action('FilesController@download', [$files->images[0]->file_id, $files->images[0]->file_code])}}');"@else style="background-image: url('{{asset("images/empatia/default_img_contents.jpg")}}')" @endif > </div>
</a>
<h2 class="title-card-project"><a href="{{action("SecondCycleController@show",["cbKey" => $cbKey,"level" => "projects", "topicKey" => $p])}}">{{$space->getAttribute($p,'title')}}</a></h2>
<ul class="list-subprojects">
@foreach($space->getLinks($p) as $i)
<li class="subproject"><h3><a href="{{action("SecondCycleController@show",["cbKey" => $cbKey,"level" => "subprojects", "topicKey" => $i])}}">{{$space->getAttribute($i,'title')}}</a></h3></li>
<?php 
        $coords = $space->getAttribute($i,'location');
        if ($coords != null){
                $markers[] = array_merge(array("<div><h1><a href=\"".action("SecondCycleController@show",["cbKey" => $cbKey,"level" => "subprojects", "topicKey" => $i])."\">".$space->getAttribute($i,'title')."</a></h1></div>"),explode(",",$coords));
        }
?>
@endforeach
</ul>
</div>
</div>
@endforeach
</div>
</div>
            <div class="margin-top">
                {!! Form::oneMapsLocations("mapsSubProjects", "", $markers, array("markerIcon" => asset('images/default/pins/construction.png'), "zoom" => 13, "folderIcons" => "/images/default/pins/", "defaultLocation" => ((isset($markers[0]))?"{$markers[0][1]}, {$markers[0][2]}":"40.20103490981625, -8.411235809326172"), "style" => "height:400px;width:100%;"), array("select" => "geometry", "from" => "1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk", "where" => "ISO_2DIGIT IN ('PT')")) !!}
	    </div>

