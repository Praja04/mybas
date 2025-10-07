<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PKWNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $title;
    protected $app;
    protected $description;
    protected $link;

    public function __construct($app, $title, $description, $link = '')
    {
        $this->title = $title;
        $this->app = $app;
        $this->description = $description;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'app' => $this->app,
            'title' => $this->title,
            'description' => $this->description,
            'link' => $this->link
        ];
        return $this->view('mail.pkw-notification', ['data' => $data]);
    }
}
