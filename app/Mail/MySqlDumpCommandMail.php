<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MySqlDumpCommandMail extends Mailable
{
    use Queueable, SerializesModels;

    public $parameters = [
      'title' => 'MySqlDumpCommand completed',
      'message' => 'MySqlDumpCommand completed',
    ];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->view('emails.mysqldumpcommand', $this->parameters);
    }
}
