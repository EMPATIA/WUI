<?php
	
if(!function_exists ('alert')) 
{
	function alert($title = null, $message = null)
	{
	    $flash = app('Infinety\Alerts\Flash');
	    if (func_num_args() == 0) {
	        return $flash;
	    }
	    return $flash->info($title, $message);
	}
}
