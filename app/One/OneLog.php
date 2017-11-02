<?php
namespace App\One;

use App\Jobs\SendLog;
use Illuminate\Foundation\Bus\DispatchesJobs;


class OneLog {
    use DispatchesJobs;
    
    /**
     * Adds a log record at the DEBUG level.
     *
     * @param string $message The log message
     */
    public static function debug($message){
        $job = OneLog::log("info",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }
    
    /**
     * Adds a log record at the INFO level.
     *
     * @param string $message The log message
     */
    public static function info($message){
        $job = OneLog::log("info",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }
    
    /**
     * Adds a log record at the NOTICE level.
     *
     * @param string $message The log message
     */
    public static function notice($message){
        $job = OneLog::log("notice",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }    
        
    /**
     * Adds a log record at the ERROR level.
     *
     * @param string $message The log message
     */
    public static function error($message){
        $job = OneLog::log("error",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }

    /**
     * Adds a log record at the CRITICAL level.
     *
     * @param string $message The log message
     */
    public static function critical($message){
        $job = OneLog::log("crtical",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }    

    /**
     * Adds a log record at the ALERT level.
     *
     * @param string $message The log message
     */
    public static function alert($message){
        $job = OneLog::log("crtical",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }    
    
    /**
      * Adds a log record at the EMERGENCY level.
      *
      * @param string $message The log message
      */
     public static function emergency($message){
        $job = OneLog::log("emergency",$message);
        app('Illuminate\Contracts\Bus\Dispatcher')->dispatch($job);
    }         
    
    /**
     * Sets object to create the job «SendLog»
     *
     * @param string $type
     * @param string $message
     * @static 
     */
    private static function log($type, $message){
        $job = new SendLog();
        $job->setType($type);
        $job->setMessage($message);
        return $job;
    }    
    

}