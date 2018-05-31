<div class="box-private">
    {!! $form or null !!}

    @if(!empty($title) || !empty($title_button))
        <div class="box-header">
            <h3 class="box-title">{!! $title or null  !!}</h3>
            <div class="box-tools pull-right">
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

