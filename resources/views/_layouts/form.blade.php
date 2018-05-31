<div class="box box-primary">
    {!! $form or null !!}

    @if(isset($title))
        <div class="box-header with-border">
            <h3 class="box-title">{!! $title !!}</h3>

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