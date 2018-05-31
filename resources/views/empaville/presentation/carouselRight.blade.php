<div style="float: right">
    <a class="right carousel-control" style="width: 5%;position: fixed" id="rightBtn" onclick="location.href='{{ action('EmpavillePresentationController@next',['cbKey'=> $cbKey, 'id' =>($id+1),'lang' => $lang]) }}'" role="button">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true" style="color: black"></span>
        <span class="sr-only">{{trans('empavillePresentation.next')}}</span>
    </a>
</div>