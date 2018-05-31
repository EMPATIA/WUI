@extends('private._private.index')

@section('header_styles')
    <style>
        .adv-search{
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            border: 0;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #f4f4f5;
        }
        .select2levelsNeeded{
            width: 80%;
        }
    </style>
@endsection

@section('content')
    <div class="card flat topic-data-header">
        <p><label for="contentStatusComment">{{trans('privateCbs.pad')}}</label>  {{$cb->title ?? null}}<br></p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
            <br>
        </p>
        @endif
        <p><label for="contentStatusComment">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>

    <div class="" style="margin-top: 25px">
    @php
    $form = ONE::form('securityConfigurations', trans('privateTopic.details'), 'cb', 'security_configurations')
        ->settings(["model" => isset($topic) ? $topic : null, 'id'=>isset($topic) ? $topic->topic_key : null])
        ->show('CbsController@editSecurityConfigurations', null,['type' => isset($type) ? $type : null, 'cbKey' =>isset($cb) ? $cb->cb_key : null], null)
        ->create('TopicController@store', 'CbsController@show' , ['type'=> $type, 'cbKey' => isset($cb) ? $cb->cb_key : null])
        ->edit('CbsController@updateSecurityConfigurations', 'CbsController@showSecurityConfigurations', ['type' => $type,'cbKey' =>isset($cb) ? $cb->cb_key : null])
        ->open();
    @endphp

    {!! Form::hidden('title', $cb->title ?? null) !!}
    {!! Form::hidden('description', isset($cb) ? $cb->contents : null) !!}
    {!! Form::hidden('start_date', isset($cb) ? $cb->start_date : date('Y-m-d')) !!}
    {!! Form::hidden('end_date', isset($cb) && $cb->end_date!=null ? $cb->end_date  : '') !!}
    {!! Form::hidden('cb_key', isset($cb) ? $cb->cb_key : 0, ['id' => 'cb_key']) !!}



        <!-- CB Configurations -->
            <div class="card flat">
                <div class="card-title" style="padding:10px">
                    {{trans('privateCbs.Securityconfigurations')}}
                </div>
                <div class="card-body">
                  <div class="card flat margin-bottom-20">
                      <div class="card-header">
                          <a href="#"> {{$titleConfPermission}}</a>
                      </div>
                      <div class="card-body">
                            @foreach($configurations as $configuration)
                              @if($configuration->code !='create_vote_like' && $configuration->code!='create_vote_negative' && $configuration->code!='create_vote_multi' )

                                <div class="form-group">
                                    <br>
                                    <label class="col-sm-12 col-md-2 form-control-label" for="configuration_{{$configuration->id}}">{{$configuration->title ?? 'title'}}</label>
                                    <div class="col-sm-12 col-md-10">
                                        <select id="configuration_{{$configuration->id}}" name="configs[user_level_permissions][{{$configuration->id}}][]" multiple class="select2levelsNeeded" @if (ONE::actionType('securityConfigurations') == "show") disabled @endif>
                                          @if(isset($userLevels))
                                              @forelse($userLevels as $level)
                                                  <option value="{!! $level->login_level_key !!}" @if (in_array($level->login_level_key, isset($cbConfigurations[$configuration->code][$configuration->id]) ? $cbConfigurations[$configuration->code][$configuration->id] : [])) selected @endif>{!! $level->name !!}</option>
                                              @empty
                                                  {{ trans('privateCbs.no_levels_available') }}
                                              @endforelse
                                          @endif
                                        </select>
                                    </div>
                                </div>
                                <br>
                              @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="card flat">
                        <div class="card-header">
                         {{$titleVotes}}
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                @foreach($votes as $vote)
                                    <div class="form-group">
                                        <label class="col-sm-12 col-md-2 form-control-label" for="configuration_{{$vote->vote_key}}">{{$vote->name}}</label>
                                        <div class="col-sm-12 col-md-10">
                                            <select id="vote_{{$vote->vote_key}}" name="vote[vote][{{$vote->vote_key}}][]" multiple class="select2levelsNeeded" @if (ONE::actionType('securityConfigurations') == "show") disabled @endif>
                                              @if(isset($userLevels))
                                                  @forelse($userLevels as $level)
                                                      <option value="{!! $level->login_level_key !!}" @if (in_array($level->login_level_key, isset($eventLevel[$vote->vote_key][0]) ? $eventLevel[$vote->vote_key][0] :[])) selected @endif>{!! $level->name !!}</option>
                                                    @empty
                                                      {{ trans('privateCbs.no_levels_available') }}
                                                  @endforelse
                                              @endif
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    {!! $form->make() !!}
@endsection

@section('scripts')
    <script>
        $(".select2levelsNeeded").select2({
            templateResult: function (data) {
                var $res = $('<span></span>');
                var $check = $('<input type="checkbox" class="inputCheckBoxSelect2" style="margin-right:5px;" />');

                $res.text(data.text);

                if (data.element) {
                    $res.prepend($check);
                    $check.prop('checked', data.element.selected);
                }
                return $res;
            }
        });
    </script>
@endsection
