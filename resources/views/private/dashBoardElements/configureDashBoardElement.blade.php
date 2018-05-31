
<div class="form-group">
@foreach($configurations as $configuration)
    <div>
        @include('private.dashBoardElements.configurationAccordingToType',['configuration' => $configuration])
    </div>
@endforeach
</div>