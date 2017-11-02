<div class="box box-solid oneFormBox">
    {!! $form or null !!}

    @if(isset($title))
        <div class="box-header oneFormHeader">
            {{--<h3 class="oneFormTitle">{!! $title !!}</h3>--}}

            <div class="box-tools pull-right oneFormButtons" >
                {!! $title_button or null !!}
            </div>
        </div>
    @endif

    <div class="box-body oneFormBody">
        {!! $body !!}
    </div>

    @if(isset($submit))
        <div class="box-footer oneFormSubmit">
            {!! $submit or null !!} {!! $cancel or null !!}
        </div>
    @endif


    @if(isset($back))
        <div class="box-footer oneFormBack">
            {!! $back or null !!}
        </div>
    @endif

    {!! $form_close or null !!}

    @if (isset($deleteSettings) && $deleteSettings != null)
        @include("_layouts.deleteModal",$deleteSettings)
    @endif

</div>