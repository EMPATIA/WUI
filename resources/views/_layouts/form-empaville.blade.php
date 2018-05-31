<div class="box-empaville">
    {!! $form or null !!}

    @if(isset($title))
        <div class="box-header " style="color: #ffffff; background-color: #333333;">
            <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;">{!! $title !!}</h3>

            <div class="box-tools pull-right" >
                {!! $title_button or null !!}
            </div>
        </div>
    @endif

    <div class="box-body">
        {!! $body !!}
    </div>

    @if(isset($submit))
        <div class="box-footer">
            {!! $submit or null !!} {!! $cancel or null !!}
        </div>
    @endif

    @if(isset($back))
        <div class="box-footer">
            {!! $back or null !!}
        </div>
    @endif

    {!! $form_close or null !!}

    @if (isset($deleteSettings) && $deleteSettings != null)
        @include("_layouts.deleteModal",$deleteSettings)
    @endif

</div>