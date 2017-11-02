<?php

/**
 * Displays the Negative Voting Interface.
 *
 * Configurations example:
 *   $configurations["plusLabels"] = ["plus","plus"];
 *   $configurations["minusLabels"] = ["minus","minus"];
 *
 *
 *  Example:
 * @foreach($voteType as $vt)
 * @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
 *           {!! Html::oneNegativeVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
 *           {!! Html::oneMultiVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
 *           {!! Html::oneLikes($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @endif
 * @endforeach
 *
 * @param String $topicKey
 * @param String $cbKey
 * @param String $voteKey
 * @param String $likeCounter
 * @param String $dislikeCounter
 * @param Integer $allReadyVoted
 * @param Array $configurations
 * @return html
 */
Html::macro('oneNegativeVoting', function ($topicKey, $cbKey, $voteKey, $showTotalVotes, $likeCounter, $dislikeCounter, $allReadyVoted, $configurations = [], $styles = [], $disable = false,$securityConfigurationsVotes=[]) {
    $html = "";
    ob_start();

    $buttonClass = "oneNegativeVoting" . $topicKey . $voteKey;
    ?>
    <!-- HTML -->
    <?php
    $typeVote=false;
    $parameters=[];
    foreach ($securityConfigurationsVotes as $key => $value) {
        if($key==$voteKey){
            if(!empty($value)){
                foreach ($value as $parameter=>$parameterUserType) {
                    if($parameter=='parameterUserTypes'){
                        if(!empty($parameterUserType)){
                            $typeVote=true;
                            foreach ($parameterUserType as $parameterUserTypeName) {
                                if ($parameterUserTypeName == 'email_verification'){
                                    $parameters[] = trans("cbsIdea.email_verification");
                                } elseif ($parameterUserTypeName == 'manual_verification'){
                                    $parameters[] = trans("cbsIdea.manual_verification");
                                }  elseif ($parameterUserTypeName == 'sms_verification') {
                                    $parameters[] = trans("cbsIdea.sms_verification");
                                }  else{
                                    $parameters[]=$parameterUserTypeName;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    ?>

    <div class="votting-buttons <?php echo ($styles['voting-buttons-div']) ?? ''; ?>">
        <!-- Positive vote  -->

        <div class="oneNegativeVote-wrapper <?php echo ($styles['voting-button-positive-div']) ?? ''; ?>">
            <div class="oneNegativeVoteBtn-div">
                <?php if (!$disable) {


                    if ($typeVote==false || empty($securityConfigurationsVotes)){ ?>
                        <a class='<?php echo $buttonClass; ?> positiveVoteBtnStyle'
                           data='{"type":"NegativeVoting","topicKey": "<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                            <div
                                id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div
                                id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php } else{ ?>
                        <a class='positiveVoteBtnStyle' href ="javascript:messageNegativeVoting();" >
                            <div
                                id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div
                                id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php  } ?>
                <?php } else { ?>
                    <a href="<?php echo action('AuthController@login'); ?>">
                        <div
                            id="divPositiveUnselected_<?php echo $buttonClass; ?>" style="margin: auto" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
                            <div class='oneNegativeVoteBtn neutralVote vote-button-opacity'>
                                <!--                            <a href="{{ action('AuthController@login') }}"></a>-->
                                <i class="fa fa-check" aria-hidden="true"></i>
                            </div>
                        </div>
                    </a>
                <?php } ?>

            </div>
            <div class="positiveVoteNumberDivStyle">
                <div class="numberPositiveVote">
                    <div class="voteNumber neutralVoteNumber">
                        <p>
                            <?php if ($showTotalVotes == 1) { ?>
                                <span id="positiveCounter_<?php echo $buttonClass; ?>">
                                <?php echo $likeCounter; ?>
                            </span>
                                <span class="voteType-label">
                                <?php echo trans('votes.positive_vote') ?>
                            </span>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Negative vote -->

        <div class="oneNegativeVote-wrapper <?php echo ($styles['voting-button-negative-div']) ?? ''; ?>">
            <div class="oneNegativeVoteBtn-div">
                <?php if (!$disable) {

                    if ($typeVote==false || empty($securityConfigurationsVotes)){ ?>
                        <a class='<?php echo $buttonClass; ?> negativeVoteBtnStyle'
                           data='{"type":"NegativeVoting","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                            <div
                                id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div
                                id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php }else{?>
                        <a class='negativeVoteBtnStyle' href="javascript:messageNegativeVoting();">
                            <div
                                id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div
                                id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>

                    <?php  } ?>
                <?php } else { ?>
                    <a href="<?php echo action('AuthController@login'); ?>">
                        <div
                            id="divNegativeUnselected_<?php echo $buttonClass; ?>" style="margin: auto" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
                            <div class='oneNegativeVoteBtn neutralVote vote-button-opacity'>
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
            <div class="negativeVoteNumberDivStyle">
                <div class="numberNegativeVote">
                    <div class="voteNumber neutralVoteNumber">
                        <p>
                            <?php if ($showTotalVotes == 1) { ?>
                                <span id="negativeCounter_<?php echo $buttonClass; ?>">
                                <?php echo $dislikeCounter; ?>
                            </span>
                                <span class="voteType-label">
                                <?php echo trans('votes.negative_vote') ?>
                            </span>
                            <?php } ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade modal-followers" id="modalMessageNegativeVoting" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo trans("basic.information") ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <?php echo trans("basic.MissingConfigurationPermissions") ?>
                            <br><br>
                            <span class="sr-only">Error:</span>
                            <?php echo trans("basic.MissingFields:") ?>
                            <ul class="list-group">
                                <?php foreach ($parameters as  $value) {?>
                                    <li class="list-group-item">
                                        <?php echo $value ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if( !empty(Session::get('user')->user_key)){ ?>
                        <a href="<?php echo action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) ?>" class="btn btn-info" role="button"><?php echo trans("basicLayout.user_profile") ?></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans("basic.close") ?></button>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>




    <!-- JavaScript -->
    <script>

        function messageNegativeVoting(){
            $('#modalMessageNegativeVoting').modal('show');
        }


        $(".<?php echo $buttonClass; ?>").click(function () {
            //block buttons
            $(".votting-buttons").css('opacity', '0.5');
            $(".votting-buttons").css('pointer-events', 'none');
            var obj = jQuery.parseJSON($(this).attr("data"));
            // Vote
            $.ajax({
                method: obj.method, // Type of response and matches what we said in the route
                url: obj.url, // This is the url we gave in the route
                data: {
                    topicKey: obj.topicKey,
                    value: obj.value,
                    voteKey: obj.voteKey,
                    _token: obj.csrf_token
                }, // a JSON object to send back
                success: function (responseVote) { // What to do if we succeed
                    var response = JSON.parse(responseVote);

                    if (typeof response.vote != "undefined" && response.vote == '-1') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').show();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').hide();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();

                    } else if (typeof response.vote != "undefined" && response.vote == '1') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').show();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').hide();

                    } else if (typeof response.vote != "undefined" && response.vote == '0') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();
                    }
                    //Positive and negative counters
                    if (typeof response.totalPositive != "undefined") {
                        $('#remainingCounter_<?php echo $voteKey; ?>').text(response.total);

                        if ($('#positiveCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#positiveCounter_<?php echo $buttonClass; ?>').text(response.totalPositive);
                        }

                        if ($('#negativeCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#negativeCounter_<?php echo $buttonClass; ?>').text(response.totalNegative);
                        }

                        if ($('#remainingPositiveCounter_<?php echo $voteKey; ?>').length){
                            $('#remainingPositiveCounter_<?php echo $voteKey; ?>').text(response.total-response.negative);
                        }

                        if ($('#remainingNegativeCounter_<?php echo $voteKey; ?>').length){
                            $('#remainingNegativeCounter_<?php echo $voteKey; ?>').text(response.negative);
                        }
                    }

                    //enable buttons
                    $(".votting-buttons").css('opacity', '1');
                    $(".votting-buttons").css("pointer-events", "auto");

                    if ($(".oneVoteInfo")[0]) {

                        if (typeof response.total != "undefined") {
                            document.getElementById('info-total-votes').innerHTML = response['total'];
                        }
                        if (typeof response.negative != "undefined") {
                            document.getElementById('info-negative-votes').innerHTML = response['negative'];
                        }
                    }

                    //message for remaining votes
                    var msg = "";
                    if (typeof response.total != "undefined") {
                        msg += "<?php echo trans("votes.remainingTotalVotes"); ?>: " + response['total'] + ". ";
                    }
                    //message for negative votes
                    if (typeof response.negative != "undefined") {
                        msg += "<?php echo trans("votes.youCanUse"); ?> " + response['negative'] + " <?php echo trans("votes.negativeVotes"); ?>";
                    }

                    if (msg != "") {
                        toastr.info(msg);
                    } else {
                        toastr.error('<?php echo trans('vote.youDontHaveMore') ?>');
                    }

                    $.ajax({
                        method: 'GET',
                        url: "<?php echo action('PublicTopicController@getQuestionnaireModalData', ['cbKey'=>$cbKey, 'topicKey' =>$topicKey, 'code' =>'vote_event', 'voteKey'=>$voteKey]); ?>",
                        success: function(response){
                            if(response!=='false'){
                                $('#questionnaireVotesModal').html(response);
                                $('#questionnaireVotesModal').modal('show');
                            }
                        }
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".votting-buttons").css('opacity', '1');
                    $(".votting-buttons").css("pointer-events", "auto");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    toastr.error("<?php echo trans("votes.errorOccurredWhileVoting"); ?>");
                }
            });
        });
    </script>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});

/**
 * Displays the Multi Voting Interface.
 *
 * Configurations example:
 *   $configurations["plusLabels"] = ["plus","plus"];
 *
 *  Example:
 * @foreach($voteType as $vt)
 * @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
 *           {!! Html::oneNegativeVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
 *           {!! Html::oneMultiVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
 *           {!! Html::oneLikes($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @endif
 * @endforeach
 *
 * @param String $topicKey
 * @param String $cbKey
 * @param String $voteKey
 * @param String $likeCounter
 * @param String $dislikeCounter
 * @param Integer $allReadyVoted
 * @param Array $configurations
 * @return html
 */
Html::macro('oneMultiVoting', function ($topicKey, $cbKey, $voteKey,  $likeCounter, $allReadyVoted, $configurations = [], $styles = [], $disable = false, $votedLabel = false, $voteLabel = false,$reloadIfSuccess = false, $votesInside = false,$securityConfigurationsVotes = [], $phases = false, $type = "", $canVote = false, $finalPhase = false, $remainingVotes = 0) {

    $html = "";
    $buttonClass = "oneMultiVoting" . $topicKey . $voteKey;

    ob_start();
    ?>
    <?php
    $typeVote = false;
    $parameters = [];
    foreach ($securityConfigurationsVotes as $key => $value) {
        if($key==$voteKey){
            if(!empty($value)){
                foreach ($value as $parameter=>$parameterUserType) {
                    if($parameter=='parameterUserTypes'){
                        if(!empty($parameterUserType)){
                            $typeVote=true;
                            foreach ($parameterUserType as $parameterUserTypeName) {
                                if ($parameterUserTypeName == 'email_verification'){
                                    $parameters[] = trans("cbsIdea.email_verification");
                                } elseif ($parameterUserTypeName == 'manual_verification'){
                                    $parameters[] = trans("cbsIdea.manual_verification");
                                }  elseif ($parameterUserTypeName == 'sms_verification') {
                                    $parameters[] = trans("cbsIdea.sms_verification");
                                }  else{
                                    $parameters[]=$parameterUserTypeName;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    ?>
    <!-- Multi Voting -->
    <div class="votting-buttons multipleVote-wrapper <?php echo ($styles['voting-buttons-div']) ?? ''; ?>">
        <?php if (array_key_exists('allow_multiple_per_one', $configurations) && $configurations['allow_multiple_per_one'] == 0) { ?>
            <div class="multiple-positive-vote">
                <!-- Positive button only -->
                <?php if (!$disable && !$finalPhase) { ?>
                    <a href="<?php echo action('AuthController@login'); ?>">
                        <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" style="margin: auto" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
                            <div class="multiVoteOnlyPositiveBtnDiv">
                                <div class='oneNegativeVoteBtn neutralVote no-login-vote vote-button-opacity'>
                                    <i class="fa fa-check" aria-hidden="true"></i> <?php echo ($voteLabel ?? ''); ?>
                                    <?php if($votesInside && !$phases){ ?>
                                        <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php } else {
                    if($finalPhase){ ?>
                        <div class="multiVoteOnlyPositiveBtnDiv multiVoteOnlyPositiveBtnDiv">
                            <div class='oneNegativeVoteBtn positiveVoteNumber'>
                                <?php if($votesInside){ ?>
                                    <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }else{
                        if(!empty($securityConfigurationsVotes) && !empty($parameters)){?>
                            <a href="<?php echo action('AuthController@stepperManager', ['stepper' => 'sms_validation']); ?>" style="cursor: pointer!important">
                                <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" style="margin: auto;cursor: pointer!important" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.complete_profile') ?></div>">
                                    <div class="multiVoteOnlyPositiveBtnDiv">
                                        <div class='oneNegativeVoteBtn neutralVote no-login-vote vote-button-opacity'>
                                            <i class="fa fa-check" aria-hidden="true"></i> <?php echo ($voteLabel ?? ''); ?>
                                            <?php if($votesInside && !$phases){ ?>
                                                <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php }else{
                            if($canVote){ ?>
                                <a class='votting-buttons <?php echo $buttonClass; ?>'
                                   data='{"type":"MultiVoting","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                    <div data-toggle="tooltip" title="<?php echo trans('vote.withdraw_selection') ?>"
                                         id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>>
                                        <div class="multiVoteOnlyPositiveBtnDiv">
                                            <div class='oneNegativeVoteBtn positiveVote'>
                                                <i class="fa fa-check" aria-hidden="true"></i> <?php echo ($votedLabel ?? ''); ?>
                                                <?php if($votesInside && !$phases){ ?>
                                                    <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>>
                                        <div class="multiVoteOnlyPositiveBtnDiv">
                                            <div class='oneNegativeVoteBtn neutralVote'>
                                                <i class="fa fa-check" aria-hidden="true"></i> <?php echo ($voteLabel ?? ''); ?>
                                                <?php if($votesInside && !$phases){ ?>
                                                    <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php }else{ ?>
                                <?php if(!$finalPhase){ ?>
                                    <!--                            <div id="divPositiveUnselected_--><?php //echo $buttonClass; ?><!--" style="margin: auto" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;--><?php //echo trans('vote.votes_submited') ?><!--</div>">-->
                                    <!--                                <div class="multiVoteOnlyPositiveBtnDiv">-->
                                    <!--                                    <div class='oneNegativeVoteBtn neutralVote no-login-vote vote-button-opacity'>-->
                                    <!--                                        <i class="fa fa-check" aria-hidden="true"></i> --><?php //echo ($voteLabel ?? ''); ?>
                                    <!--                                        --><?php //if($votesInside && !$phases){ ?>
                                    <!--                                            <span id="positiveCounter_--><?php //echo $buttonClass; ?><!--"><span class="smaller">--><?php //echo $likeCounter; ?><!--</span></span>-->
                                    <!--                                        --><?php //} ?>
                                    <!--                                    </div>-->
                                    <!--                                </div>-->
                                    <!--                            </div>-->
                                    <!--                            <a class='votting-buttons --><?php //echo $buttonClass; ?><!--'-->
                                    <!--                               data='{"type":"MultiVoting","topicKey":"--><?php //echo $topicKey; ?><!--","value":1,"voteKey":"--><?php //echo $voteKey; ?><!--","csrf_token":"--><?php //echo csrf_token(); ?><!--","url": "--><?php //echo action("PublicTopicController@vote", [$cbKey]); ?><!--", "method":"POST"}'>-->
                                    <div data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.votes_submited') ?></div>"
                                         id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>>
                                        <div class="multiVoteOnlyPositiveBtnDiv">
                                            <div class='oneNegativeVoteBtn positiveVote'>
                                                <i class="fa fa-check" aria-hidden="true"></i> <?php echo 'Stimmzettel bereits abgeschickt'; ?>
                                                <?php if($votesInside && !$phases){ ?>
                                                    <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.votes_submited') ?></div>"
                                         id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>>
                                        <div class="multiVoteOnlyPositiveBtnDiv">
                                            <div class='oneNegativeVoteBtn neutralVote'>
                                                <i class="fa fa-check" aria-hidden="true"></i> <?php echo 'Stimmzettel bereits abgeschickt'; ?>
                                                <?php if($votesInside && !$phases){ ?>
                                                    <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!--                            </a>-->
                                <?php }
                            } ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>

            <?php if(!$votesInside){ ?>
                <div class="">
                    <?php if ($likeCounter !== '') { ?>
                        <p class="word"> <?php echo trans('vote.votes') ?>:
                            <span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo $likeCounter; ?></span>
                        </p>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } elseif (array_key_exists('allow_multiple_per_one', $configurations) && $configurations['allow_multiple_per_one'] == 1) { ?>

            <!-- Positive button -->
            <!-- Coluna de Botoes -->
            <div class="multipleVoteCol">
                <div class="multipleVoteBtn">
                    <?php if ($disable) {


                        if ($typeVote==false || empty($securityConfigurationsVotes)){ ?>
                            <a class='votting-buttons <?php echo $buttonClass; ?>'
                               data='{"type":"MultiVoting","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                <div>
                                    <div class='oneNegativeVoteBtn positiveVote'>
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </a>
                        <?php } else { ?>

                            <a class='votting-buttons' href="javascript:messageMultiVoting();">
                                <div>
                                    <div class='oneNegativeVoteBtn positiveVote'>
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php } else { ?>
                        <a href="<?php echo action('AuthController@login'); ?>">
                            <div class="votting-buttons" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
                                <div class='oneNegativeVoteBtn neutralVote vote-button-opacity'>
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php } ?>

                </div>
                <!-- Negative button -->
                <div class="multipleVoteBtn">
                    <?php if (!$disable) {
                        if ($typeVote ==false || empty($securityConfigurationsVotes)){ ?>

                            <a class='votting-buttons <?php echo $buttonClass; ?>'
                               data='{"type":"MultiVoting","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                <div>
                                    <div class='oneNegativeVoteBtn neutralVote'>
                                        <i class="fa fa-minus" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </a>

                        <?php } else { ?>
                            <a class='votting-buttons' href="javascript:messageMultiVoting();">
                                <div>
                                    <div class='oneNegativeVoteBtn neutralVote'>
                                        <i class="fa fa-minus" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </a>
                        <?php }?>

                    <?php } else { ?>
                        <a href="<?php echo action('AuthController@login'); ?>">
                            <div class="votting-buttons" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
                                <div class='oneNegativeVoteBtn neutralVote vote-button-opacity'>
                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <!-- Multiple vote counts -->
            <!-- Coluna de Texto/Vote count -->
            <div class="multipleVoteCol">
                <div class="voteNumberMultipleVote yourVote" id="countVotes_<?php echo $buttonClass; ?>">
                    <p class=""> <?php echo trans('vote.votes') ?>:
                        <span
                            id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo isset($allReadyVoted) ? $allReadyVoted : '0'; ?></span>
                    </p>
                </div>
                <div class="voteNumberMultipleVote">
                    <?php if ($likeCounter !== '') { ?>
                        <p class=""> <?php echo trans('vote.total') ?> <?php echo trans('vote.votes') ?>:
                            <span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo $likeCounter; ?></span>
                        </p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="modal fade modal-after-vote" id="modalMessageAfterVote" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width: 90%!important">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <?php if(!empty($remainingVotes)){
//                                $html= trans('votes.text_before_number_ideas_left') ;
//                                $html .= '<span id=remainingVotesSpan></span>';
//                                $html .= trans('votes.text_after_number_ideas_left');

                                echo trans('votes.text_before_number_ideas_left') . ' ' . '<span id="remainingVotesSpan"></span>' . ' ' . trans('votes.text_after_number_ideas_left');
                            }
                            ?>
                        </div>
                    </div>
                    <div style="margin-top: 30px">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="pull-left buttons-modal-votes">
                                    <button type="button" class="btn btn-default btn-modal-change-vote" style="margin-top: 0px" data-dismiss="modal"><?php echo trans('votes.change_continue_voting'); ?></button>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="pull-right buttons-modal-votes">
                                    <a href="<?php echo action("PublicCbsController@showTopicsVoted",["cbKey"=>$cbKey, "type"=>$type]) ?>" class="btn btn-success btn-modal-finish-vote"><?php echo trans('votes.submit_votes'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-followers" id="modalMessageNoVotes" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width: 90%!important">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <span class="fa fa-exclamation-triangle" style="color: #D4D700"></span>
                            <?php echo trans('vote.reached_limit_votes'); ?>

                        </div>
                    </div>
                    <div style="margin-top: 30px">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="pull-left buttons-modal-votes">
                                    <button type="button" class="btn btn-default btn-modal-change-vote" style="margin-top: 0px" data-dismiss="modal"><?php echo trans('votes.change_votes'); ?></button>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="pull-right buttons-modal-votes">
                                    <a href="<?php echo action("PublicCbsController@showTopicsVoted",["cbKey"=>$cbKey, "type"=>$type]) ?>" class="btn btn-success btn-modal-finish-vote"><?php echo trans('votes.submit_votes'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Somethings happening here! JavaScript -->
    <div class="modal fade modal-followers" id="modalMessageMultiVoting" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo trans("votes.information") ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <?php echo trans("votes.missing_configuration_permissions") ?>
                            <br><br>
                            <span class="sr-only">Error:</span>
                            <?php echo trans("votes.missing_fields:") ?>
                            <ul class="list-group">
                                <?php foreach ($parameters as  $value) {?>
                                    <li class="list-group-item" style="color:black!important">
                                        <?php echo $value ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if( !empty(Session::get('user')->user_key)){ ?>
                        <a href="<?php echo action('PublicUsersController@show',['userKey' => Session::get('user')->user_key]) ?>" class="btn btn-info" role="button"><?php echo trans("votes.user_profile") ?></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans("votes.close") ?></button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-followers" id="modalMessageMultiVoting" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo trans("basic.information") ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <?php echo trans("basic.MissingConfigurationPermissions") ?>
                            <br><br>
                            <span class="sr-only">Error:</span>
                            <?php echo trans("basic.MissingFields:") ?>
                            <ul class="list-group">
                                <?php foreach ($parameters as  $value) {?>
                                    <li class="list-group-item">
                                        <?php echo $value ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if( !empty(Session::get('user')->user_key)){ ?>
                        <a href="<?php echo action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) ?>" class="btn btn-info" role="button"><?php echo trans("basicLayout.user_profile") ?></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans("basic.close") ?></button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>



    <script>


        function messageMultiVoting(){
            $('#modalMessageMultiVoting').modal('show');
        }

        $(".<?php echo $buttonClass; ?>").click(function (event) {
            //block buttons
            $(".votting-buttons").css('opacity', '0.5');
            $(".votting-buttons").css('pointer-events', 'none');
            var obj = jQuery.parseJSON($(this).attr("data"));
            // Vote

            $.ajax({
                method: obj.method, // Type of response and matches what we said in the route
                url: obj.url, // This is the url we gave in the route
                data: {
                    topicKey: obj.topicKey,
                    value: obj.value,
                    voteKey: obj.voteKey,
                    _token: obj.csrf_token
                }, // a JSON object to send back
                success: function (responseVote) { // What to do if we succeed
                    var response = JSON.parse(responseVote);

                    if (typeof response.vote != "undefined" && response.vote >= '0') {
                        //buttons | USING [id] NOTATION ON PURPOSE
                        if (response.vote == '0' && $('#divPositive_<?php echo $buttonClass; ?>').length != 0) {
                            $('[id="divPositive_<?php echo $buttonClass; ?>"]').hide();
                            $('[id="divPositiveUnselected_<?php echo $buttonClass; ?>"]').show();
                        } else if (response.vote == '1' && $('#divPositive_<?php echo $buttonClass; ?>').length != 0) {
                            $("#remainingVotesSpan").html(response['total']);
                            $('#modalMessageAfterVote').modal('show');
                            $('[id="divPositive_<?php echo $buttonClass; ?>"]').show();
                            $('[id="divPositiveUnselected_<?php echo $buttonClass; ?>"]').hide();
                        }
                        // count votes
                        $('#remainingCounter_<?php echo $voteKey; ?>').text(response.total);
                        $('#countVotes_<?php echo $buttonClass; ?>').text(response.vote);
                    }

                    //enable buttons
                    $(".votting-buttons").css('opacity', '1');
                    $(".votting-buttons").css("pointer-events", "auto");

                    //Positive and negative counters
                    if (typeof response.totalPositive != "undefined") {
                        if ($('#positiveCounter_<?php echo $buttonClass; ?>').length != 0) {
                            <?php if($votesInside){ ?>
                            $('#positiveCounter_<?php echo $buttonClass; ?>').html('<span class="smaller">' + response.totalPositive + '</span>');
                            <?php }else{ ?>
                            $('#positiveCounter_<?php echo $buttonClass; ?>').text(response.totalPositive);
                            <?php } ?>

                        }
                    }

                    if (typeof response.errorMsg != "undefined") {
                        //Error message
                        if ($("#votes-error-modal").length) {
                            $("#votes-error-modal").find(".modal-body").html(response.errorMsg);
                            $("#votes-error-modal").modal("show");
                        } else
                            toastr.error(response.errorMsg);
                    } else {
                        //message for remaining votes
                        var msg = "";
                        if (typeof response.total != "undefined" && response.total != -1) {
                            msg += "<?php echo trans("votes.remainingTotalVotes"); ?>: " + response['total'];
                        }

                        if (msg != "") {
                            if (typeof response.userVotes != "undefined") {
                                $("#total-votes-count").html(response.userVotes);
                                if (parseInt(response.userVotes)>0)
                                    $(".btn-submit-votes").removeClass("disabled");
                                else
                                    $(".btn-submit-votes").addClass("disabled");
                            }
                            <?php if($reloadIfSuccess){ ?>
                                location.reload();
                            <?php } ?>
                        }
                    }

                    $.ajax({
                        method: 'GET',
                        url: "<?php echo action('PublicTopicController@getQuestionnaireModalData', ['cbKey'=>$cbKey, 'topicKey' =>$topicKey, 'code' =>'vote_event', 'voteKey'=>$voteKey]); ?>",
                        success: function(response){
                            if(response!=='false'){
                                $('#questionnaireVotesModal').html(response);
                                $('#questionnaireVotesModal').modal('show');
                            }
                        }
                    });

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".votting-buttons").css('opacity', '1');
                    $(".votting-buttons").css("pointer-events", "auto");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    toastr.error("An error occurred while voting!");
                }
            });
        });
    </script>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});

/**
 * Displays the Like Voting Interface.
 *
 * Configurations example:
 *   $configurations["allow_dislike"] = 1;
 *   $configurations["likeLabels"] = ["Like","Liked"];
 *   $configurations["dislikeLabels"] = ["Dislike","Disliked"];
 *
 *  Example:
 * @foreach($voteType as $vt)
 * @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
 *           {!! Html::oneNegativeVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
 *           {!! Html::oneMultiVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
 *           {!! Html::oneLikes($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 * @endif
 * @endforeach
 *
 * @param String $topicKey
 * @param String $cbKey
 * @param String $voteKey
 * @param String $likeCounter
 * @param String $dislikeCounter
 * @param Integer $allReadyVoted
 * @param Array $configurations
 * @return html
 */
Html::macro('oneLikes', function ($topicKey, $cbKey, $voteKey, $likeCounter, $dislikeCounter, $allReadyVoted, $configurations = [], $styles = [], $disable = false, $votesInside = false, $securityConfigurationsVotes = [], $keepVisible = false) {
    $html = "";
    $buttonClass = "oneLikes" . $topicKey . $voteKey;
    ob_start();
    ?>
    <!-- HTML -->
    <?php
    $typeVote=false;
    $parameters=[];
    foreach ($securityConfigurationsVotes as $key => $value) {
        if($key==$voteKey){
            if(!empty($value)){
                foreach ($value as $parameter=>$parameterUserType) {
                    if($parameter=='parameterUserTypes'){
                        if(!empty($parameterUserType)){
                            $typeVote=true;
                            foreach ($parameterUserType as $parameterUserTypeName) {
                                if ($parameterUserTypeName == 'email_verification'){
                                    $parameters[] = trans("cbsIdea.email_verification");
                                } elseif ($parameterUserTypeName == 'manual_verification'){
                                    $parameters[] = trans("cbsIdea.manual_verification");
                                }  elseif ($parameterUserTypeName == 'sms_verification') {
                                    $parameters[] = trans("cbsIdea.sms_verification");
                                }  else{
                                    $parameters[]=$parameterUserTypeName;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    ?>
    <div class="votting-buttons <?php echo ($styles['voting-buttons-div']) ?? ''; ?>">
        <!-- LIKE -->
        <div class="oneNegativeVote-wrapper <?php echo ($styles['voting-button-positive-div']) ?? ''; ?>">
            <div class="oneNegativeVoteBtn-div">
                <?php if($keepVisible){ ?>

                    <div
                        id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>
                        style="margin: auto">
                        <div class='oneNegativeVoteBtn positiveVote'>
                            <i class="demo-icon icon-empatiadefault_like-btn"></i>
                            <?php if($votesInside){ ?>
                                <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                            <?php } ?>

                        </div>
                    </div>
                    <div
                        id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>
                        style="margin: auto">
                        <div class='oneNegativeVoteBtn neutralVote'>
                            <i class="demo-icon icon-empatiadefault_like-btn"></i>
                            <?php if($votesInside){ ?>
                                <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                            <?php } ?>
                        </div>
                    </div>
                <?php }else{ ?>

                    <?php if (!$disable) {
                        if ($typeVote == false || empty($securityConfigurationsVotes)){ ?>
                            <a class=' <?php echo $buttonClass; ?>'
                               data='{"type":"Like","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                <div
                                    id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>
                                    style="margin: auto">
                                    <div class='oneNegativeVoteBtn positiveVote'>
                                        <i class="demo-icon icon-empatiadefault_like-btn"></i>
                                        <?php if($votesInside){ ?>
                                            <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                        <?php } ?>

                                    </div>
                                </div>
                                <div
                                    id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>
                                    style="margin: auto">
                                    <div class='oneNegativeVoteBtn neutralVote'>
                                        <i class="demo-icon icon-empatiadefault_like-btn"></i>
                                        <?php if($votesInside){ ?>
                                            <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>

                        <?php  }else{?>
                            <a class='' href="javascript:messageLikes();">
                                <div
                                    id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>
                                    style="margin: auto">
                                    <div class='oneNegativeVoteBtn positiveVote'>
                                        <i class="demo-icon icon-empatiadefault_like-btn"></i>
                                        <?php if($votesInside){ ?>
                                            <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                        <?php } ?>

                                    </div>
                                </div>
                                <div
                                    id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>
                                    style="margin: auto">
                                    <div class='oneNegativeVoteBtn neutralVote'>
                                        <i class="demo-icon icon-empatiadefault_like-btn"></i>
                                        <?php if($votesInside){ ?>
                                            <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>
                        <?php  }?>
                    <?php } else { ?>
                        <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" style="margin: auto;">
                            <div class='oneNegativeVoteBtn neutralVote vote-button-opacity' style="cursor: default!important" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.vote_no_longer_possible') ?></div>">
                                <i class="demo-icon icon-empatiadefault_like-btn"></i>
                                <?php if($votesInside){ ?>
                                    <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>


            </div>
            <div class="positiveVoteNumberDivStyle">
                <div class="numberPositiveVote">
                    <?php if(!$votesInside){ ?>
                        <?php if ($likeCounter !== '') { ?>
                            <div class="voteNumber neutralVoteNumber">
                                <p><span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo $likeCounter; ?></span> <?php /*echo trans('vote.votes') */ ?>
                                </p>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if (count($configurations) > 0 && array_key_exists('allow_dislike', $configurations) && !empty($configurations["allow_dislike"])) { ?>
            <!-- DISLIKE -->
            <div class="oneNegativeVote-wrapper <?php echo ($styles['voting-button-negative-div']) ?? ''; ?> oneDislikeVote-wrapper">
                <div class="oneNegativeVoteBtn-div">
                    <?php if (!$disable) {

                        if ($typeVote == false || empty($securityConfigurationsVotes)){ ?>
                            <a class='<?php echo $buttonClass; ?>'
                               data='{"type":"Dislike","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                <div
                                    id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'hidden' : ''; ?>>
                                    <div class='oneNegativeVoteBtn positiveVote'>
                                        <i class="demo-icon icon-empatiadefault_dislike-btn"></i>
                                    </div>
                                </div>
                                <div
                                    id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'hidden' : ''; ?>>
                                    <div class='oneNegativeVoteBtn neutralVote'>
                                        <i class="demo-icon icon-empatiadefault_dislike-btn"></i>
                                    </div>
                                </div>
                            </a>
                        <?php     }else{?>
                            <a class='' href="javascript:messageLikes();"
                               data='{"type":"Dislike","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                <div
                                    id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'hidden' : ''; ?>>
                                    <div class='oneNegativeVoteBtn positiveVote'>
                                        <i class="demo-icon icon-empatiadefault_dislike-btn"></i>
                                    </div>
                                </div>
                                <div
                                    id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'hidden' : ''; ?>>
                                    <div class='oneNegativeVoteBtn neutralVote'>
                                        <i class="demo-icon icon-empatiadefault_dislike-btn"></i>
                                    </div>
                                </div>
                            </a>
                        <?php   }?>
                    <?php } else { ?>
                        <a href="<?php echo action('AuthController@login'); ?>">
                            <div
                                id="divNegativeUnselected_<?php echo $buttonClass; ?>" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
                                <div class='oneNegativeVoteBtn neutralVote vote-button-opacity'>
                                    <i class="demo-icon icon-empatiadefault_dislike-btn"></i>
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
                <div class="positiveVoteNumberDivStyle">
                    <div class="numberPositiveVote">
                        <?php if ($dislikeCounter !== '') { ?>
                            <div class="voteNumber neutralVoteNumber">
                                <p><span
                                        id="negativeCounter_<?php echo $buttonClass; ?>"><?php echo $dislikeCounter; ?></span> <?php /*echo trans('vote.votes') */ ?>
                                </p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>


    <div class="modal fade modal-followers" id="modalMessageLikes" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo trans("basic.information") ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <?php echo trans("basic.MissingConfigurationPermissions") ?>
                            <br><br>
                            <span class="sr-only">Error:</span>
                            <?php echo trans("basic.MissingFields:") ?>
                            <ul class="list-group">
                                <?php foreach ($parameters as  $value) {?>
                                    <li class="list-group-item">
                                        <?php echo $value ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if( !empty(Session::get('user')->user_key)){ ?>
                        <a href="<?php echo action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) ?>" class="btn btn-info" role="button"><?php echo trans("basicLayout.user_profile") ?></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans("basic.close") ?></button>
                    <?php } ?>

                </div>
            </div>

        </div>
    </div>

    <!-- JavaScript -->
    <script>

        function messageLikes(){
            $('#modalMessageLikes').modal('show');
        }


        $(".<?php echo $buttonClass; ?>").click(function () {
            //block buttons
            $(".votting-buttons").css('opacity', '0.5');
            $(".votting-buttons").css('pointer-events', 'none');
            var obj = jQuery.parseJSON($(this).attr("data"));
            // Vote
            $.ajax({
                method: obj.method, // Type of response and matches what we said in the route
                url: obj.url, // This is the url we gave in the route
                data: {
                    topicKey: obj.topicKey,
                    value: obj.value,
                    voteKey: obj.voteKey,
                    _token: obj.csrf_token
                }, // a JSON object to send back
                success: function (responseVote) { // What to do if we succeed
                    var response = JSON.parse(responseVote);

                    if (typeof response.vote != "undefined" && response.vote == '-1') {
                        // Buttons

                        $('#divNegative_<?php echo $buttonClass; ?>').show();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').hide();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();

                    } else if (typeof response.vote != "undefined" && response.vote == '1') {
                        // Buttons

                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').show();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').hide();

                    } else if (typeof response.vote != "undefined" && response.vote == '0') {

                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();

                    }

                    //enable buttons
                    $(".votting-buttons").css('opacity', '1');
                    $(".votting-buttons").css("pointer-events", "auto");

                    //Positive and negative counters
                    if (typeof response.totalPositive != "undefined") {
                        if ($('#positiveCounter_<?php echo $buttonClass; ?>').length != 0) {
                            <?php if($votesInside){ ?>
                            $('#positiveCounter_<?php echo $buttonClass; ?>').html('<span class="smaller">' + response.totalPositive + '</span>');
                            <?php }else{ ?>
                            $('#positiveCounter_<?php echo $buttonClass; ?>').text(response.totalPositive);
                            <?php } ?>
                        }
                        if ($('#negativeCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#negativeCounter_<?php echo $buttonClass; ?>').text(response.totalNegative);
                        }
                    }

                    var msg = "";
                    if (typeof response.total != "undefined") {
                        msg += "<?php echo trans("votes.remainingTotalVotes"); ?>: " + response['total'];
                    }
                    if (typeof response.negative != "undefined") {
                        msg += "<?php echo trans("votes.youCanUse"); ?>" + response['negative'] + "<?php echo trans("votes.negativeVotes"); ?>";
                    }

                    if (msg != "") {
                        toastr.info(msg);
                    }

                    $.ajax({
                        method: 'GET',
                        url: "<?php echo action('PublicTopicController@getQuestionnaireModalData', ['cbKey'=>$cbKey, 'topicKey' =>$topicKey, 'code' =>'vote_event', 'voteKey'=>$voteKey]); ?>",
                        success: function(response){
                            if(response!=='false'){
                                $('#questionnaireVotesModal').html(response);
                                $('#questionnaireVotesModal').modal('show');
                            }
                        }
                    });

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".votting-buttons").css('opacity', '1');
                    $(".votting-buttons").css("pointer-events", "auto");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    toastr.error("<?php echo trans('votes.errorOccurredWhileVoting'); ?>");
                }
            });
        });
    </script>

    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;

});

/**
 * Displays the Votes info Interface.
 *
 * @param Array $remainingVotes
 */
Html::macro('oneVoteInfo', function ($remainingVotes = []) {
    $html = "";
    ob_start();
    ?>
    <!-- Bootstrap Alert -->
    <div class='oneVoteInfo alert'>
        <!-- bootstrap close -->
        <a href="#" class="close oneVoteInfoClose" data-dismiss="alert">&times;</a>
        <?php if (isset($remainingVotes->total)) { ?>
            <?php echo trans("votes.remainingTotalVotes"); ?>: <span
                id="info-total-votes"><?php echo $remainingVotes->total ?></span>.<br/>
        <?php } ?>
        <?php if (isset($remainingVotes->negative)) { ?>
            <?php echo trans("votes.youCanUse"); ?> <span
                id="info-negative-votes"><?php echo $remainingVotes->negative ?></span> <?php echo trans("votes.negativeVotes"); ?>.
        <?php } ?>
    </div>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;

});
