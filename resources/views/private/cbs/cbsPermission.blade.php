@extends('private._private.index')

@section('content')
    <style>
        .permission:hover{
            background-color: rgb(245,245,245);
        }
    </style>

    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment"> {{trans('privateCbs.name')}}</label>&nbsp {{$permissions->name}}</p>
    </div>

    <div class="box box-primary" style="margin-top: 10px">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateCbs.listPermissions') }}</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-8"><b> {{ trans('privateCbs.menus') }}</b></div>
                <div class="col-4"><b> {{ trans('privateCbs.permission') }} </b></div>
            </div>

            @foreach($permissions->permissions as $permission)
               <div class="row" style="margin-top: 10px">
                    <div class="col-8 permission" >{{ $permission->code }}</div>
                    <div class="col-1">
                        <div class="row">
                            @if(is_null($permissions->groupPermissions))
                                <div id="ok_{{$permission->code }}" class="col-12 btn btn-success" onclick="changePermission('{{$permission->code}}','{{$permissions->userPermissions->id}}','{{$permissions->groupPermissions}}',1)" style="{{ in_array($permission->code,$permissions->userPermissions->code)  ? "" : "display:none" }}"><i class="fa fa-check"></i></div>
                                <div id="ko_{{ $permission->code}}" class="col-12 btn btn-danger" onclick="changePermission('{{$permission->code}}','{{$permissions->userPermissions->id}}','{{$permissions->groupPermissions}}',0)" style="{{ in_array($permission->code,$permissions->userPermissions->code) ? "display:none" : "" }}"><i class="fa fa-times"></i></div>
                                <div id="error_{{ $permission->code }}" class="col-12 btn btn-warning" style="display:none"><i class="fa fa-exclamation-triangle"></i></div>
                                <div id="wait_{{ $permission->code }}" class="col-12 btn btn-secondary" style="display:none">
                                    <img src="{{asset('images/spinner.gif')}}"  alt="loading..." style="display: inline-block;width: 18px" />
                                </div>
                            @else
                                <div id="ok_{{$permission->code }}" class="col-12 btn btn-success" onclick="changePermission('{{$permission->code}}','{{$permissions->userPermissions}}','{{$permissions->groupPermissions->id}}',1)" style="{{ in_array($permission->code,$permissions->groupPermissions->code)  ? "" : "display:none" }}"><i class="fa fa-check"></i></div>
                                <div id="ko_{{ $permission->code}}" class="col-12 btn btn-danger" onclick="changePermission('{{$permission->code}}','{{$permissions->userPermissions}}','{{$permissions->groupPermissions->id}}',0)" style="{{ in_array($permission->code,$permissions->groupPermissions->code) ? "display:none" : "" }}"><i class="fa fa-times"></i></div>
                                <div id="error_{{ $permission->code }}" class="col-12 btn btn-warning" style="display:none"><i class="fa fa-exclamation-triangle"></i></div>
                                <div id="wait_{{ $permission->code }}" class="col-12 btn btn-secondary" style="display:none">
                                    <img src="{{asset('images/spinner.gif')}}"  alt="loading..." style="display: inline-block;width: 18px" />
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-3"></div>
                </div>
            @endforeach
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function changePermission(code,userId,groupId,permission) {
            $("#wait_"+code).show();
            $("#ko_" +code).hide();
            $("#ok_" +code).hide();
            $("#error_"+code).hide();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('CbsController@updatePermission',[$type,$cbKey])}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'code': code,
                    'userId':userId,
                    'groupId':groupId,
                    'permission': permission,
                },
                success: function (response) { // What to do if we succeed
                    if(permission)
                        $("#ko_" +code).show();
                    else
                        $("#ok_" +code).show();
                },
                complete: function () {
                    $("#wait_"+code).hide();
                },
                error: function (response) {
                    $("#error_"+code).show();
                },
            });
        };

    </script>
@endsection
