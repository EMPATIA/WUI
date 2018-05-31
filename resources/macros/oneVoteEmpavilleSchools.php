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
 *   @foreach($voteType as $vt)
 *       @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
 *           {!! Html::oneNegativeVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
 *           {!! Html::oneMultiVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
 *           {!! Html::oneLikes($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @endif
 *   @endforeach
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
Html::macro('oneEmpavilleSchoolsNegativeVoting', function($topicKey,$cbKey,$voteKey,$likeCounter,$dislikeCounter,$allReadyVoted,$configurations = []) {
    $html = "";
    ob_start();

    $buttonClass = "oneNegativeVoting".$topicKey.$voteKey;
    ?>
    <!-- HTML -->
    <!-- HTML -->
    <div class="row oneNegativeVotingRow oneNegativeVotingRowStyle votting-buttons">
        <div class=" col-xs-12 oneNegativeVotingCol oneNegativeVotingColStyle oneNegativeVote-wrapper">
            <!-- Positive vote  -->
            <div class="oneNegativeVoteBtn-div">
                <a class='<?php echo $buttonClass; ?> positiveVoteBtnStyle' data='{"type":"NegativeVoting","topicKey": "<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote",[$cbKey]); ?>", "method":"POST"}'>
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
                    <?php
                    if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 2){
                        echo "<label class='label_plus' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:none;" : "display:block;")."'  >".$configurations["plusLabels"][0]."</label>";
                    } else   if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 1){
                        echo "<label>".$configurations["plusLabels"][0]."</label>";
                    }
                    if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 2){
                        echo "<label class='label_plus_selected' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:block;" : "display:none;")."' >".$configurations["plusLabels"][1]."</label>";
                    }
                    ?>
                </a>
            </div>
<!--            <div class="positiveVoteNumberDivStyle">-->
<!--                <div class="numberPositiveVote">-->
<!--                        <div class="voteNumber neutralVoteNumber" >-->
<!--                            <p><span id="positiveCounter_--><?php //echo $buttonClass; ?><!--">--><?php //echo $likeCounter; ?><!--</span> --><?php //echo trans('vote.votes') ?><!--</p>-->
<!--                        </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>

        <div class=" col-xs-12 oneNegativeVotingCol oneNegativeVotingColStyle oneNegativeVote-wrapper">
            <!-- Negative vote -->
            <div class="oneNegativeVoteBtn-div">
                <a class='<?php echo $buttonClass; ?> negativeVoteBtnStyle' data='{"type":"NegativeVoting","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote",[$cbKey]); ?>", "method":"POST"}'>
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
                    <?php
                    if( count($configurations) > 0 && array_key_exists('minusLabels', $configurations) && count($configurations["minusLabels"]) == 2 ){
                        echo "<label class='label_minus' style='".((!empty($allReadyVoted) && $allReadyVoted == -1) ? "display:none;" : "display:block;")."'>".$configurations["minusLabels"][0]."</label>";
                    } else if( count($configurations) > 0 && array_key_exists('minusLabels', $configurations) && count($configurations["minusLabels"]) == 1 ){
                        echo "<label>".$configurations["minusLabels"][0]."</label>";
                    }
                    if(count($configurations) > 0 && array_key_exists('minusLabels', $configurations) && count($configurations["minusLabels"]) == 2){
                        echo "<label class='label_minus_selected' style='".((!empty($allReadyVoted) && $allReadyVoted == -1) ? "display:block;" : "display:none;")."' >".$configurations["minusLabels"][1]."</label>";
                    }
                    ?>
                </a>
            </div>
<!--            <div class="negativeVoteNumberDivStyle">-->
<!--                <div class="numberNegativeVote">-->
<!--                    <div class="voteNumber neutralVoteNumber" >-->
<!--                        <p><span id="negativeCounter_--><?php //echo $buttonClass; ?><!--">--><?php //echo $dislikeCounter; ?><!--</span> --><?php //echo trans('vote.votes') ?><!--</p>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        $( ".<?php echo $buttonClass; ?>").click(function() {
            //block buttons
            $(".votting-buttons").css('opacity','0.5');
            $(".votting-buttons").css('pointer-events','none');
            var obj = jQuery.parseJSON( $(this).attr("data") );
            // Vote
            $.ajax({
                method: obj.method, // Type of response and matches what we said in the route
                url: obj.url, // This is the url we gave in the route
                data: {
                    topicKey: obj.topicKey,
                    value: obj.value,
                    voteKey: obj.voteKey,
                    _token:  obj.csrf_token
                }, // a JSON object to send back
                success: function (responseVote) { // What to do if we succeed
                    var response = JSON.parse(responseVote);

                    if (typeof response.vote != "undefined" &&  response.vote == '-1') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').show();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').hide();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();
                        // Labels
                        $(".<?php echo $buttonClass; ?> .label_minus").hide();
                        $(".<?php echo $buttonClass; ?> .label_minus_selected").show();
                        $(".<?php echo $buttonClass; ?> .label_plus").show();
                        $(".<?php echo $buttonClass; ?> .label_plus_selected").hide();
                    } else if (typeof response.vote != "undefined" && response.vote == '1') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').show();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').hide();
                        // Labels
                        $(".<?php echo $buttonClass; ?> .label_plus").hide();
                        $(".<?php echo $buttonClass; ?> .label_plus_selected").show();
                        $(".<?php echo $buttonClass; ?> .label_minus").show();
                        $(".<?php echo $buttonClass; ?> .label_minus_selected").hide();
                    } else if (typeof response.vote != "undefined" && response.vote == '0') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();
                        // Labels
                        $(".<?php echo $buttonClass; ?> .label_plus").show();
                        $(".<?php echo $buttonClass; ?> .label_plus_selected").hide();
                        $(".<?php echo $buttonClass; ?> .label_minus").show();
                        $(".<?php echo $buttonClass; ?> .label_minus_selected").hide();
                    }
                    //Positive and negative counters
                    if(typeof response.totalPositive != "undefined") {
                        if ($('#positiveCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#positiveCounter_<?php echo $buttonClass; ?>').text(response.totalPositive);
                        }
                        if ($('#negativeCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#negativeCounter_<?php echo $buttonClass; ?>').text(response.totalNegative);
                        }
                    }

                    //enable buttons
                    $(".votting-buttons").css('opacity','1');
                    $(".votting-buttons").css("pointer-events","auto");

                    if ($(".oneVoteInfo")[0]){

                        if(typeof response.total != "undefined"){
                            document.getElementById('info-total-votes').innerHTML = response['total'];
                        }
                        if(typeof response.negative != "undefined"){
                            document.getElementById('info-negative-votes').innerHTML = response['negative'];
                        }
                    }

                    //message for remaining votes
                    var msg = "";
                    if(typeof response.total != "undefined"){
                        msg += "<?php echo trans("votes.remainingTotalVotes"); ?>: " + response['total']+". " ;
                    }
                    //message for negative votes
                    if(typeof response.negative != "undefined"){
                        msg += "<?php echo trans("votes.youCanUse"); ?> " + response['negative'] + " <?php echo trans("votes.negativeVotes"); ?>";
                    }

                    if(msg!="") {
                        toastr.info(msg);
                    }else {
                        toastr.error('<?php echo trans('vote.youDontHaveMore') ?>');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".votting-buttons").css('opacity','1');
                    $(".votting-buttons").css("pointer-events","auto");
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
 * Displays the Multi Voting Interface.
 *
 * Configurations example:
 *   $configurations["plusLabels"] = ["plus","plus"];
 *
 *  Example:
 *   @foreach($voteType as $vt)
 *       @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
 *           {!! Html::oneNegativeVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
 *           {!! Html::oneMultiVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
 *           {!! Html::oneLikes($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @endif
 *   @endforeach
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
Html::macro('oneEmpavilleSchoolsMultipleVoting', function($topicKey,$cbKey,$voteKey,$likeCounter,$allReadyVoted,$configurations = []) {
    $html = "";
    $buttonClass = "oneMultiVoting".$topicKey.$voteKey;

    ob_start();
    ?>
    <!-- Multi Voting -->
    <div class="multipleVote-wrapper">
        <?php if(array_key_exists('allow_multiple_per_one', $configurations) && $configurations['allow_multiple_per_one'] == 0 ){?>
            <div class="">
                <div class="">
                    <!-- Positive button only -->
                    <a class='votting-buttons btn btn-xs <?php echo $buttonClass; ?>' data='{"type":"MultiVoting","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote",[$cbKey]); ?>", "method":"POST"}'>
                        <div id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?>>
                            <div class="multiVoteOnlyPositiveBtnDiv">
                                <div class='oneNegativeVoteBtn positiveVote'>
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?>>
                            <div class="multiVoteOnlyPositiveBtnDiv">
                                <div class='oneNegativeVoteBtn neutralVote'>
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                        <?php
                        if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 2){
                            echo "<label class='label_plus' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:none;" : "display:block;")."'  >".$configurations["plusLabels"][0]."</label>";
                        } else   if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 1){
                            echo "<label>".$configurations["plusLabels"][0]."</label>";
                        }
                        if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 2){
                            echo "<label class='label_plus_selected' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:block;" : "display:none;")."' >".$configurations["plusLabels"][1]."</label>";
                        }
                        ?>
                    </a>
                </div>

                <div class="multipleVoteCol">
                    <?php if($likeCounter !== ''){ ?>
                        <div class="voteNumberMultipleVote yourVote" id="countVotes_<?php echo $buttonClass; ?>" >
                            <p class=""> <?php echo  trans('vote.total')?> <?php echo  trans('vote.votes')?>:
                                <span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo isset($allReadyVoted) ? $allReadyVoted : '0'; ?></span>
                            </p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } elseif(array_key_exists('allow_multiple_per_one', $configurations) && $configurations['allow_multiple_per_one'] == 1 ){?>

            <!-- Positive button -->
            <!-- Coluna de Botoes -->
            <div class="multipleVoteCol">
                <div class="multipleVoteBtn">
                    <a class='votting-buttons <?php echo $buttonClass; ?>' data='{"type":"MultiVoting","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote",[$cbKey]); ?>", "method":"POST"}'>
                        <div>
                            <div class='oneNegativeVoteBtn positiveVote'>
                                <i class="fa fa-plus" aria-hidden="true"></i>
                            </div>
                        </div>
                        <?php
                        if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 2){
                            echo "<label class='label_plus' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:none;" : "display:block;")."'  >".$configurations["plusLabels"][0]."</label>";
                        } else   if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 1){
                            echo "<label>".$configurations["plusLabels"][0]."</label>";
                        }
                        if(count($configurations) > 0 && array_key_exists('plusLabels', $configurations) && count($configurations["plusLabels"]) == 2){
                            echo "<label class='label_plus_selected' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:block;" : "display:none;")."' >".$configurations["plusLabels"][1]."</label>";
                        }
                        ?>
                    </a>
                </div>
                <!-- Negative button -->
                <div class="multipleVoteBtn">
                    <a class='votting-buttons <?php echo $buttonClass; ?>' data='{"type":"MultiVoting","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote",[$cbKey]); ?>", "method":"POST"}'>
                        <div>
                            <div class='oneNegativeVoteBtn neutralVote'>
                                <i class="fa fa-minus" aria-hidden="true"></i>
                            </div>
                        </div>
                        <?php
                        if( count($configurations) > 0 && array_key_exists('dislikeLabels', $configurations) && count($configurations["dislikeLabels"]) == 2 ){
                            echo "<label class='label_minus' style='".((!empty($allReadyVoted) && $allReadyVoted == -1) ? "display:none;" : "display:block;")."'>".$configurations["dislikeLabels"][0]."</label>";
                        } else if( count($configurations) > 0 && array_key_exists('minusLabels', $configurations) && count($configurations["dislikeLabels"]) == 1 ){
                            echo "<label>".$configurations["dislikeLabels"][0]."</label>";
                        }
                        if(count($configurations) > 0 && array_key_exists('minusLabels', $configurations) && count($configurations["dislikeLabels"]) == 2){
                            echo "<label class='label_minus_selected' style='".((!empty($allReadyVoted) && $allReadyVoted == -1) ? "display:block;" : "display:none;")."' >".$configurations["dislikeLabels"][1]."</label>";
                        }
                        ?>
                    </a>
                </div>
            </div>

            <!-- Multiple vote counts -->
            <!-- Coluna de Texto/Vote count -->
            <div class="multipleVoteCol">
                <div class="voteNumberMultipleVote yourVote" id="countVotes_<?php echo $buttonClass; ?>" >
                    <p class=""> <?php echo  trans('vote.total')?> <?php echo  trans('vote.votes')?>:
                        <span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo isset($allReadyVoted) ? $allReadyVoted : '0'; ?></span>
                    </p>
                </div>
                <div class="voteNumberMultipleVote">
                    <?php if($likeCounter != ''){ ?>
                        <p class=""> <?php echo  trans('vote.total')?> <?php echo  trans('vote.votes')?>:
                            <span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo $likeCounter; ?></span>
                        </p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <!-- Somethings happening here! JavaScript -->
    <script>
        $( ".<?php echo $buttonClass; ?>" ).click(function() {
            //block buttons
            $(".votting-buttons").css('opacity','0.5');
            $(".votting-buttons").css('pointer-events','none');
            var obj = jQuery.parseJSON( $(this).attr("data") );
            // Vote
            $.ajax({
                method: obj.method, // Type of response and matches what we said in the route
                url: obj.url, // This is the url we gave in the route
                data: {
                    topicKey: obj.topicKey,
                    value: obj.value,
                    voteKey: obj.voteKey,
                    _token:  obj.csrf_token
                }, // a JSON object to send back
                success: function (responseVote) { // What to do if we succeed
                    var response = JSON.parse(responseVote);
                    if (typeof response.vote != "undefined" && response.vote >= '0') {
                        //buttons
                        if(response.vote == '0' && $('#divPositive_<?php echo $buttonClass; ?>').length != 0) {
                            $('#divPositive_<?php echo $buttonClass; ?>').hide();
                            $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();
                        }else if(response.vote == '1' && $('#divPositive_<?php echo $buttonClass; ?>').length != 0){
                            $('#divPositive_<?php echo $buttonClass; ?>').show();
                            $('#divPositiveUnselected_<?php echo $buttonClass; ?>').hide();
                        }
                        // count votes
                        $('#countVotes_<?php echo $buttonClass; ?>').text(response.vote);
                        // Labels
                        $(".<?php echo $buttonClass; ?> .label_plus").hide();
                        $(".<?php echo $buttonClass; ?> .label_plus_selected").show();
                    }

                    //enable buttons
                    $(".votting-buttons").css('opacity','1');
                    $(".votting-buttons").css("pointer-events","auto");

                    //Positive and negative counters
                    if(typeof response.totalPositive != "undefined") {
                        if ($('#positiveCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#positiveCounter_<?php echo $buttonClass; ?>').text(response.totalPositive);
                        }
                    }

                    if(typeof response.errorMsg != "undefined") {
                        //Error message
                        toastr.error(response.errorMsg);
                    }else{
                        //message for remaining votes
                        var msg = "";
                        if(typeof response.total != "undefined"){
                            msg += "<?php echo trans("votes.remainingTotalVotes"); ?>: "+ response['total'];
                        }

                        if(msg!="") {
                            toastr.info(msg);
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".votting-buttons").css('opacity','1');
                    $(".votting-buttons").css("pointer-events","auto");
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
 *   @foreach($voteType as $vt)
 *       @if( $vt["method"] == "VOTE_METHOD_NEGATIVE" )
 *           {!! Html::oneNegativeVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
 *           {!! Html::oneMultiVoting($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
 *           {!! Html::oneLikes($topic->topic_key,$cb->cb_key,$vt["key"],$topic->statistics->like_counter, $topic->statistics->dislike_counter, !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,$configurations) !!}
 *       @endif
 *   @endforeach
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
Html::macro('oneEmpavilleSchoolsLikesVoting', function($topicKey,$cbKey,$voteKey,$likeCounter,$dislikeCounter,$allReadyVoted,$configurations = []) {
    $html = "";
    $buttonClass = "oneLikes".$topicKey.$voteKey;
    ob_start();
    ?>
    <!-- HTML -->
    <div class="votting-buttons">
<!--        <div class="--><?php //echo (count($configurations) > 0 && array_key_exists('allow_dislike', $configurations) && $configurations["allow_dislike"] == 1) ? "col-md-6 col-sm-6" : "col-md-6" ?><!-- col-lg-6 col-xs-12 oneLikesColStyle">-->
        <!-- LIKE -->
        <div class="oneNegativeVote-wrapper">
            <div class="oneNegativeVoteBtn-div">
                <a class=' <?php echo $buttonClass; ?>' data='{"type":"Like","topicKey":"<?php echo $topicKey; ?>","value":1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote",[$cbKey]); ?>", "method":"POST"}'>
                    <div id="divPositive_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != 1) ? 'hidden' : ''; ?> style="margin: auto">
                        <div class='oneNegativeVoteBtn positiveVote'>
                            <i class="demo-icon icon-empatiadefault_like-btn"></i>
                        </div>
                    </div>
                    <div id="divPositiveUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == 1) ? 'hidden' : ''; ?> style="margin: auto">
                        <div class='oneNegativeVoteBtn neutralVote'>
                            <i class="demo-icon icon-empatiadefault_like-btn"></i>
                        </div>
                    </div>
                    <?php
                    if(count($configurations) > 0 && array_key_exists('likeLabels', $configurations) && count($configurations["likeLabels"]) == 2){
                        echo "<label class='label_plus' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:none;" : "display:block;")."'  >".$configurations["likeLabels"][0]."</label>";
                    } else   if(count($configurations) > 0 && array_key_exists('likeLabels', $configurations) && count($configurations["likeLabels"]) == 1){
                        echo "<label>".$configurations["likeLabels"][0]."</label>";
                    }
                    if(count($configurations) > 0 && array_key_exists('likeLabels', $configurations) && count($configurations["likeLabels"]) == 2){
                        echo "<label class='label_plus_selected' style='".((!empty($allReadyVoted) && $allReadyVoted == 1) ? "display:block;" : "display:none;")."' >".$configurations["likeLabels"][1]."</label>";
                    }
                    ?>
                </a>
            </div>
            <div class="positiveVoteNumberDivStyle">
                <div class="numberPositiveVote">
                    <?php if($likeCounter !== ''){ ?>
                        <div class="voteNumber neutralVoteNumber" >
                            <p><span id="positiveCounter_<?php echo $buttonClass; ?>"><?php echo $likeCounter; ?></span> <?php echo trans('vote.votes') ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if(count($configurations) > 0 && array_key_exists('allow_dislike', $configurations) ){?>
            <div class="oneNegativeVote-wrapper">
                <!-- DISLIKE -->
                <div class="oneNegativeVoteBtn-div">
                    <a class='<?php echo $buttonClass; ?>' data='{"type":"Dislike","topicKey":"<?php echo $topicKey; ?>","value":-1,"voteKey":"<?php echo $voteKey; ?>","csrf_token":"<?php echo csrf_token(); ?>","url": "<?php echo action("PublicTopicController@vote",[$cbKey]); ?>", "method":"POST"}'>
                        <div id="divNegative_<?php echo $buttonClass; ?>" <?php echo (empty($allReadyVoted) || $allReadyVoted != -1) ? 'hidden' : ''; ?>>
                            <div class='oneNegativeVoteBtn positiveVote'>
                                <i class="demo-icon icon-empatiadefault_dislike-btn"></i>
                            </div>
                        </div>
                        <div id="divNegativeUnselected_<?php echo $buttonClass; ?>" <?php echo (!empty($allReadyVoted) && $allReadyVoted == -1) ? 'hidden' : ''; ?>>
                            <div class='oneNegativeVoteBtn neutralVote'>
                                <i class="demo-icon icon-empatiadefault_dislike-btn"></i>
                            </div>
                        </div>
                        <?php
                        if( count($configurations) > 0 && array_key_exists('dislikeLabels', $configurations) && count($configurations["dislikeLabels"]) == 2 ){
                            echo "<label class='label_minus' style='".((!empty($allReadyVoted) && $allReadyVoted == -1) ? "display:none;" : "display:block;")."'>".$configurations["dislikeLabels"][0]."</label>";
                        } else if( count($configurations) > 0 && array_key_exists('minusLabels', $configurations) && count($configurations["dislikeLabels"]) == 1 ){
                            echo "<label>".$configurations["dislikeLabels"][0]."</label>";
                        }
                        if(count($configurations) > 0 && array_key_exists('minusLabels', $configurations) && count($configurations["dislikeLabels"]) == 2){
                            echo "<label class='label_minus_selected' style='".((!empty($allReadyVoted) && $allReadyVoted == -1) ? "display:block;" : "display:none;")."' >".$configurations["dislikeLabels"][1]."</label>";
                        }
                        ?>
                    </a>
                </div>
                <div class="positiveVoteNumberDivStyle">
                    <div class="numberPositiveVote">
                        <?php if($dislikeCounter !== ''){ ?>
                            <div class="voteNumber neutralVoteNumber" >
                                <p><span id="negativeCounter_<?php echo $buttonClass; ?>"><?php echo $dislikeCounter; ?></span> <?php echo trans('vote.votes') ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        <?php } ?>
    </div>
    <!-- JavaScript -->
    <script>
        $( ".<?php echo $buttonClass; ?>" ).click(function() {
            //block buttons
            $(".votting-buttons").css('opacity','0.5');
            $(".votting-buttons").css('pointer-events','none');
            var obj = jQuery.parseJSON($(this).attr("data"));
            // Vote
            $.ajax({
                method: obj.method, // Type of response and matches what we said in the route
                url: obj.url, // This is the url we gave in the route
                data: {
                    topicKey: obj.topicKey,
                    value: obj.value,
                    voteKey: obj.voteKey,
                    _token:  obj.csrf_token
                }, // a JSON object to send back
                success: function (responseVote) { // What to do if we succeed
                    var response = JSON.parse(responseVote);

                    if (typeof response.vote != "undefined" &&  response.vote == '-1') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').show();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').hide();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();
                        // Labels
                        $(".<?php echo $buttonClass; ?> .label_minus").hide();
                        $(".<?php echo $buttonClass; ?> .label_minus_selected").show();
                        $(".<?php echo $buttonClass; ?> .label_plus").show();
                        $(".<?php echo $buttonClass; ?> .label_plus_selected").hide();
                    } else if (typeof response.vote != "undefined" && response.vote == '1') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').show();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').hide();
                        // Labels
                        $(".<?php echo $buttonClass; ?> .label_plus").hide();
                        $(".<?php echo $buttonClass; ?> .label_plus_selected").show();
                        $(".<?php echo $buttonClass; ?> .label_minus").show();
                        $(".<?php echo $buttonClass; ?> .label_minus_selected").hide();
                    } else if (typeof response.vote != "undefined" && response.vote == '0') {
                        // Buttons
                        $('#divNegative_<?php echo $buttonClass; ?>').hide();
                        $('#divNegativeUnselected_<?php echo $buttonClass; ?>').show();
                        $('#divPositive_<?php echo $buttonClass; ?>').hide();
                        $('#divPositiveUnselected_<?php echo $buttonClass; ?>').show();
                        // Labels
                        $(".<?php echo $buttonClass; ?> .label_plus").show();
                        $(".<?php echo $buttonClass; ?> .label_plus_selected").hide();
                        $(".<?php echo $buttonClass; ?> .label_minus").show();
                        $(".<?php echo $buttonClass; ?> .label_minus_selected").hide();
                    }

                    //enable buttons
                    $(".votting-buttons").css('opacity','1');
                    $(".votting-buttons").css("pointer-events","auto");

                    //Positive and negative counters
                    if(typeof response.totalPositive != "undefined") {
                        if ($('#positiveCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#positiveCounter_<?php echo $buttonClass; ?>').text(response.totalPositive);
                        }
                        if ($('#negativeCounter_<?php echo $buttonClass; ?>').length != 0) {
                            $('#negativeCounter_<?php echo $buttonClass; ?>').text(response.totalNegative);
                        }
                    }

                    var msg = "";
                    if(typeof response.total != "undefined"){
                        msg += "<?php echo trans("votes.remainingTotalVotes"); ?>: "+ response['total'];
                    }
                    if(typeof response.negative != "undefined"){
                        msg += "<?php echo trans("votes.youCanUse"); ?>" + response['negative'] + "<?php echo trans("votes.negativeVotes"); ?>";
                    }

                    if(msg!="") {
                        toastr.info(msg);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    //enable buttons
                    $(".votting-buttons").css('opacity','1');
                    $(".votting-buttons").css("pointer-events","auto");
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
 * Displays the Votes info Interface.
 *
 * @param Array $remainingVotes*/
Html::macro('oneVoteInfo', function($remainingVotes = []) {
    $html = "";
    ob_start();
    ?>
    <!-- Bootstrap Alert -->
    <div class='oneVoteInfo alert'>
        <!-- bootstrap close -->
        <a href="#" class="close oneVoteInfoClose" data-dismiss="alert">&times;</a>
        <?php if(isset($remainingVotes->total)){ ?>
            <?php echo trans("votes.remainingTotalVotes"); ?>: <span id="info-total-votes"><?php echo $remainingVotes->total?></span>.<br/>
        <?php } ?>
        <?php if(isset($remainingVotes->negative)){ ?>
            <?php echo trans("votes.youCanUse"); ?> <span id="info-negative-votes"><?php echo $remainingVotes->negative?></span> <?php echo trans("votes.negativeVotes"); ?>.
        <?php } ?>
    </div>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;

});
