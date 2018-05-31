@extends('private._private.index')

@section('header_styles')
    <link href="{!! asset(elixir('css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')) !!}" rel="stylesheet"
          type="text/css"/>
@endsection
@section('header_scripts')
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/moment-with-locales.js')) !!}"></script>
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/bootstrap-datetimepicker.js')) !!}"></script>
@endsection

@section('content')
    @include('private.cbs.tabs')

    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment" style="margin-left:5px; margin-top:5px;">{{trans('privateCbs.pad')}}</label>  {{$cb->title}}<br></p>
        <p><label for="contentStatusComment" style="margin-left:5px;">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $author->user_key, 'role' => $author->role ?? null])}}">
                {{$author->name}}
            </a>
            <br>
        </p>
        <p><label for="contentStatusComment" style="margin-left:5px; margin-bottom:5px;">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>


    <div class="margin-top-20">
        @php
        $form = ONE::form('cbsConfigurations', trans('privateTopic.details'), 'cb', 'configurations')
            ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
            ->show('CbsController@editConfigurations', null,['type' => isset($type) ? $type : null, 'cbKey' =>isset($cb) ? $cb->cb_key : null], null)
            ->create('TopicController@store', 'CbsController@show' , ['type'=> $type, 'cbKey' => isset($cb) ? $cb->cb_key : null])
            ->edit('CbsController@update', 'CbsController@showConfigurations', ['type' => $type,'cbKey' =>isset($cb) ? $cb->cb_key : null, 'configurations_flag' => 1])
            ->open();
        @endphp

            {!! Form::hidden('title', isset($cb) ? $cb->title : null) !!}
            {!! Form::hidden('description', isset($cb) ? $cb->contents : null) !!}
            {!! Form::hidden('tag', isset($cb) ? $cb->tag : null) !!}
            {!! Form::hidden('start_date', isset($cb) ? $cb->start_date : date('Y-m-d')) !!}
            {!! Form::hidden('end_date', isset($cb) && $cb->end_date!=null ? $cb->end_date  : '') !!}
            {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}
            {!! Form::hidden('parent_cb_id', isset($cb) ? $cb->parent_cb_id : 0, ['id' => 'parent_cb_id']) !!}

        @if(Session::get('user_role') == 'admin')
            <!-- CB Configurations -->
            <div class="card flat">
                <div class="card-title" style="padding:10px">
                    {{trans('privateCbs.configurations')}}
                </div>
                <div class="card-body">
                    @foreach($configurations as $configuration)
                        <div class="col-12" style="padding-left: 0px;">
                            <div class="card flat">
                                <div class="card-header">
                                    <a class="collapsed block accordion-header" role="button" data-toggle="collapse"
                                       href="#collapse_{{$configuration->id}}" aria-expanded="false" aria-controls="collapse_{{$configuration->id}}">
                                        {{$configuration->title}}
                                    </a>
                                </div>
                                <div id="collapse_{{$configuration->id}}" class="panel-collapse collapse show" role="tabpanel">
                                    <div class="card-body">
                                        @foreach($configuration->configurations as $option)
                                            {!! Form::oneSwitch('configs['.$configuration->code.'][]',$option->title, in_array($option->id, (isset($cbConfigurations[$option->code]) ? array_keys($cbConfigurations[$option->code]) : []) ) , array("groupClass"=>"row", "labelClass" => "col-12", "switchClass" => "col-12", "value" => $option->id, "id" => "configuration_".$option->id ) ) !!}
                                        @endforeach
                                    </div>
                                    @if($configuration->code == "general_configurations")
                                        <div class="card-body">
                                            <div class="row" style="margin-bottom: 10px">
                                                <div class="col-3">
                                                                                        
                                                    <!-- Start Submit Proposal -->
                                                    
                                                    <label for="startSubmitProposal">{{trans('privateCbs.start_submit_proposal')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.startSubmitProposalDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='startSubmitProposal' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="start_submit_proposal" @if(isset($cb->start_submit_proposal)) value="{{$cb->start_submit_proposal}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <!-- End Submit Proposal -->
                                                    
                                                    <label for="endSubmitProposal">{{trans('privateCbs.end_submit_proposal')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.endSubmitProposalDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='endSubmitProposal' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="end_submit_proposal" @if(isset($cb->end_submit_proposal)) value="{{$cb->end_submit_proposal}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 10px">
                                                <div class="col-3">
                                            
                                                    <!-- Start Technical Analysis -->
                                                    
                                                    <label for="startTA">{{trans('privateCbs.start_technical_analysis')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.startTechnicalAnalysisDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='startTA' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="start_technical_analysis" @if(isset($cb->start_technical_analysis)) value="{{$cb->start_technical_analysis}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                            
                                                    <!-- End Technical Analysis -->
                                                    
                                                    <label for="endTA">{{trans('privateCbs.end_technical_analysis')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.endTechnicalAnalysisDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='endTA' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="end_technical_analysis" @if(isset($cb->end_technical_analysis)) value="{{$cb->end_technical_analysis}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 10px">
                                                <div class="col-3">

                                                    <!-- Start Complaint -->
                                                    
                                                    <label for="startComplaint">{{trans('privateCbs.start_complaint')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.startComplaintDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='startComplaint' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="start_complaint" @if(isset($cb->start_complaint)) value="{{$cb->start_complaint}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                            
                                                    <!-- End Complaint -->
                                                    
                                                    <label for="endComplaint">{{trans('privateCbs.end_complaint')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.endComplaintDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='endComplaint' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="end_complaint" @if(isset($cb->end_complaint)) value="{{$cb->end_complaint}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 10px">
                                                <div class="col-3">

                                                    <!-- Start Vote -->
                                                    
                                                    <label for="startVote">{{trans('privateCbs.start_vote')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.startVoteDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='startVote' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="start_vote" @if(isset($cb->start_vote)) value="{{$cb->start_vote}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                            
                                                    <!-- End Vote -->
                                                    
                                                    <label for="endVote">{{trans('privateCbs.end_vote')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.endVoteDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='endVote' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="end_vote" @if(isset($cb->end_vote)) value="{{$cb->end_vote}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 10px">
                                                <div class="col-3">

                                                    <!-- Start Show Results -->
                                                    
                                                    <label for="startShowResults">{{trans('privateCbs.start_show_results')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.startShowResultsDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='startShowResults' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="start_show_results" @if(isset($cb->start_show_results)) value="{{$cb->start_show_results}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                            
                                                    <!-- End Show Results -->
                                                    
                                                    <label for="endShowResults">{{trans('privateCbs.end_show_results')}}</label>
                                                        <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateCbs.endShowResultsDescription')}}</span>
                                                    <br>
                                                    <div class="form-group">
                                                        <div class='input-group date' id='endShowResults' style="max-width: 300px">
                                                            <input type='text' class="form-control" name="end_show_results" @if(isset($cb->end_show_results)) value="{{$cb->end_show_results}}" @endif @if(ONE::actionType('cbsConfigurations') == 'show') disabled @endif/>
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar">
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {!! $form->make() !!}

    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#startSubmitProposal').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#endSubmitProposal').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#startTA').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#endTA').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#startComplaint').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#endComplaint').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#startVote').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#endVote').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#startShowResults').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
            $('#endShowResults').datetimepicker({
            useCurrent: false, //Important! See issue #1075

            format: 'YYYY-MM-DD HH:mm:ss',
            });
        });
    </script>
@endsection
