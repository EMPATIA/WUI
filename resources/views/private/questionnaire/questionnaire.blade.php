@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('questionnaire', trans('privateQuestionnaires.details'), 'q', 'q')
                ->settings(["model" => isset($questionnaire) ? $questionnaire : null,'id'=>isset($questionnaire) ? $questionnaire->form_key : null])
                ->show('QuestionnairesController@edit', 'QuestionnairesController@delete', ['key' => isset($questionnaire) ? $questionnaire->form_key : null], 'QuestionnairesController@index')
                ->create('QuestionnairesController@store', 'QuestionnairesController@show', ['key' => isset($questionnaire) ? $questionnaire->form_key : null])
                ->edit('QuestionnairesController@update', 'QuestionnairesController@show', ['key' => isset($questionnaire) ? $questionnaire->form_key : null])
                ->open()
            @endphp

            <hr style="margin: 10px 0 10px 0">
            @if( !empty($languages) && count($languages) > 0)
                <div class="row">
                    <div class="col-12">
                        @foreach($languages as $language)
                            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
                            <div style="padding:10px;">
                                <!--Title-->
                            {!! Form::oneText($language->default == true ? 'title_'.$language->code: 'title_'.$language->code,
                                trans('privateQuestionnaire.title'),
                                isset($translations[$language->code]) ? $translations[$language->code]->title : null,
                                ['class' => 'form-control', 'id' => 'title_'.$language->code]) !!}
                            <!--Description-->
                                {!! Form::oneText($language->default == true ? 'description_'.$language->code: 'description_'.$language->code,
                                        trans('privateQuestionnaire.description'),
                                        isset($translations[$language->code]) ? $translations[$language->code]->description : null,
                                        ['class' => 'form-control', 'id' => 'description_'.$language->code, 'size' => '30x2', 'style' => 'resize: vertical']) !!}
                            </div>
                        @endforeach
                        @php $form->makeTabs(); @endphp
                    </div>
                </div>
            @endif
            @if(ONE::actionType('questionnaire')=='show')
                {!! Form::oneText('title', trans('privateQuestionnaire.title'), isset($questionnaire) ? $questionnaire->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
                {!! Form::oneTextArea('description', trans('privateQuestionnaire.description'), isset($questionnaire) ? $questionnaire->description : null, ['class' => 'form-control', 'id' => 'description', 'size' => '30x2', 'style' => 'resize: vertical']) !!}
            @endif
            {!! Form::oneDate('start_date', trans('privateQuestionnaire.startDate'), isset($questionnaire) ? substr($questionnaire->start_date, 0, 10) : null, ['id' => 'start_date']) !!}
            {!! Form::oneDate('end_date', trans('privateQuestionnaire.endDate'), isset($questionnaire) ? substr($questionnaire->end_date, 0, 10) : null, ['id' => 'end_date']) !!}

            {!! Form::oneCheckbox('public', trans('privateQuestionnaire.public'), isset($questionnaire) ? $questionnaire->public: null, ((isset($questionnaire) ? $questionnaire->public : null) == 1 ? true : false), ['id' => 'public']) !!}


            @if(ONE::actionType('questionnaire') == 'show')
                <a href="{{ action("PublicQController@showQ",$questionnaire->form_key) }}" target="_blank" class="btn btn-flat btn-success btn-sm">
                    <i class="fa fa-link" aria-hidden="true"></i>
                    {{trans('questionnaire.url')}}</a>
                <a href="{{ action("QuestionnairesController@downloadPdf", $questionnaire->form_key) }}" target="_blank" class="btn btn-flat btn-success btn-sm">
                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    {{trans('questionnaire.downloadPdf')}}</a>

                @if(ONE::verifyModuleAccess('notify','message_all_users'))
                    <a href="{{ action("PrivateNewslettersController@create", ['f'=>'newsletters', 'qKey' => $questionnaire->form_key]) }}" target="_blank" class="btn btn-flat btn-success btn-sm">
                        <i class="fa fa-envelope-o" aria-hidden="true"></i>
                        {{trans('questionnaire.sendEmail')}}</a>
                @endif

                <br>

                <hr style="margin: 10px 0 10px 0">
            @endif

            @if(ONE::actionType('questionnaire') == 'show')
                <div class="box box-primary" style="margin-top: 35px; border: 1px solid #cecece; border-top:3px solid #3c8dbc;">
                    <div class="box-header">
                        <div class="box-title">
                            {!! trans('questionnaire.QuestionGroups') !!}
                        </div>
                        @if(Session::get('user_role') == 'admin')
                            <div class="box-tools pull-right">
                                {!! ONE::actionButtons($questionnaire->form_key, ['create' => 'QuestionGroupsController@create']) !!}
                            </div>
                        @endif
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div id="menu-buttons" class="col-12">
                                <button class="btn btn-flat btn-secondary" data-action="expand-all" type="button">{{ trans('privateAccessMenu.expandAll') }}</button>
                                <button class="btn btn-flat btn-secondary" data-action="collapse-all" type="button">{{ trans('privateAccessMenu.collapseAll') }}</button>
                            </div>
                        </div>
                        <div class="dd" id="nestable">

                        </div>
                    </div>
                </div>
            @endif
            {!! $form->make() !!}
        </div>
    </div>

@endsection



@section('scripts')
    <script>

        @if(ONE::actionType('questionnaire') == 'show')
        $(function () {
            $.get('{{ URL::action('QuestionGroupsController@getQuestionGroups', isset($questionnaire) ? $questionnaire->form_key : null)}}',
                function (data) {
                    $("#nestable").html(data);
                    dataNestable();
                    $('.dd').nestable('collapseAll');
                })
                .fail(function (xhr, status, error) {
                    alert("An AJAX error occured: " + status + "\nError: " + error);
                });
        });
        function dataNestable() {

            $('.dd').nestable({
                dropCallback: function (details) {
                    var order = [];
                    $("li[data-id='" + details.destId + "']").find('ol:first').children().each(function (index, elem) {
                        order[index] = $(elem).attr('data-id');
                    });
                    if (order.length === 0) {
                        var rootOrder = [];
                        $("#nestable > ol > li").each(function (index, elem) {
                            rootOrder[index] = $(elem).attr('data-id');
                        });
                    }
                    var groupType = $("li[data-id='" + details.sourceId + "']").attr('data-type');
                    var destinationGroupType = $("li[data-id='" + details.destId + "']").attr('data-type');
                    $.post('{{ URL::action('QuestionGroupsController@updateOrder')}}',
                        {
                            _token: "{{ csrf_token() }}",
                            groupType: groupType,
                            destinationGroupType: destinationGroupType,
                            source: details.sourceId,
                            destination: details.destId,
                            order: JSON.stringify(order),
                            rootOrder: JSON.stringify(rootOrder)
                        },
                        function (data) {
//                                    console.log('data ' + data);
                        })
                        .done(function (result) {
                            if(result == 'false'){
                                toastr.error('{{ trans('questionnaire.failedInOrderQuestionGroupOrQuestion') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                                location.reload();
                            }

                        })
                        .fail(function () {
                            toastr.error('{{ trans('questionnaire.failedInOrderQuestionGroupOrQuestion') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                            location.reload();
                        });
                }
            });

        }

        $(function () {
            $('#questionnaire_answers').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('QuestionnairesController@getTableUserAnswers', $questionnaire->form_key) !!}',
                columns: [
                    { data: 'form_reply_key', name: 'form_reply_key' },
                    { data: 'name', name: 'name' },
                    { data: 'completed', name: 'completed' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });

        $('#menu-buttons').on('click', function (e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        });
        @endif
    </script>
@endsection

