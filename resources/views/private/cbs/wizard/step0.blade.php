@extends('private._private.index')

@section('content')
    <div class="box box-primary">
            <div class="" style="">
                <div id="cbWizard">
                    <div class=""  style="">
                            <div class="cb-empatia-wrapper">            
                                <a type="" class="btn-flat btn-cancel pull-right" href="{{ action("CbsController@indexManager") }}"style="margin-top: 20px; margin-right:20px;">{!! trans("privateCbs.cancel") !!}</a>
                               <br>
                            </div>
                            <br>
                        </div>
                        <div class="tab-pane" style="height:auto;">
                            <div style="margin-top:50px;text-align: center;">
                                <div class="row">
                                    @foreach($typesList as $type)
                                        @if(ONE::isAdmin())
                                            <div class="col-3" style="margin-top:15px;">
                                                    <a href="{{ action("CbsController@create",["type"=> $type->code]) }}" style="color:#000">
                                                <div class="card bg-light mb-3" style="max-width: 18rem;">
                                                    <div class="card-body">
                                                        <?php 
                                                        switch ($type->code) {
                                                            case $type->code == "idea":
                                                                $title = trans('privateIdeas.ideas');
                                                                break;
                                                            case $type->code == "forum":
                                                                $title = trans('privateForums.forums');
                                                                break;
                                                            case $type->code == "discussion":
                                                                $title = trans('privateDiscussions.discussions');
                                                                break;
                                                            case $type->code == "proposal":
                                                                $title = trans('privateProposals.proposals');
                                                                break;
                                                            case $type->code == "project_2c":
                                                                $title = trans('privateProject2Cs.project_2cs').' '.(isset($cb->title) ? $cb->title : null);
                                                                break;
                                                            case $type->code == "publicConsultation":
                                                                $title = trans('privatePublicConsultations.public_consultations');
                                                                break;
                                                            case $type->code == "tematicConsultation":
                                                                $title = trans('privateTematicConsultations.tematic_consultations');
                                                                break;
                                                            case $type->code == "survey":
                                                                $title = trans('privateSurveys.surveys');
                                                                break;
                                                            case $type->code == "project":
                                                                $title = trans('privateProject.project').' '.(isset($cb->title) ? $cb->title : null);
                                                                break;
                                                            case $type->code == "phase1":
                                                                $title = trans('privatePhaseOne.phase1').' '.(isset($cb->title) ? $cb->title : null);
                                                                break;
                                                            case $type->code == "phase2":
                                                                $title = trans('privatePhaseTwo.phase2').' '.(isset($cb->title) ? $cb->title : null);
                                                                break;
                                                            case $type->code == "phase3":
                                                                $title = trans('privatePhaseThree.phase3').' '.(isset($cb->title) ? $cb->title : null);
                                                                break;
                                                            case $type->code == "qa":
                                                                $title = trans('privatePhaseThree.qa').' '.(isset($cb->title) ? $cb->title : null);
                                                                break;
                                                        }
                                                        
                                                        ?>
                                                    <h5 class="card-title" style="background:none;">{{$title}}</h5>
                                                    <p class="card-text"></p>
                                                    </div>
                                                </div>
                                                </a>
                                            </div>
                                            {{--    --}}
                                        @endif
                                    @endforeach
                                </div>
                            <br>
                            </div>
                        </div>
                    </div>
                </div>
    </div>
@endsection

