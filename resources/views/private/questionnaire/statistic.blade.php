@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-12">

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">
                        {!! trans('questionnaire.statistics') !!}
                    </h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <div style="">
                        <div>
                            <div style="display: inline-block">
                                <a class="btn btn-flat empatia" href="{{ URL::action('QuestionnaireAnswersController@statistics', isset($questionnaire) ? $questionnaire->form_key : null)}}">
                                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                                    {{ trans("questionnaire.statistics") }}
                                </a>
                            </div>
                            <div class="pull-right">
                                <!--
                                    <a id="exportData2Pdf" onclick="javscript:exportData2Pdf()" type="button" class="btn btn-sm btn-flat btn-success disabled" style="cursor:pointer;">
                                        <i class="fa fa-file-pdf-o"></i> {!! trans('privateQuestionnaire.exportSelectedToPdf') !!}
                                    </a>
                                &nbsp;-->
                                <a onclick="javscript:exportData2Pdf()" type="" class="btn btn-sm btn-flat btn-submit">
                                    <i class="fa fa-file-pdf-o"></i> {!! trans('privateQuestionnaire.exportAllToPdf') !!}
                                </a>

                                <a href="{{action('QuestionnaireAnswersController@excel', ['key' => $questionnaire->form_key])}}"
                                   type="" class="btn btn-sm btn-flat btn-submit">
                                    <i class="fa fa-file-excel-o"></i> {!! trans('privateQuestionnaire.downloadExcel') !!}
                                </a>
                            </div>
                        </div>
                        <div class="margin-top-20">
                            {!! Form::open(['id' => 'formExport2Pdf', 'action' => ["QuestionnairesController@downloadPdfAnswerByForm",'key' => $questionnaire->form_key], 'method' => 'post']) !!}
                            <table id="questionnaire_answers" class="table table-striped dataTable no-footer table-responsive">
                                <thead>
                                <tr>
                                    <th style="width:10px;"></th>
                                    <th>{{ trans('privateQuestionnaire.name') }}</th>
                                    <th>{{ trans('privateQuestionnaire.completed') }}</th>
                                    <th>{{ trans('privateQuestionnaire.created_at') }}</th>
                                    <th>{{ trans('privateQuestionnaire.udpated_at') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                            </table>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        function checkIfExportIsAvailable(){
            var bvar = false;
            $(".input-checkbox-key").each( function() {
                if( bvar == false && $( this ).is(":checked")){
                    bvar = true;
                }
            });
            //
            if(bvar)
                $("#exportData2Pdf").removeClass("disabled");
            else
                $("#exportData2Pdf").addClass("disabled");
        }

        function exportData2Pdf(){
            $("#formExport2Pdf").submit();
        }

        $(function () {
            getSidebar('{{ action("OneController@getSidebar") }}', 'statistics', '{{ isset($questionnaire) ? $questionnaire->form_key : null }}', 'q' );

            $('#questionnaire_answers').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                ajax: '{!! action('QuestionnairesController@getTableUserAnswers', isset($questionnaire) ? $questionnaire->form_key : null) !!}',
                columns: [
                    { data: 'form_reply_key', name: 'form_reply_key', orderable: false},
                    { data: 'name', name: 'name' },
                    { data: 'completed', name: 'completed' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection