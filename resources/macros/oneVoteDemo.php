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
Html::macro('oneNegativeVotingDemo', function ($topicKey, $cbKey, $voteKey, $showTotalVotes, $likeCounter, $dislikeCounter, $allReadyVoted, $configurations = [], $styles = [], $disable = false,$securityConfigurationsVotes=[]) {
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
                                    $parameters[] = trans("demoCbsIdea.email_verification");
                                } elseif ($parameterUserTypeName == 'manual_verification'){
                                    $parameters[] = trans("demoCbsIdea.manual_verification");
                                }  elseif ($parameterUserTypeName == 'sms_verification') {
                                    $parameters[] = trans("demoCbsIdea.sms_verification");
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

    <div class="row votting-buttons <?php echo ($styles['voting-buttons-div']) ?? ''; ?>">
        <div class="col-12 align-self-end idea-details-buttons">
            <div class="row buttons-row">
                <!-- Negative vote -->
                    <div class="col-6 <?php echo ($styles['voting-button-negative-div']) ?? ''; ?>">
                        <?php if (!$disable) {
                            if ($typeVote==false || empty($securityConfigurationsVotes)){ ?>
                                <a class='<?php echo $buttonClass; ?> negativeVoteBtnStyle' data='{"type":"NegativeVoting","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                            <div id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php }else{?>
                        <a class='negativeVoteBtnStyle' href="javascript:messageNegativeVoting();">
                            <div id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>

                    <?php  } ?>
                <?php } else { ?>
                    <a href="<?php echo action('AuthController@login'); ?>">
                        <div id="divNegativeUnselected_<?php echo $buttonClass; ?>" style="margin: auto" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
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

        <!-- Positive vote  -->

        <div class="oneNegativeVote-wrapper <?php echo ($styles['voting-button-positive-div']) ?? ''; ?>">
            <div class="oneNegativeVoteBtn-div">
                <?php if (!$disable) {
                        
                    if ($typeVote==false || empty($securityConfigurationsVotes)){ ?>
                        <a class='<?php echo $buttonClass; ?> positiveVoteBtnStyle' data='{"type":"NegativeVoting","topicKey": "<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                            <div id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php } else{ ?>
                        <a class='positiveVoteBtnStyle' href ="javascript:messageNegativeVoting();" >
                            <div id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                            <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>>
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                    <?php  } ?>
                <?php } else { ?>
                    <a href="<?php echo action('AuthController@login'); ?>">
                        <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" style="margin: auto" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
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

        
    </div>


    <div class="modal fade modal-followers" id="modalMessageNegativeVoting" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo trans("demo.information") ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <?php echo trans("demo.MissingConfigurationPermissions") ?>
                            <br><br>
                            <span class="sr-only">Error:</span>
                            <?php echo trans("demo.MissingFields:") ?>
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
                        <a href="<?php echo action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) ?>" class="btn btn-info" role="button"><?php echo trans("demoLayout.user_profile") ?></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans("demo.close") ?></button>
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
                    } else {
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

Html::macro('oneMultiVotingDemo', function ($topicKey, $cbKey, $voteKey, $likeCounter, $allReadyVoted, $configurations = [], $styles = [], $login = false, $votedLabel = false, $voteLabel = false, $reloadIfSuccess = false, $votesInside = false, $loginLevels = [], $submited = true, $cbType = "") {
    $disable = false;

    $html = "";
    $buttonClass = "oneMultiVotingDemo" . $topicKey . $voteKey;
    ob_start();

    $loginLevelKeyNotAllowed = [];

    if(!empty($loginLevels)){
        foreach($loginLevels->vote as $key => $level){

            if(!$level->allowed)
                $loginLevelKeyNotAllowed[] = $key;
        }
    }

    $voted = ($allReadyVoted) ? 'voted' : 'vote';
    $selected = 'voted';
    $unselected = 'vote';

    ?>
    <!-- Multi Voting -->
    <div class="row votting-buttons multipleVote-wrapper">
        <div class="col-12 align-self-end idea-details-buttons <?php echo ($styles['voting-button-positive-div']) ?? ''; ?> positive-btn-container" style="padding: 0! important;">
            <?php if (array_key_exists('allow_multiple_per_one', $configurations) && $configurations['allow_multiple_per_one'] == 0) { ?>
                <!-- Positive button only -->
                <div class="col-12  button-like <?php echo ($styles['voting-button-negative-div']) ?? ''; ?>">
                    <?php if ($login) { ?>
                        <?php if(!$submited){


                            if(empty($loginLevelKeyNotAllowed)){ ?>
                                <a class='<?php echo $voted ?>  <?php echo $buttonClass; ?>' style="pointer-events: auto" data='{"type":"MultiVoting","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                    <div>
                                            <!-- <i class="fa fa-check fas"></i> -->
                                            <span id="voteLabel<?php echo $buttonClass; ?>" style="margin-left: 5px; font-weight: bold">
                                                <?php if($allReadyVoted){ ?>
                                                    <?php echo ONE::transCb('cb_voted', $cbKey); ?>
                                                <?php }else{ ?>
                                                    <?php echo ONE::transCb('cb_vote', $cbKey); ?>
                                                <?php } ?>

                                            <?php if($likeCounter!="") { ?>
                                                <span id="positiveCounter_<?php echo $buttonClass; ?>"><span class="smaller"><?php echo $likeCounter; ?></span></span>
                                            <?php } ?>
                                        </div>
                                    </span>
                                </a>




                            <?php } else { ?>
                                <a class='like-vote-button' href="javascript:messageMultiVoting();">
				<span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo ONE::transCb('cb_vote', $cbKey); ?></span>
                                    <?php if($likeCounter!=""){ ?>
                                        <span class="smaller">(<?php echo $likeCounter; ?>)</span>
                                    <?php } ?>

                                    <!-- <span>
                                        <div>
                                            <span style="margin-right: 5px; font-weight: bold">
                                                <?php if($allReadyVoted){ ?>
                                                    Votada
                                                <?php }else{ ?>
                                                    Vota
                                                <?php } ?>
                                            </span><i class="fa fa-heart" aria-hidden="true"></i>
                                        </div>
                                    </span> -->
                                </a>
                            <?php } ?>
                        <?php }else{ ?>
                            <?php if($allReadyVoted) { ?>
                                <a class='voted submited <?php echo $voted ?>'
                                style="pointer-events: none; color: white; display: flex;justify-content: center;align-items: center;font-size: 1rem;text-transform: uppercase;font-weight: 600;">
                                    <span>
                                        <div>
                                            <span >
                                                <?php echo ONE::transCb('cb_voted', $cbKey); ?>
                                            </span>
                                        </div>
                                    </span>
                                </a>
                            <?php }else{ ?>
                                <a class='like-vote-button-off submited like-vote-button facebook-button pads-facebook-buttons'>
                                    <span>
                                        <div>
                                            <span style="margin-right: 5px; font-weight: bold">
                                                <?php echo ONE::transCb('cb_not_voted', $cbKey); ?>
                                            </span>
                                        </div>
                                    </span>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    <?php }?>
                </div>

                <!-- <?php if (!$votesInside) { ?>
                    <div class="">
                        <?php if ($likeCounter !== '') { ?>
                            <p class="word"> <?php echo trans('vote.votes') ?>:
                                <span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo $likeCounter; ?></span>
                            </p>
                        <?php } ?>
                    </div>
                <?php } ?> -->

            <?php } ?>
            </span>
        </div>
    </div>

    <!--
        Already reached limit votes
    -->
    <div class="modal fade modal-followers" id="modalMessageLimitVotes" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><?php echo ONE::transCb("cb_modal_information", $cbKey) ?></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <?php echo ONE::transCb("cb_rechead_limit_votes_message", $cbKey) ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo ONE::transCb("modal_button_limitevote_close", $cbKey) ?></button>
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
                    <h4 class="modal-title"><?php echo ONE::transCb("cb_information", $cbKey) ?></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" style="text-align:left">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <?php echo ONE::transCb("cb_missing_configurations_permissions", $cbKey) ?>
                    <br><br>
                    <span class="sr-only">Error:</span>
                    <ul class="square-bullet">
                    <?php if(!empty($loginLevelKeyNotAllowed)){
                                foreach($loginLevelKeyNotAllowed as $loginLevelKey){
                                    if(isset($loginLevels->vote->$loginLevelKey)){
                                        foreach($loginLevels->vote->$loginLevelKey->missingAttributesPerLevel as $attribute){
                                            foreach($attribute as $parameter){
                                                //                                                if(!in_array($missingAttribute, $missingAttributes)){?>
                                                <li>
                                                    <?php echo $parameter; ?>
                                                </li>
                                                <!--                                            --><?php //  }?>
                                            <?php   }?>
                                        <?php   }?>
                                    <?php   }?>
                                <?php   }?>
                            <?php   }else{?>

                            <?php   }?>    
                    </ul>
                </div>
                <div class="modal-footer">
                    <?php if (!empty(Session::get('user')->user_key)) { ?>
                        <button type="button" id="close-modal-login-levels" class="cancel-btn"
                                data-dismiss="modal"><?php echo ONE::transCb("cb_close", $cbKey) ?></button>
                        <a href="<?php echo action('PublicUsersController@edit', ['userKey' => Session::get('user')->user_key, 'f' => 'user']) ?>"
                            class="submit-btn" role="button"><?php echo ONE::transCb("cb_user_profile", $cbKey) ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function messageAlreadyVoted(){
            $('#modalMessageLimitVotes').modal('show');
            $("#votesRemaining").hide();
            $(".voteBannerSubmit").show();
            $(".submited-votes").hide();

        }

        function messageMultiVoting() {
            setTimeout(function(){
                $('#modalMessageMultiVoting').modal('show');
            },1000);
        }

        $(".<?php echo $buttonClass; ?>").click(function (event) {
            //block buttons
            $(".<?php echo $buttonClass; ?>").closest(".votting-buttons").css('opacity', '0.5');
            $(".<?php echo $buttonClass; ?>").closest(".votting-buttons").css('pointer-events', 'none');
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
                    console.log(response);
                    if(response.total !=0){
                        $("#votesRemaining").show();
                        $(".voteBannerSubmit").hide();
                        $(".submited-votes").hide();
                        $("#totalVotes").html(response.total);
                        // User votes && total votes counters

                    }
                    if(response.total == 0){
                        $("#votesRemaining").hide();
                        $(".voteBannerSubmit").show();
                        $(".submited-votes").hide();
                        $("#totalVotes").html(response.total);
                    }


                    if (typeof response.vote != "undefined" && response.vote >= 0) {
console.log(response);
                        if(response.userVotes == 2){
                                $("#submitVotesButton").css("pointer-events", "auto")
                            }else{
                                $("#submitVotesButton").css("pointer-events", "none")
                            }

                        //buttons | USING [id] NOTATION ON PURPOSE
                        if (response.vote == 0 ){
                            if(! $('#submitVotesButton').hasClass('submit-votes-div-off') ){
                                $('#submitVotesButton').addClass('submit-votes-div-off')    ;
                            }
                            $('.<?php echo $buttonClass; ?>').removeClass('voted');
                            $('.<?php echo $buttonClass; ?> > div > span').html('<?php echo ONE::transCb('cb_vote', $cbKey); ?>');

                        } else if (response.vote == 1) {
                            if($('#submitVotesButto<?php echo $buttonClass; ?>').hasClass('submit-votes-div-off')){
                                $('#submitVotesButton').removeClass('submit-votes-div-off');
                            }
                            
                            // $(".<?php echo $buttonClass; ?>").removeClass('vote').addClass('voted');
                            $(".<?php echo $buttonClass; ?>").addClass('voted');
                            $('.<?php echo $buttonClass; ?> > span > div > span').html('<?php echo ONE::transCb('cb_voted', $cbKey); ?>');
                            $('#voteLabel<?php echo $buttonClass; ?>').html('<?php echo ONE::transCb('cb_voted', $cbKey); ?>');
                            $('#label-banner-votes').text('Já votaste num projeto. Agora submete o teu voto!');
                        }

                        if(typeof response.userVotes !== 'undefined'){
                            $("#total_votes<?php echo $voteKey; ?>").text(response.total);
                        }
                        if(typeof response.userVotes !== 'undefined' ){
                            $("#user_votes<?php echo $voteKey; ?>").text(response.userVotes);
                        }


                        // count votes
                        $('#remainingCounter_<?php echo $voteKey; ?>').text(response.total);
                        $('#countVotes_<?php echo $buttonClass; ?>').text(response.vote);
                    }

                    //enable buttons
                    $(".<?php echo $buttonClass; ?>").closest(".votting-buttons").css('opacity', '1');
                    $(".<?php echo $buttonClass; ?>").closest(".votting-buttons").css("pointer-events", "auto");


                    window.tmp = "<?php echo $buttonClass; ?>";

                    //Positive and negative counters

                    if (typeof response.totalPositive != "undefined") {
                        if ($('#voteLabel<?php echo $buttonClass; ?>').length != 0) {
                            <?php if($likeCounter!="") { ?>
                            $('#voteLabel<?php echo $buttonClass; ?>').append('&nbsp;<span class="smaller">' + response.totalPositive + '</span>');
                            <?php } else{ ?>
                            //$('#positiveCounter_<?php echo $buttonClass; ?>').text(response.totalPositive);
                            <?php } ?>
                        }
                    }

                    if (typeof response.errorMsg != "undefined") {
                        messageAlreadyVoted();
                        //Error message
                        if ($("#votes-error-modal").length) {
                            $("#votes-error-modal").find(".modal-body").html(response.errorMsg);
                            $("#votes-error-modal").modal("show");
                        }
                    } else {
                        // message for remaining votes
                        var msg = "";
                        if (typeof response.total != "undefined" && response.total != -1) {
                            msg += "<?php echo trans("votes.remainingTotalVotes"); ?>: " + response['total'];
                        }
                        if (msg != "") {
                            if (typeof response.userVotes != "undefined") {
                                $("#total-votes-count-for").html(response.userVotes);
                                if (parseInt(response.userVotes) > 0) {
                                    $(".btn-submit-votes").removeClass("disabled");
                                } else {
                                    $(".btn-submit-votes").addClass("disabled");
                                }
                            }
                            <?php if($reloadIfSuccess){ ?>
                            location.reload();
                            <?php } ?>
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".<?php echo $buttonClass; ?>").closest(".votting-buttons").css('opacity', '1');
                    $(".<?php echo $buttonClass; ?>").closest(".votting-buttons").css("pointer-events", "auto");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
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
Html::macro('oneLikesDemo', function ($topicKey, $cbKey, $voteKey, $likeCounter, $dislikeCounter, $allReadyVoted, $configurations = [], $styles = [], $disable = false, $votesInside = false, $securityConfigurationsVotes = [], $keepVisible = false) {
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
                                    $parameters[] = trans("demoCbsIdea.email_verification");
                                } elseif ($parameterUserTypeName == 'manual_verification'){
                                    $parameters[] = trans("demoCbsIdea.manual_verification");
                                }  elseif ($parameterUserTypeName == 'sms_verification') {
                                    $parameters[] = trans("demoCbsIdea.sms_verification");
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

    <div class="row votting-buttons <?php echo ($styles['voting-buttons-div']) ?? ''; ?>">
        <div class="col-12 align-self-end idea-details-buttons">
            <div class="row buttons-row <?php echo ($styles['voting-buttons-div']) ?? ''; ?>">
                <?php if (count($configurations) > 0 && array_key_exists('allow_dislike', $configurations) && !empty($configurations["allow_dislike"])) { ?>
                <!-- DISLIKE -->
                    <div class="col-6 <?php echo ($styles['voting-button-negative-div']) ?? ''; ?>" style="padding: 0! important;">
                        <?php if (!$disable) {
                            if ($typeVote == false || empty($securityConfigurationsVotes)){ ?>
                                <a class='<?php echo $buttonClass; ?> button-link-wrapper' data='{"type":"Dislike","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                    <div id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'style="display:none;"' : ''; ?>>
                                        <div class="button-dislike"><i class="fa fa-thumbs-down" aria-hidden="true"></i><span id="negativeCounter_<?php echo $buttonClass; ?>">Dislike</span>
                                            <?php if ($dislikeCounter !== '') { ?>
                                                <span class="smaller"> (<?php echo $dislikeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'style="display:none;"' : ''; ?>>
                                        <div class="button-dislike"><i class="fa fa-thumbs-down" aria-hidden="true"></i><span id="negativeCounter_<?php echo $buttonClass; ?>">Dislike</span>
                                            <?php if ($dislikeCounter !== '') { ?>
                                                <span class="smaller"> (<?php echo $dislikeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </a>
                            <?php }else{ ?>
                                <a class='button-link-wrapper' href="javascript:messageLikes();" data='{"type":"Dislike","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                    <div id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'style="display:none;"' : ''; ?>>
                                        <div class="button-dislike"><i class="fa fa-thumbs-down" aria-hidden="true"></i><span id="negativeCounter_<?php echo $buttonClass; ?>">Dislike</span>
                                            <?php if ($dislikeCounter !== '') { ?>
                                                <span class="smaller"> (<?php echo $dislikeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'style="display:none;"' : ''; ?>>
                                        <div class="button-dislike"><i class="fa fa-thumbs-down" aria-hidden="true"></i><span id="negativeCounter_<?php echo $buttonClass; ?>">Dislike</span>
                                            <?php if ($dislikeCounter !== '') { ?>
                                                <span class="smaller"> (<?php echo $dislikeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <a href="<?php echo action('AuthController@login'); ?>" class="button-link-wrapper">
                                <div id="divNegativeUnselected_<?php echo $buttonClass; ?>" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>">
                                    <div class="button-dislike"><i class="fa fa-thumbs-down" aria-hidden="true"></i><span id="negativeCounter_<?php echo $buttonClass; ?>">Dislike</span>
                                        <?php if ($dislikeCounter !== '') { ?>
                                            <span class="smaller"> (<?php echo $dislikeCounter; ?>)</span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
                
                <!-- LIKE -->
                                        
                <div class="<?php if (count($configurations) > 0 && array_key_exists('allow_dislike', $configurations) && !empty($configurations["allow_dislike"])){ echo 'col-6';} else { echo 'col-12'    ;} ?>  oneNegativeVote-wrapper <?php echo ($styles['voting-button-positive-div']) ?? ''; ?>" style="padding: 0! important;" >
                    <?php if($keepVisible){ ?>
                        <div id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'style="display:none;"' : ''; ?>>
                            <div class='button-like'><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="positiveCounter_<?php echo $buttonClass; ?>">Like</span>
                                <?php if($votesInside){ ?>
                                    <span class="smaller"> (<?php echo $likeCounter; ?>)</span>
                                <?php } ?>
                            </div>
                        </div>
                        <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'style="display:none;"' : ''; ?>>
                            <div class='button-like'><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="positiveCounter_<?php echo $buttonClass; ?>">Like</span>
                                <?php if($votesInside){ ?>
                                    <span class="smaller"> (<?php echo $likeCounter; ?>)</span>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <?php if (!$disable) {
                            if ($typeVote == false || empty($securityConfigurationsVotes)){ ?>
                                <a class='<?php echo $buttonClass; ?> button-link-wrapper' data='{"type":"Like","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote", [$cbKey]); ?>", "method":"POST"}'>
                                    <div id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'style="display:none;"' : ''; ?>>
                                        <div class="button-like"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="positiveCounter_<?php echo $buttonClass; ?>">Like</span>
                                            <?php if($votesInside){ ?>
                                                <span class="smaller"> (<?php echo $likeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'style="display:none;"' : ''; ?>>
                                        <div class="button-like"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="positiveCounter_<?php echo $buttonClass; ?>">Like</span>
                                            <?php if($votesInside){ ?>
                                                <span class="smaller"> (<?php echo $likeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </a>
                            <?php }else{ ?>
                                <a class='button-link-wrapper' href="javascript:messageLikes();">
                                    <div id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'style="display:none;"' : ''; ?>>
                                        <div class='button-like'><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="positiveCounter_<?php echo $buttonClass; ?>">Like</span>
                                            <?php if($votesInside){ ?>
                                                <span class="smaller"> (<?php echo $likeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'style="display:none;"' : ''; ?>>
                                        <div class='button-like'><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="positiveCounter_<?php echo $buttonClass; ?>">Like</span>
                                            <?php if($votesInside){ ?>
                                                <span class="smaller"> (<?php echo $likeCounter; ?>)</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <a href="<?php echo action('AuthController@login'); ?>" class="button-link-wrapper">
                                <div id="divPositiveUnselected_<?php echo $buttonClass; ?>">
                                    <div class='button-like' style="cursor: default!important" data-toggle="tooltip" data-html="true" data-original-title="<div style='display:inline;'><i class='fa fa-exclamation-circle' aria-hidden='true' style='display:inline;'></i>&nbsp;<?php echo trans('vote.login_to_vote') ?></div>"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span id="positiveCounter_<?php echo $buttonClass; ?>">Like</span>
                                        <?php if($votesInside){ ?>
                                            <span class="smaller"> (<?php echo $likeCounter; ?>)</span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div>
                    <?php if(!$votesInside){ ?>
                        <?php if ($likeCounter !== '') { ?>
                            <div class="voteNumber neutralVoteNumber">
                                <span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo $likeCounter; ?></span> <?php /*echo trans('vote.votes') */ ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-followers" id="modalMessageLikes" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><?php echo trans("demo.information") ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="alert alert-danger" role="alert">
                            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                            <?php echo trans("demo.MissingConfigurationPermissions") ?>
                            <br><br>
                            <span class="sr-only">Error:</span>
                            <?php echo trans("demo.MissingFields:") ?>
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
                        <a href="<?php echo action('PublicUsersController@edit',['userKey' => Session::get('user')->user_key,'f' => 'user']) ?>" class="btn btn-info" role="button"><?php echo trans("demoLayout.user_profile") ?></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo trans("demo.close") ?></button>
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
            console.log(".<?php echo $buttonClass; ?>");
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

                    // User votes && total votes counters
                    if(typeof response.userVotes !== 'undefined'){
                        $("#total_votes<?php echo $voteKey; ?>").html(response.total);
                    }
                    if(typeof response.userVotes !== 'undefined' ){
                        $("#user_votes<?php echo $voteKey; ?>").html(response.userVotes);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".votting-buttons").css('opacity', '1');
                    $(".votting-buttons").css("pointer-events", "auto");
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
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
