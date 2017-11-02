<?php

/**
 * Displays the message info.
 *
*/

Html::macro('oneMessageInfo', function($message, $infoBefore = true) {
    $html = "";
    ob_start();
    ?>
    <div class="alert alert-info">
        <span class="glyphicon glyphicon-info-sign"></span>
        <strong><?php if($infoBefore) echo trans("messageInfo.info"); ?></strong> <?php echo $message?>
    </div>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});