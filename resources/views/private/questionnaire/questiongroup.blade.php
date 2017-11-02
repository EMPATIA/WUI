@extends('private._private.index')


@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('questiongroup', trans('privateQuestionGroups.details'), 'q', 'q')
                    ->settings(["model" => isset($questiongroup) ? $questiongroup : null,'id'=>isset($questiongroup) ? $questiongroup->question_group_key : null])
                    ->show('QuestionGroupsController@edit', 'QuestionGroupsController@delete', ['key' => isset($questiongroup) ? $questiongroup->question_group_key : null], 'QuestionnairesController@show', ['key' =>  isset($questiongroup) ? $questiongroup->form->form_key : null])
                    ->create('QuestionGroupsController@store', 'QuestionnairesController@show', ['key' => isset($formKey) ? $formKey : (isset($questiongroup) ? $questiongroup->question_group_key : null)])
                    ->edit('QuestionGroupsController@update', 'QuestionGroupsController@show', ['key' => isset($questiongroup) ? $questiongroup->question_group_key : null])
                    ->open();
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

            @if(ONE::actionType('questiongroup') == 'show')

                {!! Form::oneText('title', trans('privateQuestionGroup.title'), isset($questiongroup) ? $questiongroup->title : null, ['class' => 'form-control', 'id' => 'title']) !!}
                {!! Form::oneTextArea('description', trans('privateQuestionGroup.description'), isset($questiongroup) ? $questiongroup->description : null, ['class' => 'form-control', 'id' => 'description', 'size' => '30x2', 'style' => 'resize: vertical']) !!}
            @endif
            {!! Form::hidden('form_key', isset($questiongroup) ? $questiongroup->form->form_key : (isset($formKey) ? $formKey : null))!!}
            {!! Form::hidden('position', isset($questiongroup) ? $questiongroup->position: '') !!}
            @if(ONE::actionType('questiongroup') == 'show')

                <div class="card box box-primary" style="margin-top: 35px; border: 1px solid #cecece; border-top:3px solid #3c8dbc;">
                    <div class="box-header">
                        <div class="box-title">
                            {{trans('privateQuestionGroup.questions')}}
                        </div>
                        <div class="box-tools pull-right">
                            {!! ONE::actionButtons($questiongroup->question_group_key , ['create' => 'QuestionsController@create']) !!}
                        </div>
                    </div>
                    <div  class="panel-collapse collapse show">
                        <div class="box-body" style="min-height: 100px;">
                            <div class="dd" id="nestable" style="padding: 10px">

                            </div>
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
        $(function () {
            @if(ONE::actionType('questiongroup') == 'show')
                            $.get('{{ URL::action('QuestionsController@getQuestions', $questiongroup->question_group_key)}}',
                function (data) {
                    $("#nestable").html(data);
                })
                .fail(function (xhr, status, error) {
                    alert("An AJAX error occured: " + status + "\nError: " + error);
                });
            @endif

        });
        @if(ONE::actionType('questiongroup') == 'show')

    $('.dd').nestable({
            maxDepth:1,
            dropCallback: function (details) {
                var order = [];
                $(".dd-list").find('.dd-item').each(function (index, elem) {
                    order[index] = $(elem).attr('data-id');
                });


                $.post('{{ URL::action('QuestionsController@updateOrder')}}',
                    {
                        _token: "{{ csrf_token() }}",
                        order: JSON.stringify(order)
                    },
                    function (data) {
                        // console.log('data '+data);
                    })
                    .done(function ($result) {
                        //console.log($result);

                    })
                    .fail(function () {
                        //alert('fail');
                    });

            }
        });
        @endif

    </script>
@endsection
