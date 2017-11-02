@extends('private._private.index')

@section('content')
    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title">{!! trans('user.userDetailsReadOnly') !!}</h3>
        </div>
        @if($userObj->moderated)
        <div class="col-sm-12 text-right">
            <a href='javascript:oneActivate("{{ action('UsersController@moderateUser',['userKey' => $user->user_key, 'site_key' => $userObj->moderation_site_key ]) }}")' class='btn btn-success btn-sm'><i class='glyphicon glyphicon-thumbs-up'></i> {{trans("users.authorize")}}</a>
        </div>
        @endif
    
    <div class="box-body">
        <!-- this is a dummy form -->
        {{ Form::open(['action' => ['UsersController@showReadOnly', 'userKey' =>  null, 'id' =>  null, 'type' => null]]) }}

        <!-- User details -->    
        {!! Form::oneFieldShow('name', trans('user.name'),  isset($user) ? $user->name  : null, ['class' => 'form-control', 'id' => 'name']) !!}
        {!! Form::oneFieldShow('email', trans('user.email'),  isset($user) ? $user->email  : null, ['class' => 'form-control', 'id' => 'email']) !!}
        {!! Form::oneFieldShow('status', trans('user.status'),  isset($status) ? $status  : null, ['class' => 'form-control', 'id' => 'status']) !!}

        <!-- Identity card and Vat number - to save this we have to correct Auth Module first -->    
        {!! Form::oneFieldShow('identity_card', trans('PublicUser.identityCard'), isset($user) ? $user->identity_card : null, ['class' => 'form-control', 'id' => 'identity_card', 'required' => 'required']) !!}
        {!! Form::oneFieldShow('vat_number', trans('PublicUser.vatNumber'), isset($user) ? $user->vat_number  : null, ['class' => 'form-control', 'id' => 'vat_number']) !!}

        <!-- Parameters -->        
        @if(isset($registerParameters))
            @foreach($registerParameters as $parameter)

                @if($parameter['parameter_type_code'] == 'text')
                    @if(!empty($parameter['value']))
                        {!! Form::oneFieldShow($parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value'] : null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                    @endif
                @elseif($parameter['parameter_type_code'] == 'text_area')
                    @if(!empty($parameter['value']))
                        {!! Form::oneFieldShow($parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value']:null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'] , ($parameter['mandatory'] == true ? 'required' : null) ]) !!}
                    @endif
                @elseif($parameter['parameter_type_code'] == 'radio_buttons')
                    @if(!empty($parameter['value']) && count($parameter['parameter_user_options'])> 0)
                    {!! Form::oneFieldShow($parameter['parameter_user_type_key'], $parameter['name'],
                        !empty($parameter['value']) ? $parameter['value'] : null,
                        ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                    @endif
                @elseif($parameter['parameter_type_code'] == 'check_box')
                    @if(!empty($parameter['value']) && count($parameter['parameter_user_options'])> 0)
                        {!! Form::oneFieldShow($parameter['parameter_user_type_key'], $parameter['name'],
                            !empty($parameter['value']) ? $parameter['value'] : null,
                            ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}                
                    @endif
                @elseif(!empty($parameter['value']) && $parameter['parameter_type_code'] == 'dropdown')
                    {!! Form::oneFieldShow($parameter['parameter_user_type_key'], $parameter['name'],
                        !empty($parameter['value']) ? $parameter['value'] : null,
                        ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}
                @elseif(!empty($parameter['value']) && $parameter['parameter_type_code'] == 'birthday')
                    {!! Form::oneFieldShow($parameter['parameter_user_type_key'], $parameter['name'],
                        !empty($parameter['value']) ? $parameter['value'] : null,
                        ['class' => 'form-control', 'id' => $parameter['parameter_user_type_key'], ($parameter['mandatory'] == true ? 'required' : null)]) !!}            
                @elseif( !empty($parameter['value']["name"]) && $parameter['parameter_type_code'] == 'file')
                    <dt>{{ $parameter['name'] }}</dt>

                    @if( substr(strrchr($parameter['value']["name"],'.'),1) == "png" || substr(strrchr($parameter['value']["name"],'.'),1) == "bmp" || substr(strrchr($parameter['value']["name"],'.'),1) == "jpg" || substr(strrchr($parameter['value']["name"],'.'),1) == "jpeg"  )
                        <div class="polaroid">
                            <p>{{ $parameter['value']["name"] }}</p>
                            <a target="_blank" href="{{ URL::action("FilesController@download",[$parameter['value']["id"],$parameter['value']["code"]]) }}">
                                <img src="{{ URL::action("FilesController@download",[$parameter['value']["id"],$parameter['value']["code"], 1]) }}" />
                            </a>
                        </div>
                    @else
                        {!! ONE::fileIconByFilename($parameter['value']["name"] ) !!} <a href="{{ URL::action("FilesController@download",[$parameter['value']["id"],$parameter['value']["code"]]) }}" target="_blank">{{ $parameter['value']["name"] }}</a>
                    @endif

                    <!--
                    <div class="form-group">
                        <label for="{{$parameter['parameter_user_type_key']}}">{{ $parameter['name'] }}: @if($parameter['mandatory'])
                                <span class="required-symbol">*</span> @endif</label>
                        <div class="box-tools dropFilesArea" id="{{$parameter['parameter_user_type_key']}}">
                            {!! ONE::fileSingleUploadBox("drop-zone", trans("cb.drag_and_drop_files_to_here") , 'user-file', 'files-list', (isset($parameter['value']['name']) ? $parameter['value']['name'] : null)) !!}
                        </div>
                        {!! Form::hidden($parameter['parameter_user_type_key'], (isset($parameter['value']['id']) ? $parameter['value']['id'] : null), ['id' => 'file_id']) !!}
                    </div>
                    -->
                    <hr style="margin: 10px 0 10px 0">
                @endif
            @endforeach
        @endif    

    {{ Form::close() }}
    </div>
        
    <div class="box-footer">
        <a href="{{ action("UsersController@indexCompleted") }}" class="btn btn-flat empatia"><i class="fa fa-arrow-left"></i>  {!! trans('user.back') !!}</a>
    </div>        
@endsection


@section('header_styles')
    <style>
    .polaroid {
      position: relative;
      width: 220px;
      border:1px solid #eee;
    }

    .polaroid img {
      border: 10px solid #fff;
      border-bottom: 45px solid #fff;
      -webkit-box-shadow: 3px 3px 3px #777;
         -moz-box-shadow: 3px 3px 3px #777;
              box-shadow: 3px 3px 3px #777;
    }

    .polaroid p {
      position: absolute;
      text-align: center;
      width: 100%;
      bottom: 0px;
      color: #888;
    }                        
    </style>  
 @endsection
   