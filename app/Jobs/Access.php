<?php

namespace App\Jobs;

use App\One\One;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Access extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $url = null;
    protected $ip = null;
    protected $sessionId = null;
    protected $action = null;
    protected $result = null;
    protected $user_Key = null;
    protected $topic_key = null;
    protected $details = null;
    protected $error = null;
    protected $cb_key = null;
    protected $post_key = null;
    protected $q_key = null;
    protected $vote_key = null;
    protected $content_key = null;
    protected $entity_key = null;
    protected $site_key = null;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $url, $ip, $sessionId,$action, $result,$user_Key,$topic_key, $details, $error, $content_key, $cb_key, $post_key, $q_key, $vote_key, $entity_key, $site_key)
    {
        $this->url = $url;
        $this->ip = $ip;
        $this->sessionId = $sessionId;
        $this->action = $action;
        $this->result = $result;
        $this->user_Key = $user_Key;
        $this->topic_key = $topic_key;
        $this->details = $details;
        $this->error = $error;
        $this->content_key = $content_key;
        $this->cb_key = $cb_key;
        $this->post_key = $post_key;
        $this->q_key = $q_key;
        $this->vote_key = $vote_key;
        $this->entity_key = $entity_key;
        $this->site_key = $site_key;

    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ONE::post([
            'component' => 'logs',
            'api' => 'access',
            'params' => [
                'url'           => $this->url,
                'ip'            => $this->ip,
                'session_id'    => $this->sessionId,
                'entity_key'    => $this->entity_key,
                'site_key'      => $this->site_key,
                'action'        => $this->action,
                'result'        => $this->result,
                'user_key'      => $this->user_Key,
                'topic_key'     => $this->topic_key,
                'details'       => $this->details,
                'error'         => $this->error,
                'content_key'   => $this->content_key,
                'cb_key'        => $this->cb_key,
                'post_key'      => $this->post_key,
                'q_key'         => $this->q_key,
                'vote_key'      => $this->vote_key
            ]
        ]);
    }
}
