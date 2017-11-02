<div id="votesGroupDiv_{{ $voteCounter }}" class="box votesGroupDiv" style="display:none;">

    <div class="card flat">
        <div id='parameter' class="card-header">
            <span id="cbVoteTitle_{{ $voteCounter }}" style="font-weight:bold;">{!! trans("privateCbs.vote") !!}</span> 
            <a class="btn btn-flat btn-danger btn-sm pull-right" onclick="removeVote({{ $voteCounter }})" data-original-title="Delete"><i class="fa fa-remove"></i></a>
        </div>
        <div class="box-body">      
    
            <div class="form-group">
                <label for="voteName_{{ $voteCounter }}">{{ trans('privateCbs.voteName') }}</label>
                <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_name") }}</span>
                <input class="form-control" id="voteName_{{ $voteCounter }}" required="required" name="voteName_{{ $voteCounter }}" type="text" onchange="changeVoteTitle({{ $voteCounter }})" onfocusout="changeVoteTitle({{ $voteCounter }})">
            </div>

            <div class="form-group">
                <label for="voteStartDate_{{ $voteCounter }}">{{ trans('privateCbs.voteStartDate') }}</label>
                <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_start_date") }}</span>                <div class="input-group date">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                    <input id="voteStartDate_{{ $voteCounter }}" class="form-control oneDatePicker" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="voteStartDate_{{ $voteCounter }}" type="text">
                </div>
            </div>

            <div class="form-group">
                <label for="voteStartTime_{{ $voteCounter }}">{{ trans('privateCbs.voteStartTime') }} </label>
                <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_start_time") }}</span>
                <div class="input-group time"><span class="input-group-addon">
                        <i class="glyphicon glyphicon-time"></i></span><input id="voteStartTime_{{ $voteCounter }}" class="form-control oneTimePicker" name="voteStartTime_{{ $voteCounter }}" type="text">
                </div>
            </div>

            <div class="form-group">
                <label for="voteEndDate_{{ $voteCounter }}">{{ trans('privateCbs.voteEndDate') }}</label>
                <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_end_date") }}</span>

                <div class="input-group date">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                    <input id="voteEndDate_{{ $voteCounter }}" class="form-control oneDatePicker" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="voteEndDate_{{ $voteCounter }}" type="text">
                </div>
            </div>

            <div class="form-group">
                <label for="voteEndTime_{{ $voteCounter }}">{{ trans('privateCbs.voteEndTime')}}</label>
                <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_end_time") }}</span>
                <div class="input-group time">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span><input id="voteEndTime_{{ $voteCounter }}" class="form-control oneTimePicker" name="voteEndTime_{{ $voteCounter }}" type="text">
                </div>
            </div>


            <div class="card flat">
                <div class="card-header">{{ trans("privateCbs.configurations")}}
                </div>
                <div class="box-body">
                    <div class='form-group'>
                        <label for="methodGroupSelect_{{ $voteCounter }}">{{ trans("privateCbs.voteTypes")}}</label>
                        <select class="form-control" id="methodGroupSelect_{{ $voteCounter }}" name="methodGroupSelect_{{ $voteCounter }}"
                                onchange="showMethods({{ $voteCounter }})" required>
                            <option value="">{{ trans("privateCbs.select_vote_type")}}</option>
                            @if(!empty($methodGroup))
                                @foreach($methodGroup as $group)
                                    <option value="{{$group->id}}">{{$group->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div id="methodsDiv_{{ $voteCounter }}">
                    </div>
                </div>
                <div id="configurationsDiv_{{ $voteCounter }}" class="box-body">
                </div>
            </div>
            
        </div>
</div>    