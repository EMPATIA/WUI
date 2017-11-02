<!-- Modal Vote -->
<div class="modal fade" id="modalAddVote{{ $voteCounter }}">
    
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ trans("privateCbs.addVote") }}</h4>
            </div>
            <div id="voteWizard_{{ $voteCounter }}" class="modal-body">
                   <div class="navbar">
                      <div class="navbar-inner">
                            <ul class="nav nav-pills">
                               <li class="active disabledTab"><a href="#stepVote{{ $voteCounter }}_1" data-toggle="tab" data-step="1">1</a></li>
                               <li class="disabledTab"><a href="#stepVote{{ $voteCounter }}_2" data-toggle="tab" data-step="2">2</a></li>
                            </ul>
                      </div>
                   </div>
                   <div class="tab-content">
                      <div class="tab-pane fade in active" id="stepVote{{ $voteCounter }}_1">

                        <div class="well">                 
                                <div class="form-group">
                                    <label for="voteName_{{ $voteCounter }}">{{ trans('privateCbs.voteName') }}</label>
                                    <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_name") }}</span>
                                    <input class="form-control" id="voteName_{{ $voteCounter }}" required="required" name="voteName_{{ $voteCounter }}" type="text" onchange="changeVoteTitle({{ $voteCounter }})" onfocusout="changeVoteTitle({{ $voteCounter }})">
                                </div>

                                <div class="form-group">
                                    <label for="voteStartDate_{{ $voteCounter }}">{{ trans('privateCbs.voteStartDate') }}</label>
                                    <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_start_date") }}</span>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                        <input id="voteStartDate_{{ $voteCounter }}" class="form-control oneDatePicker" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="voteStartDate_{{ $voteCounter }}" type="text">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="voteStartTime_{{ $voteCounter }}">{{ trans('privateCbs.voteStartTime') }} </label>
                                    <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_start_time") }}</span>
                                    <div class="input-group time"><span class="input-group-addon">
                                            <i class="glyphicon glyphicon-time"></i></span><input id="voteStartTime_{{ $voteCounter }}" class="form-control oneTimePicker" name="voteStartTime_{{ $voteCounter }}" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="voteEndDate_{{ $voteCounter }}">{{ trans('privateCbs.voteEndDate') }}</label>
                                    <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_end_date") }}</span>
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                        <input id="voteEndDate_{{ $voteCounter }}" class="form-control oneDatePicker" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="voteEndDate_{{ $voteCounter }}" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="voteEndTime_{{ $voteCounter }}">{{ trans('privateCbs.voteEndTime')}}</label>
                                    <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateCbs.help_vote_end_time") }}</span>
                                    <div class="input-group time">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span><input id="voteEndTime_{{ $voteCounter }}" class="form-control oneTimePicker" name="voteEndTime_{{ $voteCounter }}" type="text">
                                    </div>
                                </div>
                        </div> 
            
                        <!-- Buttons: Next -->
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-flat empatia next pull-right" href="#">{!! trans("privateCbs.next") !!} <i class="fa fa-step-forward" aria-hidden="true"></i></a>  
                                <br/><br/>
                            </div>
                        </div>                          
                          
                      </div>      
            
                    <div class="tab-pane fade" id="stepVote{{ $voteCounter }}_2">
                        {{ trans("privateCbs.configurations")}}
                       <div class="well"> 
                          <div class='form-group'>
                              <label for="methodGroupSelect_{{ $voteCounter }}">{{ trans("privateCbs.voteTypes")}}</label>
                              <select class="form-control" id="methodGroupSelect_{{ $voteCounter }}" name="methodGroupSelect_{{ $voteCounter }}"
                                      onchange="showMethods({{ $voteCounter }})" required>
                                  <option value="">{{ trans("privateCbs.select_vote_type")}}</option>
                                  @if(!empty($methodGroup))
                                      @foreach($methodGroup as $group)
                                          <option value="{{$group->id}}">{{$group->name}}</option>
                                      @endforeach
                                  @endif
                              </select>
                          </div>
                          <div id="methodsDiv_{{ $voteCounter }}">
                          </div>
                          <div id="configurationsDiv_{{ $voteCounter }}" class="box-body">
                          </div>
                       </div>
                        <!-- Buttons: Previous && Next -->
                        <a class="btn btn-flat empatia prev " href="#"><i class="fa fa-step-backward" aria-hidden="true"></i> {!! trans("privateCbs.previous") !!}</a>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <!--
                <input type="hidden" name="_tokenPost" value="{{ csrf_token() }}">
                -->
                <button type="button" style='margin-left:10px;' class="btn btn-primary pull-right col-sm-2 "
                        id="buttonEditPost"  onclick="addVote2List({{ $voteCounter }});" >{{ trans("privateCbs.add") }}
                </button>
                <button type="button" class="btn btn-secondary col-sm-2 pull-right" data-dismiss="modal"
                        id="frm_cancel">{{ trans("privateCbs.close") }}
                </button>
            </div>            
            
        </div>
    </div>
</div> 


<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "0",
        "hideDuration": "0",
        "timeOut": "0",
        "extendedTimeOut": "0",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    setTimeout(
        function(){

            /* Stepper Engine [create.blade.php] --------------------------- START */
            $('#voteWizard_{{ $voteCounter }} .next').click(function(){

               var stepDiv = $(this).parents('.tab-pane').next().attr("id");

                if( stepDiv == "stepVote{{ $voteCounter }}_2" && $("#voteName_{{ $voteCounter }}").val() =="" ){
                    $("#voteName_{{ $voteCounter }}").focus();
                    toastr.error("{!! preg_replace( "/\r|\n/", "", htmlentities(trans("privateCbs.nameIsRequiredOnTab"),ENT_QUOTES)) !!} #1!");
                    return false;   
                } else {
                    var nextId = stepDiv;
                    $('[href=#'+nextId+']').tab('show');
                    return false;
                }
                
            })

            $('#voteWizard_{{ $voteCounter }} .prev').click(function(){
              var nextId = $(this).parents('.tab-pane').prev().attr("id");
              $('[href=#'+nextId+']').tab('show');
              return false;
            })        

            $('#voteWizard_{{ $voteCounter }} a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

              //update progress
              var step = $(e.target).data('step');

              // 
              /*
              if(step == 7){
                  $('form').find('input[type=submit]').show();
              }
              */
              //e.relatedTarget // previous tab
            })            
            
            
        }
    , 1);    


</script>