@extends('private._private.index')

@section('content')
    <div class="content-header">
        <h1>{{trans('privateTechnicalAnalysis.the_topic').' '.$topicTitle.' '.trans('privateTechnicalAnalysis.does_not_have_technical_analysis')}}</h1>
    </div>
    <br>
    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title">{{trans('privateTechnicalAnalysis.technical_analysis')}}</h3>
        </div>
        <div class="box-body">
            <a href="{{ action('TechnicalAnalysisController@create', ['type'=>$type,'cbKey'=>$cbKey, 'topicKey' => $topicKey]) }}" class="btn btn-flat btn-submit" data-toggle="tooltip" data-delay="{&quot;show&quot;:&quot;1000&quot;}" title="" data-original-title="{{trans("privateTechnicalAnalysis.create_technical_analysis")}}"><i class="fa fa-plus" style="margin-right: 5px"></i>{{trans("privateTechnicalAnalysis.create_technical_analysis")}}</a>
        </div>
    </div>

    <div class="action-btn-container">
        <a href="{{action('TopicController@show',['type' => $type,'cbKey' => $cbKey,'topicKey' => $topicKey])}}" class="btn btn-secondary btn-flat back-btn"><i class="fa fa-arrow-left"></i> {{trans('privateTechnicalAnalysis.back')}}</a>
    </div>

@endsection

@section('scripts')
    <script>
        $(function() {
            var array = ["{{ isset($type) ? $type : null }}", "{{isset($cbKey) ? $cbKey : null}}", "{{isset($topicKey) ? $topicKey : null}}"]
            getSidebar('{{ action("OneController@getSidebar") }}', 'technicalAnalysis', array, 'topics'  );
        });
    </script>
@endsection