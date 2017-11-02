@if(ONE::actionType('node') != 'show')
    <div class="card-header">
        <i>{!! trans("privateCbs.add_moderator") !!}</i>

        <a  href="" class="btn btn-flat btn-success btn-sm pull-right" data-toggle="modal" data-target="#managersModal">
            <i class="fa fa-plus"></i>
        </a>
    </div>
@endif

<div id="moderatorsGroup">
    <!-- Moderators List -->
    @if(!empty($moderators))
        @foreach($moderators as $moderator)
            <div id='moderatorDivItem_{{ $moderator->user_key }}' class="user-panel">
                {{--<input type="hidden" name="moderators[]" value="{{ $moderator->user_key ?? null }}">--}}
                <div class="image" style="float: left">
                    @if($moderator->photo_id > 0)
                        <img src="{{URL::action('FilesController@download',[$moderator->photo_id, $moderator->photo_code])}}/1"
                             class="rounded-circle" alt="User Image">
                    @else
                        <img src="{{asset('images/icon-user-default-160x160.png')}}"
                             class="rounded-circle" alt="User Image">
                    @endif
                </div>
                <div style="padding: 5px; margin-left: 60px;">
                    <b>{{$moderator->name}}</b><br>
                </div>
                @if(ONE::actionType('node') != 'show')
                    <div style="position: absolute; right: 10px; top: 10px">
                        <a href="javascript:uncheckModerator('{{ $moderator->user_key }}')">
                            <i style="color:red;" class="fa fa-remove"></i>
                        </a>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>


<!-- Add Moderator (Initial Hidden / shows on click privateCbs.addModerator) -->
<div class="modal fade" tabindex="-1" role="dialog" id="managersModal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{trans("privateCbs.add_moderator")}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table id="users_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                            <thead>
                            <tr>
                                <th style="width:10%;"></th>
                                <th>{{ trans('privateCbs.name') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateCbs.close")}}</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <!--
                <button type="button" class="btn btn-primary" id="buttonSubmit">{{trans("privateCbs.save_changes")}}</button>
                -->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
    $('#users_list').DataTable({
        language: {
            url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
            search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
        },
        processing: false,
        serverSide: false,
        bDestroy: true,
        bInfo: false,
        ajax: '{!!  URL::action('MPCbsController@allUsers',["component_key"=>$operator->component_key  ?? null] ) !!}',
        columns: [
            { data: 'moderadorCheckbox', name: 'moderadorCheckbox', searchable: false, orderable: false, width: "30px" },
            { data: 'name', name: 'name' }
        ],
        order: [['1', 'asc']]
    }).on( 'draw.dt', function () {
        $("#users_list_paginate").parent().attr("class","col-md-12");
    });

    function toggleModeratorItem(obj,name,userImage){
        if ($(obj).is(":checked")){
            html = "<div id='moderatorDivItem_"+$(obj).val()+"' class='user-card moderatorDivItem'>";
            html += "<div class='image' style='float: left'>";
            html += "<img src='"+userImage+"' class='rounded-circle' alt='User Image' />";
            html += "</div>";
            html += "<div style='padding: 5px; margin-left: 60px;'>";
            html += "<b>"+name+"</b>";
            html += "</div>";
            html += "<div style='position: absolute; right: 10px; top: 10px'>";
            html += "<a href='javascript:uncheckModerator(\""+$(obj).val()+"\")'><i style='color:red;' class='fa fa-remove'></i></a>";
            html += "</div>";
            html += "</div>";
            $("#moderatorsGroup").append(html);
        } else {
            $("#moderatorDivItem_"+$(obj).val()).detach();
        }
    }

    function uncheckModerator(userKey){
        $("#moderatorCheckbox_"+userKey).prop( "checked", false );
        $("#moderatorDivItem_"+userKey).detach();
    }
</script>


<style>
    .oneSwitch {
        position: relative; width: 60px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .oneSwitch-checkbox {
        display: none;
    }
    .oneSwitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .oneSwitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .oneSwitch-inner:before, .oneSwitch-inner:after {
        display: block; float: left; width: 50%; height: 20px; padding: 0; line-height: 20px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .oneSwitch-inner:before {
        content: "";
        padding-left: 10px;
        background-color: #62A351; color: #FFFFFF;
    }
    .oneSwitch-inner:after {
        content: "";
        padding-right: 10px;
        background-color: #EEEEEE; color: #999999;
        text-align: right;
    }
    .oneSwitch-switch {
        display: block; width: 18px; margin: 1px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 36px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s;
    }
    .oneSwitch-checkbox:checked + .oneSwitch-label .oneSwitch-inner {
        margin-left: 0;
    }
    .oneSwitch-checkbox:checked + .oneSwitch-label .oneSwitch-switch {
        right: 0px;
    }

    .moderatorDivItem:hover {
        background: #e8e8e8;
    }
</style>