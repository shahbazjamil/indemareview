<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{

    protected $table = 'email_configurations';

    protected $fillable = [
        "user_id",
        "company_id",
        "driver",
        "host",
        "port",
        "encryption",
        "user_name" ,
        "password",
        "sender_name",
        "sender_email"
    ];

    /**
     * @return array
     */
    public function verifySmtpConnection()
    {
        if ($this->driver == 'smtp') {
            try {
                $transport = new \Swift_SmtpTransport($this->host, $this->port, $this->encryption);
                $transport->setUsername($this->user_name);
                $transport->setPassword($this->password);

                $mailer = new \Swift_Mailer($transport);
                $mailer->getTransport()->start();

                return [
                    'success' => true,
                    'message' => __('messages.smtpSuccess')
                ];
            } catch (\Swift_TransportException $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
    }
}
