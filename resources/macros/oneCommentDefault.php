<?php

/*
 *
 *
Display the "Normal Comments" Interface
 *
 *
*/
Html::macro('oneCommentsNormal', function($messages, $usersNames, $cbKey, $type, $topicKey, $configurations, $isModerator, $canRemove = true) {
    $html = "";
    ob_start();
    ?>
    <!-- HTML -->
    <?php foreach ($messages as $message) { ?>
        <div class="col-xs-12 no-padding-left">
            <div class="row my-comment-row">
                <div class="box-footer box-comments" id="post_div_<?php echo $message->id ?>">
                    <div class="col-md-2 col-sm-4">
                        <div style="text-align: center;padding-top: 15px;">
                            <div style="height: 100px; width: 90px; text-align: center; margin: auto;">
                                <?php if (isset($usersNames[$message->created_by]) && $usersNames[$message->created_by]['photo_id'] > 0) { ?>
                                    <img style="width: 100%" class="img-circle"  src="<?php echo URL::action('FilesController@download',[$usersNames[$message->created_by]['photo_id'], $usersNames[$message->created_by]['photo_code'], 1]) ?>">

                                <?php } else { ?>
                                    <div class="text-center">
                                        <div class="new-user-img">
                                            <i class="fa fa-user fa-new-user"></i>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <p>
                                <?php if (isset($usersNames[$message->created_by]['name'])) { ?>
                                    <span class="user-name-comment"><?php echo $usersNames[$message->created_by]['name'] ?></span>
                                <?php } else { ?>
                                    <span class="user-name-comment"><?php echo trans('oneCommentDefault.anonymous') ?></span>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-8 forum-comment">
                        <div style="height: 30px; padding-right: 0px; padding-left: 0px;">
                            <div class="text-muted">
                                <?php if ($message->version != 1) { ?>
                                    <b><small style="cursor: pointer" onclick="showHistory('<?php echo $message->post_key ?>')"><i class="fa fa-info" ></i> <?php  echo  trans('oneCommentDefault.editedAt')  ?> <?php echo $message->updated_at ?></small></b>
                                <?php } else { ?>
                                    <small><i class="fa fa-clock-o" aria-hidden="true"></i> <?php  echo  trans('oneCommentDefault.createdAt')  ?> <?php echo $message->created_at ?></small>
                                <?php } ?>
                                <?php if(ONE::checkCBsOption($configurations, 'DISABLE-COMMENTS-FUNCTIONALITY') == false){ ?>
                                    <div class="text-muted pull-right">
                                        <?php if ((ONE::isAuth()) && (ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE')) && ($message->created_by != ONE::getUserKey())) { ?>
                                            <button type="button" class="btn btn-box-tool button-abuse" id="buttonAbuse_<?php echo $message->post_key ?>" onclick="reportAbuse('buttonAbuse_<?php echo $message->post_key ?>', '<?php echo $message->post_key ?>');" style="color:red; margin-top: -10px">
                                                <i class="fa fa-warning"></i> <?php echo trans('oneCommentDefault.reportAbuse')  ?>
                                            </button>
                                        <?php } ?>
                                        <?php if (ONE::isAuth() && ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS') || ONE::checkCBsOption($configurations, 'COMMENTS-ANONYMOUS')) { ?>
                                            <button type="button" class="btn btn-box-tool forum-comment-reply" id='reply_<?php echo $message->id ?>' onclick="replyPost('<?php echo $message->id ?>',this.id)" title="<?php echo trans("oneCommentDefault.reply");?>">
                                                <i class="fa fa-reply"></i>
                                            </button>
                                        <?php } ?>
                                        <?php if (ONE::isAuth() && ONE::getUserKey() == $message->created_by || $isModerator == 1) { ?>
                                            <div class="btn-group forum-comment-reply">
                                                <button type="button" class="btn btn-box-tool dropdown-toggle transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="<?php echo trans("oneCommentDefault.edit_or_remove");?>">
                                                    <i class="fa fa-pencil"> </i>
                                                </button>
                                                <div class="dropdown-menu  pull-right" style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center;background-color: #fafafa;font-size: 12px;">
                                                    <a class="dropdown-item" style="cursor: pointer" onclick="editPost('<?php echo $message->post_key ?>')"><?php echo trans('oneCommentDefault.edit') ?></a>
                                                    <?php if($canRemove){ ?>
                                                        <div class="divider"></div>
                                                        <a class="dropdown-item" href="javascript:oneDelete('<?php echo action('PublicPostController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'postKey' => $message->post_key,'type'=>$type])  ?>')"> <?php echo trans('oneCommentDefault.remove') ?> </a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- post text -->
                        <div>
                            <div id="post_contents_<?php echo $message->post_key ?>" style="padding-bottom: 20px"> <?php echo $message->contents ?></div>
                            <?php foreach(array_reverse($message->replies) as $reply) { ?>
                                <div style="margin-left: 10px; margin-top: 5px;">
                                    <small><b><i class="fa fa-reply" title="Reply"></i> <?php echo isset($usersNames[$reply->created_by]['name']) ? $usersNames[$reply->created_by]['name'] : $reply->created_by ?></b> <?php echo trans('oneCommentDefault.in') ?> <?php echo $reply->created_at ?></small>
                                    <?php if(ONE::checkCBsOption($configurations, 'DISABLE-COMMENTS-FUNCTIONALITY') == false){ ?>
                                        <div class="text-muted pull-right">
                                            <?php if ((ONE::isAuth()) && (ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE')) && ($reply->created_by != ONE::getUserKey())) { ?>
                                                <button type="button" class="btn btn-box-tool button-abuse" id="buttonAbuse_<?php echo $reply->post_key ?>" onclick="reportAbuse('buttonAbuse_<?php echo $reply->post_key ?>', '<?php echo $reply->post_key ?>');" style="color:red; margin-top: -10px">
                                                    <i class="fa fa-warning"></i> <?php echo trans('oneCommentDefault.reportAbuse')  ?>
                                                </button>
                                            <?php } ?>
                                            <?php if (ONE::isAuth() && ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS') || ONE::checkCBsOption($configurations, 'COMMENTS-ANONYMOUS')) { ?>
                                                <button type="button" class="btn btn-box-tool forum-comment-reply" id='reply_<?php echo $message->id ?>' onclick="replyPost('<?php echo $message->id ?>',this.id)" title="<?php echo trans("oneCommentDefault.reply");?>">
                                                    <i class="fa fa-reply"></i>
                                                </button>

                                            <?php } ?>
                                            <?php if (ONE::isAuth() && ONE::getUserKey() == $reply->created_by || $isModerator == 1) { ?>
                                                <div class="btn-group forum-comment-reply">
                                                    <button type="button" class="btn btn-box-tool dropdown-toggle transparent" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="<?php echo trans("oneCommentDefault.edit_or_remove");?>">
                                                        <i class="fa fa-pencil"> </i>
                                                    </button>
                                                    <div class="dropdown-menu  pull-right" style="width: 80px !important; min-width: 80px; max-width: 80px;text-align: center;background-color: #fafafa;font-size: 12px;">
                                                        <a class="dropdown-item" style="cursor: pointer" onclick="editPost('<?php echo $reply->post_key ?>')"><?php echo trans('oneCommentDefault.edit') ?></a>
                                                        <?php if($canRemove){ ?>
                                                            <div class="divider"></div>
                                                            <a class="dropdown-item" href="javascript:oneDelete('<?php echo action('PublicPostController@delete', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'postKey' => $reply->post_key,'type'=>$type])  ?>')"> <?php echo trans('oneCommentDefault.remove') ?> </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div id="post_contents_<?php echo $reply->post_key ?>" style=" padding: 15px;margin-bottom: 10px;"> <?php echo $reply->contents ?></div>

                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if(ONE::checkCBsOption($configurations, 'DISABLE-COMMENTS-FUNCTIONALITY') == false){ ?>
        <?php if (ONE::isAuth() && ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS') || ONE::checkCBsOption($configurations, 'COMMENTS-ANONYMOUS')) { ?>
            <div class="col-xs-12 no-padding-left">
                <div id="new_post">
                    <form name="topic" accept-charset="UTF-8" method="POST"
                          onsubmit="return validate('contents_area');"
                          action="<?php echo action('PublicPostController@store', ['topicKey' => $topicKey, 'type' => $type, 'cbKey' => $cbKey]) ?>">
                        <div class="col-md-2 col-sm-4 post-create-photo">
                            <div style="text-align: center; height: 200px;" class="padding-top-35">
                                <div style="height: 100px; width: 90px; text-align: center; margin: auto;">
                                    <?php if (Session::has('user') && Session::get('user')->photo_id > 0) { ?>
                                        <img style="width: 100%" class="img-circle"
                                             src="<?php echo URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1]) ?>">
                                    <?php } else { ?>
                                        <div class="text-center">
                                            <div class="new-user-img">
                                                <i class="fa fa-user fa-new-user"></i>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="user">
                                    <div><?php  Session::has('user') ? Session::get('user')->name : trans('oneCommentDefault.anonymous') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 col-sm-8 no-padding-left">
                            <div class="contents-parent">
                                <input type="hidden" name="_token" value="<?php echo csrf_token()  ?>"/>
                                <input type="hidden" name="parent_id" value="0"/>
                                <textarea type="text" name="contents" id="contents_area" rows="6" class="form-control" placeholder="<?php echo trans('oneCommentDefault.writeYourComment')?>" style="resize: none"></textarea>
                            </div>
                            <div class="box-footer clearfix " id="button-append" style="padding-left: 0;">
                                <button class="btn btn-sm btn-primary pull-left btn-flat btn-submit my-submit" type="submit"><?php echo trans('oneCommentDefault.submit') ?></button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        <?php } ?>
    <?php } ?>





    <?php
    $html .= ob_get_contents();
    ob_end_clean();
    return $html;
});

/**
 *  Display the "Positive And Negative Comments" Interface
 *
 * Configurations example:
 *
 *  $styles = [
 *      'cmt-title' => 'cmt-title',
 *      'cmt-panel-header' => 'cmt-panel-header',
 *      'cmt-panel-body' => 'cmt-panel-body',
 *      'cmt-add-comment-btn-box' => 'cmt-add-comment-btn-box'
 *  ]
 *
 * The array values are CSS selectors and must have a corresponding selector
 * and declaration block in a linked CSS file.
 *
 *
 * The following values ​​can be used in the configuration:
 *
 * | cmt-title -> title box
 * | cmt-panel-header -> main panel header box
 * | cmt-panel-body -> main panel body box
 * | cmt-box-new
 * | cmt-add-comment-btn-box -> new comment - add comment button box
 * | cmt-add-comment-btn -> new comment - add comment button
 * | cmt-new-comment-box -> new comment - add comment button
 * | cmt-user-img-box-new -> new comment - user avatar main box
 * | cmt-user-img-new -> new comment - user avatar inner box
 * | cmt-send-btn-box -> new comment - send comment button box
 * | cmt-send-btn -> new comment - send comment button
 * | cmt-main-box -> comment main box
 * | cmt-box -> comment inner box
 * | cmt-user-img-box -> user avatar main box
 * | cmt-user-img -> user avatar inner box
 * | cmt-info-box -> name and date information main box
 * | cmt-user-info -> username
 * | cmt-date-info -> comment date
 * | cmt-text-box -> comment text main box
 * | cmt-text -> comment text
 * | cmt-actions -> comment actions buttons box
 * | cmt-actions-btn-group -> action buttons group
 * | cmt-actions-btn -> action button
 * | cmt-actions-btn-options -> edit and remove action options
 * | cmt-report-abuse -> report abuse top layer
 * | cmt-report-abuse-btn -> report abuse button
 * | cmt-report-abuse-text -> if false, doesn't print the abuse text of the button
 * | cmt-no-comments -> no comments to display
 *
 */

Html::macro('oneComments', function($configurations, $topic, $comments, $usersNames, $cbKey, $type, $topicKey, $commentType, $styles = []) {
    $html = "";
    ob_start();
    ?>

    <!-- HTML -->
    <div class="panel <?php echo ($styles['cmt-panel-header']) ?? ''; ?>">
        <!-- Pannel Heading, Comments Type and Add Comment Button -->
        <div class="panel-heading">
            <div class="row">
                <div class="col-xs-12 comment-box-header <?php echo ($styles['cmt-title']) ?? ''; ?>">

                    <?php if (ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS') && ( (ONE::isAuth())|| ONE::checkCBsOption($configurations, 'COMMENTS-ANONYMOUS'))) { ?>
                        <div class="btn-commentDiv <?php echo ($styles['cmt-add-comment-btn-box']) ?? ''; ?>">
                            <button type="button" id="btn-<?php echo $commentType ?>" class="btn btn-default btn-comment <?php echo ($styles['cmt-add-comment-btn']) ?? ''; ?>">+</button>
                        </div>
                    <?php } ?>
                    <div class="comment-box-title"><?php echo trans('oneCommentDefault.'.$commentType.'Comments')?></div>
                </div>
            </div>
        </div>
        <!-- New Comment Body, New Comment Box, User Avatar and Send Comment Button -->
        <div class="panel-body <?php echo ($styles['cmt-panel-body']) ?? ''; ?>">
            <?php if (ONE::checkCBsOption($configurations, 'ALLOW-COMMENTS') && ( (ONE::isAuth())|| ONE::checkCBsOption($configurations, 'COMMENTS-ANONYMOUS'))) { ?>
                <div id="<?php echo $commentType ?>_comment" class="commentGroup collapse <?php echo ($styles['cmt-box-new']) ?? ''; ?> ">
                    <div class="col-md-2 newComment-userImg hidden-xs">
                        <!--User Avatar-->
                        <div class="loggedUserImg <?php echo ($styles['cmt-user-img-new']) ?? ''; ?> hidden-xs">
                            <?php if(!is_null(Session::get('user'))) {
                                if(Session::get('user')->photo_id > 0){ ?>
                                    <img class="img-responsive img-sm " src="<?php echo URL::action('FilesController@download',[Session::get('user')->photo_id, Session::get('user')->photo_code, 1])?>">
                                <?php } else { ?>
                                    <div class="text-center">
                                        <div class="new-user-img">
                                            <i class="fa fa-user fa-new-user"></i>
                                        </div>
                                    </div>
                                <?php }
                            } else { ?>
                                <img class="img-responsive img-sm" src="<?php echo  asset('images/default/icon-user-default-160x160.png') ?>">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-10 col-xs-12 no-padding-left">
                        <div class="row my-comment-row">
                            <!--New Comment Box-->
                            <div class="<?php echo ($styles['cmt-title']) ?? ''; ?>">
                                <form name="<?php echo $commentType ?>_comment" accept-charset="UTF-8" method="POST" id="<?php echo $commentType ?>_comment_form" onsubmit="return validate('new_post_contents') " action="<?php echo action('PublicPostController@store', ['topicId' => $topic->topic_key, 'commentType' => $commentType])?>">
                                    <div class="img-push">
                                        <input type="hidden" name="_token" value="<?php echo  csrf_token() ?>"/>
                                        <input type="hidden" name="parent_id" value="0"/>
                                        <div class="form-group newCommentBox">
                                            <textarea class="form-control" name="contents" rows="5" placeholder="<?php echo trans('oneCommentDefault.writeYourComment')?>"></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!--User Avatar and Send Comment Button-->
                            <div class="">

                            </div>
                            <!--Send Comment Button-->
                            <div class="row send-button-row <?php echo ($styles['cmt-send-btn-box']) ?? ''; ?>">
                                <div class="col-xs-12">
                                    <div class="margin-comment-button">
                                        <button onclick="comments()" type="submit" form="<?php echo $commentType ?>_comment_form" class="btn btn-outlined btn-success login-button btn-block <?php echo ($styles['cmt-send-btn']) ?? ''; ?>"><i class="fa fa-arrow-right" aria-hidden="true"></i><?php //echo  trans("oneCommentDefault.send") ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!--Existing Comments-->
            <?php
            if (!is_null($comments)){
                foreach ($comments as $comment){ ?>
                    <div class="well <?php echo ($styles['cmt-main-box']) ?? ''; ?>">
                        <div class="row">
                            <div class="col-xs-12 <?php echo ($styles['cmt-box']) ?? ''; ?>">
                                <div class="row">
                                    <!--User Avatar, Name and Comment Date-->
                                    <div class="col-md-4 col-sm-12 info-box<?php echo ($styles['cmt-user-img-box']) ?? ''; ?>">
                                        <!--User Avatar-->
                                        <div class="userImg commentProfile <?php echo ($styles['cmt-user-img']) ?? ''; ?>">
                                            <?php if (isset($usersNames[$comment->created_by]['photo_id']) && ($usersNames[$comment->created_by]['photo_id'] > 0)) { ?>
                                                <img class="img-sm" src="<?php echo URL::action('FilesController@download', [$usersNames[$comment->created_by]['photo_id'], $usersNames[$comment->created_by]['photo_code'], 1]) ?>">
                                            <?php } else { ?>
                                                <div class="text-center">
                                                    <div class="new-user-img">
                                                        <i class="fa fa-user fa-new-user"></i>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <!--User Name and Comment Date-->
                                        <div class="<?php echo ($styles['cmt-info-box']) ?? ''; ?>">
                                            <?php if (isset($usersNames[$comment->created_by])) { ?>
                                                <div class="username <?php echo ($styles['cmt-user-info']) ?? ''; ?>"><?php echo $usersNames[$comment->created_by]['name'] ?></div>
                                            <?php }else{  ?>
                                                <div class="username <?php echo ($styles['cmt-user-info']) ?? ''; ?>"><?php echo trans('oneCommentDefault.anonymous') ?></div>
                                            <?php }  ?>
                                            <div class="createdAt <?php echo ($styles['cmt-date-info']) ?? ''; ?>">
                                                <?php echo \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d H:i')?>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Comment Text and Comment Actions-->

                                    <!-- Comment Actions, Edit and Remove Button -->
                                    <?php if (ONE::isAuth() && isset($comment->created_by) && Session::has('user') && Session::get('user')->user_key == $comment->created_by) { ?>
                                        <div class="col-md-8 col-sm-12 commentActions <?php echo ($styles['cmt-actions']) ?? ''; ?>">
                                            <div class="btn-group <?php echo ($styles['cmt-actions-btn-group']) ?? ''; ?>">
                                                <button type="button" class="btn btn-box-tool dropdown-toggle <?php echo ($styles['cmt-actions-btn']) ?? ''; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-pencil" title="<?php echo trans('oneCommentDefault.editOrRemove') ?>"> </i>
                                                </button>
                                                <div class="dropdown-menu pull-right comment-actions <?php echo ($styles['cmt-actions-btn-options']) ?? ''; ?>">
                                                    <a class="dropdown-item comment-actions-item" onclick="editMessage('<?php echo $cbKey ?>','<?php echo $comment->post_key ?>','<?php echo $type ?>')"><?php echo trans("oneCommentDefault.edit") ?></a>
                                                    <div class="divider"></div>
                                                    <a class="dropdown-item" href="javascript:oneDelete('<?php echo action('PublicPostController@delete', ['cbKey' => $cbKey, 'topicKey' => $topicKey, 'postKey' => $comment->post_key, 'type' => $type]) ?>')"><?php echo trans("oneCommentDefault.remove") ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <!-- Report Abuse Button -->
                                    <?php if (!empty(Session::get('user')->user_key) && ONE::checkCBsOption($configurations, 'ALLOW-REPORT-ABUSE') && isset($comment->created_by) && $comment->created_by != Session::get('user')->user_key) { ?>
                                        <div class="col-md-8 col-sm-12 commentActions <?php echo ($styles['cmt-actions']) ?? ''; ?>">
                                            <div class="btn-group <?php echo ($styles['cmt-report-abuse']) ?? ''; ?>">
                                                <button type="button" class="btn btn-box-tool darkBtn <?php echo ($styles['cmt-report-abuse-btn']) ?? ''; ?>" id="buttonAbuse_<?php echo $comment->post_key ?>" onclick="reportAbuse('buttonAbuse_<?php echo $comment->post_key ?>', '<?php echo $comment->post_key ?>');">
                                                    <i class="fa fa-warning"></i>
                                                    <?php if (!isset($styles["cmt-report-abuse-text"]) || $styles["cmt-report-abuse-text"]!="false") { ?>
                                                        <?php echo trans('oneCommentDefault.reportAbuse') ?>
                                                    <?php } ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="col-md-8 col-sm-12 comment-text <?php echo ($styles['cmt-text-box']) ?? ''; ?>">
                                        <div class="commentText <?php echo ($styles['cmt-text']) ?? ''; ?>">
                                            <div class="comment-content" id="post_contents_<?php echo $comment->post_key ?>">
                                                <?php echo $comment->contents ?>
                                                <!-- TODO: Link to use with Dot Dot Dot Plugin-->
                                                <!-- <a href="#" class="readmore">Read more &raquo;</a>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else { ?>
                <div class="row">
                    <div class="col-xs-12 <?php echo ($styles['cmt-no-comments']) ?? ''; ?>">
                        <?php echo Html::oneMessageInfo(trans("oneCommentDefault.noCommentsToDisplay") )?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        $( "#btn-<?php echo $commentType ?>" ).click(function() {
            $('.commentGroup').collapse('hide');
            $('#<?php echo $commentType ?>_comment').collapse('show');
            $('.btn-comment').html('+');
            $(this).html('-');

            $("#<?php echo $commentType ?>_comment").on('hidden.bs.collapse', function(){
                $("#btn-<?php echo $commentType ?>").html('+');
            });
        });

        $btnComments = $(".btn-comments");

        function comments(){
            $btnComments.css('opacity','0.5');
            $btnComments.css('pointer-events','none');
            return true;
        }

        <!-- Dot Dot Dot -->
        /*            $(document).ready(function () {
         $(".comment-content").dotdotdot({
         ellipsis: '... ',
         wrap: 'word',
         watch: true,
         height: 100,
         aft: null
         });
         });*/


    </script>

    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});