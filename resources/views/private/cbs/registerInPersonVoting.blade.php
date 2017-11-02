@extends('private._private.index')
<style>
    .margin-0{
        margin:0!important;
    }
    .translations-table thead tr:first-child td:first-child{
        padding: 8px 0px!important;
        font-size: 14px!important;
    }
    .translations-table thead td{
        font-size: 14px!important;
    }
    .fields td{
        position: relative;
    }
    .fields td .fa{
        position: absolute;
        right: -15px;
        top: 15px;
    }
    .fields td img{
        position: absolute;
        right: -15px;
        top: 15px;
    }
    .color-blue{
        color:#3c8dbc;
    }
    .color-green{
        color: green;
    }
    .color-orange{
        color:orange;
    }
    .cursor{
        cursor: pointer;
    }
    .my-new-user-btn{
        height: 22px;
        padding-top: 4px!important;
    }
    .new-user-line{
        width: 100%;
        float: left;
        margin-bottom: 5px;
    }
    .hidden{
        display: none;
    }
    .project-item{
        background: #eee;
        padding: 0px 5px;
        border: 1px solid #ccc;
        margin-right: 2px;
    }
</style>
@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-file-text" aria-hidden="true"></i> {{ trans('privateUsers.registerUsersVotes') }}</h3>
        </div>
        <div class="box-body">
            <div class="row margin-0">
                <table class="table translations-table">
                    <thead>
                    <tr>
                        <td class="text-center">{{ trans('privateUsers.code') }}</td>
                        <td class="text-center">{{ trans('privateUsers.votes') }}</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="fields">
                        <td><input type="text" name="code" class="form-control"></td>
                        <td><input type="text" name="votes" class="form-control"></td>
                    </tr>
                    </tbody>
                </table>

                <div id="votes-list" class="hidden">
                    <div class="row margin-0">
                        <div class="col-2"><b>{{ trans('privateUsers.code') }}</b></div>
                        <div class="col-2"><b>{{ trans('privateUsers.status') }}</b></div>
                        <div class="col-8"><b>{{ trans('privateUsers.user_votes') }}</b></div>
                    </div>
                    <div class="user-votes-list"></div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="confirmReplaceVotes">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="card-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">{{ trans('registerInPersonVoting.warning_user_has_already_voted_title') }}</h4>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="clicked-element-id" value="">
                                <h3>{{ trans('registerInPersonVoting.warning_user_has_already_voted_subtitle') }}</h3>
                                <p>{{ trans('registerInPersonVoting.warning_user_has_already_voted_consequences_explanation') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('registerInPersonVoting.ignore') }}</button>
                        <button type="button" class="btn btn-primary" id="submit-votes">{{ trans('registerInPersonVoting.replace') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click','.replace-user-votes', function(e){
            $('#confirmReplaceVotes').modal('show');
            $('#clicked-element-id').val($(this).attr('id'));

        });


        $(document).on('click','#submit-votes', function(e){
            var elementId = $("#clicked-element-id").val();
            $("#clicked-element-id").val([]);
            $('#confirmReplaceVotes').modal('hide');
            /*$("#"+elementId).parent('.fields').find('td:last').remove();*/
            var elementToSubmit = $("#"+elementId).parents('.fields').eq(0).find('td:last');
            elementToSubmit.find('.replace-user-votes').remove();
            triggerVoting(elementToSubmit,"{{action('CbsVoteController@replaceUserVotesWithInPersonVotes')}}",false);
        });


        $(document).on('keydown','.fields td:last', function(e){
            var element = $(this);
            if (e.keyCode == 13) {
                triggerVoting(element,"{{action('CbsVoteController@saveInPersonVotes')}}",true);
            }
        });

        function triggerVoting(element,voteUrl,addLine){
            element.parent('.fields').find("input").prop("disabled", false);
            var inputs = element.parent().find(':input').serializeArray();
            console.log(inputs);
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: voteUrl, // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    "inputs": inputs,
                    "vote_event_key": "{{ $voteKey }}",
                    "cbKey": "{{ $cbKey }}"
                },beforeSend:function (){
                    element.parent('.fields').find("input").prop("disabled", true);
                    element.append('<img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading" class="loader pull-right" style="width: 20px; padding-top:2px;"/>');
                    if(addLine) {
                        $('.translations-table').find('tbody').append("<tr class='fields'>" +
                            "<td><input type='text' name='code' class='form-control'></td>" +
                            "<td><input type='text' name='votes' class='form-control'></td>" +
                            "</tr>");
                        $('.translations-table').find('tr:last').find('td:first').find('input').focus();
                    }

                },success: function (response) { // What to do if we succeed
                    element.find('.loader').remove();
                    if(response.error){
                        element.append('<i class="fa fa-exclamation-triangle" aria-hidden="true" style="color:red;" title="' + response.error + '"></i>');
                    }else if(response.warning){
                        element.append(response.warning);
                    }else{
                        console.log(response);
                        $("#votes-list").removeClass('hidden');
                        $(".user-votes-list").prepend(response);
                        element.append('<i class="fa fa-check" aria-hidden="true" style="color:green;"></i>');
                    }


                }
            });
        }
    </script>
{{--

--}}

@endsection