<div style="float: left">
    <a class="left carousel-control" id="leftBtn" style="width: 5%;position: fixed" onclick="location.href='{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>($id-1),'lang' => $lang]) }}'" role="button">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true" style="color: black"></span>
        <span class="sr-only">{{trans('empavillePresentation.previous')}}</span>
    </a>
</div>