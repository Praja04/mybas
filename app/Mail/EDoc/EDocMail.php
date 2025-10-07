<?php

namespace App\Mail\EDoc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EDocMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $title;
    protected $app;
    protected $description;
    protected $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
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
        // dd($data);
        return $this->view('mail.edoc.edoc_notification', ['data' => $data]);
    }
}
