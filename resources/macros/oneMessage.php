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
        <strong><?php if($infoBefore) echo ONE::transSite("message_info"); ?></strong><br><?php echo $message?>
    </div>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});