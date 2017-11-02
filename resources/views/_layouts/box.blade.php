<div class="box box-primary">
    @if(!isset($header) || $header)
    <div class="box-header with-border">
        <h3 class="box-title">@yield('box_title')</h3>

        <div class="box-tools pull-right">
            @yield('box_title_buttons')
        </div>
    </div>
    @endif

    @if(!isset($body) || $body)
    <div class="box-body">
        @yield('box_body')
    </div>
    @endif

    @if(!isset($footer) || $footer)
    <div class="box-footer">
        @yield('box_footer')
    </div>
    @endif

</div>