<?php
/**
 * Created by PhpStorm.
 * User: Vitor Fonseca
 * Date: 18/04/2017
 * Time: 14:12
 */

namespace App\Jobs;


use App\ComModules\LogsRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ONE;

class Logger implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $method;
    protected $parameters;

    public function __construct($method, $parameters)
    {
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function handle()
    {
        LogsRequest::setTracking($this->method, $this->parameters);
    }
}