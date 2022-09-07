<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadPublicEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
//        $address = 'adil.raza@systemsltd.com';
//        $name = 'WorkSuite Payroll Systems';
        
        $address = 'timothy@indema.co';
        $name = 'Indema';
        if(isset($this->data->FromEmail) && !empty($this->data->FromEmail)) {
            $address = $this->data->FromEmail;
        }
        if(isset($this->data->FromName) && !empty($this->data->FromName)) {
            $name = $this->data->FromName;
        }

        $headerData = [
            'category' => 'category',
            'unique_args' => [
                'variable_1' => 'abc'
            ]
        ];

        $header = $this->asString($headerData);

        $this->withSwiftMessage(function ($message) use ($header) {
            $message->getHeaders()
                    ->addTextHeader('X-SMTPAPI', $header);
        });

        return $this->view('admin.lead.email_public')
                    ->from($address, $name)
                    ->replyTo($address, $name)
                    // ->subject($this->data['subject'])
                    // ->with(['bodyMessage' => htmlspecialchars($this->data['message'])]);
                    ->subject($this->data->Subject)
                    ->with(['companyName' => htmlspecialchars($this->data->companyName), 'LeadName' => htmlspecialchars($this->data->LeadName) , 'LeadEmail' => htmlspecialchars($this->data->LeadEmail) , 'LeadPhone' => htmlspecialchars($this->data->LeadPhone)]); 
    }

    private function asJSON($data)
    {
        $json = json_encode($data);
        $json = preg_replace('/(["\]}])([,:])(["\[{])/', '$1$2 $3', $json);
        return $json;
    }


    private function asString($data)
    {
        $json = $this->asJSON($data);
        return wordwrap($json, 76, "\n   ");
    }
}
