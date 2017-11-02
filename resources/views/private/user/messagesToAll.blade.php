@extends('private._private.index')
@section('header_styles')
    <link href="{{ asset("css/jquery.scrollbar.css") }}" rel='stylesheet' type='text/css'>
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">{{ trans('privateSendMessageToUsers.title') }}</h3>
        </div>
        <div class="box-body">
            <div class="new-message-div row">
                <div class="col-12">
                    <h5>{{ trans("privateSendMessageToUsers.explanation") }}</h5>
                </div>
                <div class="pull-left col-md-12 col-12 pad-0">
                    <textarea name="message" class="form-control new-message" placeholder="{{ trans("privateSendMessageToUsers.write_message_placeholder") }}" style="height: 150px" id="message-composer"></textarea>
                </div>
                <div class="col-9">
                    <div class="checkbox pull-left margin-top-20">
                        <label>
                            <input id="send-email" type="checkbox" value="send-email">
                            {{trans('privateSendMessageToUsers.send_email')}}
                        </label>
                    </div>
                </div>
                <div class="pull-right col-md-3 col-3 text-right margin-top-20">
                    <a class="btn btn-flat empatia send-message pull-right">{{ trans("privateSendMessageToUsers.send_message") }}</a>
                </div>

            </div>
            <div id="sending-message-div" class="text-center loader" style="display:none;">
                <img src="{{ asset('images/bluePreLoader.gif') }}" alt="Loading"/><br>
                {{trans("privateSendMessageToUsers.sending_messages")}}
            </div>
            <div id="sent-message-div"></div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" role="dialog" id="sendMessageConfirmation">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" style="color: black;">{{trans("privateSendMessageToUsers.confirm_send_modal_title")}}</h4>
                </div>
                <div class="modal-body" style="color: black;">
                    <div id="message-content-preview" style="border: 1px solid #000;padding: 10px;"></div>
                    <h5 id="message-send-email-preview"></h5>
                    <hr>
                    <div class="alert alert-warning text-center h4" style="margin-bottom: 0;">
                        {{trans("privateSendMessageToUsers.time_consuming_operation_reminder")}}
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary empatia confirm-message-send">
                        <i class="fa fa-send" aria-hidden="true"></i>
                        {{ trans('privateSendMessageToUsers.confirm_send') }}
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateSendMessageToUsers.back")}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="{{ asset("js/tinymce/tinymce.min.js") }}"></script>
    <script>
        $(document).ready(function() {
            {!! ONE::addTinyMCE("#message-composer", ['action' => action('UsersController@getTinyMCE')]) !!}
            $(document).on('click', '.send-message', function () {
                messageComposerValue = tinyMCE.get('message-composer').getContent();
                messagePreview = $("#message-content-preview");
                sendEmailPreview = $("#message-send-email-preview");
                if (messageComposerValue)
                    messagePreview.html(messageComposerValue);
                else {
                    messagePreview.html("<i>{{ trans('privateSendMessageToUsers.message_empty') }}");
                    $(".confirm-message-send").addClass("disabled");
                }

                if ($("#send-email:checked").length>0)
                    sendEmailPreview.text("{{ trans('privateSendMessageToUsers.send_email') }}");
                else
                    sendEmailPreview.text("{{ trans('privateSendMessageToUsers.dont_send_email') }}");

                $("#sendMessageConfirmation")
                    .modal('show')
                    .on("hidden.bs.modal", function () {
                        messagePreview.text("");
                        sendEmailPreview.text("");
                        $(".confirm-message-send").removeClass("disabled");
                    });
            });
            $(document).on('click','.confirm-message-send', function () {
                if (!$(this).hasClass("disabled")) {
                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('UsersController@sendMessageToAll')}}", // This is the url we gave in the route
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'message': tinyMCE.get('message-composer').getContent(),
                            'send_email': $('#send-email').is(":checked")
                        }, beforeSend: function () {
                            $('#sendMessageConfirmation').modal('hide');
                            $("#sending-message-div").show();
                            $("#message-composer, #send-email").attr("disabled","disabled");
                            $("a.btn.send-message").addClass("disabled");
                        }, success: function (response) {
                            htmlToPrint = "<hr>";
                            if (response.hasOwnProperty("success")) {
                                htmlToPrint +=
                                    "<h4>{{ trans("privateSendMessageToUsers.success") }}</h4>" +
                                    "<ul>" +
                                    "<li><b>{{ trans("privateSendMessageToUsers.messages_success_count") }}:</b> " + response.messages.success +"</li>" +
                                    "<li><b>{{ trans("privateSendMessageToUsers.messages_failed_count") }}:</b> " + response.messages.failed +"</li>";

                                if (response.emails.hasOwnProperty("sent") && response.emails.sent === true)
                                    htmlToPrint +=
                                        "<li><b>{{ trans("privateSendMessageToUsers.emails_success_count") }}:</b> " + response.emails.success +"</li>" +
                                        "<li><b>{{ trans("privateSendMessageToUsers.emails_failed_count") }}:</b> " + response.emails.failed +"</li>";
                                else
                                    htmlToPrint +=
                                        "<li><b>{{ trans("privateSendMessageToUsers.no_emails_sent") }}</b></li>";

                                htmlToPrint += "</ul>";
                            } else
                                htmlToPrint += "<h4>{{ trans("privateSendMessageToUsers.failed") }}</h4>";

                            $("#sending-message-div").hide();
                            $("#sent-message-div").html(htmlToPrint).show();
                        }
                    });
                }
            });
        });
    </script>
@endsection