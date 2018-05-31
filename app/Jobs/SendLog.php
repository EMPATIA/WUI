<?php

namespace App\Jobs;

use App\One\One;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLog extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $type = "";
    private $message = "";
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }
    
    /**
     * Sets log type.
     *
     * @return void
     */
    public function setType($type)
    {
       $this->type = $type;
    }
    
    /**
     * Sets log message.
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }    
    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        One::sendLog($this->type, $this->message);
    }
}
