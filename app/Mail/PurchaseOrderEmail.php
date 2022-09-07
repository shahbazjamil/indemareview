<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderEmail extends Mailable
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
        
        if($this->data->pdf){
            
            if(isset($this->data->specification_file) && !empty($this->data->specification_file)) {
                return $this->view('admin.vendor.email')
                    ->from($address, $name)
                    ->replyTo($address, $name)
                    // ->subject($this->data['subject'])
                    // ->with(['bodyMessage' => htmlspecialchars($this->data['message'])]);
                    ->subject($this->data->Subject)
                    ->attachData($this->data->pdf->output(), $this->data->filename . '.pdf')
                    ->attach($this->data->specification_file)
                    ->with(['bodyMessage' => htmlspecialchars($this->data->Message)]);
                
            } else {
                return $this->view('admin.vendor.email')
                    ->from($address, $name)
                    ->replyTo($address, $name)
                    // ->subject($this->data['subject'])
                    // ->with(['bodyMessage' => htmlspecialchars($this->data['message'])]);
                    ->subject($this->data->Subject)
                    ->attachData($this->data->pdf->output(), $this->data->filename . '.pdf')
                    ->with(['bodyMessage' => htmlspecialchars($this->data->Message)]);
            }
            
            
        } else {
            
            return $this->view('admin.vendor.email')
                    ->from($address, $name)
                    ->replyTo($address, $name)
                    // ->subject($this->data['subject'])
                    // ->with(['bodyMessage' => htmlspecialchars($this->data['message'])]);
                    ->subject($this->data->Subject)
                    ->with(['bodyMessage' => htmlspecialchars($this->data->Message)]);
            
        }

        
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
