<?php

namespace App\Mail;

use App\Job;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PiqlIt extends Mailable
{
    use Queueable, SerializesModels;

    private $job;
    private $host;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Job $job, string $host)
    {
        $this->job = $job;
        $this->host = $host;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	$user = User::find($this->job->owner);
    	return $this->view('bucket.emails.piqlit')
            ->with([
            		'jobName' => $this->job->name,
            		'userName' => $user->full_name,
                    'host' => $this->host,
            ]);
    }
}
