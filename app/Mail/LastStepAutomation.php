<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LastStepAutomation extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->data['email_type'] == '1'){
            return $this->subject($this->data['email_template']['subject'])
                ->with($this->data)
                ->view('mail.automations.last_step_automation');
        }else{
            return $this->subject($this->data['email_template']['subject'])
                ->attach($this->data['email_template']['gmail_file_url'])
                ->with($this->data)
                ->view('mail.automations.last_step_automation');
        }
    }
}
